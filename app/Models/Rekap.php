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
        'total_penghasilan_shopee',
        'total_penghasilan_tiktok',
        'total_hpp_shopee',
        'total_hpp_tiktok',
        'total_iklan_shopee',
        'total_iklan_tiktok',
        'operasional',
    ];
    protected $table = 'rekaps';

    public function toko()
    {
        return $this->belongsTo(Toko::class);
    }
}
