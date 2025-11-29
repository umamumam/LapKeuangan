<?php

namespace App\Exports;

use App\Models\Banding;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class BandingExport implements FromCollection, WithHeadings, WithMapping
{
    public function collection()
    {
        return Banding::all();
    }

    public function headings(): array
    {
        return [
            'Tanggal',
            'Status Banding',
            'Ongkir',
            'No Resi',
            'No Pesanan',
            'No Pengajuan',
            'Alasan',
            'Status Penerimaan',
            'Username',
            'Nama Pengirim',
            'No HP',
            'Alamat',
            'Marketplace'
        ];
    }

    public function map($banding): array
    {
        return [
            $banding->tanggal ? $banding->tanggal->format('d/m/Y H:i') : '',
            $banding->status_banding,
            $banding->ongkir,
            $banding->no_resi,
            $banding->no_pesanan,
            $banding->no_pengajuan,
            $banding->alasan,
            $banding->status_penerimaan,
            $banding->username,
            $banding->nama_pengirim,
            $banding->no_hp,
            $banding->alamat,
            $banding->marketplace
        ];
    }
}
