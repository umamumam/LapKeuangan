<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SupplierTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'supplier_id',
        'tanggal',
        'sisa_nota',
        'lusin',
        'potong',
        'nama_barang',
        'harga',
        'jumlah',
        'tf',
    ];

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }
}
