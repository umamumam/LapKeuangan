<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ResellerPayment extends Model
{
    use HasFactory;

    protected $fillable = [
        'reseller_id',
        'type',
        'tgl',
        'nominal',
        'keterangan',
    ];

    public function reseller()
    {
        return $this->belongsTo(Reseller::class);
    }
}
