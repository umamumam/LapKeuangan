<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Barang extends Model
{
    use HasFactory;

    protected $fillable = [
        'reseller_id',
        'supplier_id',
        'namabarang',
        'ukuran',
        'hpp',
        'hargabeli_perpotong',
        'hargabeli_perlusin',
        'hargajual_perpotong',
        'hargajual_perlusin',
        'keuntungan',
    ];

    public function reseller()
    {
        return $this->belongsTo(Reseller::class);
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }
}
