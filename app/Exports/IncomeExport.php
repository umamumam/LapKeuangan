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
        return Income::with('orders')->get();
    }

    public function headings(): array
    {
        return [
            'No Pesanan',
            'No Pengajuan',
            'Total Penghasilan',
            'Toko ID',
            'Marketplace',
            'Jumlah Item',
            'Tanggal Dibuat'
        ];
    }

    public function map($income): array
    {
        return [
            $income->no_pesanan,
            $income->no_pengajuan,
            $income->total_penghasilan,
            $income->toko_id,
            $income->marketplace,
            $income->orders->count(),
            $income->created_at->format('d/m/Y H:i')
        ];
    }
}
