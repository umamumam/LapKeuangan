<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Income extends Model
{
    use HasFactory;

    protected $fillable = [
        'no_pesanan',
        'no_pengajuan',
        'total_penghasilan'
    ];

    protected $casts = [
        'total_penghasilan' => 'integer'
    ];

    /**
     * Relasi ke orders berdasarkan no_pesanan
     * Satu income bisa memiliki banyak order dengan no_pesanan yang sama
     */
    public function orders()
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
    // ===========================================================================================
    public function scopePeriode($query, $startDate, $endDate)
    {
        return $query->whereBetween('created_at', [$startDate, $endDate]);
    }

    // Scope untuk bulan tertentu
    public function scopeBulan($query, $year, $month)
    {
        return $query->whereYear('created_at', $year)
            ->whereMonth('created_at', $month);
    }

    // Accessor untuk total HPP income ini
    public function getTotalHppAttribute()
    {
        return $this->orders->sum(function ($order) {
            $netQuantity = $order->jumlah - $order->returned_quantity;
            return $netQuantity * $order->produk->hpp_produk;
        });
    }

    // Accessor untuk laba income ini
    public function getLabaAttribute()
    {
        return $this->total_penghasilan - $this->total_hpp;
    }
}
