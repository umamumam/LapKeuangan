<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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

    // Relasi dengan incomes berdasarkan periode
    public function incomes()
    {
        return Income::whereBetween('created_at', [$this->periode_awal, $this->periode_akhir]);
    }

    // Relasi dengan orders berdasarkan periode
    public function orders()
    {
        return Order::whereBetween('created_at', [$this->periode_awal, $this->periode_akhir]);
    }

    public function getTotalPenghasilanAttribute()
    {
        return $this->incomes()->sum('total_penghasilan');
    }

    public function getHppAttribute()
    {
        // Ambil HPP dari data income dalam periode
        return $this->incomes()->get()->sum(function ($income) {
            return $income->orders->sum(function ($order) {
                $netQuantity = $order->jumlah - $order->returned_quantity;
                return $netQuantity * $order->produk->hpp_produk;
            });
        });
    }

    public function getLabaRugiAttribute()
    {
        // Laba/Rugi = Total Penghasilan - HPP - Operasional - Iklan
        return $this->total_penghasilan - $this->hpp - $this->operasional - $this->iklan;
    }

    public function getRasioOperasionalAttribute()
    {
        return $this->total_pendapatan > 0 ?
            round(($this->operasional / $this->total_pendapatan) * 100, 2) : 0;
    }

    public function getRasioMarginAttribute()
    {
        return $this->total_pendapatan > 0 ?
            round((($this->total_pendapatan - $this->hpp) / $this->total_pendapatan) * 100, 2) : 0;
    }

    public function getRasioLabaAttribute()
    {
        return $this->total_pendapatan > 0 ?
            round(($this->laba_rugi / $this->total_pendapatan) * 100, 2) : 0;
    }

    // Metrics berdasarkan data aktual
    public function getAovAktualAttribute()
    {
        $totalOrders = $this->orders()->sum('jumlah');
        $totalRevenueFromOrders = $this->orders()->sum('total_harga_produk');
        return $totalOrders > 0 ? round($totalRevenueFromOrders / $totalOrders, 2) : 0;
    }

    public function getBasketSizeAktualAttribute()
    {
        $totalProducts = $this->orders()->sum('jumlah');
        $totalOrders = $this->orders()->count();
        return $totalOrders > 0 ? round($totalProducts / $totalOrders, 2) : 0;
    }

    public function getRoasAktualAttribute()
    {
        return $this->iklan > 0 ? round(($this->total_pendapatan / $this->iklan) * 100, 2) : 0;
    }

    public function getAcosAktualAttribute()
    {
        return $this->total_pendapatan > 0 ? round(($this->iklan / $this->total_pendapatan) * 100, 2) : 0;
    }

    // Helper untuk generate nama periode otomatis
    public static function generateNamaPeriode($periodeAwal)
    {
        $awal = \Carbon\Carbon::parse($periodeAwal);
        return $awal->locale('id')->translatedFormat('F Y');
    }

    // Helper untuk generate periode akhir otomatis (akhir bulan)
    public static function generatePeriodeAkhir($periodeAwal)
    {
        return \Carbon\Carbon::parse($periodeAwal)->endOfMonth();
    }
}
