<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MonthlySummary extends Model
{
    use HasFactory;

    protected $fillable = [
        'periode_awal',
        'periode_akhir',
        'nama_periode',
        'total_harga_produk',
        'total_order_qty',
        'total_return_qty',
        'total_penghasilan',
        'total_income_count',
        'total_hpp',
        'laba_rugi'
    ];

    protected $casts = [
        'periode_awal' => 'datetime',
        'periode_akhir' => 'datetime',
    ];

    public function monthlyFinance()
    {
        return $this->hasOne(MonthlyFinance::class, 'nama_periode', 'nama_periode');
    }

    public function getTotalPendapatanAttribute()
    {
        return $this->monthlyFinance ? $this->monthlyFinance->total_pendapatan : 0;
    }

    public function getOperasionalAttribute()
    {
        return $this->monthlyFinance ? $this->monthlyFinance->operasional : 0;
    }

    public function getIklanAttribute()
    {
        return $this->monthlyFinance ? $this->monthlyFinance->iklan : 0;
    }

    public function getRasioAdminLayananAttribute()
    {
        return $this->monthlyFinance ? $this->monthlyFinance->rasio_admin_layanan : 0;
    }

    public function getKeteranganAttribute()
    {
        return $this->monthlyFinance ? $this->monthlyFinance->keterangan : null;
    }

    public static function generateNamaPeriode($periodeAwal)
    {
        $awal = Carbon::parse($periodeAwal);
        return $awal->locale('id')->translatedFormat('F Y');
    }

    public static function calculateForPeriod($periodeAwal)
    {
        $startDate = Carbon::parse($periodeAwal)->startOfMonth()->startOfDay();
        $endDate = Carbon::parse($periodeAwal)->endOfMonth()->endOfDay();

        $namaPeriode = self::generateNamaPeriode($startDate);

        \Log::info("Calculating summary for period: {$startDate} to {$endDate}");

        $incomes = Income::with(['orders.produk'])
            ->whereBetween('created_at', [$startDate, $endDate])
            ->get();

        $totalPenghasilan = $incomes->sum('total_penghasilan');
        $totalIncomeCount = $incomes->count();

        $totalHpp = $incomes->sum(function ($income) {
            return $income->orders->sum(function ($order) {
                $netQuantity = $order->jumlah - $order->returned_quantity;
                return $netQuantity * $order->produk->hpp_produk;
            });
        });

        $orders = Order::with('produk')
            ->whereBetween('pesananselesai', [$startDate, $endDate])
            ->get();

        $totalHargaProduk = $orders->sum('total_harga_produk');
        $totalOrderQty = $orders->sum('jumlah');
        $totalReturnQty = $orders->sum('returned_quantity');

        $labaRugi = $totalPenghasilan - $totalHpp;

        \Log::info("Calculation results:");
        \Log::info("- Total Incomes: " . $totalIncomeCount);
        \Log::info("- Total Penghasilan: " . $totalPenghasilan);
        \Log::info("- Total HPP: " . $totalHpp);
        \Log::info("- Total Orders: " . $orders->count());
        \Log::info("- Total Harga Produk: " . $totalHargaProduk);

        return [
            'periode_awal' => $startDate,
            'periode_akhir' => $endDate,
            'nama_periode' => $namaPeriode,
            'total_harga_produk' => $totalHargaProduk,
            'total_order_qty' => $totalOrderQty,
            'total_return_qty' => $totalReturnQty,
            'total_penghasilan' => $totalPenghasilan,
            'total_income_count' => $totalIncomeCount,
            'total_hpp' => $totalHpp,
            'laba_rugi' => $labaRugi
        ];
    }

    public static function generateForPeriod($periodeAwal)
    {
        try {
            $data = self::calculateForPeriod($periodeAwal);

            $summary = self::updateOrCreate(
                ['nama_periode' => $data['nama_periode']],
                $data
            );

            \Log::info("Successfully generated summary for {$data['nama_periode']}");
            return $summary;

        } catch (\Exception $e) {
            \Log::error("Failed to generate summary: " . $e->getMessage());
            throw $e;
        }
    }

    public static function generateCurrentMonth()
    {
        return self::generateForPeriod(now());
    }

    public static function generatePreviousMonth()
    {
        return self::generateForPeriod(now()->subMonth());
    }

    public function getMarginAttribute()
    {
        return $this->total_penghasilan - $this->total_hpp;
    }

    public function getRasioMarginAttribute()
    {
        $totalPendapatan = $this->total_pendapatan;

        return $totalPendapatan > 0 ?
            round((($totalPendapatan - $this->total_hpp) / $totalPendapatan) * 100, 2) : 0;
    }

    public function getLabaRugiComprehensiveAttribute()
    {
        $margin = $this->total_penghasilan - $this->total_hpp;
        return $margin - $this->operasional - $this->iklan;
    }

    public function getAovAttribute()
    {
        return $this->total_order_qty > 0 ?
            round($this->total_harga_produk / $this->total_order_qty, 2) : 0;
    }

    public function getNetQuantityAttribute()
    {
        return $this->total_order_qty - $this->total_return_qty;
    }

    public function getBasketSizeAttribute()
    {
        return $this->total_income_count > 0 ?
            round($this->total_order_qty / $this->total_income_count, 2) : 0;
    }

    public function getRoasAttribute()
    {
        return $this->iklan > 0 ?
            round(($this->total_pendapatan / $this->iklan) * 100, 2) : 0;
    }

    public function getAcosAttribute()
    {
        return $this->total_pendapatan > 0 ?
            round(($this->iklan / $this->total_pendapatan) * 100, 2) : 0;
    }

    public function scopePeriode($query, $year, $month)
    {
        return $query->whereYear('periode_awal', $year)
                    ->whereMonth('periode_awal', $month);
    }

    public function scopeDateRange($query, $startDate, $endDate)
    {
        return $query->where('periode_awal', '>=', $startDate)
                    ->where('periode_akhir', '<=', $endDate);
    }

    public function scopeWithMonthlyFinance($query)
    {
        return $query->with('monthlyFinance');
    }
}
