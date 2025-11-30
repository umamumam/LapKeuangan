<?php

namespace App\Exports;

use App\Models\Sampel;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class SampelExport implements FromCollection, WithHeadings, WithMapping
{
    public function collection()
    {
        return Sampel::orderBy('nama')->get();
    }

    public function headings(): array
    {
        return [
            'Nama Sampel',
            'Ukuran',
            'Harga',
        ];
    }

    public function map($sampel): array
    {
        return [
            $sampel->nama,
            $sampel->ukuran,
            $sampel->harga,
        ];
    }
}
