<?php

namespace App\Imports;

use App\Models\PengirimanSampel;
use App\Models\Sampel;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Carbon\Carbon;

class PengirimanSampelImport implements ToCollection, WithHeadingRow
{
    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            // Cari sampel berdasarkan nama dan ukuran
            $sampel = Sampel::where('nama', $row['nama_sampel'])
                        ->where('ukuran', $row['ukuran_sampel'])
                        ->first();

            if (!$sampel) {
                // Jika sampel tidak ditemukan, skip atau buat baru (sesuaikan dengan kebutuhan)
                continue;
            }

            // Hitung totalhpp dan total_biaya
            $totalhpp = $row['jumlah'] * $sampel->harga;
            $total_biaya = $totalhpp + $row['ongkir'];

            PengirimanSampel::create([
                'tanggal' => Carbon::createFromFormat('Y-m-d H:i', $row['tanggal']),
                'username' => $row['username'],
                'no_resi' => $row['no_resi'],
                'sampel_id' => $sampel->id,
                'jumlah' => $row['jumlah'],
                'ongkir' => $row['ongkir'],
                'totalhpp' => $totalhpp,
                'total_biaya' => $total_biaya,
                'penerima' => $row['penerima'],
                'contact' => $row['contact'],
                'alamat' => $row['alamat']
            ]);
        }
    }
}
