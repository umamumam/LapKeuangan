<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Toko extends Model
{
    use HasFactory;

    protected $fillable = ['nama'];
    protected $table = 'tokos';

    public function periodes(): HasMany
    {
        return $this->hasMany(Periode::class);
    }

    public function periodesShopee(): HasMany
    {
        return $this->periodes()->where('marketplace', 'Shopee');
    }

    public function periodesTiktok(): HasMany
    {
        return $this->periodes()->where('marketplace', 'Tiktok');
    }

    public function rekaps()
    {
        return $this->hasMany(Rekap::class);
    }

    public function bandings()
    {
        return $this->hasMany(Banding::class);
    }

    public function pengirimanSampels()
    {
        return $this->hasMany(PengirimanSampel::class);
    }

    public function activePeriodes()
    {
        $today = now()->format('Y-m-d');
        return $this->periodes()
            ->where('tanggal_mulai', '<=', $today)
            ->where('tanggal_selesai', '>=', $today);
    }
}
