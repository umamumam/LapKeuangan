<?php

namespace App\Exports;

use App\Models\Order;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class OrderPerPeriodeExport implements FromCollection, WithHeadings, WithMapping, WithStyles
{
    protected $periodeId;
    protected $periodeName;
    protected $periodeMarketplace;

    public function __construct($periodeId)
    {
        $this->periodeId = $periodeId;
        // Ambil data periode untuk digunakan
        $periode = \App\Models\Periode::find($periodeId);
        $this->periodeName = $periode->nama_periode ?? 'Periode-' . $periodeId;
        $this->periodeMarketplace = $periode->marketplace ?? null;
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return Order::with(['produk', 'periode'])
            ->where('periode_id', $this->periodeId)
            ->orderBy('id', 'asc')
            ->get();
    }

    public function map($order): array
    {
        $noPesanan = $order->no_pesanan;
        // Handle jika no_pesanan numerik untuk mencegah format scientific di Excel
        if (is_numeric($noPesanan) && ctype_digit((string) $noPesanan)) {
            $noPesanan = "'" . $noPesanan;
        }

        $noResi = $order->no_resi;
        if (is_numeric($noResi) && ctype_digit((string) $noResi)) {
            $noResi = "'" . $noResi;
        }

        return [
            $noPesanan,
            $noResi,
            $order->produk->nama_produk,
            $order->produk->nama_variasi,
            $order->produk->sku_induk,
            $order->produk->hpp_produk,
            $order->jumlah,
            $order->returned_quantity,
            $order->total_harga_produk,
            $this->periodeName,
            $this->periodeMarketplace,
            // Hitung net quantity
            $order->jumlah - $order->returned_quantity,
            // Hitung total HPP (net quantity * hpp)
            ($order->jumlah - $order->returned_quantity) * $order->produk->hpp_produk,
        ];
    }

    public function headings(): array
    {
        return [
            'No Pesanan',
            'No Resi',
            'Nama Produk',
            'Variasi',
            'SKU',
            'HPP Produk',
            'Jumlah',
            'Return Qty',
            'Total Harga Produk',
            'Periode',
            'Marketplace',
            'Net Quantity (Jml - Return)',
            'Total HPP (Net Qty × HPP)',
        ];
    }

    /**
     * Apply styles to the Excel sheet
     */
    public function styles(Worksheet $sheet)
    {
        return [
            // Style header row
            1 => [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'color' => ['rgb' => '4F81BD']
                ]
            ],
            // Auto-size columns
            'A:M' => ['autoSize' => true],
        ];
    }
}
