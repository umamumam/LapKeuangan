<?php

namespace App\Imports;

use App\Models\Income;
use App\Models\Order;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;

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

                // Skip baris kosong
                if (empty($noPesanan) && empty($noPengajuan) && empty($totalPenghasilan)) {
                    continue;
                }

                $data = [
                    'no_pesanan' => $noPesanan,
                    'no_pengajuan' => $noPengajuan,
                    'total_penghasilan' => $this->parseInteger($totalPenghasilan),
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
                ], [
                    'no_pesanan.required' => 'Nomor pesanan wajib diisi',
                    'no_pesanan.unique' => 'Nomor pesanan sudah ada dalam database',
                    'total_penghasilan.required' => 'Total penghasilan wajib diisi',
                    'total_penghasilan.integer' => 'Total penghasilan harus berupa angka',
                ]);

                if ($validator->fails()) {
                    $this->failedOrders[] = [
                        'no_pesanan' => $data['no_pesanan'] ?? 'Tidak diketahui',
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
                    'row' => $rowNumber,
                    'reason' => $e->getMessage()
                ];
                continue;
            }
        }
    }

    /**
     * Helper untuk mendapatkan nilai cell dengan berbagai kemungkinan nama kolom
     */
    private function getCellValue($row, $possibleKeys)
    {
        foreach ($possibleKeys as $key) {
            // Cek dengan berbagai format case
            $lowerKey = strtolower($key);
            $camelKey = str_replace(' ', '', ucwords(str_replace('_', ' ', $key)));

            if (isset($row[$key]) && !empty($row[$key]) && $row[$key] !== '') {
                return $row[$key];
            }
            if (isset($row[$lowerKey]) && !empty($row[$lowerKey]) && $row[$lowerKey] !== '') {
                return $row[$lowerKey];
            }
            if (isset($row[$camelKey]) && !empty($row[$camelKey]) && $row[$camelKey] !== '') {
                return $row[$camelKey];
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
