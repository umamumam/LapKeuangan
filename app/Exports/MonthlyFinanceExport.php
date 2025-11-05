<?php

namespace App\Exports;

use App\Models\MonthlyFinance;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class MonthlyFinanceExport implements FromCollection, WithHeadings, WithMapping
{
    public function collection()
    {
        return MonthlyFinance::orderBy('periode_awal', 'desc')->get();
    }

    public function headings(): array
    {
        return [
            'Periode',
            'Total Pendapatan',
            'Total Penghasilan',
            'HPP',
            'Operasional',
            'Iklan',
            'Rasio Admin & Layanan (%)',
            'Laba/Rugi',
            'Rasio Operasional (%)',
            'Rasio Margin (%)',
            'Rasio Laba (%)',
            'AOV Aktual',
            'Basket Size Aktual',
            'ROAS Aktual (%)',
            'ACOS Aktual (%)',
            'Keterangan'
        ];
    }

    public function map($finance): array
    {
        return [
            $finance->nama_periode,
            $finance->total_pendapatan,
            $finance->total_penghasilan,
            $finance->hpp,
            $finance->operasional,
            $finance->iklan,
            $finance->rasio_admin_layanan,
            $finance->laba_rugi,
            $finance->rasio_operasional,
            $finance->rasio_margin,
            $finance->rasio_laba,
            $finance->aov_aktual,
            $finance->basket_size_aktual,
            $finance->roas_aktual,
            $finance->acos_aktual,
            $finance->keterangan
        ];
    }
}
