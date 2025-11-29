<?php

namespace App\Http\Controllers;

use App\Models\Banding;
use Illuminate\Http\Request;
use App\Exports\BandingExport;
use App\Imports\BandingImport;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class BandingController extends Controller
{
    public function index()
    {
        $bandings = Banding::orderBy('tanggal', 'desc')->get();
        return view('bandings.index', compact('bandings'));
    }

    public function create()
    {
        $statusBandingOptions = Banding::getStatusBandingOptions();
        $ongkirOptions = Banding::getOngkirOptions();
        $alasanOptions = Banding::getAlasanOptions();
        $marketplaceOptions = Banding::getMarketplaceOptions();

        return view('bandings.create', compact(
            'statusBandingOptions',
            'ongkirOptions',
            'alasanOptions',
            'marketplaceOptions'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'tanggal' => 'required|date',
            'status_banding' => 'required|in:Berhasil,Ditinjau,Ditolak',
            'ongkir' => 'required|in:Dibebaskan,Ditanggung,-',
            'no_resi' => 'nullable|string|max:100',
            'no_pesanan' => 'required|string|max:100',
            'no_pengajuan' => 'nullable|string|max:100',
            'alasan' => 'required|in:Barang Palsu,Tidak Sesuai Ekspektasi Pembeli,Barang Belum Diterima,Cacat,Jumlah Barang Retur Kurang,Bukan Produk Asli Toko',
            'username' => 'required|string|max:100',
            'nama_pengirim' => 'required|string|max:100',
            'no_hp' => 'nullable|string|max:20',
            'alamat' => 'required|string',
            'marketplace' => 'required|in:Shopee,Tiktok'
        ]);

        try {
            Banding::create($request->all());

            return redirect()->route('bandings.index')
                ->with('success', 'Data banding berhasil ditambahkan!');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Gagal menambahkan data banding: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function show(Banding $banding)
    {
        return view('bandings.show', compact('banding'));
    }

    public function edit(Banding $banding)
    {
        $statusBandingOptions = Banding::getStatusBandingOptions();
        $ongkirOptions = Banding::getOngkirOptions();
        $alasanOptions = Banding::getAlasanOptions();
        $marketplaceOptions = Banding::getMarketplaceOptions();

        return view('bandings.edit', compact(
            'banding',
            'statusBandingOptions',
            'ongkirOptions',
            'alasanOptions',
            'marketplaceOptions'
        ));
    }

    public function update(Request $request, Banding $banding)
    {
        $request->validate([
            'tanggal' => 'required|date',
            'status_banding' => 'required|in:Berhasil,Ditinjau,Ditolak',
            'ongkir' => 'required|in:Dibebaskan,Ditanggung,-',
            'no_resi' => 'nullable|string|max:100',
            'no_pesanan' => 'required|string|max:100',
            'no_pengajuan' => 'nullable|string|max:100',
            'alasan' => 'required|in:Barang Palsu,Tidak Sesuai Ekspektasi Pembeli,Barang Belum Diterima,Cacat,Jumlah Barang Retur Kurang,Bukan Produk Asli Toko',
            'username' => 'required|string|max:100',
            'nama_pengirim' => 'required|string|max:100',
            'no_hp' => 'nullable|string|max:20',
            'alamat' => 'required|string',
            'marketplace' => 'required|in:Shopee,Tiktok'
        ]);

        try {
            $banding->update($request->all());

            return redirect()->route('bandings.index')
                ->with('success', 'Data banding berhasil diperbarui!');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Gagal memperbarui data banding: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function destroy(Banding $banding)
    {
        try {
            $banding->delete();

            return redirect()->route('bandings.index')
                ->with('success', 'Data banding berhasil dihapus!');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Gagal menghapus data banding: ' . $e->getMessage());
        }
    }

    public function deleteAll()
    {
        try {
            $bandingCount = Banding::count();

            if ($bandingCount === 0) {
                return redirect()->route('bandings.index')
                    ->with('warning', 'Tidak ada data banding untuk dihapus.');
            }

            // Hapus transaction() karena truncate() sudah atomic
            Banding::truncate();

            return redirect()->route('bandings.index')
                ->with('success', "Semua data banding ($bandingCount data) berhasil dihapus!");

        } catch (\Exception $e) {
            \Log::error('Delete All Bandings Error: ' . $e->getMessage());

            return redirect()->route('bandings.index')
                ->with('error', 'Gagal menghapus semua data banding: ' . $e->getMessage());
        }
    }

    public function export(Request $request)
    {
        $filename = 'data_banding_' . date('Y-m-d_H-i-s') . '.xlsx';

        return Excel::download(new BandingExport(), $filename);
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls,csv|max:10240' // 10MB max
        ]);

        try {
            $import = new BandingImport();
            Excel::import($import, $request->file('file'));

            $successCount = $import->getSuccessCount();
            $failedImports = $import->getFailedImports();

            $message = "Import selesai. Berhasil: {$successCount} data";

            if (!empty($failedImports)) {
                $failedCount = count($failedImports);
                $message .= ", Gagal: {$failedCount} data";

                // Simpan detail error ke session untuk ditampilkan
                session()->flash('import_errors', $failedImports);
            }

            return redirect()->route('bandings.index')
                ->with('success', $message);

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Gagal mengimport data: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function downloadTemplate()
    {
        $filename = 'template_import_banding.xlsx';

        // Create sample data for template
        $sampleData = [
            [
                '01/01/2024 10:00',
                'Ditinjau',
                'Dibebaskan',
                'RESI123456789',
                'PESANAN001',
                'PENGAJUAN001',
                'Barang Belum Diterima',
                'customer123',
                'John Doe',
                '081234567890',
                'Jl. Contoh Alamat No. 123, Jakarta',
                'Shopee'
            ]
        ];

        $export = new BandingExport(collect($sampleData)->map(function($item) {
            return (object) [
                'tanggal' => $item[0],
                'status_banding' => $item[1],
                'ongkir' => $item[2],
                'no_resi' => $item[3],
                'no_pesanan' => $item[4],
                'no_pengajuan' => $item[5],
                'alasan' => $item[6],
                'username' => $item[7],
                'nama_pengirim' => $item[8],
                'no_hp' => $item[9],
                'alamat' => $item[10],
                'marketplace' => $item[11]
            ];
        }));

        return Excel::download($export, $filename);
    }
}
