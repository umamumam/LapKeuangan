<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Produk extends Model
{
    use HasFactory;

    protected $table = 'produks';

    protected $fillable = [
        'sku_induk',
        'nama_produk',
        'nomor_referensi_sku',
        'nama_variasi',
        'hpp_produk',
    ];

    protected $casts = [
        'hpp_produk' => 'integer',
    ];

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class, 'produk_id');
    }
}
