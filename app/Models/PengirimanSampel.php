<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PengirimanSampel extends Model
{
    use HasFactory;

    protected $table = 'pengiriman_sampels';

    protected $fillable = [
        'tanggal',
        'username',
        'jumlah',
        'no_resi',
        'ongkir',
        'sampel_id',
        'totalhpp',
        'total_biaya',
        'penerima',
        'contact',
        'alamat'
    ];

    protected $casts = [
        'tanggal' => 'datetime',
    ];

    // Relasi dengan model Sampel
    public function sampel()
    {
        return $this->belongsTo(Sampel::class);
    }

    // Accessor untuk totalhpp (calculated)
    public function getTotalhppAttribute()
    {
        return $this->jumlah * $this->sampel->harga;
    }

    // Accessor untuk total_biaya (calculated)
    public function getTotalBiayaAttribute()
    {
        return $this->totalhpp + $this->ongkir;
    }

    // Event untuk menghitung total sebelum save
    protected static function boot()
    {
        parent::boot();

        static::saving(function ($model) {
            // Hitung totalhpp berdasarkan jumlah dan harga sampel
            if ($model->sampel && $model->jumlah) {
                $model->totalhpp = $model->jumlah * $model->sampel->harga;
            }

            // Hitung total_biaya
            $model->total_biaya = $model->totalhpp + $model->ongkir;
        });
    }
}
