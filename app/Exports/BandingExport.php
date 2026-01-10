<?php

namespace App\Exports;

use App\Models\Banding;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;

class BandingExport implements FromCollection, WithHeadings, WithMapping, WithTitle
{
    protected $bandings;
    protected $startDate;
    protected $endDate;
    protected $marketplace;
    protected $tokoId;
    protected $statusBanding;

    public function __construct($bandings, $startDate = null, $endDate = null, $marketplace = null, $tokoId = null, $statusBanding = null)
    {
        $this->bandings = $bandings;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->marketplace = $marketplace;
        $this->tokoId = $tokoId;
        $this->statusBanding = $statusBanding;
    }

    public function collection()
    {
        return $this->bandings;
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
            'Marketplace',
            'Toko'
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
            $banding->marketplace,
            $banding->toko ? $banding->toko->nama : ''
        ];
    }

    public function title(): string
    {
        $title = 'Data Banding';

        if ($this->startDate || $this->endDate || $this->marketplace || $this->tokoId || $this->statusBanding) {
            $title .= ' (Filtered)';
        }

        return $title;
    }
}
