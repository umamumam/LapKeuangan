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
        'total_pendapatan',
        'operasional',
        'iklan',
        'rasio_admin_layanan',
        'keterangan'
    ];

    protected $casts = [
        'periode_awal' => 'date',
        'periode_akhir' => 'date',
        'rasio_admin_layanan' => 'decimal:2',
    ];

    /**
     * Relasi ke MonthlySummary berdasarkan nama_periode
     */
    public function summary()
    {
        return $this->hasOne(MonthlySummary::class, 'nama_periode', 'nama_periode');
    }

    /**
     * Accessor untuk total_penghasilan dari summary
     */
    public function getTotalPenghasilanAttribute()
    {
        return $this->summary ? $this->summary->total_penghasilan : 0;
    }

    /**
     * Accessor untuk HPP dari summary
     */
    public function getHppAttribute()
    {
        return $this->summary ? $this->summary->total_hpp : 0;
    }

    /**
     * Accessor untuk laba/rugi yang sudah include operasional dan iklan
     */
    public function getLabaRugiAttribute()
    {
        $penghasilanBersih = $this->total_penghasilan - $this->hpp;
        return $penghasilanBersih - $this->operasional - $this->iklan;
    }

    /**
     * Accessor untuk rasio operasional
     */
    public function getRasioOperasionalAttribute()
    {
        return $this->total_pendapatan > 0 ?
            round(($this->operasional / $this->total_pendapatan) * 100, 2) : 0;
    }

    /**
     * Accessor untuk rasio margin
     */
    public function getRasioMarginAttribute()
    {
        return $this->total_pendapatan > 0 ?
            round((($this->total_pendapatan - $this->hpp) / $this->total_pendapatan) * 100, 2) : 0;
    }

    /**
     * Accessor untuk rasio laba
     */
    public function getRasioLabaAttribute()
    {
        return $this->total_pendapatan > 0 ?
            round(($this->laba_rugi / $this->total_pendapatan) * 100, 2) : 0;
    }

    /**
     * Accessor untuk AOV aktual dari summary
     */
    public function getAovAktualAttribute()
    {
        return $this->summary ? $this->summary->aov : 0;
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
     * Accessor untuk ROAS aktual
     */
    public function getRoasAktualAttribute()
    {
        return $this->iklan > 0 ?
            round(($this->total_pendapatan / $this->iklan) * 100, 2) : 0;
    }

    /**
     * Accessor untuk ACOS aktual
     */
    public function getAcosAktualAttribute()
    {
        return $this->total_pendapatan > 0 ?
            round(($this->iklan / $this->total_pendapatan) * 100, 2) : 0;
    }

    /**
     * Accessor untuk total order quantity dari summary
     */
    public function getTotalOrderQtyAttribute()
    {
        return $this->summary ? $this->summary->total_order_qty : 0;
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
