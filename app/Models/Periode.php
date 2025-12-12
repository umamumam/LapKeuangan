<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Periode extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama_periode',
        'tanggal_mulai',
        'tanggal_selesai',
        'toko_id',
        'marketplace',

        // Data agregasi dari orders
        'total_harga_produk',
        'jumlah_order',
        'returned_quantity',
        'total_hpp_produk',

        // Data agregasi dari incomes
        'total_penghasilan',
        'jumlah_income',

        // Data per marketplace
        'total_penghasilan_shopee',
        'total_income_count_shopee',
        'total_hpp_shopee',

        'total_penghasilan_tiktok',
        'total_income_count_tiktok',
        'total_hpp_tiktok',

        // Status generate
        'is_generated',
        'generated_at'
    ];

    protected $casts = [
        'tanggal_mulai' => 'date',
        'tanggal_selesai' => 'date',
        'generated_at' => 'datetime',
        'is_generated' => 'boolean'
    ];

    protected $table = 'periodes';

    public function toko(): BelongsTo
    {
        return $this->belongsTo(Toko::class);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function incomes(): HasMany
    {
        return $this->hasMany(Income::class);
    }

    public function monthlyFinance()
    {
        return $this->hasOne(MonthlyFinance::class);
    }

    public function scopeNotGenerated($query)
    {
        return $query->where('is_generated', false);
    }

    public function scopeGenerated($query)
    {
        return $query->where('is_generated', true);
    }

    public function scopeByMarketplace($query, $marketplace)
    {
        return $query->where('marketplace', $marketplace);
    }

    public function scopeBetweenDates($query, $start, $end)
    {
        return $query->whereBetween('tanggal_mulai', [$start, $end])
            ->orWhereBetween('tanggal_selesai', [$start, $end]);
    }
}
