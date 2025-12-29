<?php

namespace App\Exports;

use App\Models\PengirimanSampel;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class PengirimanSampelExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    public function collection()
    {
        return PengirimanSampel::with(['sampel1', 'sampel2', 'sampel3', 'sampel4', 'sampel5'])
            ->orderBy('tanggal', 'desc')
            ->get();
    }

    public function headings(): array
    {
        return [
            'Tanggal',
            'Username',
            'No Resi',
            'Ongkir',
            'Nama Sampel 1',
            'Ukuran Sampel 1',
            'Jumlah 1',
            'Nama Sampel 2',
            'Ukuran Sampel 2',
            'Jumlah 2',
            'Nama Sampel 3',
            'Ukuran Sampel 3',
            'Jumlah 3',
            'Nama Sampel 4',
            'Ukuran Sampel 4',
            'Jumlah 4',
            'Nama Sampel 5',
            'Ukuran Sampel 5',
            'Jumlah 5',
            'Total HPP',
            'Total Biaya',
            'Penerima',
            'Contact',
            'Alamat',
            'Toko',
        ];
    }

    public function map($pengiriman): array
    {
        $row = [
            $pengiriman->tanggal->format('Y-m-d H:i'),
            $pengiriman->username,
            $pengiriman->no_resi,
            $pengiriman->ongkir,
        ];
        // Tambahkan data untuk sampel 1-5
        for ($i = 1; $i <= 5; $i++) {
            $sampel = $pengiriman->{"sampel{$i}"};
            $jumlah = $pengiriman->{"jumlah{$i}"} ?? 0;

            if ($sampel && $jumlah > 0) {
                $row[] = $sampel->nama;
                $row[] = $sampel->ukuran;
                $row[] = $jumlah;
            } else {
                $row[] = null;
                $row[] = null;
                $row[] = null;
            }
        }

        // Tambahkan data lainnya
        $row[] = $pengiriman->totalhpp;
        $row[] = $pengiriman->total_biaya;
        $row[] = $pengiriman->penerima;
        $row[] = $pengiriman->contact;
        $row[] = $pengiriman->alamat;
        $row[] = $pengiriman->toko ? $pengiriman->toko->nama : 'N/A';

        return $row;
    }

    public function styles(Worksheet $sheet)
    {
        return [
            // Style untuk header
            1 => [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'color' => ['rgb' => '4F81BD']]
            ],
            // Style untuk kolom angka
            'T' => ['alignment' => ['horizontal' => 'right']],
        ];
    }
}
