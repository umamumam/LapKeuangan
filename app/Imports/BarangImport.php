<?php

namespace App\Imports;

use App\Models\Barang;
use App\Models\Reseller;
use App\Models\Supplier;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithCalculatedFormulas;

class BarangImport implements ToModel, WithHeadingRow, WithCalculatedFormulas
{
    private $resellers;
    private $suppliers;

    public function __construct()
    {
        $this->resellers = Reseller::pluck('id', 'nama')->toArray();
        $this->suppliers = Supplier::pluck('id', 'nama')->toArray();
    }

    public function model(array $row)
    {
        $hpp = $this->cleanNumber($row['hpp']);
        $beli_ptg = $this->cleanNumber($row['harga_beli_per_potong']);
        $beli_lsn = $this->cleanNumber($row['harga_beli_per_lusin']);
        $jual_ptg = $this->cleanNumber($row['harga_jual_per_potong']);
        $jual_lsn = $this->cleanNumber($row['harga_jual_per_lusin']);
        $harga_grosir = $this->cleanNumber($row['harga_grosir']);
        $keuntungan_input = $this->cleanNumber($row['keuntungan']);

        $keuntungan = 0;
        if ($keuntungan_input > 0) {
            $keuntungan = $keuntungan_input;
        } elseif ($jual_ptg > 0) {
            $dasar_biaya = ($hpp > 0) ? $hpp : $beli_ptg;
            if ($dasar_biaya > 0) {
                $keuntungan = $jual_ptg - $dasar_biaya;
            }
        }

        // Lookup Reseller and Supplier by name
        $reseller_id = null;
        if (!empty($row['reseller'])) {
            $reseller_name = trim($row['reseller']);
            $reseller_id = $this->resellers[$reseller_name] ?? null;
            if (!$reseller_id) {
                $reseller = Reseller::firstOrCreate(['nama' => $reseller_name]);
                $this->resellers[$reseller_name] = $reseller->id;
                $reseller_id = $reseller->id;
            }
        }

        $supplier_id = null;
        if (!empty($row['supplier'])) {
            $supplier_name = trim($row['supplier']);
            $supplier_id = $this->suppliers[$supplier_name] ?? null;
            if (!$supplier_id) {
                $supplier = Supplier::firstOrCreate(['nama' => $supplier_name]);
                $this->suppliers[$supplier_name] = $supplier->id;
                $supplier_id = $supplier->id;
            }
        }

        return new Barang([
            'reseller_id'         => $reseller_id,
            'supplier_id'         => $supplier_id,
            'namabarang'          => $row['nama_barang'],
            'ukuran'              => $row['ukuran'],
            'hpp'                 => $hpp,
            'hargabeli_perpotong' => $beli_ptg,
            'hargabeli_perlusin'  => $beli_lsn,
            'hargajual_perpotong' => $jual_ptg,
            'hargajual_perlusin'  => $jual_lsn,
            'harga_grosir'       => $harga_grosir,
            'keuntungan'          => $keuntungan,
        ]);
    }

    private function cleanNumber($value)
    {
        if ($value === null || $value === '' || $value === 0 || $value === "0") {
            return 0;
        }

        $numeric = preg_replace('/[^0-9-]/', '', $value);
        return $numeric === '' ? 0 : (int)$numeric;
    }
}
