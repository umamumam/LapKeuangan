<?php

namespace App\Exports;

use App\Models\PengembalianPenukaran;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class PengembalianPenukaranExport implements FromCollection, WithHeadings, WithMapping, WithStyles
{
    public function collection()
    {
        return PengembalianPenukaran::orderBy('tanggal', 'desc')->get();
    }

    public function headings(): array
    {
        return [
            'Tanggal',
            'Jenis',
            'Marketplace',
            'Resi Penerimaan',
            'Resi Pengiriman',
            'Pembayaran',
            'Nama Pengirim',
            'No HP',
            'Alamat',
            'Keterangan',
        ];
    }

    public function map($row): array
    {
        return [
            $row->tanggal,
            $row->jenis,
            $row->marketplace,
            $row->resi_penerimaan,
            $row->resi_pengiriman,
            $row->pembayaran,
            $row->nama_pengirim,
            $row->no_hp,
            $row->alamat,
            $row->keterangan,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            // Style untuk header
            1 => [
                'font' => ['bold' => true],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['argb' => 'FFE0E0E0']
                ]
            ],
        ];
    }
}
