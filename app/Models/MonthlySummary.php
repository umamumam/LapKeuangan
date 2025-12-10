<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use App\Models\Income;
use App\Models\Order;

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
        // Kolom lama untuk backward compatibility
        'total_penghasilan',
        'total_income_count',
        'total_hpp',
        'laba_rugi',
        // Kolom baru untuk Shopee
        'total_penghasilan_shopee',
        'total_income_count_shopee',
        'total_hpp_shopee',
        'laba_rugi_shopee',
        // Kolom baru untuk Tiktok
        'total_penghasilan_tiktok',
        'total_income_count_tiktok',
        'total_hpp_tiktok',
        'laba_rugi_tiktok',
    ];

    protected $casts = [
        'periode_awal' => 'datetime',
        'periode_akhir' => 'datetime',
        'total_harga_produk' => 'integer',
        'total_order_qty' => 'integer',
        'total_return_qty' => 'integer',
        'total_penghasilan' => 'integer',
        'total_income_count' => 'integer',
        'total_hpp' => 'integer',
        'laba_rugi' => 'integer',
        'total_penghasilan_shopee' => 'integer',
        'total_income_count_shopee' => 'integer',
        'total_hpp_shopee' => 'integer',
        'laba_rugi_shopee' => 'integer',
        'total_penghasilan_tiktok' => 'integer',
        'total_income_count_tiktok' => 'integer',
        'total_hpp_tiktok' => 'integer',
        'laba_rugi_tiktok' => 'integer',
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

        Log::info("Calculating summary for period: {$startDate} to {$endDate}");

        // Get incomes dengan pemisahan marketplace
        $incomesShopee = Income::with(['orders.produk'])
            ->whereBetween('created_at', [$startDate, $endDate])
            ->where('marketplace', 'Shopee')
            ->get();

        $incomesTiktok = Income::with(['orders.produk'])
            ->whereBetween('created_at', [$startDate, $endDate])
            ->where('marketplace', 'Tiktok')
            ->get();

        // Hitung untuk Shopee
        $totalPenghasilanShopee = $incomesShopee->sum('total_penghasilan');
        $totalIncomeCountShopee = $incomesShopee->count();
        $totalHppShopee = self::calculateHppForIncomes($incomesShopee);

        // Hitung untuk Tiktok
        $totalPenghasilanTiktok = $incomesTiktok->sum('total_penghasilan');
        $totalIncomeCountTiktok = $incomesTiktok->count();
        $totalHppTiktok = self::calculateHppForIncomes($incomesTiktok);

        // Hitung total
        $totalPenghasilan = $totalPenghasilanShopee + $totalPenghasilanTiktok;
        $totalIncomeCount = $totalIncomeCountShopee + $totalIncomeCountTiktok;
        $totalHpp = $totalHppShopee + $totalHppTiktok;

        // Laba Rugi per marketplace
        $labaRugiShopee = $totalPenghasilanShopee - $totalHppShopee;
        $labaRugiTiktok = $totalPenghasilanTiktok - $totalHppTiktok;
        $labaRugi = $labaRugiShopee + $labaRugiTiktok;

        // Get orders data (total dari semua marketplace)
        $orders = Order::with('produk')
            ->whereBetween('pesananselesai', [$startDate, $endDate])
            ->get();

        $totalHargaProduk = $orders->sum('total_harga_produk');
        $totalOrderQty = $orders->sum('jumlah');
        $totalReturnQty = $orders->sum('returned_quantity');

        Log::info("Calculation results:");
        Log::info("- Total Penghasilan Shopee: " . $totalPenghasilanShopee);
        Log::info("- Total Penghasilan Tiktok: " . $totalPenghasilanTiktok);
        Log::info("- Total Penghasilan: " . $totalPenghasilan);
        Log::info("- Total HPP Shopee: " . $totalHppShopee);
        Log::info("- Total HPP Tiktok: " . $totalHppTiktok);
        Log::info("- Total HPP: " . $totalHpp);
        Log::info("- Laba Rugi Shopee: " . $labaRugiShopee);
        Log::info("- Laba Rugi Tiktok: " . $labaRugiTiktok);
        Log::info("- Total Orders: " . $orders->count());

        return [
            'periode_awal' => $startDate,
            'periode_akhir' => $endDate,
            'nama_periode' => $namaPeriode,
            'total_harga_produk' => $totalHargaProduk,
            'total_order_qty' => $totalOrderQty,
            'total_return_qty' => $totalReturnQty,
            // Total (backward compatibility)
            'total_penghasilan' => $totalPenghasilan,
            'total_income_count' => $totalIncomeCount,
            'total_hpp' => $totalHpp,
            'laba_rugi' => $labaRugi,
            // Shopee
            'total_penghasilan_shopee' => $totalPenghasilanShopee,
            'total_income_count_shopee' => $totalIncomeCountShopee,
            'total_hpp_shopee' => $totalHppShopee,
            'laba_rugi_shopee' => $labaRugiShopee,
            // Tiktok
            'total_penghasilan_tiktok' => $totalPenghasilanTiktok,
            'total_income_count_tiktok' => $totalIncomeCountTiktok,
            'total_hpp_tiktok' => $totalHppTiktok,
            'laba_rugi_tiktok' => $labaRugiTiktok,
        ];
    }

    /**
     * Calculate HPP for a collection of incomes
     */
    private static function calculateHppForIncomes($incomes)
    {
        return $incomes->sum(function ($income) {
            return $income->orders->sum(function ($order) {
                $netQuantity = $order->jumlah - $order->returned_quantity;
                return $netQuantity * ($order->produk->hpp_produk ?? 0);
            });
        });
    }

    public static function generateForPeriod($periodeAwal)
    {
        try {
            $data = self::calculateForPeriod($periodeAwal);

            $summary = self::updateOrCreate(
                ['nama_periode' => $data['nama_periode']],
                $data
            );

            Log::info("Successfully generated summary for {$data['nama_periode']}");
            return $summary;

        } catch (\Exception $e) {
            Log::error("Failed to generate summary: " . $e->getMessage());
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

    // Accessor methods untuk perhitungan tambahan

    public function getMarginAttribute()
    {
        return $this->total_penghasilan - $this->total_hpp;
    }

    public function getMarginShopeeAttribute()
    {
        return $this->total_penghasilan_shopee - $this->total_hpp_shopee;
    }

    public function getMarginTiktokAttribute()
    {
        return $this->total_penghasilan_tiktok - $this->total_hpp_tiktok;
    }

    public function getRasioMarginAttribute()
    {
        $totalPendapatan = $this->total_pendapatan;

        return $totalPendapatan > 0 ?
            round((($totalPendapatan - $this->total_hpp) / $totalPendapatan) * 100, 2) : 0;
    }

    public function getRasioMarginShopeeAttribute()
    {
        return $this->total_penghasilan_shopee > 0 ?
            round(($this->laba_rugi_shopee / $this->total_penghasilan_shopee) * 100, 2) : 0;
    }

    public function getRasioMarginTiktokAttribute()
    {
        return $this->total_penghasilan_tiktok > 0 ?
            round(($this->laba_rugi_tiktok / $this->total_penghasilan_tiktok) * 100, 2) : 0;
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

    public function getAovShopeeAttribute()
    {
        return $this->total_income_count_shopee > 0 ?
            round($this->total_penghasilan_shopee / $this->total_income_count_shopee, 2) : 0;
    }

    public function getAovTiktokAttribute()
    {
        return $this->total_income_count_tiktok > 0 ?
            round($this->total_penghasilan_tiktok / $this->total_income_count_tiktok, 2) : 0;
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

    public function getBasketSizeShopeeAttribute()
    {
        // Estimate basket size for Shopee
        return $this->total_income_count_shopee > 0 ?
            round($this->getOrdersCountByMarketplace('Shopee') / $this->total_income_count_shopee, 2) : 0;
    }

    public function getBasketSizeTiktokAttribute()
    {
        // Estimate basket size for Tiktok
        return $this->total_income_count_tiktok > 0 ?
            round($this->getOrdersCountByMarketplace('Tiktok') / $this->total_income_count_tiktok, 2) : 0;
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

    /**
     * Get estimated orders count by marketplace
     */
    public function getOrdersCountByMarketplace($marketplace)
    {
        // Ini adalah estimasi sederhana berdasarkan income count
        // Anda mungkin perlu menyesuaikan dengan logika bisnis yang sebenarnya

        if ($marketplace === 'Shopee') {
            // Asumsi: setiap income mewakili minimal 1 order
            return $this->total_income_count_shopee;
        } elseif ($marketplace === 'Tiktok') {
            return $this->total_income_count_tiktok;
        }

        return 0;
    }

    // Scope methods

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

    /**
     * Scope untuk marketplace tertentu
     */
    public function scopeByMarketplace($query, $marketplace)
    {
        if ($marketplace === 'Shopee') {
            return $query->select(
                'periode_awal',
                'periode_akhir',
                'nama_periode',
                'total_penghasilan_shopee as total_penghasilan',
                'total_income_count_shopee as total_income_count',
                'total_hpp_shopee as total_hpp',
                'laba_rugi_shopee as laba_rugi'
            );
        } elseif ($marketplace === 'Tiktok') {
            return $query->select(
                'periode_awal',
                'periode_akhir',
                'nama_periode',
                'total_penghasilan_tiktok as total_penghasilan',
                'total_income_count_tiktok as total_income_count',
                'total_hpp_tiktok as total_hpp',
                'laba_rugi_tiktok as laba_rugi'
            );
        }

        return $query;
    }
}
