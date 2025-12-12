<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MonthlyFinance extends Model
{
    use HasFactory;

    protected $fillable = [
        'periode_id',
        'total_pendapatan',
        'operasional',
        'iklan',
        'rasio_admin_layanan',
        'keterangan'
    ];

    protected $casts = [
        'rasio_admin_layanan' => 'decimal:2',
        'total_pendapatan' => 'integer',
        'operasional' => 'integer',
        'iklan' => 'integer',
    ];

    protected $table = 'monthly_finances';

    public function periode()
    {
        return $this->belongsTo(Periode::class);
    }

}
