<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Rekap extends Model
{
    use HasFactory;
    protected $fillable = [
        'nama_periode',
        'tahun',
        'toko_id',
        'total_pendapatan_shopee',
        'total_pendapatan_tiktok',
        'total_penghasilan_shopee',
        'total_penghasilan_tiktok',
        'total_hpp_shopee',
        'total_hpp_tiktok',
        'total_iklan_shopee',
        'total_iklan_tiktok',
        'operasional',
        'rasio_admin_layanan_shopee',
        'rasio_admin_layanan_tiktok',
        'rasio_operasional',
        'aov_aktual_shopee',
        'aov_aktual_tiktok',
        'basket_size_aktual_shopee',
        'basket_size_aktual_tiktok',
    ];
    protected $table = 'rekaps';

    public function toko()
    {
        return $this->belongsTo(Toko::class);
    }
}
