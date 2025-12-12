<?php

namespace App\Exports;

use App\Models\Order;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class OrderExport implements FromCollection, WithHeadings, WithMapping, WithStyles
{
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return Order::with('produk')->orderBy('created_at', 'desc')->get();
    }

    public function map($order): array
    {
        return [
            $order->no_pesanan,
            $order->no_resi,
            $order->produk->nama_produk,
            $order->produk->nama_variasi,
            $order->jumlah,
            $order->returned_quantity,
            $order->total_harga_produk,
            $order->periode_id,
        ];
    }

    public function headings(): array
    {
        return [
            'no_pesanan',
            'no_resi',
            'nama_produk',
            'nama_variasi',
            'jumlah',
            'returned_quantity',
            'total_harga_produk',
            'periode_id',
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
