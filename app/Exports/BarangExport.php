<?php

namespace App\Exports;

use App\Models\Barang;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class BarangExport implements FromCollection, WithHeadings, WithMapping
{
    public function collection()
    {
        return Barang::with(['reseller', 'supplier'])->get();
    }

    public function headings(): array
    {
        return [
            'Reseller',
            'Supplier',
            'Nama Barang',
            'Ukuran',
            'HPP',
            'Harga Grosir',
        ];
    }

    public function map($barang): array
    {
        return [
            $barang->reseller->nama ?? '',
            $barang->supplier->nama ?? '',
            $barang->namabarang,
            $barang->ukuran,
            $barang->hpp,
            $barang->harga_grosir,
        ];
    }
}
