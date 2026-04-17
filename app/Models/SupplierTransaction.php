<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SupplierTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'supplier_id',
        'tgl',
        'total_barang',
        'total_uang',
        'bayar',
        'total_tagihan',
        'retur',
        'bukti_tf',
    ];

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function details()
    {
        return $this->hasMany(SupplierTransactionDetail::class, 'supplier_transaction_id');
    }
}
