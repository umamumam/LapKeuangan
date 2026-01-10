<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Banding extends Model
{
    use HasFactory;

    protected $fillable = [
        'tanggal',
        'status_banding',
        'ongkir',
        'no_resi',
        'no_pesanan',
        'no_pengajuan',
        'alasan',
        'status_penerimaan',
        'username',
        'nama_pengirim',
        'no_hp',
        'alamat',
        'marketplace',
        'toko_id',
        'statusditerima'
    ];

    protected $casts = [
        'tanggal' => 'datetime'
    ];

    protected $attributes = [
        'statusditerima' => 'Belum',
    ];

    public static function getStatusDiterimaOptions()
    {
        return [
            'OK' => 'OK',
            'Belum' => 'Belum'
        ];
    }

    public function toko()
    {
        return $this->belongsTo(Toko::class);
    }

    public static function getStatusBandingOptions()
    {
        return [
            'Berhasil' => 'Berhasil',
            'Ditinjau' => 'Ditinjau',
            'Ditolak' => 'Ditolak',
            '-' => '-'
        ];
    }

    public static function getOngkirOptions()
    {
        return [
            'Dibebaskan' => 'Dibebaskan',
            'Ditanggung' => 'Ditanggung',
            '-' => '-'
        ];
    }

    public static function getAlasanOptions()
    {
        return [
            'Barang Palsu' => 'Barang Palsu',
            'Tidak Sesuai Ekspektasi Pembeli' => 'Tidak Sesuai Ekspektasi Pembeli',
            'Barang Belum Diterima' => 'Barang Belum Diterima',
            'Cacat' => 'Cacat',
            'Jumlah Barang Retur Kurang' => 'Jumlah Barang Retur Kurang',
            'Bukan Produk Asli Toko' => 'Bukan Produk Asli Toko',
            '-' => '-'
        ];
    }

    public static function getStatusPenerimaanOptions()
    {
        return [
            'Diterima dengan baik' => 'Diterima dengan baik',
            'Cacat' => 'Cacat',
            '-' => '-'
        ];
    }

    public static function getMarketplaceOptions()
    {
        return [
            'Shopee' => 'Shopee',
            'Tiktok' => 'Tiktok'
        ];
    }

    // Method untuk export
    public static function getExportHeaders()
    {
        return [
            'Tanggal',
            'Status Banding',
            'Ongkir',
            'No Resi',
            'No Pesanan',
            'No Pengajuan',
            'Alasan',
            'Status Penerimaan',
            'Username',
            'Nama Pengirim',
            'No HP',
            'Alamat',
            'Marketplace',
            'Status Diterima',
            'Dibuat Pada',
            'Diupdate Pada'
        ];
    }

    public function toExportArray()
    {
        return [
            $this->tanggal->format('d/m/Y H:i'),
            $this->status_banding,
            $this->ongkir,
            $this->no_resi,
            $this->no_pesanan,
            $this->no_pengajuan,
            $this->alasan,
            $this->status_penerimaan,
            $this->username,
            $this->nama_pengirim,
            $this->no_hp,
            $this->alamat,
            $this->marketplace,
            $this->statusditerima,
            $this->created_at->format('d/m/Y H:i'),
            $this->updated_at->format('d/m/Y H:i')
        ];
    }
}
