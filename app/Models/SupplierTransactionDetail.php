<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SupplierTransactionDetail extends Model
{
    protected $fillable = [
        'supplier_transaction_id',
        'barang_id',
        'jumlah',
        'subtotal',
    ];

    public function barang()
    {
        return $this->belongsTo(Barang::class);
    }

    public function supplierTransaction()
    {
        return $this->belongsTo(SupplierTransaction::class);
    }
}
