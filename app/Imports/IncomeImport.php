<?php

namespace App\Imports;

use App\Models\Toko;
use App\Models\Order;
use App\Models\Income;
use Illuminate\Validation\Rule;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Carbon\Carbon;

class IncomeImport implements ToCollection, WithHeadingRow
{
    private $failedOrders = [];
    private $rowCount = 0;
    private $successCount = 0;
    private $defaultTokoId;
    private $defaultMarketplace;

    /**
     * Constructor untuk menerima default values
     */
    public function __construct($defaultTokoId = null, $defaultMarketplace = null)
    {
        $this->defaultTokoId = $defaultTokoId;
        $this->defaultMarketplace = $defaultMarketplace;
    }

    /**
     * Main method untuk memproses collection dari Excel
     */
    public function collection(Collection $rows)
    {
        $this->rowCount = count($rows);

        foreach ($rows as $index => $row) {
            $rowNumber = $index + 2; // +2 karena heading row + base 1

            try {
                // Normalisasi nama kolom untuk membaca berbagai format
                $noPesanan = $this->getCellValue($row, ['no_pesanan', 'no pesanan', 'nomor_pesanan']);
                $noPengajuan = $this->getCellValue($row, ['no_pengajuan', 'no pengajuan', 'nomor_pengajuan']);
                $totalPenghasilan = $this->getCellValue($row, ['total_penghasilan', 'total penghasilan', 'penghasilan']);
                $tokoId = $this->getCellValue($row, ['toko_id', 'toko id', 'id_toko', 'id toko']);
                // TAMBAH: Ambil kolom marketplace dari Excel
                $marketplace = $this->getCellValue($row, ['marketplace', 'market_place', 'platform']);

                // Ambil kolom tanggal dari Excel
                $tanggalDibuat = $this->getCellValue($row, ['created_at', 'tanggal', 'tanggal_dibuat', 'tgl_dibuat', 'date', 'tanggal_buat']);

                // Skip baris kosong
                if (empty($noPesanan) && empty($noPengajuan) && empty($totalPenghasilan)) {
                    continue;
                }

                // Handle toko_id - gunakan dari Excel atau default
                $finalTokoId = $this->determineTokoId($tokoId, $rowNumber, $noPesanan);
                if ($finalTokoId === false) {
                    continue; // Skip jika toko tidak valid
                }

                // Handle marketplace - gunakan dari Excel atau default
                $finalMarketplace = $this->determineMarketplace($marketplace, $rowNumber, $noPesanan);
                if ($finalMarketplace === false) {
                    continue; // Skip jika marketplace tidak valid
                }

                // Parse tanggal dari Excel
                $parsedDate = $this->parseExcelDate($tanggalDibuat, $rowNumber, $noPesanan);

                // Siapkan data untuk disimpan
                $data = [
                    'no_pesanan' => $this->parseNoPesanan($noPesanan),
                    'no_pengajuan' => $this->parseNoPengajuan($noPengajuan),
                    'total_penghasilan' => $this->parseInteger($totalPenghasilan),
                    'toko_id' => $finalTokoId,
                    'marketplace' => $finalMarketplace, // TAMBAH: Marketplace
                    // HANYA created_at yang diinput dari Excel, updated_at = created_at
                    'created_at' => $parsedDate,
                    'updated_at' => $parsedDate, // Sama dengan created_at
                ];

                // Validasi dasar - HAPUS validasi unique untuk no_pesanan
                $validator = Validator::make($data, [
                    'no_pesanan' => [
                        'required',
                        'string',
                        'max:100',
                        // HAPUS: Rule::unique('incomes', 'no_pesanan')
                        // no_pesanan tidak harus unique, bisa ada data duplikat
                    ],
                    'no_pengajuan' => 'nullable|string|max:100',
                    'total_penghasilan' => 'required|integer',
                    'toko_id' => 'required|exists:tokos,id',
                    'marketplace' => 'required|in:Shopee,Tiktok', // TAMBAH: Validasi marketplace
                    'created_at' => 'required|date',
                    // updated_at TIDAK divalidasi karena otomatis = created_at
                ], [
                    'no_pesanan.required' => 'Nomor pesanan wajib diisi',
                    // HAPUS: 'no_pesanan.unique' => 'Nomor pesanan sudah ada dalam database',
                    'total_penghasilan.required' => 'Total penghasilan wajib diisi',
                    'total_penghasilan.integer' => 'Total penghasilan harus berupa angka',
                    'toko_id.required' => 'Toko ID wajib diisi',
                    'toko_id.exists' => 'Toko ID tidak valid atau tidak ditemukan',
                    'marketplace.required' => 'Marketplace wajib diisi', // TAMBAH: Pesan error
                    'marketplace.in' => 'Marketplace harus Shopee atau Tiktok', // TAMBAH: Pesan error
                    'created_at.required' => 'Tanggal dibuat wajib diisi',
                    'created_at.date' => 'Format tanggal dibuat tidak valid',
                    // Tidak ada pesan error untuk updated_at
                ]);

                if ($validator->fails()) {
                    $this->failedOrders[] = [
                        'no_pesanan' => $data['no_pesanan'] ?? 'Tidak diketahui',
                        'toko_id' => $tokoId ?? '-',
                        'marketplace' => $marketplace ?? '-', // TAMBAH: Simpan marketplace yang gagal
                        'row' => $rowNumber,
                        'reason' => implode(', ', $validator->errors()->all())
                    ];
                    continue;
                }

                // Create income - gunakan create dengan timestamps manual
                $income = new Income($data);
                $income->created_at = $parsedDate;
                $income->updated_at = $parsedDate;
                $income->save();
                $this->successCount++;

            } catch (\Exception $e) {
                $this->failedOrders[] = [
                    'no_pesanan' => $data['no_pesanan'] ?? 'Tidak diketahui',
                    'toko_id' => $tokoId ?? '-',
                    'marketplace' => $marketplace ?? '-', // TAMBAH: Simpan marketplace yang gagal
                    'row' => $rowNumber,
                    'reason' => $e->getMessage()
                ];
                continue;
            }
        }
    }

    /**
     * Helper untuk menentukan marketplace dari Excel atau default
     */
    private function determineMarketplace($marketplaceFromExcel, $rowNumber, $noPesanan)
    {
        // Jika ada marketplace dari Excel
        if (!empty($marketplaceFromExcel) && $marketplaceFromExcel !== '') {
            $marketplace = trim($marketplaceFromExcel);

            // Normalisasi nilai marketplace
            $marketplace = $this->normalizeMarketplace($marketplace);

            // Validasi nilai marketplace
            if (in_array($marketplace, ['Shopee', 'Tiktok'])) {
                return $marketplace;
            } else {
                $this->failedOrders[] = [
                    'no_pesanan' => $noPesanan ?? 'Tidak diketahui',
                    'toko_id' => '-',
                    'marketplace' => $marketplaceFromExcel,
                    'row' => $rowNumber,
                    'reason' => 'Marketplace tidak valid. Harus "Shopee" atau "Tiktok"'
                ];
                return false;
            }
        }

        // Jika tidak ada marketplace dari Excel, gunakan default
        if ($this->defaultMarketplace && in_array($this->defaultMarketplace, ['Shopee', 'Tiktok'])) {
            return $this->defaultMarketplace;
        }

        // Jika tidak ada default marketplace dan tidak ada dari Excel
        $this->failedOrders[] = [
            'no_pesanan' => $noPesanan ?? 'Tidak diketahui',
            'toko_id' => '-',
            'marketplace' => $marketplaceFromExcel ?? '-',
            'row' => $rowNumber,
            'reason' => 'Marketplace tidak diisi dan tidak ada default marketplace yang dipilih'
        ];
        return false;
    }

    /**
     * Helper untuk menormalisasi nilai marketplace
     */
    private function normalizeMarketplace($value)
    {
        $value = trim($value);

        // Case insensitive matching
        $lowerValue = strtolower($value);

        if ($lowerValue === 'shopee' || $lowerValue === 'shp' || $lowerValue === 'sh') {
            return 'Shopee';
        }

        if ($lowerValue === 'tiktok' || $lowerValue === 'tiktok shop' || $lowerValue === 'tiktokshop' || $lowerValue === 'tt') {
            return 'Tiktok';
        }

        return $value; // Return as is jika tidak cocok
    }

    /**
     * Helper untuk parsing tanggal dari Excel
     */
    private function parseExcelDate($excelDate, $rowNumber, $noPesanan)
    {
        // Jika kosong, gunakan tanggal sekarang
        if (empty($excelDate) || $excelDate === '' || $excelDate === 'NULL' || $excelDate === 'null') {
            return now();
        }

        try {
            // Handle jika sudah berupa object Carbon atau DateTime
            if ($excelDate instanceof \Carbon\Carbon || $excelDate instanceof \DateTime) {
                return $excelDate;
            }

            // Handle numeric value (Excel serial date)
            if (is_numeric($excelDate)) {
                // Coba parse sebagai Excel serial date
                $timestamp = ($excelDate - 25569) * 86400; // Convert Excel date to Unix timestamp
                return Carbon::createFromTimestamp($timestamp);
            }

            // Handle string dates - coba berbagai format
            $formats = [
                'Y-m-d H:i:s',
                'Y-m-d H:i',
                'Y-m-d',
                'd/m/Y H:i:s',
                'd/m/Y H:i',
                'd/m/Y',
                'm/d/Y H:i:s',
                'm/d/Y H:i',
                'm/d/Y',
                'd-m-Y H:i:s',
                'd-m-Y H:i',
                'd-m-Y',
                'm-d-Y H:i:s',
                'm-d-Y H:i',
                'm-d-Y',
            ];

            foreach ($formats as $format) {
                try {
                    $parsed = Carbon::createFromFormat($format, $excelDate);
                    if ($parsed !== false) {
                        return $parsed;
                    }
                } catch (\Exception $e) {
                    continue;
                }
            }

            // Coba parse dengan Carbon secara natural
            try {
                return Carbon::parse($excelDate);
            } catch (\Exception $e) {
                throw new \Exception('Format tanggal tidak dikenali: ' . $excelDate);
            }

        } catch (\Exception $e) {
            $this->failedOrders[] = [
                'no_pesanan' => $noPesanan ?? 'Tidak diketahui',
                'toko_id' => '-',
                'marketplace' => '-',
                'row' => $rowNumber,
                'reason' => 'Format tanggal tidak valid: ' . $excelDate . ' - ' . $e->getMessage()
            ];
            return now(); // Fallback ke waktu sekarang
        }
    }

    /**
     * Helper: Konversi no_pengajuan ke string dengan handle scientific notation
     */
    private function parseNoPengajuan($value)
    {
        if (is_null($value) || $value === '' || $value === 'NULL' || $value === 'null') {
            return null;
        }

        // Handle scientific notation (2,04276E+14 → 204276000000000)
        if (is_string($value) && preg_match('/^[0-9,]*\.?[0-9]+E\+[0-9]+$/i', $value)) {
            $floatValue = (float) str_replace(',', '.', $value);
            return number_format($floatValue, 0, '', ''); // Convert to full number string
        }

        // Handle regular numbers (convert to string to preserve precision)
        if (is_numeric($value)) {
            return (string) $value;
        }

        // Return as is for strings
        return (string) $value;
    }

    /**
     * Helper: Konversi no_pesanan ke string dengan handle scientific notation
     */
    private function parseNoPesanan($value)
    {
        if (is_null($value) || $value === '' || $value === 'NULL' || $value === 'null') {
            return null;
        }

        // Handle scientific notation (2,04276E+14 → 204276000000000)
        if (is_string($value) && preg_match('/^[0-9,]*\.?[0-9]+E\+[0-9]+$/i', $value)) {
            $floatValue = (float) str_replace(',', '.', $value);
            return number_format($floatValue, 0, '', ''); // Convert to full number string
        }

        // Handle regular numbers (convert to string to preserve precision)
        if (is_numeric($value)) {
            return (string) $value;
        }

        // Return as is for strings
        return (string) $value;
    }

    /**
     * Helper untuk menentukan toko_id dari Excel atau default
     */
    private function determineTokoId($tokoIdFromExcel, $rowNumber, $noPesanan)
    {
        // Jika ada toko_id dari Excel
        if (!empty($tokoIdFromExcel) && $tokoIdFromExcel !== '') {
            $tokoId = $this->parseInteger($tokoIdFromExcel);

            // Cek apakah toko exists
            if (Toko::where('id', $tokoId)->exists()) {
                return $tokoId;
            } else {
                $this->failedOrders[] = [
                    'no_pesanan' => $noPesanan ?? 'Tidak diketahui',
                    'toko_id' => $tokoIdFromExcel,
                    'marketplace' => '-',
                    'row' => $rowNumber,
                    'reason' => 'Toko ID tidak ditemukan dalam database'
                ];
                return false;
            }
        }

        // Jika tidak ada toko_id dari Excel, gunakan default
        if ($this->defaultTokoId && Toko::where('id', $this->defaultTokoId)->exists()) {
            return $this->defaultTokoId;
        }

        // Jika tidak ada default toko dan tidak ada toko_id dari Excel
        $this->failedOrders[] = [
            'no_pesanan' => $noPesanan ?? 'Tidak diketahui',
            'toko_id' => $tokoIdFromExcel ?? '-',
            'marketplace' => '-',
            'row' => $rowNumber,
            'reason' => 'Toko ID tidak diisi dan tidak ada default toko yang dipilih'
        ];
        return false;
    }

    /**
     * Helper untuk mendapatkan nilai cell dengan berbagai kemungkinan nama kolom
     */
    private function getCellValue($row, $possibleKeys)
    {
        foreach ($possibleKeys as $key) {
            // Cek dengan berbagai format case
            $lowerKey = strtolower($key);
            $snakeKey = str_replace(' ', '_', $lowerKey);
            $camelKey = str_replace(' ', '', ucwords(str_replace('_', ' ', $key)));

            $keysToCheck = [$key, $lowerKey, $snakeKey, $camelKey];

            foreach ($keysToCheck as $checkKey) {
                if (isset($row[$checkKey]) && !empty($row[$checkKey]) && $row[$checkKey] !== '') {
                    return $row[$checkKey];
                }
            }
        }
        return null;
    }

    /**
     * Helper untuk parsing nilai integer dari berbagai format
     */
    private function parseInteger($value)
    {
        if (is_null($value) || $value === '' || $value === 'NULL' || $value === 'null') {
            return 0;
        }

        if (is_numeric($value)) {
            return (int) $value;
        }

        $stringValue = trim((string)$value);

        $isNegative = false;

        if (preg_match('/^\(.*\)$/', $stringValue)) {
            $isNegative = true;
            $stringValue = preg_replace('/[\(\)]/', '', $stringValue);
        }
        elseif (strpos($stringValue, '-') !== false) {
            $isNegative = true;
        }

        $cleaned = preg_replace('/[^0-9,.-]/', '', $stringValue);

        $cleaned = str_replace(['.', ','], '', $cleaned);

        if ($cleaned === '' || !is_numeric($cleaned)) {
            return 0;
        }

        $result = (int) $cleaned;

        if ($isNegative) {
            $result = -abs($result);
        }

        return $result;
    }

    /**
     * Getter untuk mendapatkan daftar order yang gagal
     */
    public function getFailedOrders()
    {
        return $this->failedOrders;
    }

    /**
     * Getter untuk mendapatkan jumlah baris yang diproses
     */
    public function getRowCount()
    {
        return $this->rowCount;
    }

    /**
     * Getter untuk mendapatkan jumlah data yang berhasil diimport
     */
    public function getSuccessCount()
    {
        return $this->successCount;
    }
}
