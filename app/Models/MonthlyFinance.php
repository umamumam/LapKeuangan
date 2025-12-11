<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MonthlyFinance extends Model
{
    use HasFactory;

    protected $fillable = [
        'periode_awal',
        'periode_akhir',
        'nama_periode',
        'total_pendapatan_shopee',
        'total_pendapatan_tiktok',
        'operasional',
        'iklan_shopee',
        'iklan_tiktok',
        'rasio_admin_layanan_shopee',
        'rasio_admin_layanan_tiktok',
        'keterangan'
    ];

    protected $casts = [
        'periode_awal' => 'date',
        'periode_akhir' => 'date',
        'rasio_admin_layanan_shopee' => 'decimal:2',
        'rasio_admin_layanan_tiktok' => 'decimal:2',
    ];

    /**
     * Relasi ke MonthlySummary berdasarkan nama_periode
     */
    public function summary()
    {
        return $this->hasOne(MonthlySummary::class, 'nama_periode', 'nama_periode');
    }

    // ========== ACCESSORS UNTUK TOTAL ==========

    /**
     * Accessor untuk total pendapatan semua marketplace
     */
    public function getTotalPendapatanAttribute()
    {
        return $this->total_pendapatan_shopee + $this->total_pendapatan_tiktok;
    }

    /**
     * Accessor untuk total iklan semua marketplace
     */
    public function getTotalIklanAttribute()
    {
        return $this->iklan_shopee + $this->iklan_tiktok;
    }

    /**
     * Accessor untuk rata-rata rasio admin layanan
     */
    public function getRasioAdminLayananAttribute()
    {
        $totalPendapatan = $this->total_pendapatan;
        if ($totalPendapatan > 0) {
            $adminShopee = $this->total_pendapatan_shopee * ($this->rasio_admin_layanan_shopee / 100);
            $adminTiktok = $this->total_pendapatan_tiktok * ($this->rasio_admin_layanan_tiktok / 100);
            $totalAdmin = $adminShopee + $adminTiktok;
            return round(($totalAdmin / $totalPendapatan) * 100, 2);
        }
        return 0;
    }

    // ========== ACCESSORS DARI SUMMARY ==========

    /**
     * Accessor untuk total_penghasilan dari summary
     */
    public function getTotalPenghasilanAttribute()
    {
        return $this->summary ? $this->summary->total_penghasilan : 0;
    }

    /**
     * Accessor untuk total_penghasilan shopee dari summary
     */
    public function getTotalPenghasilanShopeeAttribute()
    {
        return $this->summary ? $this->summary->total_penghasilan_shopee : 0;
    }

    /**
     * Accessor untuk total_penghasilan tiktok dari summary
     */
    public function getTotalPenghasilanTiktokAttribute()
    {
        return $this->summary ? $this->summary->total_penghasilan_tiktok : 0;
    }

    /**
     * Accessor untuk HPP dari summary
     */
    public function getHppAttribute()
    {
        return $this->summary ? $this->summary->total_hpp : 0;
    }

    /**
     * Accessor untuk HPP shopee dari summary
     */
    public function getHppShopeeAttribute()
    {
        return $this->summary ? $this->summary->total_hpp_shopee : 0;
    }

    /**
     * Accessor untuk HPP tiktok dari summary
     */
    public function getHppTiktokAttribute()
    {
        return $this->summary ? $this->summary->total_hpp_tiktok : 0;
    }

    /**
     * Accessor untuk laba/rugi shopee dari summary
     */
    public function getLabaRugiShopeeAttribute()
    {
        return $this->summary ? $this->summary->laba_rugi_shopee : 0;
    }

    /**
     * Accessor untuk laba/rugi tiktok dari summary
     */
    public function getLabaRugiTiktokAttribute()
    {
        return $this->summary ? $this->summary->laba_rugi_tiktok : 0;
    }

    // ========== PERHITUNGAN LABA/RUGI ==========

    /**
     * Accessor untuk laba/rugi kotor (sebelum operasional dan iklan)
     */
    public function getLabaRugiKotorAttribute()
    {
        return $this->summary ? $this->summary->laba_rugi : 0;
    }

    /**
     * Accessor untuk laba/rugi yang sudah include operasional dan iklan
     */
    public function getLabaRugiAttribute()
    {
        $labaRugiKotor = $this->laba_rugi_kotor;
        return $labaRugiKotor - $this->operasional - $this->total_iklan;
    }

    /**
     * Accessor untuk laba/rugi shopee net (setelah operasional & iklan)
     */
    public function getLabaRugiNetShopeeAttribute()
    {
        $labaRugiShopee = $this->laba_rugi_shopee;
        // Alokasikan operasional dan iklan proporsional berdasarkan pendapatan
        // $proporsiShopee = $this->total_pendapatan > 0 ?
        //     ($this->total_pendapatan_shopee / $this->total_pendapatan) : 0;
        $operasionalShopee = $this->operasional;
        return $labaRugiShopee - $operasionalShopee - $this->iklan_shopee;
    }

    /**
     * Accessor untuk laba/rugi tiktok net (setelah operasional & iklan)
     */
    public function getLabaRugiNetTiktokAttribute()
    {
        $labaRugiTiktok = $this->laba_rugi_tiktok;
        // Alokasikan operasional dan iklan proporsional berdasarkan pendapatan
        // $proporsiTiktok = $this->total_pendapatan > 0 ?
        //     ($this->total_pendapatan_tiktok / $this->total_pendapatan) : 0;
        $operasionalTiktok = $this->operasional;
        return $labaRugiTiktok - $operasionalTiktok - $this->iklan_tiktok;
    }

    // ========== RASIO-RASIO ==========

    /**
     * Accessor untuk rasio operasional
     */
    public function getRasioOperasionalAttribute()
    {
        return $this->total_pendapatan > 0 ?
            round(($this->operasional / $this->total_pendapatan) * 100, 2) : 0;
    }

    /**
     * Accessor untuk rasio margin kotor
     */
    public function getRasioMarginKotorAttribute()
    {
        return $this->summary ? $this->summary->rasio_margin : 0;
    }

    /**
     * Accessor untuk rasio margin shopee
     */
    public function getRasioMarginShopeeAttribute()
    {
        return $this->summary ? $this->summary->rasio_margin_shopee : 0;
    }

    /**
     * Accessor untuk rasio margin tiktok
     */
    public function getRasioMarginTiktokAttribute()
    {
        return $this->summary ? $this->summary->rasio_margin_tiktok : 0;
    }

    /**
     * Accessor untuk rasio laba net
     */
    public function getRasioLabaAttribute()
    {
        return $this->total_pendapatan > 0 ?
            round(($this->laba_rugi / $this->total_pendapatan) * 100, 2) : 0;
    }

    /**
     * Accessor untuk rasio laba net shopee
     */
    public function getRasioLabaNetShopeeAttribute()
    {
        return $this->total_pendapatan_shopee > 0 ?
            round(($this->laba_rugi_net_shopee / $this->total_pendapatan_shopee) * 100, 2) : 0;
    }

    /**
     * Accessor untuk rasio laba net tiktok
     */
    public function getRasioLabaNetTiktokAttribute()
    {
        return $this->total_pendapatan_tiktok > 0 ?
            round(($this->laba_rugi_net_tiktok / $this->total_pendapatan_tiktok) * 100, 2) : 0;
    }

    // ========== METRIK PERFORMANCE ==========

    /**
     * Accessor untuk AOV aktual dari summary
     */
    public function getAovAktualAttribute()
    {
        return $this->summary ? $this->summary->aov : 0;
    }

    /**
     * Accessor untuk AOV shopee
     */
    public function getAovShopeeAttribute()
    {
        return $this->summary ? $this->summary->aov_shopee : 0;
    }

    /**
     * Accessor untuk AOV tiktok
     */
    public function getAovTiktokAttribute()
    {
        return $this->summary ? $this->summary->aov_tiktok : 0;
    }

    /**
     * Accessor untuk basket size aktual
     */
    public function getBasketSizeAktualAttribute()
    {
        if (!$this->summary || $this->summary->total_order_qty == 0) {
            return 0;
        }

        $totalProducts = $this->summary->total_order_qty;
        $totalOrders = $this->summary->total_income_count;
        return $totalOrders > 0 ? round($totalProducts / $totalOrders, 2) : 0;
    }

    /**
     * Accessor untuk ROAS shopee
     */
    public function getRoasShopeeAttribute()
    {
        return $this->iklan_shopee > 0 ?
            round(($this->total_pendapatan_shopee / $this->iklan_shopee) * 1, 2) : 0;
    }

    /**
     * Accessor untuk ROAS tiktok
     */
    public function getRoasTiktokAttribute()
    {
        return $this->iklan_tiktok > 0 ?
            round(($this->total_pendapatan_tiktok / $this->iklan_tiktok) * 1, 2) : 0;
    }

    /**
     * Accessor untuk ROAS total
     */
    public function getRoasAktualAttribute()
    {
        return $this->total_iklan > 0 ?
            round(($this->total_pendapatan / $this->total_iklan) * 100, 2) : 0;
    }

    /**
     * Accessor untuk ACOS shopee
     */
    public function getAcosShopeeAttribute()
    {
        return $this->total_pendapatan_shopee > 0 ?
            round(($this->iklan_shopee / $this->total_pendapatan_shopee) * 100, 2) : 0;
    }

    /**
     * Accessor untuk ACOS tiktok
     */
    public function getAcosTiktokAttribute()
    {
        return $this->total_pendapatan_tiktok > 0 ?
            round(($this->iklan_tiktok / $this->total_pendapatan_tiktok) * 100, 2) : 0;
    }

    /**
     * Accessor untuk ACOS total
     */
    public function getAcosAktualAttribute()
    {
        return $this->total_pendapatan > 0 ?
            round(($this->total_iklan / $this->total_pendapatan) * 100, 2) : 0;
    }

    // ========== METRIK QUANTITY ==========

    /**
     * Accessor untuk total order quantity dari summary
     */
    public function getTotalOrderQtyAttribute()
    {
        return $this->summary ? $this->summary->total_order_qty : 0;
    }

    /**
     * Accessor untuk total order shopee dari summary
     */
    public function getTotalOrderShopeeAttribute()
    {
        return $this->summary ? $this->summary->getOrdersCountByMarketplace('Shopee') : 0;
    }

    /**
     * Accessor untuk total order tiktok dari summary
     */
    public function getTotalOrderTiktokAttribute()
    {
        return $this->summary ? $this->summary->getOrdersCountByMarketplace('Tiktok') : 0;
    }

    /**
     * Accessor untuk total return quantity dari summary
     */
    public function getTotalReturnQtyAttribute()
    {
        return $this->summary ? $this->summary->total_return_qty : 0;
    }

    /**
     * Accessor untuk net quantity dari summary
     */
    public function getNetQuantityAttribute()
    {
        return $this->summary ? $this->summary->net_quantity : 0;
    }

    // ========== HELPER METHODS ==========

    /**
     * Helper untuk generate nama periode otomatis
     */
    public static function generateNamaPeriode($periodeAwal)
    {
        $awal = Carbon::parse($periodeAwal);
        return $awal->locale('id')->translatedFormat('F Y');
    }

    /**
     * Helper untuk generate periode akhir otomatis (akhir bulan)
     */
    public static function generatePeriodeAkhir($periodeAwal)
    {
        return Carbon::parse($periodeAwal)->endOfMonth();
    }

    /**
     * Get periode_akhir sebagai akhir hari (23:59:59)
     */
    public function getPeriodeAkhirEndOfDayAttribute()
    {
        return Carbon::parse($this->periode_akhir)->endOfDay();
    }

    /**
     * Get periode_awal sebagai awal hari (00:00:00)
     */
    public function getPeriodeAwalStartOfDayAttribute()
    {
        return Carbon::parse($this->periode_awal)->startOfDay();
    }

    /**
     * Scope untuk query berdasarkan periode
     */
    public function scopePeriodeRange($query, $startDate, $endDate)
    {
        return $query->where('periode_awal', '>=', $startDate)
                    ->where('periode_akhir', '<=', $endDate);
    }

    /**
     * Scope dengan eager loading summary
     */
    public function scopeWithSummary($query)
    {
        return $query->with('summary');
    }
}
