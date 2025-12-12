<?php

namespace App\Exports;

use App\Models\Income;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class IncomeExport implements FromCollection, WithHeadings, WithMapping
{
    public function collection()
    {
        // Load orders + produk biar tidak N+1 query
        return Income::with(['orders.produk', 'periode'])->get();
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
            ->where('periode_id', $income->periode_id)
            ->sum(function ($order) {
                $netQuantity = $order->jumlah - $order->returned_quantity;
                return $netQuantity * $order->produk->hpp_produk;
            });

        $laba = $income->total_penghasilan - $totalHpp;

        return [
            $income->no_pesanan,
            $income->no_pengajuan,
            $income->total_penghasilan,
            $totalHpp,
            $laba,
            $income->orders->where('periode_id', $income->periode_id)->count(),
            $income->periode->nama_periode ?? '-',
            $income->periode->marketplace ?? '-',
            $income->periode->toko->nama ?? '-',
        ];
    }
}
