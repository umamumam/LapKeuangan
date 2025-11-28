<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\MonthlySummary;
use Carbon\Carbon;

class GenerateMonthlySummary extends Command
{
    protected $signature = 'summary:generate {month?} {year?}';
    protected $description = 'Generate monthly summary data';

    public function handle()
    {
        $month = $this->argument('month') ?? now()->month;
        $year = $this->argument('year') ?? now()->year;

        $periodeAwal = "{$year}-{$month}-01";

        $this->info("🚀 Generating summary for period: {$periodeAwal}");

        // Tampilkan rentang waktu yang digunakan
        $startDate = Carbon::parse($periodeAwal)->startOfMonth()->startOfDay();
        $endDate = Carbon::parse($periodeAwal)->endOfMonth()->endOfDay();

        $this->info("📅 Period range: {$startDate} to {$endDate}");

        try {
            $summary = MonthlySummary::generateForPeriod($periodeAwal);

            $this->info("✅ Summary generated successfully for {$summary->nama_periode}");
            $this->line("");
            $this->info("📊 ORDER DATA:");
            $this->info("   • Total Orders Quantity: " . number_format($summary->total_order_qty, 0, ',', '.'));
            $this->info("   • Total Return Quantity: " . number_format($summary->total_return_qty, 0, ',', '.'));
            $this->info("   • Net Quantity: " . number_format($summary->net_quantity, 0, ',', '.'));
            $this->info("   • Total Harga Produk: Rp " . number_format($summary->total_harga_produk, 0, ',', '.'));
            $this->line("");
            $this->info("💰 INCOME DATA:");
            $this->info("   • Total Incomes: " . number_format($summary->total_income_count, 0, ',', '.'));
            $this->info("   • Total Penghasilan: Rp " . number_format($summary->total_penghasilan, 0, ',', '.'));
            $this->line("");
            $this->info("🏷️ HPP & PROFIT:");
            $this->info("   • Total HPP: Rp " . number_format($summary->total_hpp, 0, ',', '.'));
            $this->info("   • Laba/Rugi: Rp " . number_format($summary->laba_rugi, 0, ',', '.'));
            $this->info("   • Margin: {$summary->rasio_margin}%");
            $this->line("");

        } catch (\Exception $e) {
            $this->error("❌ Failed to generate summary: " . $e->getMessage());
            $this->error("💡 Check laravel.log for details");
            return 1;
        }

        return 0;
    }
}
