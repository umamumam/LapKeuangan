<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sampel extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama',
        'ukuran',
        'harga'
    ];

    protected $casts = [
        'harga' => 'integer'
    ];

    // Relasi dengan pengiriman
    public function pengirimanSampels()
    {
        return $this->hasMany(PengirimanSampel::class);
    }

    // Method untuk mendapatkan opsi sampel
    public static function getSampelOptions()
    {
        return self::all()->pluck('nama', 'id')->toArray();
    }

    // Method untuk mendapatkan harga berdasarkan ID
    public static function getHarga($id)
    {
        $sampel = self::find($id);
        return $sampel ? $sampel->harga : 0;
    }

    // Accessor untuk format harga
    public function getHargaFormattedAttribute()
    {
        return 'Rp ' . number_format($this->harga, 0, ',', '.');
    }

    // Method untuk export
    public function toExportArray()
    {
        return [
            $this->nama,
            $this->ukuran,
            $this->harga_formatted,
            $this->created_at->format('d/m/Y H:i'),
            $this->updated_at->format('d/m/Y H:i')
        ];
    }

    public static function getExportHeaders()
    {
        return [
            'Nama Sampel',
            'Ukuran',
            'Harga',
            'Dibuat Pada',
            'Diupdate Pada'
        ];
    }
}
