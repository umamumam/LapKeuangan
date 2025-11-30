<?php

namespace App\Exports;

use App\Models\PengirimanSampel;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class PengirimanSampelExport implements FromCollection, WithHeadings, WithMapping
{
    public function collection()
    {
        return PengirimanSampel::with('sampel')->get();
    }

    public function headings(): array
    {
        return [
            'Tanggal',
            'Username',
            'No Resi',
            'Nama Sampel',
            'Ukuran Sampel',
            'Jumlah',
            'Harga Sampel',
            'Total HPP',
            'Ongkir',
            'Total Biaya',
            'Penerima',
            'Contact',
            'Alamat'
        ];
    }

    public function map($pengiriman): array
    {
        return [
            $pengiriman->tanggal->format('Y-m-d H:i'),
            $pengiriman->username,
            $pengiriman->no_resi,
            $pengiriman->sampel->nama,
            $pengiriman->sampel->ukuran,
            $pengiriman->jumlah,
            $pengiriman->sampel->harga,
            $pengiriman->totalhpp,
            $pengiriman->ongkir,
            $pengiriman->total_biaya,
            $pengiriman->penerima,
            $pengiriman->contact,
            $pengiriman->alamat
        ];
    }
}
