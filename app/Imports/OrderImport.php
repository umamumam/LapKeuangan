<?php

namespace App\Imports;

use App\Models\Order;
use App\Models\Produk;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;

class OrderImport implements ToModel, WithHeadingRow, WithValidation, SkipsOnFailure, SkipsEmptyRows
{
    use SkipsFailures;

    private $rows = 0;
    private $failedOrders = [];

    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        ++$this->rows;

        // Cari produk berdasarkan kombinasi nama_produk DAN nama_variasi
        $produk = Produk::where('nama_produk', $row['nama_produk'])
            ->where('nama_variasi', $row['nama_variasi'] ?? null)
            ->first();

        if (!$produk) {
            // Simpan informasi pesanan yang gagal
            $this->failedOrders[] = [
                'no_pesanan' => $row['no_pesanan'],
                'nama_produk' => $row['nama_produk'],
                'nama_variasi' => $row['nama_variasi'] ?? '',
                'reason' => 'Produk tidak ditemukan'
            ];
            return null;
        }

        // Parse total_harga_produk
        $totalHargaProduk = $this->parseInteger($row['total_harga_produk'] ?? 0);

        // Parse no_resi (nullable)
        $noResi = !empty($row['no_resi']) ? trim($row['no_resi']) : null;

        // Parse periode_id (nullable)
        $periodeId = !empty($row['periode_id']) ? $this->parseInteger($row['periode_id']) : null;

        try {
            return new Order([
                'no_pesanan' => $row['no_pesanan'],
                'no_resi' => $noResi,
                'produk_id' => $produk->id,
                'jumlah' => $row['jumlah'],
                'returned_quantity' => $row['returned_quantity'] ?? 0,
                'total_harga_produk' => $totalHargaProduk,
                'periode_id' => $periodeId, // ✅ TAMBAHKAN INI
            ]);
        } catch (\Exception $e) {
            // Simpan informasi pesanan yang gagal karena error database
            $this->failedOrders[] = [
                'no_pesanan' => $row['no_pesanan'],
                'nama_produk' => $row['nama_produk'],
                'nama_variasi' => $row['nama_variasi'] ?? '',
                'reason' => 'Error database: ' . $e->getMessage()
            ];
            return null;
        }
    }

    /**
     * Define validation rules
     */
    public function rules(): array
    {
        return [
            'no_pesanan' => 'required|string|max:100',
            'no_resi' => 'nullable|string|max:100',
            'nama_produk' => 'required|string|max:255',
            'nama_variasi' => 'required|string|max:100',
            'jumlah' => 'required|integer|min:1',
            'returned_quantity' => 'nullable|integer|min:0',
            'total_harga_produk' => 'required|integer|min:0',
            'periode_id' => 'nullable|integer', // ✅ TAMBAHKAN INI
        ];
    }

    /**
     * Custom validation messages
     */
    public function customValidationMessages()
    {
        return [
            'no_pesanan.required' => 'No. Pesanan wajib diisi',
            'no_pesanan.string' => 'No. Pesanan harus berupa teks',
            'no_pesanan.max' => 'No. Pesanan maksimal 100 karakter',
            'no_resi.string' => 'No. Resi harus berupa teks',
            'no_resi.max' => 'No. Resi maksimal 100 karakter',
            'nama_produk.required' => 'Nama Produk wajib diisi',
            'nama_produk.string' => 'Nama Produk harus berupa teks',
            'nama_produk.max' => 'Nama Produk maksimal 255 karakter',
            'nama_variasi.required' => 'Nama Variasi wajib diisi',
            'nama_variasi.string' => 'Nama Variasi harus berupa teks',
            'nama_variasi.max' => 'Nama Variasi maksimal 100 karakter',
            'jumlah.required' => 'Jumlah wajib diisi',
            'jumlah.integer' => 'Jumlah harus berupa angka',
            'jumlah.min' => 'Jumlah minimal 1',
            'returned_quantity.integer' => 'Returned Quantity harus berupa angka',
            'returned_quantity.min' => 'Returned Quantity minimal 0',
            'total_harga_produk.required' => 'Total Harga Produk wajib diisi',
            'total_harga_produk.integer' => 'Total Harga Produk harus berupa angka',
            'total_harga_produk.min' => 'Total Harga Produk minimal 0',
            'periode_id.integer' => 'Periode ID harus berupa angka', // ✅ TAMBAHKAN INI
        ];
    }

    /**
     * Custom validation with conditions
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $data = $validator->getData();

            foreach ($data as $rowIndex => $row) {
                $rowNumber = $rowIndex + 2; // +2 karena heading row + base 0

                // Cek apakah produk dengan nama_produk dan nama_variasi exists
                $exists = Produk::where('nama_produk', $row['nama_produk'] ?? '')
                    ->where('nama_variasi', $row['nama_variasi'] ?? '')
                    ->exists();

                if (!$exists) {
                    $errorMessage = "Baris {$rowNumber}: Produk dengan nama '{$row['nama_produk']}' dan variasi '{$row['nama_variasi']}' tidak ditemukan di database";
                    $validator->errors()->add(
                        $rowIndex . '.nama_produk',
                        $errorMessage
                    );

                    // Simpan informasi pesanan yang gagal
                    $this->failedOrders[] = [
                        'no_pesanan' => $row['no_pesanan'],
                        'nama_produk' => $row['nama_produk'],
                        'nama_variasi' => $row['nama_variasi'] ?? '',
                        'reason' => $errorMessage,
                        'row' => $rowNumber
                    ];
                }

                // Validasi returned_quantity tidak lebih besar dari jumlah
                if (isset($row['returned_quantity']) && $row['returned_quantity'] > $row['jumlah']) {
                    $errorMessage = "Baris {$rowNumber}: Returned quantity tidak boleh lebih besar dari jumlah";
                    $validator->errors()->add(
                        $rowIndex . '.returned_quantity',
                        $errorMessage
                    );

                    // Simpan informasi pesanan yang gagal
                    $this->failedOrders[] = [
                        'no_pesanan' => $row['no_pesanan'],
                        'nama_produk' => $row['nama_produk'],
                        'nama_variasi' => $row['nama_variasi'] ?? '',
                        'reason' => $errorMessage,
                        'row' => $rowNumber
                    ];
                }

                // Validasi total_harga_produk tidak boleh negatif
                if (isset($row['total_harga_produk']) && $row['total_harga_produk'] < 0) {
                    $errorMessage = "Baris {$rowNumber}: Total Harga Produk tidak boleh negatif";
                    $validator->errors()->add(
                        $rowIndex . '.total_harga_produk',
                        $errorMessage
                    );

                    // Simpan informasi pesanan yang gagal
                    $this->failedOrders[] = [
                        'no_pesanan' => $row['no_pesanan'],
                        'nama_produk' => $row['nama_produk'],
                        'nama_variasi' => $row['nama_variasi'] ?? '',
                        'reason' => $errorMessage,
                        'row' => $rowNumber
                    ];
                }

                // Validasi no_resi max length
                if (isset($row['no_resi']) && strlen($row['no_resi']) > 100) {
                    $errorMessage = "Baris {$rowNumber}: No. Resi maksimal 100 karakter";
                    $validator->errors()->add(
                        $rowIndex . '.no_resi',
                        $errorMessage
                    );

                    // Simpan informasi pesanan yang gagal
                    $this->failedOrders[] = [
                        'no_pesanan' => $row['no_pesanan'],
                        'nama_produk' => $row['nama_produk'],
                        'nama_variasi' => $row['nama_variasi'] ?? '',
                        'reason' => $errorMessage,
                        'row' => $rowNumber
                    ];
                }

                // ✅ TAMBAHKAN: Validasi periode_id jika diisi
                if (!empty($row['periode_id'])) {
                    $periodeId = $this->parseInteger($row['periode_id']);
                    if ($periodeId <= 0) {
                        $errorMessage = "Baris {$rowNumber}: Periode ID harus angka positif";
                        $validator->errors()->add(
                            $rowIndex . '.periode_id',
                            $errorMessage
                        );

                        // Simpan informasi pesanan yang gagal
                        $this->failedOrders[] = [
                            'no_pesanan' => $row['no_pesanan'],
                            'nama_produk' => $row['nama_produk'],
                            'nama_variasi' => $row['nama_variasi'] ?? '',
                            'reason' => $errorMessage,
                            'row' => $rowNumber
                        ];
                    }
                }
            }
        });
    }

    /**
     * Parse integer from various formats
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

    /**
     * Prepare the data before validation
     */
    public function prepareForValidation($data, $index)
    {
        // Bersihkan dan format data sebelum validasi
        return [
            'no_pesanan' => trim($data['no_pesanan'] ?? ''),
            'no_resi' => isset($data['no_resi']) ? trim($data['no_resi']) : null,
            'nama_produk' => trim($data['nama_produk'] ?? ''),
            'nama_variasi' => trim($data['nama_variasi'] ?? ''),
            'jumlah' => intval($data['jumlah'] ?? 0),
            'returned_quantity' => isset($data['returned_quantity']) ? intval($data['returned_quantity']) : 0,
            'total_harga_produk' => $this->parseInteger($data['total_harga_produk'] ?? 0),
            'periode_id' => isset($data['periode_id']) ? $this->parseInteger($data['periode_id']) : null, // ✅ TAMBAHKAN INI
        ];
    }

    /**
     * Get row count
     */
    public function getRowCount(): int
    {
        return $this->rows;
    }

    /**
     * Get failed orders dengan no_pesanan
     */
    public function getFailedOrders(): array
    {
        return $this->failedOrders;
    }
}
