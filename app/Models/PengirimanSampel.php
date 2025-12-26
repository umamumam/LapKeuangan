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
        'no_resi',
        'ongkir',
        'sampel1_id', 'jumlah1',
        'sampel2_id', 'jumlah2',
        'sampel3_id', 'jumlah3',
        'sampel4_id', 'jumlah4',
        'sampel5_id', 'jumlah5',
        'totalhpp',
        'total_biaya',
        'penerima',
        'contact',
        'alamat'
    ];

    protected $casts = [
        'tanggal' => 'datetime',
    ];

    // Relasi dengan model Sampel untuk setiap sampel
    public function sampel1()
    {
        return $this->belongsTo(Sampel::class, 'sampel1_id');
    }

    public function sampel2()
    {
        return $this->belongsTo(Sampel::class, 'sampel2_id');
    }

    public function sampel3()
    {
        return $this->belongsTo(Sampel::class, 'sampel3_id');
    }

    public function sampel4()
    {
        return $this->belongsTo(Sampel::class, 'sampel4_id');
    }

    public function sampel5()
    {
        return $this->belongsTo(Sampel::class, 'sampel5_id');
    }

    // Method untuk mendapatkan semua sampel yang diinput
    public function getAllSampels()
    {
        $sampels = [];

        for ($i = 1; $i <= 5; $i++) {
            $sampelId = $this->{"sampel{$i}_id"};
            $jumlah = $this->{"jumlah{$i}"};

            if ($sampelId && $jumlah > 0) {
                $sampel = Sampel::find($sampelId);
                if ($sampel) {
                    $sampels[] = [
                        'sampel' => $sampel,
                        'jumlah' => $jumlah,
                        'subtotal' => $sampel->harga * $jumlah
                    ];
                }
            }
        }

        return $sampels;
    }

    // Accessor untuk totalhpp (calculated dari semua sampel)
    public function getTotalhppAttribute()
    {
        $total = 0;

        for ($i = 1; $i <= 5; $i++) {
            $sampelId = $this->{"sampel{$i}_id"};
            $jumlah = $this->{"jumlah{$i}"};

            if ($sampelId && $jumlah > 0) {
                $sampel = Sampel::find($sampelId);
                if ($sampel) {
                    $total += $sampel->harga * $jumlah;
                }
            }
        }

        return $total;
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
            // Hitung totalhpp dari semua sampel
            $totalhpp = 0;

            for ($i = 1; $i <= 5; $i++) {
                $sampelId = $model->{"sampel{$i}_id"};
                $jumlah = $model->{"jumlah{$i}"} ?? 0;

                if ($sampelId && $jumlah > 0) {
                    $sampel = Sampel::find($sampelId);
                    if ($sampel) {
                        $totalhpp += $sampel->harga * $jumlah;
                    }
                }
            }

            $model->totalhpp = $totalhpp;
            $model->total_biaya = $totalhpp + $model->ongkir;
        });
    }

    // Method untuk mendapatkan detail semua sampel dalam format array
    public function getSampelDetails()
    {
        $details = [];

        for ($i = 1; $i <= 5; $i++) {
            $sampelId = $this->{"sampel{$i}_id"};
            $jumlah = $this->{"jumlah{$i}"};

            if ($sampelId && $jumlah > 0) {
                $sampel = Sampel::find($sampelId);
                if ($sampel) {
                    $details[] = [
                        'nomor' => $i,
                        'id' => $sampel->id,
                        'nama' => $sampel->nama,
                        'ukuran' => $sampel->ukuran,
                        'harga' => $sampel->harga,
                        'jumlah' => $jumlah,
                        'subtotal' => $sampel->harga * $jumlah
                    ];
                }
            }
        }

        return $details;
    }
}
