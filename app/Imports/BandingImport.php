<?php

namespace App\Imports;

use App\Models\Banding;
use App\Models\Toko;
use Illuminate\Validation\Rule;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Carbon\Carbon;

class BandingImport implements ToCollection, WithHeadingRow
{
    private $failedImports = [];
    private $rowCount = 0;
    private $successCount = 0;
    private $selectedTokoId; // Tambah property untuk menyimpan toko_id yang dipilih

    // Tambah constructor untuk menerima toko_id
    public function __construct($tokoId = null)
    {
        $this->selectedTokoId = $tokoId;
    }

    public function collection(Collection $rows)
    {
        $this->rowCount = count($rows);

        foreach ($rows as $index => $row) {
            $rowNumber = $index + 2; // +2 karena heading row + base 1

            try {
                // Normalisasi nama kolom
                $tanggal = $this->getCellValue($row, ['tanggal', 'date', 'tgl', 'waktu']);
                $statusBanding = $this->getCellValue($row, ['status_banding', 'status banding', 'status']);
                $ongkir = $this->getCellValue($row, ['ongkir', 'ongkos_kirim', 'ongkos kirim']);
                $noResi = $this->getCellValue($row, ['no_resi', 'no resi', 'resi', 'nomor_resi']);
                $noPesanan = $this->getCellValue($row, ['no_pesanan', 'no pesanan', 'nomor_pesanan']);
                $noPengajuan = $this->getCellValue($row, ['no_pengajuan', 'no pengajuan', 'nomor_pengajuan']);
                $alasan = $this->getCellValue($row, ['alasan', 'reason', 'keterangan']);
                $statusPenerimaan = $this->getCellValue($row, ['status_penerimaan', 'status penerimaan', 'penerimaan']);
                $username = $this->getCellValue($row, ['username', 'user', 'nama_user']);
                $namaPengirim = $this->getCellValue($row, ['nama_pengirim', 'nama pengirim', 'pengirim']);
                $noHp = $this->getCellValue($row, ['no_hp', 'no hp', 'telepon', 'hp', 'no_telepon']);
                $alamat = $this->getCellValue($row, ['alamat', 'address', 'lokasi']);
                $marketplace = $this->getCellValue($row, ['marketplace', 'platform', 'situs']);

                // Skip baris kosong
                if (empty($noPesanan) && empty($noPengajuan)) {
                    continue;
                }

                // Parse tanggal
                $parsedDate = $this->parseExcelDate($tanggal, $rowNumber, $noPesanan);

                // Gunakan toko_id yang dipilih dari form
                $tokoIdFinal = $this->selectedTokoId;

                // Validasi bahwa toko_id harus ada
                if (!$tokoIdFinal) {
                    throw new \Exception('Toko tidak dipilih. Silakan pilih toko terlebih dahulu.');
                }

                // Cek apakah toko ada di database
                $toko = Toko::find($tokoIdFinal);
                if (!$toko) {
                    throw new \Exception('Toko dengan ID ' . $tokoIdFinal . ' tidak ditemukan.');
                }

                $data = [
                    'tanggal' => $parsedDate,
                    'status_banding' => $statusBanding ?: '-',
                    'ongkir' => $ongkir ?: '-',
                    'no_resi' => $this->parseStringValue($noResi),
                    'no_pesanan' => $this->parseStringValue($noPesanan),
                    'no_pengajuan' => $this->parseStringValue($noPengajuan),
                    'alasan' => $alasan ?: '-',
                    'status_penerimaan' => $statusPenerimaan ?: '-',
                    'username' => $username ?: '-',
                    'nama_pengirim' => $namaPengirim ?: '-',
                    'no_hp' => $this->parseStringValue($noHp),
                    'alamat' => $alamat ?: '-',
                    'marketplace' => $marketplace ?: 'Shopee',
                    'toko_id' => $tokoIdFinal, // Gunakan toko_id dari form
                    'created_at' => now(),
                    'updated_at' => now(),
                ];

                // Validasi data
                $validator = Validator::make($data, [
                    'tanggal' => 'required|date',
                    'status_banding' => [
                        'required',
                        Rule::in(['Berhasil', 'Ditinjau', 'Ditolak', '-'])
                    ],
                    'ongkir' => [
                        'required',
                        Rule::in(['Dibebaskan', 'Ditanggung', '-'])
                    ],
                    'no_pesanan' => [
                        'nullable',
                        'string',
                        'max:100',
                        Rule::unique('bandings', 'no_pesanan')
                    ],
                    'no_pengajuan' => 'nullable|string|max:100',
                    'alasan' => [
                        'required',
                        Rule::in([
                            'Barang Palsu',
                            'Tidak Sesuai Ekspektasi Pembeli',
                            'Barang Belum Diterima',
                            'Cacat',
                            'Jumlah Barang Retur Kurang',
                            'Bukan Produk Asli Toko',
                            '-'
                        ])
                    ],
                    'status_penerimaan' => [
                        'required',
                        Rule::in(['Diterima dengan baik', 'Cacat', '-'])
                    ],
                    'username' => 'nullable|string|max:100',
                    'nama_pengirim' => 'nullable|string|max:100',
                    'no_hp' => 'nullable|string|max:20',
                    'alamat' => 'required|string',
                    'marketplace' => [
                        'required',
                        Rule::in(['Shopee', 'Tiktok'])
                    ],
                    'toko_id' => [
                        'required',
                        'exists:tokos,id'
                    ],
                ], [
                    'tanggal.required' => 'Tanggal wajib diisi',
                    'tanggal.date' => 'Format tanggal tidak valid',
                    'status_banding.required' => 'Status banding wajib diisi',
                    'status_banding.in' => 'Status banding harus Berhasil, Ditinjau, atau Ditolak',
                    'ongkir.required' => 'Ongkir wajib diisi',
                    'ongkir.in' => 'Ongkir harus Dibebaskan, Ditanggung, atau -',
                    'no_pesanan.unique' => 'Nomor pesanan sudah ada dalam database',
                    'alasan.required' => 'Alasan wajib diisi',
                    'alasan.in' => 'Alasan tidak valid',
                    'status_penerimaan.required' => 'Status penerimaan wajib diisi',
                    'status_penerimaan.in' => 'Status penerimaan harus Diterima dengan baik, Cacat, atau -',
                    'alamat.required' => 'Alamat wajib diisi',
                    'marketplace.required' => 'Marketplace wajib diisi',
                    'marketplace.in' => 'Marketplace harus Shopee atau Tiktok',
                    'toko_id.required' => 'Toko wajib diisi',
                    'toko_id.exists' => 'Toko tidak ditemukan dalam database',
                ]);

                if ($validator->fails()) {
                    $this->failedImports[] = [
                        'no_pesanan' => $data['no_pesanan'] ?? 'Tidak diketahui',
                        'row' => $rowNumber,
                        'reason' => implode(', ', $validator->errors()->all())
                    ];
                    continue;
                }

                // Create banding
                Banding::create($data);
                $this->successCount++;

            } catch (\Exception $e) {
                $this->failedImports[] = [
                    'no_pesanan' => $noPesanan ?? 'Tidak diketahui',
                    'row' => $rowNumber,
                    'reason' => $e->getMessage()
                ];
                continue;
            }
        }
    }

    // Hapus method getTokoId() dan getTokoIdByName() karena tidak digunakan lagi

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
                'd/m/Y H:i:s',
                'd/m/Y H:i',
                'd/m/Y',
                'Y-m-d H:i:s',
                'Y-m-d H:i',
                'Y-m-d',
                'm/d/Y H:i:s',
                'm/d/Y H:i',
                'm/d/Y',
                'd-m-Y H:i:s',
                'd-m-Y H:i',
                'd-m-Y',
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
            $this->failedImports[] = [
                'no_pesanan' => $noPesanan ?? 'Tidak diketahui',
                'row' => $rowNumber,
                'reason' => 'Format tanggal tidak valid: ' . $excelDate
            ];
            return now(); // Fallback ke waktu sekarang
        }
    }

    /**
     * Helper untuk parsing nilai string (handle scientific notation dan angka)
     */
    private function parseStringValue($value)
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
                if (isset($row[$checkKey]) && $row[$checkKey] !== '' && $row[$checkKey] !== null) {
                    return $row[$checkKey];
                }
            }
        }
        return null;
    }

    public function getFailedImports()
    {
        return $this->failedImports;
    }

    public function getRowCount()
    {
        return $this->rowCount;
    }

    public function getSuccessCount()
    {
        return $this->successCount;
    }
}
