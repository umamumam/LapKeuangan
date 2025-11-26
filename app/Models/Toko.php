<?php
// app/Models/Toko.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Toko extends Model
{
    use HasFactory;

    protected $fillable = ['nama'];

    protected $table = 'tokos';

    public function incomes()
    {
        return $this->hasMany(Income::class);
    }

    // ===============================================================================
    public function getTotalPenghasilanAttribute()
    {
        return $this->incomes->sum('total_penghasilan');
    }

    public function getJumlahTransaksiAttribute()
    {
        return $this->incomes->count();
    }

    public function getRataRataPenghasilanAttribute()
    {
        $jumlahTransaksi = $this->jumlah_transaksi;
        return $jumlahTransaksi > 0 ? $this->total_penghasilan / $jumlahTransaksi : 0;
    }

    public function scopeWithTotalPenghasilan($query)
    {
        return $query->withSum('incomes', 'total_penghasilan');
    }

    public function scopeWithJumlahTransaksi($query)
    {
        return $query->withCount('incomes');
    }
}
