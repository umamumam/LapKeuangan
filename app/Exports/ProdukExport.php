<?php

namespace App\Exports;

use App\Models\Produk;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ProdukExport implements FromCollection, WithHeadings, WithMapping, WithStyles
{
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return Produk::orderBy('created_at', 'desc')->get();
    }

    /**
     * Define the headings for the Excel sheet
     */
    public function headings(): array
    {
        return [
            'SKU Induk',
            'Nama Produk',
            'Nomor Referensi SKU',
            'Nama Variasi',
            'HPP Produk',
            'Tanggal Dibuat',
            'Tanggal Diupdate'
        ];
    }

    /**
     * Map the data for each row
     */
    public function map($produk): array
    {
        return [
            $produk->sku_induk ?? '',
            $produk->nama_produk,
            $produk->nomor_referensi_sku ?? '',
            $produk->nama_variasi ?? '',
            $produk->hpp_produk,
            $produk->created_at->format('d/m/Y H:i'),
            $produk->updated_at->format('d/m/Y H:i'),
        ];
    }

    /**
     * Apply styles to the Excel sheet
     */
    public function styles(Worksheet $sheet)
    {
        return [
            // Style the first row as bold text
            1 => ['font' => ['bold' => true]],
        ];
    }
}
