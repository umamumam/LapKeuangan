<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PengembalianPenukaran extends Model
{
    use HasFactory;

    protected $fillable = [
        'tanggal',
        'jenis',
        'marketplace',
        'resi_penerimaan',
        'resi_pengiriman',
        'pembayaran',
        'nama_pengirim',
        'no_hp',
        'alamat',
        'keterangan',
    ];

    protected $casts = [
        'tanggal' => 'date',
    ];

    const JENIS = [
        'Pengembalian' => 'Pengembalian',
        'Penukaran' => 'Penukaran',
        'Pengembalian Dana' => 'Pengembalian Dana',
    ];

    const MARKETPLACE = [
        'Tiktok' => 'Tiktok',
        'Shopee' => 'Shopee',
        'Reguler' => 'Reguler',
    ];

    const PEMBAYARAN = [
        'Sistem' => 'Sistem',
        'Tunai' => 'Tunai',
        'DFOD' => 'DFOD',
    ];

    public function getJenisLabelAttribute()
    {
        return self::JENIS[$this->jenis] ?? $this->jenis;
    }

    public function getMarketplaceLabelAttribute()
    {
        return self::MARKETPLACE[$this->marketplace] ?? $this->marketplace;
    }

    public function getPembayaranLabelAttribute()
    {
        return self::PEMBAYARAN[$this->pembayaran] ?? $this->pembayaran;
    }

    public function scopeFilterByJenis($query, $jenis)
    {
        if ($jenis) {
            return $query->where('jenis', $jenis);
        }
        return $query;
    }

    public function scopeFilterByMarketplace($query, $marketplace)
    {
        if ($marketplace) {
            return $query->where('marketplace', $marketplace);
        }
        return $query;
    }

    public function scopeFilterByTanggal($query, $startDate = null, $endDate = null)
    {
        if ($startDate) {
            $query->whereDate('tanggal', '>=', $startDate);
        }
        if ($endDate) {
            $query->whereDate('tanggal', '<=', $endDate);
        }
        return $query;
    }
}
