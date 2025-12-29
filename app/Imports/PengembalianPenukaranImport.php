<?php

namespace App\Imports;

use App\Models\PengembalianPenukaran;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\Importable;
use Carbon\Carbon;
use Illuminate\Validation\Rule;

class PengembalianPenukaranImport implements ToModel, WithHeadingRow, WithValidation, SkipsOnFailure
{
    use Importable, SkipsFailures;

    private $rowCount = 0;
    private $errors = [];

    public function model(array $row)
    {
        $this->rowCount++;
        $currentRow = $this->rowCount + 1;

        try {
            $tanggal = $this->parseDate($row['tanggal'] ?? $row['Tanggal'] ?? null, $currentRow);
            $noHp = $this->formatPhoneNumber($row['no_hp'] ?? $row['No HP'] ?? $row['No_HP'] ?? '', $currentRow);

            $resiPenerimaan = $row['resi_penerimaan'] ?? $row['Resi Penerimaan'] ?? $row['Resi_Penerimaan'] ?? null;
            $resiPengiriman = $row['resi_pengiriman'] ?? $row['Resi Pengiriman'] ?? $row['Resi_Pengiriman'] ?? null;

            $jenis = $this->normalizeEnum($row['jenis'] ?? $row['Jenis'] ?? null, 'jenis', $currentRow);
            $marketplace = $this->normalizeEnum($row['marketplace'] ?? $row['Marketplace'] ?? null, 'marketplace', $currentRow);
            $pembayaran = $this->normalizeEnum($row['pembayaran'] ?? $row['Pembayaran'] ?? null, 'pembayaran', $currentRow);

            $namaPengirim = $row['nama_pengirim'] ?? $row['Nama Pengirim'] ?? $row['Nama_Pengirim'] ?? null;
            $alamat = $row['alamat'] ?? $row['Alamat'] ?? null;
            $keterangan = $row['keterangan'] ?? $row['Keterangan'] ?? null;

            if (empty($namaPengirim)) {
                throw new \Exception("Nama pengirim tidak boleh kosong");
            }

            if (empty($alamat)) {
                throw new \Exception("Alamat tidak boleh kosong");
            }

            return new PengembalianPenukaran([
                'tanggal' => $tanggal,
                'jenis' => $jenis,
                'marketplace' => $marketplace,
                'resi_penerimaan' => $resiPenerimaan,
                'resi_pengiriman' => $resiPengiriman,
                'pembayaran' => $pembayaran,
                'nama_pengirim' => $namaPengirim,
                'no_hp' => $noHp,
                'alamat' => $alamat,
                'keterangan' => $keterangan,
            ]);

        } catch (\Exception $e) {
            $this->errors[] = [
                'row' => $currentRow,
                'resi' => $resiPenerimaan ?? '-',
                'nama' => $row['nama_pengirim'] ?? $row['Nama Pengirim'] ?? $row['Nama_Pengirim'] ?? '-',
                'error' => $e->getMessage(),
                'data' => [
                    'tanggal' => $row['tanggal'] ?? $row['Tanggal'] ?? '-',
                    'jenis' => $row['jenis'] ?? $row['Jenis'] ?? '-',
                    'marketplace' => $row['marketplace'] ?? $row['Marketplace'] ?? '-',
                    'no_hp' => $row['no_hp'] ?? $row['No HP'] ?? $row['No_HP'] ?? '-',
                ]
            ];
            return null;
        }
    }

    public function rules(): array
    {
        return [
            '*.tanggal' => ['required'],
            '*.jenis' => ['required'],
            '*.marketplace' => ['required'],
            '*.pembayaran' => ['required'],
            '*.nama_pengirim' => ['required'],
            '*.no_hp' => ['required'],
            '*.alamat' => ['required'],
        ];
    }

    public function customValidationMessages()
    {
        return [
            'tanggal.required' => 'Tanggal wajib diisi',
            'jenis.required' => 'Jenis wajib diisi',
            'marketplace.required' => 'Marketplace wajib diisi',
            'pembayaran.required' => 'Pembayaran wajib diisi',
            'nama_pengirim.required' => 'Nama pengirim wajib diisi',
            'no_hp.required' => 'No HP wajib diisi',
            'alamat.required' => 'Alamat wajib diisi',
        ];
    }

    private function parseDate($date, $rowNumber)
    {
        if (empty($date)) {
            throw new \Exception("Tanggal tidak boleh kosong");
        }

        try {
            if (is_numeric($date)) {
                if ($date < 60) {
                    $date += 1;
                }
                $unixTimestamp = ($date - 25569) * 86400;
                return Carbon::createFromTimestamp($unixTimestamp)->format('Y-m-d');
            }

            return Carbon::parse($date)->format('Y-m-d');
        } catch (\Exception $e) {
            throw new \Exception("Format tanggal tidak valid: '$date'");
        }
    }

    private function formatPhoneNumber($phone, $rowNumber)
    {
        if (empty($phone)) {
            throw new \Exception("No HP tidak boleh kosong");
        }

        $originalPhone = $phone;
        $phone = (string) $phone;

        if (strpos($phone, 'E+') !== false) {
            $phone = (string) floatval($phone);
            $phone = rtrim($phone, '.0');
        }

        $phone = preg_replace('/[^0-9]/', '', $phone);

        if (empty($phone)) {
            throw new \Exception("No HP harus mengandung angka");
        }

        if (strlen($phone) < 10) {
            throw new \Exception("No HP terlalu pendek: '$originalPhone'");
        }

        if (strlen($phone) > 15) {
            throw new \Exception("No HP terlalu panjang: '$originalPhone'");
        }

        if (strpos($phone, '0') === 0) {
            $phone = '62' . substr($phone, 1);
        }

        if (strpos($phone, '62') !== 0) {
            if (strlen($phone) >= 10 && strlen($phone) <= 12) {
                $phone = '62' . $phone;
            } else {
                throw new \Exception("Format no HP tidak dikenali: '$originalPhone'");
            }
        }

        return '+' . $phone;
    }

    private function normalizeEnum($value, $type, $rowNumber)
    {
        if (empty($value)) {
            throw new \Exception(ucfirst($type) . " tidak boleh kosong");
        }

        $value = trim((string) $value);

        $enums = [
            'jenis' => ['Pengembalian', 'Penukaran', 'Pengembalian Dana'],
            'marketplace' => ['Tiktok', 'Shopee', 'Reguler'],
            'pembayaran' => ['Sistem', 'Tunai', 'DFOD']
        ];

        foreach ($enums[$type] as $enum) {
            if (strtolower($value) === strtolower($enum)) {
                return $enum;
            }
        }

        $allowed = implode(', ', $enums[$type]);
        throw new \Exception(ucfirst($type) . " '$value' tidak valid. Harus salah satu dari: $allowed");
    }

    public function getRowCount(): int
    {
        return $this->rowCount;
    }

    public function getErrors(): array
    {
        return $this->errors;
    }
}
