<?php

namespace App\Imports;

use App\Models\PengembalianPenukaran;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Carbon\Carbon;

class PengembalianPenukaranImport implements ToCollection, WithHeadingRow
{
    private $failedRows = [];
    private $rowCount = 0;
    private $successCount = 0;

    public function collection(Collection $rows)
    {
        $this->rowCount = count($rows);

        foreach ($rows as $index => $row) {
            $rowNumber = $index + 2; // +2 karena baris header +1 dan index dimulai dari 0

            try {
                // Ambil data dari Excel dengan berbagai kemungkinan nama kolom
                $tanggal = $this->getCellValue($row, ['tanggal', 'date']);
                $jenis = $this->getCellValue($row, ['jenis', 'type']);
                $marketplace = $this->getCellValue($row, ['marketplace', 'market_place']);
                $resiPenerimaan = $this->getCellValue($row, ['resi_penerimaan', 'resi penerimaan', 'receipt_received', 'receipt received']);
                $resiPengiriman = $this->getCellValue($row, ['resi_pengiriman', 'resi pengiriman', 'receipt_shipped', 'receipt shipped']);
                $pembayaran = $this->getCellValue($row, ['pembayaran', 'payment']);
                $namaPengirim = $this->getCellValue($row, ['nama_pengirim', 'nama pengirim', 'sender_name', 'sender name']);
                $noHp = $this->getCellValue($row, ['no_hp', 'no hp', 'phone', 'telepon', 'telephone']);
                $alamat = $this->getCellValue($row, ['alamat', 'address']);
                $keterangan = $this->getCellValue($row, ['keterangan', 'description', 'note']);
                $statusditerima = $this->getCellValue($row, ['statusditerima', 'status diterima', 'status_received', 'status received']);

                // Skip baris kosong
                if (empty($namaPengirim) && empty($noHp)) {
                    continue;
                }

                // Format tanggal dari berbagai format
                $tanggalFormatted = $this->parseDate($tanggal, $rowNumber, $namaPengirim);
                if (!$tanggalFormatted) {
                    $this->failedRows[] = [
                        'nama_pengirim' => $namaPengirim ?? 'Tidak diketahui',
                        'row' => $rowNumber,
                        'reason' => 'Format tanggal tidak valid: ' . $tanggal
                    ];
                    continue;
                }

                // Validasi nilai enum
                $jenis = $this->validateEnum($jenis, ['Pengembalian', 'Penukaran', 'Pengembalian Dana', 'Pengiriman Gagal'], 'Jenis', $rowNumber, $namaPengirim);
                if (!$jenis) continue;

                $marketplace = $this->validateEnum($marketplace, ['Tiktok', 'Shopee', 'Reguler'], 'Marketplace', $rowNumber, $namaPengirim);
                if (!$marketplace) continue;

                $pembayaran = $this->validateEnum($pembayaran, ['Sistem', 'Tunai', 'DFOD'], 'Pembayaran', $rowNumber, $namaPengirim);
                if (!$pembayaran) continue;

                // Status diterima default ke 'Belum' jika tidak ada
                if ($statusditerima) {
                    $statusditerima = $this->validateEnum($statusditerima, ['OK', 'Belum'], 'Status Diterima', $rowNumber, $namaPengirim);
                    if (!$statusditerima) continue;
                } else {
                    $statusditerima = 'Belum';
                }

                // Siapkan data untuk disimpan
                $data = [
                    'tanggal' => $tanggalFormatted,
                    'jenis' => $jenis,
                    'marketplace' => $marketplace,
                    'resi_penerimaan' => $this->cleanString($resiPenerimaan),
                    'resi_pengiriman' => $this->cleanString($resiPengiriman),
                    'pembayaran' => $pembayaran,
                    'nama_pengirim' => $this->cleanString($namaPengirim),
                    'no_hp' => $this->formatPhoneNumber($noHp),
                    'alamat' => $this->cleanString($alamat),
                    'keterangan' => $this->cleanString($keterangan),
                    'statusditerima' => $statusditerima,
                ];

                // Validasi data
                $validator = Validator::make($data, [
                    'tanggal' => 'required|date',
                    'jenis' => 'required|in:Pengembalian,Penukaran,Pengembalian Dana,Pengiriman Gagal',
                    'marketplace' => 'required|in:Tiktok,Shopee,Reguler',
                    'resi_penerimaan' => 'nullable|string|max:100',
                    'resi_pengiriman' => 'nullable|string|max:100',
                    'pembayaran' => 'required|in:Sistem,Tunai,DFOD',
                    'nama_pengirim' => 'required|string|max:100',
                    'no_hp' => 'required|string|max:20',
                    'alamat' => 'required|string',
                    'keterangan' => 'nullable|string',
                    'statusditerima' => 'nullable|in:OK,Belum',
                ]);

                if ($validator->fails()) {
                    $this->failedRows[] = [
                        'nama_pengirim' => $data['nama_pengirim'] ?? 'Tidak diketahui',
                        'row' => $rowNumber,
                        'reason' => implode(', ', $validator->errors()->all())
                    ];
                    continue;
                }

                // Create data
                PengembalianPenukaran::create($data);
                $this->successCount++;

            } catch (\Exception $e) {
                $this->failedRows[] = [
                    'nama_pengirim' => $namaPengirim ?? 'Tidak diketahui',
                    'row' => $rowNumber,
                    'reason' => $e->getMessage()
                ];
                continue;
            }
        }
    }

    /**
     * Helper untuk mendapatkan nilai sel dengan berbagai kemungkinan nama kolom
     */
    private function getCellValue($row, $possibleKeys)
    {
        foreach ($possibleKeys as $key) {
            $lowerKey = strtolower($key);
            $snakeKey = str_replace(' ', '_', $lowerKey);
            $camelKey = str_replace(' ', '', ucwords(str_replace('_', ' ', $key)));
            $pascalKey = ucfirst($camelKey);

            $keysToCheck = [$key, $lowerKey, $snakeKey, $camelKey, $pascalKey];

            foreach ($keysToCheck as $checkKey) {
                if (isset($row[$checkKey]) && !empty($row[$checkKey]) && $row[$checkKey] !== '') {
                    return $row[$checkKey];
                }
            }
        }
        return null;
    }

    /**
     * Parse tanggal dari berbagai format
     */
    private function parseDate($dateValue, $rowNumber, $namaPengirim)
    {
        if (empty($dateValue)) {
            return null;
        }

        try {
            // Coba parse dari format Excel (serial number)
            if (is_numeric($dateValue)) {
                $date = Carbon::create(1899, 12, 30)->addDays($dateValue);
                return $date->format('Y-m-d');
            }

            // Coba parse dari berbagai format tanggal
            $formats = [
                'd/m/Y', 'd-m-Y', 'Y-m-d', 'Y/m/d',
                'd/m/y', 'd-m-y', 'm/d/Y', 'm-d-Y'
            ];

            foreach ($formats as $format) {
                try {
                    $date = Carbon::createFromFormat($format, $dateValue);
                    return $date->format('Y-m-d');
                } catch (\Exception $e) {
                    continue;
                }
            }

            // Coba parse secara otomatis
            $date = Carbon::parse($dateValue);
            return $date->format('Y-m-d');

        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Validasi nilai enum
     */
    private function validateEnum($value, $allowedValues, $fieldName, $rowNumber, $namaPengirim)
    {
        if (empty($value)) {
            if ($fieldName === 'Jenis' || $fieldName === 'Marketplace' || $fieldName === 'Pembayaran') {
                $this->failedRows[] = [
                    'nama_pengirim' => $namaPengirim ?? 'Tidak diketahui',
                    'row' => $rowNumber,
                    'reason' => $fieldName . ' tidak boleh kosong'
                ];
                return false;
            }
            return null;
        }

        // Cari kecocokan case-insensitive
        foreach ($allowedValues as $allowedValue) {
            if (strcasecmp($value, $allowedValue) === 0) {
                return $allowedValue;
            }
        }

        $this->failedRows[] = [
            'nama_pengirim' => $namaPengirim ?? 'Tidak diketahui',
            'row' => $rowNumber,
            'reason' => $fieldName . ' tidak valid: ' . $value . '. Harus salah satu dari: ' . implode(', ', $allowedValues)
        ];
        return false;
    }

    /**
     * Format nomor telepon
     */
    private function formatPhoneNumber($phone)
    {
        if (empty($phone)) {
            return '';
        }

        $phone = (string) $phone;
        $phone = preg_replace('/[^0-9]/', '', $phone);

        // Jika diawali dengan 0, ganti dengan 62
        if (substr($phone, 0, 1) === '0') {
            $phone = '62' . substr($phone, 1);
        }

        // Jika tidak diawali dengan 62, tambahkan
        if (substr($phone, 0, 2) !== '62') {
            $phone = '62' . $phone;
        }

        return $phone;
    }

    /**
     * Clean string (trim dan hapus karakter yang tidak perlu)
     */
    private function cleanString($value)
    {
        if (empty($value)) {
            return null;
        }

        $value = trim($value);

        // Hilangkan spasi berlebih
        $value = preg_replace('/\s+/', ' ', $value);

        return $value;
    }

    /**
     * Get failed rows
     */
    public function getFailedRows()
    {
        return $this->failedRows;
    }

    /**
     * Get total rows processed
     */
    public function getRowCount()
    {
        return $this->rowCount;
    }

    /**
     * Get success count
     */
    public function getSuccessCount()
    {
        return $this->successCount;
    }

    /**
     * Get failed count
     */
    public function getFailedCount()
    {
        return count($this->failedRows);
    }
}
