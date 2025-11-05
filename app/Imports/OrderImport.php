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
use Illuminate\Validation\Rule;

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

        // Format pesananselesai jika ada
        $pesananselesai = $this->parseDate($row['pesananselesai'] ?? null);

        // Parse total_harga_produk
        $totalHargaProduk = $this->parseInteger($row['total_harga_produk'] ?? 0);

        try {
            return new Order([
                'no_pesanan' => $row['no_pesanan'],
                'produk_id' => $produk->id,
                'jumlah' => $row['jumlah'],
                'returned_quantity' => $row['returned_quantity'] ?? 0,
                'pesananselesai' => $pesananselesai,
                'total_harga_produk' => $totalHargaProduk, // ✅ TAMBAH INI
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
            'nama_produk' => 'required|string|max:255',
            'nama_variasi' => 'required|string|max:100',
            'jumlah' => 'required|integer|min:1',
            'returned_quantity' => 'nullable|integer|min:0',
            'pesananselesai' => 'nullable|string',
            'total_harga_produk' => 'required|integer|min:0', // ✅ TAMBAH INI
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
            'total_harga_produk.required' => 'Total Harga Produk wajib diisi', // ✅ TAMBAH INI
            'total_harga_produk.integer' => 'Total Harga Produk harus berupa angka', // ✅ TAMBAH INI
            'total_harga_produk.min' => 'Total Harga Produk minimal 0', // ✅ TAMBAH INI
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

                // ✅ TAMBAH: Validasi total_harga_produk tidak boleh negatif
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
            }
        });
    }

    /**
     * Parse date from various formats
     */
    private function parseDate($dateValue)
    {
        if (empty($dateValue) || $dateValue == '-') {
            return null;
        }

        try {
            if (is_numeric($dateValue)) {
                return \Carbon\Carbon::instance(\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($dateValue));
            }

            $formats = [
                'd/m/Y H:i',
                'Y-m-d H:i:s',
                'Y-m-d H:i',
                'Y-m-d',
                'd/m/Y',
            ];

            foreach ($formats as $format) {
                try {
                    return \Carbon\Carbon::createFromFormat($format, $dateValue);
                } catch (\Exception $e) {
                    continue;
                }
            }

            return null;
        } catch (\Exception $e) {
            return null;
        }
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
            'nama_produk' => trim($data['nama_produk'] ?? ''),
            'nama_variasi' => trim($data['nama_variasi'] ?? ''),
            'jumlah' => intval($data['jumlah'] ?? 0),
            'returned_quantity' => isset($data['returned_quantity']) ? intval($data['returned_quantity']) : 0,
            'pesananselesai' => trim($data['pesananselesai'] ?? ''),
            'total_harga_produk' => $this->parseInteger($data['total_harga_produk'] ?? 0), // ✅ TAMBAH INI
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
