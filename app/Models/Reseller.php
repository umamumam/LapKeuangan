<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Reseller extends Model
{
    protected $fillable = ['nama', 'hutang_awal'];

    public function barangs()
    {
        return $this->hasMany(Barang::class);
    }

    public function transactions()
    {
        return $this->hasMany(ResellerTransaction::class);
    }

    public function payments()
    {
        return $this->hasMany(ResellerPayment::class);
    }
}
