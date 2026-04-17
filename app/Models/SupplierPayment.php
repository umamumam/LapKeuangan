<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SupplierPayment extends Model
{
    protected $fillable = [
        'supplier_id',
        'tgl',
        'keterangan',
        'nominal',
        'bukti_tf',
    ];

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }
}
