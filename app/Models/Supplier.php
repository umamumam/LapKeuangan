<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    protected $fillable = ['nama'];

    public function barangs()
    {
        return $this->hasMany(Barang::class);
    }

    public function transactions()
    {
        return $this->hasMany(SupplierTransaction::class);
    }
}
