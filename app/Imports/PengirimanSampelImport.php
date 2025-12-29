<?php

namespace App\Imports;

use App\Models\PengirimanSampel;
use App\Models\Sampel;
use App\Models\Toko;
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
    private $selectedTokoId;

    // Tambah constructor untuk menerima toko_id
    public function __construct($tokoId = null)
    {
        $this->selectedTokoId = $tokoId;
    }

    public function collection(Collection $rows)
    {
        // Validasi bahwa toko_id harus ada
        if (!$this->selectedTokoId) {
            throw new \Exception('Toko tidak dipilih. Silakan pilih toko terlebih dahulu.');
        }

        // Cek apakah toko ada di database
        $toko = Toko::find($this->selectedTokoId);
        if (!$toko) {
            throw new \Exception('Toko dengan ID ' . $this->selectedTokoId . ' tidak ditemukan.');
        }

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

            session()->flash('import_success', $this->successCount . ' data berhasil diimport ke toko "' . $toko->nama . '".');

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
                    // Cari sampel berdasarkan nama dan ukuran (case insensitive)
                    $sampel = Sampel::whereRaw('LOWER(nama) = ?', [strtolower($namaSampel)])
                        ->whereRaw('LOWER(ukuran) = ?', [strtolower($ukuranSampel)])
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
            $ongkir = (int) ($row['ongkir'] ?? 0);
            $total_biaya = $totalhpp + $ongkir;

            return [
                'tanggal' => $tanggal,
                'username' => $row['username'],
                'no_resi' => $row['no_resi'],
                'ongkir' => $ongkir,
                'toko_id' => $this->selectedTokoId, // Gunakan toko_id dari form
                'penerima' => $row['penerima'],
                'contact' => $row['contact'],
                'alamat' => $row['alamat'],
                'totalhpp' => $totalhpp,
                'total_biaya' => $total_biaya,
                'sampel_data' => $sampelData,
                'created_at' => now(),
                'updated_at' => now()
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
        // Cek duplikat berdasarkan no_resi dan tanggal (dan toko)
        $existing = PengirimanSampel::where('no_resi', $data['no_resi'])
            ->where('tanggal', $data['tanggal'])
            ->where('toko_id', $data['toko_id'])
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
                'updated_at' => $data['updated_at'],
                ...$data['sampel_data']
            ]);
        } else {
            // Create new data
            PengirimanSampel::create([
                'tanggal' => $data['tanggal'],
                'username' => $data['username'],
                'no_resi' => $data['no_resi'],
                'ongkir' => $data['ongkir'],
                'toko_id' => $data['toko_id'], // Simpan toko_id
                'penerima' => $data['penerima'],
                'contact' => $data['contact'],
                'alamat' => $data['alamat'],
                'totalhpp' => $data['totalhpp'],
                'total_biaya' => $data['total_biaya'],
                'created_at' => $data['created_at'],
                'updated_at' => $data['updated_at'],
                ...$data['sampel_data']
            ]);
        }
    }

    private function parseDate($dateString)
    {
        if (empty($dateString)) {
            return now();
        }

        try {
            // Coba parse berbagai format tanggal
            if (is_numeric($dateString)) {
                // Excel timestamp
                $unixDate = ($dateString - 25569) * 86400;
                return Carbon::createFromTimestamp($unixDate);
            } else {
                // Coba berbagai format tanggal
                $formats = [
                    'd/m/Y H:i:s',
                    'd/m/Y H:i',
                    'd/m/Y',
                    'Y-m-d H:i:s',
                    'Y-m-d H:i',
                    'Y-m-d',
                    'm/d/Y H:i:s',
                    'm/d/Y H:i',
                    'm/d/Y',
                    'd-m-Y H:i:s',
                    'd-m-Y H:i',
                    'd-m-Y',
                ];

                foreach ($formats as $format) {
                    try {
                        $date = Carbon::createFromFormat($format, $dateString);
                        if ($date !== false) {
                            return $date;
                        }
                    } catch (\Exception $e) {
                        continue;
                    }
                }

                // Coba parse secara natural
                return Carbon::parse($dateString);
            }
        } catch (\Exception $e) {
            $this->importErrors[] = [
                'row' => $this->rowNumber + 1,
                'reason' => 'Format tanggal tidak dikenali: ' . $dateString
            ];
            return now(); // Fallback ke waktu sekarang
        }
    }

    public function rules(): array
    {
        return [
            '*.tanggal' => 'nullable',
            '*.username' => 'required',
            '*.no_resi' => 'required',
            '*.ongkir' => 'nullable|numeric|min:0',
            '*.penerima' => 'required',
            '*.contact' => 'required',
            '*.alamat' => 'required',

            // Rules untuk sampel (opsional)
            '*.nama_sampel_1' => 'nullable',
            '*.ukuran_sampel_1' => 'nullable',
            '*.jumlah_1' => 'nullable|numeric|min:0',
            '*.nama_sampel_2' => 'nullable',
            '*.ukuran_sampel_2' => 'nullable',
            '*.jumlah_2' => 'nullable|numeric|min:0',
            '*.nama_sampel_3' => 'nullable',
            '*.ukuran_sampel_3' => 'nullable',
            '*.jumlah_3' => 'nullable|numeric|min:0',
            '*.nama_sampel_4' => 'nullable',
            '*.ukuran_sampel_4' => 'nullable',
            '*.jumlah_4' => 'nullable|numeric|min:0',
            '*.nama_sampel_5' => 'nullable',
            '*.ukuran_sampel_5' => 'nullable',
            '*.jumlah_5' => 'nullable|numeric|min:0',
        ];
    }

    public function customValidationMessages()
    {
        return [
            '*.username.required' => 'Kolom username wajib diisi',
            '*.no_resi.required' => 'Kolom no_resi wajib diisi',
            '*.ongkir.numeric' => 'Kolom ongkir harus berupa angka',
            '*.ongkir.min' => 'Kolom ongkir minimal 0',
            '*.penerima.required' => 'Kolom penerima wajib diisi',
            '*.contact.required' => 'Kolom contact wajib diisi',
            '*.alamat.required' => 'Kolom alamat wajib diisi',
            '*.jumlah_1.numeric' => 'Jumlah sampel 1 harus berupa angka',
            '*.jumlah_1.min' => 'Jumlah sampel 1 minimal 0',
            '*.jumlah_2.numeric' => 'Jumlah sampel 2 harus berupa angka',
            '*.jumlah_2.min' => 'Jumlah sampel 2 minimal 0',
            '*.jumlah_3.numeric' => 'Jumlah sampel 3 harus berupa angka',
            '*.jumlah_3.min' => 'Jumlah sampel 3 minimal 0',
            '*.jumlah_4.numeric' => 'Jumlah sampel 4 harus berupa angka',
            '*.jumlah_4.min' => 'Jumlah sampel 4 minimal 0',
            '*.jumlah_5.numeric' => 'Jumlah sampel 5 harus berupa angka',
            '*.jumlah_5.min' => 'Jumlah sampel 5 minimal 0',
        ];
    }

    // Method untuk mendapatkan jumlah error
    public function getErrorCount()
    {
        return count($this->importErrors);
    }

    // Method untuk mendapatkan jumlah success
    public function getSuccessCount()
    {
        return $this->successCount;
    }

    // Method untuk mendapatkan error messages
    public function getErrors()
    {
        return $this->importErrors;
    }
}
