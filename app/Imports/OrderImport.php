<?php

namespace App\Imports;

use App\Models\Order;
use App\Models\Produk;
use App\Models\Periode;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class OrderImport implements ToCollection, WithHeadingRow
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

    public function collection(Collection $rows)
    {
        $this->rowCount = count($rows);

        foreach ($rows as $index => $row) {
            $rowNumber = $index + 2;

            try {
                // Ambil data dari Excel
                $noPesanan = $this->getCellValue($row, ['no_pesanan', 'no pesanan']);
                $noResi = $this->getCellValue($row, ['no_resi', 'no resi']);
                $namaProduk = $this->getCellValue($row, ['nama_produk', 'nama produk']);
                $namaVariasi = $this->getCellValue($row, ['nama_variasi', 'nama variasi']);
                $jumlah = $this->getCellValue($row, ['jumlah']);
                $returnedQuantity = $this->getCellValue($row, ['returned_quantity', 'returned quantity']);
                $totalHargaProduk = $this->getCellValue($row, ['total_harga_produk', 'total harga produk']);
                $periodeId = $this->getCellValue($row, ['periode_id', 'periode id']);

                // Skip baris kosong
                if (empty($noPesanan) && empty($namaProduk)) {
                    continue;
                }

                // Cari produk berdasarkan nama_produk dan nama_variasi
                $produk = Produk::where('nama_produk', $namaProduk)
                    ->when($namaVariasi, function ($query) use ($namaVariasi) {
                        return $query->where('nama_variasi', $namaVariasi);
                    })
                    ->first();

                if (!$produk) {
                    $this->failedOrders[] = [
                        'no_pesanan' => $noPesanan ?? 'Tidak diketahui',
                        'row' => $rowNumber,
                        'reason' => 'Produk tidak ditemukan: ' . $namaProduk . ($namaVariasi ? ' - ' . $namaVariasi : '')
                    ];
                    continue;
                }

                // Tentukan periode_id
                $finalPeriodeId = $this->determinePeriodeId($periodeId, $rowNumber, $noPesanan);

                // Siapkan data untuk disimpan
                $data = [
                    'no_pesanan' => $noPesanan,
                    'no_resi' => $noResi,
                    'produk_id' => $produk->id,
                    'jumlah' => $this->parseInteger($jumlah),
                    'returned_quantity' => $this->parseInteger($returnedQuantity) ?? 0,
                    'total_harga_produk' => $this->parseInteger($totalHargaProduk),
                    'periode_id' => $finalPeriodeId,
                ];

                // Validasi
                $validator = Validator::make($data, [
                    'no_pesanan' => 'required|string|max:100',
                    'no_resi' => 'nullable|string|max:100',
                    'produk_id' => 'required|exists:produks,id',
                    'jumlah' => 'required|integer|min:1',
                    'returned_quantity' => 'nullable|integer|min:0',
                    'total_harga_produk' => 'required|integer|min:0',
                    'periode_id' => 'nullable|exists:periodes,id',
                ]);

                if ($validator->fails()) {
                    $this->failedOrders[] = [
                        'no_pesanan' => $data['no_pesanan'] ?? 'Tidak diketahui',
                        'row' => $rowNumber,
                        'reason' => implode(', ', $validator->errors()->all())
                    ];
                    continue;
                }

                // Validasi returned_quantity tidak boleh lebih besar dari jumlah
                if ($data['returned_quantity'] > $data['jumlah']) {
                    $this->failedOrders[] = [
                        'no_pesanan' => $data['no_pesanan'] ?? 'Tidak diketahui',
                        'row' => $rowNumber,
                        'reason' => 'Returned quantity tidak boleh lebih besar dari jumlah'
                    ];
                    continue;
                }

                // Create order
                Order::create($data);
                $this->successCount++;

            } catch (\Exception $e) {
                $this->failedOrders[] = [
                    'no_pesanan' => $noPesanan ?? 'Tidak diketahui',
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
                    'row' => $rowNumber,
                    'reason' => 'Periode ID tidak ditemukan dalam database: ' . $periodeIdFromExcel
                ];
                return null;
            }
        }

        // Jika tidak ada periode_id dari Excel, gunakan default
        if ($this->defaultPeriodeId && Periode::where('id', $this->defaultPeriodeId)->exists()) {
            return $this->defaultPeriodeId;
        }

        // Periode tidak wajib, bisa null
        return null;
    }

    // Method helper lainnya tetap sama (getCellValue, parseInteger, dll)

    private function getCellValue($row, $possibleKeys)
    {
        foreach ($possibleKeys as $key) {
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

    private function parseInteger($value)
    {
        if (is_null($value) || $value === '' || $value === 'NULL' || $value === 'null') {
            return 0;
        }

        if (is_numeric($value)) {
            return (int) $value;
        }

        $stringValue = trim((string)$value);
        $cleaned = preg_replace('/[^0-9,.-]/', '', $stringValue);
        $cleaned = str_replace(['.', ','], '', $cleaned);

        if ($cleaned === '' || !is_numeric($cleaned)) {
            return 0;
        }

        return (int) $cleaned;
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
