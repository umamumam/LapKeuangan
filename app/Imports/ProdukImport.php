<?php

namespace App\Imports;

use App\Models\Produk;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Illuminate\Validation\Rule;

class ProdukImport implements ToModel, WithHeadingRow, WithValidation, SkipsOnFailure
{
    use SkipsFailures;

    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        return new Produk([
            'sku_induk' => $row['sku_induk'] ?? null,
            'nama_produk' => $row['nama_produk'],
            'nomor_referensi_sku' => $row['nomor_referensi_sku'] ?? null,
            'nama_variasi' => $row['nama_variasi'] ?? null,
            'hpp_produk' => $row['hpp_produk'] ?? 0,
        ]);
    }

    /**
     * Define validation rules
     */
    public function rules(): array
    {
        return [
            'nama_produk' => 'required|string|max:255',
            'sku_induk' => 'nullable|string|max:100',
            'nomor_referensi_sku' => 'nullable|string|max:100',
            'nama_variasi' => 'nullable|string|max:100',
            'hpp_produk' => 'required|integer|min:0',
        ];
    }

    /**
     * Custom validation messages
     */
    public function customValidationMessages()
    {
        return [
            'nama_produk.required' => 'Nama produk wajib diisi',
            'nama_produk.max' => 'Nama produk maksimal 255 karakter',
            'hpp_produk.required' => 'HPP produk wajib diisi',
            'hpp_produk.integer' => 'HPP produk harus berupa angka',
            'hpp_produk.min' => 'HPP produk minimal 0',
        ];
    }
}
