<?php

namespace App\Imports;

use App\Models\PengirimanSampel;
use App\Models\Sampel;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PengirimanSampelImport implements ToCollection, WithHeadingRow, WithValidation
{
    private $importErrors = [];
    private $successCount = 0;
    private $rowNumber = 0;

    public function collection(Collection $rows)
    {
        DB::beginTransaction();

        try {
            foreach ($rows as $row) {
                $this->rowNumber++;

                // Validasi dan proses data
                $processedData = $this->processRow($row);

                if ($processedData) {
                    $this->saveData($processedData);
                    $this->successCount++;
                }
            }

            DB::commit();

            // Simpan session untuk error reporting
            if (!empty($this->importErrors)) {
                session()->flash('import_errors', $this->importErrors);
            }

            session()->flash('import_success', $this->successCount . ' data berhasil diimport.');

        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    private function processRow($row)
    {
        try {
            // Tanggal (handle berbagai format)
            $tanggal = $this->parseDate($row['tanggal'] ?? null);
            if (!$tanggal) {
                $this->importErrors[] = [
                    'row' => $this->rowNumber + 1,
                    'reason' => 'Format tanggal tidak valid'
                ];
                return null;
            }

            // Validasi required fields
            $requiredFields = ['username', 'no_resi', 'penerima', 'contact', 'alamat'];
            foreach ($requiredFields as $field) {
                if (empty($row[$field])) {
                    $this->importErrors[] = [
                        'row' => $this->rowNumber + 1,
                        'reason' => "Kolom $field wajib diisi"
                    ];
                    return null;
                }
            }

            // Process sampel data
            $sampelData = [];
            $totalhpp = 0;

            for ($i = 1; $i <= 5; $i++) {
                $namaSampel = $row["nama_sampel_{$i}"] ?? null;
                $ukuranSampel = $row["ukuran_sampel_{$i}"] ?? null;
                $jumlah = $row["jumlah_{$i}"] ?? 0;

                if ($namaSampel && $ukuranSampel && $jumlah > 0) {
                    // Cari sampel berdasarkan nama dan ukuran
                    $sampel = Sampel::where('nama', $namaSampel)
                        ->where('ukuran', $ukuranSampel)
                        ->first();

                    if (!$sampel) {
                        $this->importErrors[] = [
                            'row' => $this->rowNumber + 1,
                            'reason' => "Sampel {$i} tidak ditemukan: {$namaSampel} ({$ukuranSampel})"
                        ];
                        return null;
                    }

                    $sampelData["sampel{$i}_id"] = $sampel->id;
                    $sampelData["jumlah{$i}"] = (int) $jumlah;
                    $totalhpp += $sampel->harga * (int) $jumlah;
                } else {
                    $sampelData["sampel{$i}_id"] = null;
                    $sampelData["jumlah{$i}"] = 0;
                }
            }

            // Hitung total biaya (totalhpp + ongkir)
            $ongkir = (int) $row['ongkir'];
            $total_biaya = $totalhpp + $ongkir;

            return [
                'tanggal' => $tanggal,
                'username' => $row['username'],
                'no_resi' => $row['no_resi'],
                'ongkir' => $ongkir,
                'penerima' => $row['penerima'],
                'contact' => $row['contact'],
                'alamat' => $row['alamat'],
                'totalhpp' => $totalhpp,
                'total_biaya' => $total_biaya,
                'sampel_data' => $sampelData
            ];

        } catch (\Exception $e) {
            $this->importErrors[] = [
                'row' => $this->rowNumber + 1,
                'reason' => 'Error processing data: ' . $e->getMessage()
            ];
            return null;
        }
    }

    private function saveData($data)
    {
        // Cek duplikat berdasarkan no_resi dan tanggal
        $existing = PengirimanSampel::where('no_resi', $data['no_resi'])
            ->where('tanggal', $data['tanggal'])
            ->first();

        if ($existing) {
            // Update existing data
            $existing->update([
                'username' => $data['username'],
                'ongkir' => $data['ongkir'],
                'penerima' => $data['penerima'],
                'contact' => $data['contact'],
                'alamat' => $data['alamat'],
                'totalhpp' => $data['totalhpp'],
                'total_biaya' => $data['total_biaya'],
                ...$data['sampel_data']
            ]);
        } else {
            // Create new data
            PengirimanSampel::create([
                'tanggal' => $data['tanggal'],
                'username' => $data['username'],
                'no_resi' => $data['no_resi'],
                'ongkir' => $data['ongkir'],
                'penerima' => $data['penerima'],
                'contact' => $data['contact'],
                'alamat' => $data['alamat'],
                'totalhpp' => $data['totalhpp'],
                'total_biaya' => $data['total_biaya'],
                ...$data['sampel_data']
            ]);
        }
    }

    private function parseDate($dateString)
    {
        if (empty($dateString)) {
            return null;
        }

        try {
            // Coba parse berbagai format tanggal
            if (is_numeric($dateString)) {
                // Excel timestamp
                $unixDate = ($dateString - 25569) * 86400;
                return Carbon::createFromTimestamp($unixDate);
            } else {
                return Carbon::parse($dateString);
            }
        } catch (\Exception $e) {
            return null;
        }
    }

    public function rules(): array
    {
        return [
            '*.tanggal' => 'nullable',
            '*.username' => 'required',
            '*.no_resi' => 'required',
            '*.ongkir' => 'nullable|numeric',
            '*.penerima' => 'required',
            '*.contact' => 'required',
            '*.alamat' => 'required',

            // Rules untuk sampel (opsional)
            '*.nama_sampel_1' => 'nullable',
            '*.ukuran_sampel_1' => 'nullable',
            '*.jumlah_1' => 'nullable|numeric',
            '*.nama_sampel_2' => 'nullable',
            '*.ukuran_sampel_2' => 'nullable',
            '*.jumlah_2' => 'nullable|numeric',
            '*.nama_sampel_3' => 'nullable',
            '*.ukuran_sampel_3' => 'nullable',
            '*.jumlah_3' => 'nullable|numeric',
            '*.nama_sampel_4' => 'nullable',
            '*.ukuran_sampel_4' => 'nullable',
            '*.jumlah_4' => 'nullable|numeric',
            '*.nama_sampel_5' => 'nullable',
            '*.ukuran_sampel_5' => 'nullable',
            '*.jumlah_5' => 'nullable|numeric',
        ];
    }

    public function customValidationMessages()
    {
        return [
            '*.username.required' => 'Kolom username wajib diisi',
            '*.no_resi.required' => 'Kolom no_resi wajib diisi',
            '*.penerima.required' => 'Kolom penerima wajib diisi',
            '*.contact.required' => 'Kolom contact wajib diisi',
            '*.alamat.required' => 'Kolom alamat wajib diisi',
        ];
    }
}
