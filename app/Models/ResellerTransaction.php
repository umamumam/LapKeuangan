<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ResellerTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'reseller_id',
        'tgl',
        'total_barang',
        'total_uang',
        'bayar',
        'sisa_kurang',
    ];

    public function reseller()
    {
        return $this->belongsTo(Reseller::class);
    }

    public function details()
    {
        return $this->hasMany(ResellerTransactionDetail::class, 'reseller_transaction_id');
    }
}
