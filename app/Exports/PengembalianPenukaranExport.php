<?php

namespace App\Exports;

use App\Models\PengembalianPenukaran;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class PengembalianPenukaranExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithTitle
{
    protected $data;
    protected $startDate;
    protected $endDate;
    protected $jenis;
    protected $marketplace;

    public function __construct($data, $startDate = null, $endDate = null, $jenis = null, $marketplace = null)
    {
        $this->data = $data;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->jenis = $jenis;
        $this->marketplace = $marketplace;
    }

    public function collection()
    {
        return $this->data;
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
            'Status Diterima',
        ];
    }

    public function map($row): array
    {
        return [
            $row->tanggal ? \Carbon\Carbon::parse($row->tanggal)->format('d/m/Y') : '',
            $row->jenis,
            $row->marketplace,
            $row->resi_penerimaan,
            $row->resi_pengiriman,
            $row->pembayaran,
            $row->nama_pengirim,
            $row->no_hp,
            $row->alamat,
            $row->keterangan,
            $row->statusditerima,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        // Tambahkan info filter di atas header
        $filterInfo = [];

        if ($this->startDate) {
            $filterInfo[] = 'Periode: ' . \Carbon\Carbon::parse($this->startDate)->format('d/m/Y');
            if ($this->endDate) {
                $filterInfo[] = ' s/d ' . \Carbon\Carbon::parse($this->endDate)->format('d/m/Y');
            }
        }

        if ($this->jenis) {
            $filterInfo[] = 'Jenis: ' . $this->jenis;
        }

        if ($this->marketplace) {
            $filterInfo[] = 'Marketplace: ' . $this->marketplace;
        }

        $filterText = !empty($filterInfo) ? 'Data yang difilter: ' . implode(', ', $filterInfo) : 'Semua Data';

        // Tambahkan baris untuk info filter
        $sheet->insertNewRowBefore(1, 2);
        $sheet->mergeCells('A1:K1');
        $sheet->setCellValue('A1', $filterText);
        $sheet->getStyle('A1')->getFont()->setBold(true);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal('center');

        // Tambahkan jumlah data
        $sheet->setCellValue('A2', 'Total Data: ' . $this->data->count());
        $sheet->mergeCells('A2:K2');
        $sheet->getStyle('A2')->getFont()->setBold(true);
        $sheet->getStyle('A2')->getAlignment()->setHorizontal('center');

        // Style untuk header (sekarang di baris 3)
        return [
            3 => [
                'font' => ['bold' => true],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['argb' => 'FFE0E0E0']
                ]
            ],
        ];
    }

    public function title(): string
    {
        return 'Data Pengembalian Penukaran';
    }
}
