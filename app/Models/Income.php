<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Income extends Model
{
    use HasFactory;

    protected $fillable = [
        'no_pesanan',
        'no_pengajuan',
        'total_penghasilan',
        'periode_id'
    ];

    protected $casts = [
        'total_penghasilan' => 'integer'
    ];

    public function periode(): BelongsTo
    {
        return $this->belongsTo(Periode::class);
    }

    /**
     * Relasi ke orders berdasarkan no_pesanan
     * Satu income bisa memiliki banyak order dengan no_pesanan yang sama
     */
    public function orders(): HasMany
    {
        return $this->hasMany(Order::class, 'no_pesanan', 'no_pesanan');
    }

    /**
     * Relasi ke salah satu order (biasanya yang pertama)
     * untuk kemudahan akses
     */
    public function order()
    {
        return $this->hasOne(Order::class, 'no_pesanan', 'no_pesanan');
    }

    /**
     * Scope untuk filter berdasarkan periode
     */
    public function scopeByPeriode($query, $periodeId)
    {
        return $query->where('periode_id', $periodeId);
    }

    /**
     * Scope untuk income tanpa periode
     */
    public function scopeWithoutPeriode($query)
    {
        return $query->whereNull('periode_id');
    }

    /**
     * Scope untuk filter berdasarkan rentang tanggal
     */
    public function scopePeriode($query, $startDate, $endDate)
    {
        return $query->whereBetween('created_at', [$startDate, $endDate]);
    }

    /**
     * Scope untuk bulan tertentu
     */
    public function scopeBulan($query, $year, $month)
    {
        return $query->whereYear('created_at', $year)
            ->whereMonth('created_at', $month);
    }

    /**
     * Scope untuk filter berdasarkan no_pesanan
     */
    public function scopeSearchNoPesanan($query, $noPesanan)
    {
        return $query->where('no_pesanan', 'like', "%{$noPesanan}%");
    }

    /**
     * Scope untuk filter berdasarkan no_pengajuan
     */
    public function scopeSearchNoPengajuan($query, $noPengajuan)
    {
        return $query->where('no_pengajuan', 'like', "%{$noPengajuan}%");
    }

    /**
     * Accessor untuk total HPP income ini
     * Menghitung total HPP dari semua order yang terkait
     */
    public function getTotalHppAttribute()
    {
        return $this->orders->sum(function ($order) {
            $netQuantity = $order->jumlah - $order->returned_quantity;
            return $netQuantity * $order->produk->hpp_produk;
        });
    }

    /**
     * Accessor untuk laba income ini
     */
    public function getLabaAttribute()
    {
        return $this->total_penghasilan - $this->total_hpp;
    }

    /**
     * Accessor untuk mendapatkan toko melalui periode
     */
    public function getTokoAttribute()
    {
        return $this->periode?->toko;
    }

    /**
     * Accessor untuk nama toko
     */
    public function getNamaTokoAttribute()
    {
        return $this->periode?->toko?->nama ?? '-';
    }

    /**
     * Accessor untuk marketplace dari periode
     */
    public function getMarketplaceAttribute()
    {
        return $this->periode?->marketplace ?? null;
    }

    /**
     * Accessor untuk jumlah order yang terkait
     */
    public function getJumlahOrderAttribute()
    {
        return $this->orders->count();
    }

    /**
     * Accessor untuk total quantity bersih (jumlah - returned)
     */
    public function getTotalNetQuantityAttribute()
    {
        return $this->orders->sum(function ($order) {
            return $order->jumlah - $order->returned_quantity;
        });
    }

    /**
     * Method untuk menghubungkan income ke periode
     */
    public function attachToPeriode(Periode $periode)
    {
        $this->periode()->associate($periode);
        return $this->save();
    }

    /**
     * Method untuk memisahkan income dari periode
     */
    public function detachFromPeriode()
    {
        $this->periode()->dissociate();
        return $this->save();
    }
}
