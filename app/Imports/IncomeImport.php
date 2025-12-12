<?php

namespace App\Imports;

use App\Models\Income;
use App\Models\Periode;
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
    private $defaultPeriodeId;

    /**
     * Constructor untuk menerima default periode_id
     */
    public function __construct($defaultPeriodeId = null)
    {
        $this->defaultPeriodeId = $defaultPeriodeId;
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
                $periodeId = $this->getCellValue($row, ['periode_id', 'periode id', 'id_periode', 'id periode']);

                // Ambil kolom tanggal dari Excel
                $tanggalDibuat = $this->getCellValue($row, ['created_at', 'tanggal', 'tanggal_dibuat', 'tgl_dibuat', 'date', 'tanggal_buat']);

                // Skip baris kosong
                if (empty($noPesanan) && empty($noPengajuan) && empty($totalPenghasilan)) {
                    continue;
                }

                // Handle periode_id - gunakan dari Excel atau default
                $finalPeriodeId = $this->determinePeriodeId($periodeId, $rowNumber, $noPesanan);
                if ($finalPeriodeId === false) {
                    // Periode tidak wajib, bisa null
                    $finalPeriodeId = null;
                }

                // Parse tanggal dari Excel
                $parsedDate = $this->parseExcelDate($tanggalDibuat, $rowNumber, $noPesanan);

                // Siapkan data untuk disimpan
                $data = [
                    'no_pesanan' => $this->parseNoPesanan($noPesanan),
                    'no_pengajuan' => $this->parseNoPengajuan($noPengajuan),
                    'total_penghasilan' => $this->parseInteger($totalPenghasilan),
                    'periode_id' => $finalPeriodeId,
                    // HANYA created_at yang diinput dari Excel, updated_at = created_at
                    'created_at' => $parsedDate,
                    'updated_at' => $parsedDate, // Sama dengan created_at
                ];

                // Validasi dasar
                $validator = Validator::make($data, [
                    'no_pesanan' => [
                        'required',
                        'string',
                        'max:100',
                    ],
                    'no_pengajuan' => 'nullable|string|max:100',
                    'total_penghasilan' => 'required|integer',
                    'periode_id' => 'nullable|exists:periodes,id',
                    'created_at' => 'required|date',
                    // updated_at TIDAK divalidasi karena otomatis = created_at
                ], [
                    'no_pesanan.required' => 'Nomor pesanan wajib diisi',
                    // 'no_pesanan.unique' => 'Nomor pesanan sudah ada dalam database',
                    'total_penghasilan.required' => 'Total penghasilan wajib diisi',
                    'total_penghasilan.integer' => 'Total penghasilan harus berupa angka',
                    'periode_id.exists' => 'Periode ID tidak valid atau tidak ditemukan',
                    'created_at.required' => 'Tanggal dibuat wajib diisi',
                    'created_at.date' => 'Format tanggal dibuat tidak valid',
                    // Tidak ada pesan error untuk updated_at
                ]);

                if ($validator->fails()) {
                    $this->failedOrders[] = [
                        'no_pesanan' => $data['no_pesanan'] ?? 'Tidak diketahui',
                        'periode_id' => $periodeId ?? '-',
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
                    'periode_id' => $periodeId ?? '-',
                    'row' => $rowNumber,
                    'reason' => $e->getMessage()
                ];
                continue;
            }
        }
    }

    /**
     * Helper untuk menentukan periode_id dari Excel atau default
     */
    private function determinePeriodeId($periodeIdFromExcel, $rowNumber, $noPesanan)
    {
        // Jika ada periode_id dari Excel
        if (!empty($periodeIdFromExcel) && $periodeIdFromExcel !== '') {
            $periodeId = $this->parseInteger($periodeIdFromExcel);

            // Cek apakah periode exists
            if (Periode::where('id', $periodeId)->exists()) {
                return $periodeId;
            } else {
                $this->failedOrders[] = [
                    'no_pesanan' => $noPesanan ?? 'Tidak diketahui',
                    'periode_id' => $periodeIdFromExcel,
                    'row' => $rowNumber,
                    'reason' => 'Periode ID tidak ditemukan dalam database'
                ];
                return false;
            }
        }

        // Jika tidak ada periode_id dari Excel, gunakan default
        if ($this->defaultPeriodeId && Periode::where('id', $this->defaultPeriodeId)->exists()) {
            return $this->defaultPeriodeId;
        }

        // Periode tidak wajib, return null jika tidak ada default
        return null;
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
                'periode_id' => '-',
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
