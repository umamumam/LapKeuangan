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

class IncomeImport implements ToCollection, WithHeadingRow
{
    private $failedOrders = [];
    private $rowCount = 0;
    private $successCount = 0;

    public function collection(Collection $rows)
    {
        $this->rowCount = count($rows);

        foreach ($rows as $index => $row) {
            $rowNumber = $index + 2; // +2 karena heading row + base 1

            try {
                // Normalisasi nama kolom
                $noPesanan = $this->getCellValue($row, ['no_pesanan', 'no pesanan', 'nomor_pesanan']);
                $noPengajuan = $this->getCellValue($row, ['no_pengajuan', 'no pengajuan', 'nomor_pengajuan']);
                $totalPenghasilan = $this->getCellValue($row, ['total_penghasilan', 'total penghasilan', 'penghasilan']);
                $tokoId = $this->getCellValue($row, ['toko_id', 'toko id', 'id_toko', 'id toko']);

                // Skip baris kosong
                if (empty($noPesanan) && empty($noPengajuan) && empty($totalPenghasilan)) {
                    continue;
                }

                // Handle toko_id - gunakan dari Excel atau default
                $finalTokoId = $this->determineTokoId($tokoId, $rowNumber, $noPesanan);
                if ($finalTokoId === false) {
                    continue; // Skip jika toko tidak valid
                }

                $data = [
                    'no_pesanan' => $noPesanan,
                    'no_pengajuan' => $noPengajuan,
                    'total_penghasilan' => $this->parseInteger($totalPenghasilan),
                    'toko_id' => $finalTokoId,
                ];

                // Validasi dasar
                $validator = Validator::make($data, [
                    'no_pesanan' => [
                        'required',
                        'string',
                        'max:100',
                        Rule::unique('incomes', 'no_pesanan')
                    ],
                    'no_pengajuan' => 'nullable|string|max:100',
                    'total_penghasilan' => 'required|integer',
                    'toko_id' => 'required|exists:tokos,id',
                ], [
                    'no_pesanan.required' => 'Nomor pesanan wajib diisi',
                    'no_pesanan.unique' => 'Nomor pesanan sudah ada dalam database',
                    'total_penghasilan.required' => 'Total penghasilan wajib diisi',
                    'total_penghasilan.integer' => 'Total penghasilan harus berupa angka',
                    'toko_id.required' => 'Toko ID wajib diisi',
                    'toko_id.exists' => 'Toko ID tidak valid atau tidak ditemukan',
                ]);

                if ($validator->fails()) {
                    $this->failedOrders[] = [
                        'no_pesanan' => $data['no_pesanan'] ?? 'Tidak diketahui',
                        'toko_id' => $tokoId ?? '-',
                        'row' => $rowNumber,
                        'reason' => implode(', ', $validator->errors()->all())
                    ];
                    continue;
                }

                // Create income - TIDAK PERLU CEK ORDER EXISTS
                // Biarkan user import data income meskipun order belum ada
                Income::create($data);
                $this->successCount++;

            } catch (\Exception $e) {
                $this->failedOrders[] = [
                    'no_pesanan' => $data['no_pesanan'] ?? 'Tidak diketahui',
                    'toko_id' => $tokoId ?? '-',
                    'row' => $rowNumber,
                    'reason' => $e->getMessage()
                ];
                continue;
            }
        }
    }

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

        // Handle string dengan karakter non-numeric
        $cleaned = preg_replace('/[^0-9,-]/', '', (string)$value);
        $cleaned = str_replace(',', '', $cleaned); // Remove commas for thousands separator

        if ($cleaned === '' || $cleaned === '-') {
            return 0;
        }

        if (is_numeric($cleaned)) {
            return (int) $cleaned;
        }

        return 0;
    }

    public function getFailedOrders()
    {
        return $this->failedOrders;
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
