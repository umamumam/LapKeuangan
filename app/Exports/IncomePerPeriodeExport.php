<?php

namespace App\Exports;

use App\Models\Income;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class IncomePerPeriodeExport implements FromCollection, WithHeadings, WithMapping, WithColumnFormatting
{
    protected $periodeId;

    public function __construct($periodeId)
    {
        $this->periodeId = $periodeId;
    }

    public function collection()
    {
        // Hanya ambil income dengan periode_id yang dipilih
        return Income::with(['orders.produk', 'periode.toko'])
            ->where('periode_id', $this->periodeId)
            ->orderBy('id', 'asc')
            ->get();
    }

    public function headings(): array
    {
        return [
            'No Pesanan',
            'No Pengajuan',
            'Total Penghasilan',
            'Total HPP',
            'Laba',
            'Jumlah Item',
            'Periode',
            'Marketplace',
            'Toko'
        ];
    }

    public function map($income): array
    {
        // Hitung HPP persis seperti di Blade
        $totalHpp = $income->orders
            ->where('periode_id', $this->periodeId) // Pakai periode_id dari constructor
            ->sum(function ($order) {
                $netQuantity = $order->jumlah - $order->returned_quantity;
                return $netQuantity * $order->produk->hpp_produk;
            });

        $laba = $income->total_penghasilan - $totalHpp;

        $noPesanan = $income->no_pesanan;
        if (is_numeric($noPesanan) && ctype_digit((string) $noPesanan)) {
            $noPesanan = "'" . $noPesanan;
        }

        return [
            $noPesanan,
            $income->no_pengajuan ?? '-',
            $income->total_penghasilan,
            $totalHpp,
            $laba,
            $income->orders->where('periode_id', $this->periodeId)->count(),
            $income->periode ? $income->periode->nama_periode : '-',
            $income->periode ? $income->periode->marketplace : '-',
            $income->periode && $income->periode->toko ? $income->periode->toko->nama : '-',
        ];
    }

    public function columnFormats(): array
    {
        return [
            'A' => NumberFormat::FORMAT_TEXT, // Kolom No Pesanan sebagai teks
        ];
    }
}
