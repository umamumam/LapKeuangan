<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Order extends Model
{
    use HasFactory;

    protected $table = 'orders';

    protected $fillable = [
        'no_pesanan',
        'produk_id',
        'jumlah',
        'returned_quantity',
        'pesananselesai',
        'total_harga_produk',
    ];

    protected $casts = [
        'jumlah' => 'integer',
        'returned_quantity' => 'integer',
        'pesananselesai' => 'datetime',
        'total_harga_produk' => 'integer',
    ];

    public function produk(): BelongsTo
    {
        return $this->belongsTo(Produk::class, 'produk_id');
    }

    public function income()
    {
        return $this->belongsTo(Income::class, 'no_pesanan', 'no_pesanan');
    }

    // Scope untuk filter by periode
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

    // Accessor untuk quantity bersih
    public function getNetQuantityAttribute()
    {
        return $this->jumlah - $this->returned_quantity;
    }

    // Accessor untuk subtotal HPP order ini
    public function getSubtotalHppAttribute()
    {
        return $this->net_quantity * $this->produk->hpp_produk;
    }
}
