<?php

namespace App\Exports;

use App\Models\Income;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class IncomeResultExport implements FromCollection, WithHeadings, WithMapping
{
    public function collection()
    {
        return Income::with(['orders.produk'])
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($income) {
                $totalHpp = $income->orders->sum(function ($order) {
                    $netQuantity = $order->jumlah - $order->returned_quantity;
                    return $netQuantity * $order->produk->hpp_produk;
                });

                $laba = $income->total_penghasilan - $totalHpp;

                $income->total_hpp = $totalHpp;
                $income->laba = $laba;

                return $income;
            });
    }

    public function headings(): array
    {
        return [
            // 'No',
            'No Pesanan',
            'No Pengajuan',
            'Total Penghasilan',
            'HPP',
            'Laba/Rugi',
            'Persentase Laba (%)',
            'Tanggal Dibuat'
        ];
    }

    public function map($income): array
    {
        $persentase = $income->total_penghasilan > 0 ?
            round(($income->laba / $income->total_penghasilan) * 100, 2) : 0;

        return [
            // $income->id,
            $income->no_pesanan,
            $income->no_pengajuan ?? '-',
            $income->total_penghasilan,
            $income->total_hpp,
            $income->laba,
            $persentase,
            $income->created_at->format('d/m/Y H:i')
        ];
    }
}
