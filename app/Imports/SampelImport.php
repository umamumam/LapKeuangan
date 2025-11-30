<?php

namespace App\Imports;

use App\Models\Sampel;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class SampelImport implements ToModel, WithHeadingRow, WithValidation
{
    public function model(array $row)
    {
        return new Sampel([
            'nama' => $row['nama_sampel'] ?? $row['nama'] ?? null,
            'ukuran' => $row['ukuran'] ?? null,
            'harga' => $row['harga'] ?? 0,
        ]);
    }

    public function rules(): array
    {
        return [
            '*.nama_sampel' => 'required|string|max:100',
            '*.nama' => 'sometimes|string|max:100',
            '*.ukuran' => 'required|string|max:50',
            '*.harga' => 'required|integer|min:0'
        ];
    }

    public function customValidationMessages()
    {
        return [
            'nama_sampel.required' => 'Nama sampel wajib diisi',
            'ukuran.required' => 'Ukuran wajib diisi',
            'harga.required' => 'Harga wajib diisi',
            'harga.integer' => 'Harga harus berupa angka',
            'harga.min' => 'Harga tidak boleh kurang dari 0'
        ];
    }
}
