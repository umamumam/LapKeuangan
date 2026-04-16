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
            'Harga Beli Per Potong',
            'Harga Beli Per Lusin',
            'Harga Jual Per Potong',
            'Harga Jual Per Lusin',
            'Keuntungan',
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
            $barang->hargabeli_perpotong,
            $barang->hargabeli_perlusin,
            $barang->hargajual_perpotong,
            $barang->hargajual_perlusin,
            $barang->keuntungan,
        ];
    }
}
