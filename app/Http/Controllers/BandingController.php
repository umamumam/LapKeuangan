<?php

namespace App\Http\Controllers;

use App\Models\Toko;
use App\Models\Banding;
use Illuminate\Http\Request;
use App\Exports\BandingExport;
use App\Imports\BandingImport;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class BandingController extends Controller
{
    public function index(Request $request)
    {
        $marketplace = $request->input('marketplace');
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $tokoId = $request->input('toko_id');
        $statusBanding = $request->input('status_banding');

        if (!$startDate && !$endDate) {
            $startDate = now()->startOfMonth()->format('Y-m-d');
            $endDate = now()->endOfMonth()->format('Y-m-d');
        }

        $query = Banding::query()->with('toko');

        if ($marketplace && $marketplace !== 'all') {
            $query->where('marketplace', $marketplace);
        }
        if ($startDate) {
            $query->whereDate('tanggal', '>=', $startDate);
        }
        if ($endDate) {
            $query->whereDate('tanggal', '<=', $endDate);
        }
        if ($tokoId && $tokoId !== 'all') {
            $query->where('toko_id', $tokoId);
        }
        if ($statusBanding && $statusBanding !== 'all') {
            $query->where('status_banding', $statusBanding);
        }

        $bandings = $query->orderBy('tanggal', 'desc')->get();
        $marketplaceOptions = Banding::getMarketplaceOptions();
        $tokoOptions = Toko::pluck('nama', 'id');
        $statusBandingOptions = Banding::getStatusBandingOptions();

        return view('bandings.index', compact(
            'bandings',
            'marketplaceOptions',
            'tokoOptions',
            'statusBandingOptions',
            'marketplace',
            'startDate',
            'endDate',
            'tokoId',
            'statusBanding'
        ));
    }

    public function create()
    {
        $statusBandingOptions = Banding::getStatusBandingOptions();
        $ongkirOptions = Banding::getOngkirOptions();
        $alasanOptions = Banding::getAlasanOptions();
        $marketplaceOptions = Banding::getMarketplaceOptions();
        $statusPenerimaanOptions = Banding::getStatusPenerimaanOptions();
        $statusditerimaOptions = ['OK' => 'OK', 'Belum' => 'Belum']; //
        $tokoOptions = Toko::pluck('nama', 'id');

        return view('bandings.create', compact(
            'statusBandingOptions',
            'ongkirOptions',
            'alasanOptions',
            'marketplaceOptions',
            'statusPenerimaanOptions',
            'statusditerimaOptions',
            'tokoOptions'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'tanggal' => 'required|date',
            'status_banding' => 'nullable|in:Berhasil,Ditinjau,Ditolak',
            'ongkir' => 'required|in:Dibebaskan,Ditanggung,-',
            'no_resi' => 'nullable|string|max:100',
            'no_pesanan' => 'nullable|string|max:100',
            'no_pengajuan' => 'nullable|string|max:100',
            'alasan' => 'nullable|in:Barang Palsu,Tidak Sesuai Ekspektasi Pembeli,Barang Belum Diterima,Cacat,Jumlah Barang Retur Kurang,Bukan Produk Asli Toko',
            'status_penerimaan' => 'required|in:Diterima dengan baik,Cacat,-',
            'username' => 'nullable|string|max:100',
            'nama_pengirim' => 'nullable|string|max:100',
            'no_hp' => 'nullable|string|max:20',
            'alamat' => 'required|string',
            'marketplace' => 'required|in:Shopee,Tiktok',
            'toko_id' => 'required|exists:tokos,id',
            'statusditerima' => 'nullable|in:OK,Belum',
        ]);

        try {
            Banding::create($request->all());

            // Return JSON untuk request dari create-with-resi
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Data banding berhasil ditambahkan!'
                ]);
            }

            return redirect()->route('bandings.index')
                ->with('success', 'Data banding berhasil ditambahkan!');
        } catch (\Exception $e) {
            // Return JSON untuk request dari create-with-resi
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal menambahkan data banding: ' . $e->getMessage()
                ], 500);
            }

            return redirect()->back()
                ->with('error', 'Gagal menambahkan data banding: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function show(Banding $banding)
    {
        $banding->load('toko');
        return view('bandings.show', compact('banding'));
    }

    public function edit(Banding $banding)
    {
        $statusBandingOptions = Banding::getStatusBandingOptions();
        $ongkirOptions = Banding::getOngkirOptions();
        $alasanOptions = Banding::getAlasanOptions();
        $marketplaceOptions = Banding::getMarketplaceOptions();
        $statusPenerimaanOptions = Banding::getStatusPenerimaanOptions();
        $statusditerimaOptions = ['OK' => 'OK', 'Belum' => 'Belum']; //
        $tokoOptions = Toko::pluck('nama', 'id');

        return view('bandings.edit', compact(
            'banding',
            'statusBandingOptions',
            'ongkirOptions',
            'alasanOptions',
            'marketplaceOptions',
            'statusPenerimaanOptions',
            'statusditerimaOptions',
            'tokoOptions'
        ));
    }

    public function update(Request $request, Banding $banding)
    {
        $request->validate([
            'tanggal' => 'required|date',
            'status_banding' => 'nullable|in:Berhasil,Ditinjau,Ditolak',
            'ongkir' => 'required|in:Dibebaskan,Ditanggung,-',
            'no_resi' => 'nullable|string|max:100',
            'no_pesanan' => 'nullable|string|max:100',
            'no_pengajuan' => 'nullable|string|max:100',
            'alasan' => 'nullable|in:Barang Palsu,Tidak Sesuai Ekspektasi Pembeli,Barang Belum Diterima,Cacat,Jumlah Barang Retur Kurang,Bukan Produk Asli Toko',
            'status_penerimaan' => 'required|in:Diterima dengan baik,Cacat,-',
            'username' => 'nullable|string|max:100',
            'nama_pengirim' => 'nullable|string|max:100',
            'no_hp' => 'nullable|string|max:20',
            'alamat' => 'required|string',
            'marketplace' => 'required|in:Shopee,Tiktok',
            'toko_id' => 'required|exists:tokos,id',
            'statusditerima' => 'nullable|in:OK,Belum',
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
            'file' => 'required|file|mimes:xlsx,xls,csv|max:10240',
            'toko_id' => 'required|exists:tokos,id' // Validasi toko_id
        ]);

        try {
            $tokoId = $request->toko_id; // Ambil toko_id dari form
            $import = new BandingImport($tokoId); // Kirim toko_id ke import class
            Excel::import($import, $request->file('file'));

            $successCount = $import->getSuccessCount();
            $failedImports = $import->getFailedImports();

            // Dapatkan nama toko untuk ditampilkan di pesan
            $toko = \App\Models\Toko::find($tokoId);
            $tokoNama = $toko ? $toko->nama : 'Toko';

            $message = "Import selesai. Berhasil: {$successCount} data diimport ke toko '{$tokoNama}'";

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
                'Diterima dengan baik',
                'customer123',
                'John Doe',
                '081234567890',
                'Jl. Contoh Alamat No. 123, Jakarta',
                'Shopee',
                '1',
                'Belum',
            ]
        ];

        $export = new BandingExport(collect($sampleData)->map(function ($item) {
            return (object) [
                'tanggal' => $item[0],
                'status_banding' => $item[1],
                'ongkir' => $item[2],
                'no_resi' => $item[3],
                'no_pesanan' => $item[4],
                'no_pengajuan' => $item[5],
                'alasan' => $item[6],
                'status_penerimaan' => $item[7], // TAMBAH INI
                'username' => $item[8],
                'nama_pengirim' => $item[9],
                'no_hp' => $item[10],
                'alamat' => $item[11],
                'marketplace' => $item[12],
                'toko_id' => $item[13],
                'statusditerima' => $item[14],
            ];
        }));

        return Excel::download($export, $filename);
    }

    public function search()
    {
        $tokoOptions = Toko::pluck('nama', 'id');
        return view('bandings.search');
    }

    public function searchResult(Request $request)
    {
        $request->validate([
            'no_resi' => 'required|string|max:100'
        ]);

        try {
            $banding = Banding::with('toko')->where('no_resi', $request->no_resi)->first();

            if (!$banding) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data tidak ditemukan untuk nomor resi: ' . $request->no_resi
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => $banding
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    public function createWithResi($noResi)
    {
        $statusBandingOptions = Banding::getStatusBandingOptions();
        $ongkirOptions = Banding::getOngkirOptions();
        $alasanOptions = Banding::getAlasanOptions();
        $marketplaceOptions = Banding::getMarketplaceOptions();
        $statusPenerimaanOptions = Banding::getStatusPenerimaanOptions();
        $statusditerimaOptions = ['OK' => 'OK', 'Belum' => 'Belum']; //
        $tokoOptions = Toko::pluck('nama', 'id');

        return view('bandings.create-with-resi', compact(
            'statusBandingOptions',
            'ongkirOptions',
            'alasanOptions',
            'marketplaceOptions',
            'statusPenerimaanOptions',
            'statusditerimaOptions',
            'tokoOptions',
            'noResi'
        ));
    }

    public function updateStatus(Request $request, Banding $banding)
    {
        $request->validate([
            'status_banding' => 'required|in:Berhasil,Ditinjau,Ditolak',
            'status_penerimaan' => 'required|in:Diterima dengan baik,Cacat,-'
        ]);

        try {
            $banding->update([
                'status_banding' => $request->status_banding,
                'status_penerimaan' => $request->status_penerimaan
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Status berhasil diperbarui!',
                'data' => $banding
            ]);
        } catch (\Exception $e) {
            \Log::error('Update status error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui status: ' . $e->getMessage()
            ], 500);
        }
    }

    public function StatusOk(Request $request)
    {
        $marketplace = $request->input('marketplace');
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $tokoId = $request->input('toko_id');
        $statusDiterima = $request->input('status_diterima');

        if (!$startDate && !$endDate) {
            $startDate = now()->startOfMonth()->format('Y-m-d');
            $endDate = now()->endOfMonth()->format('Y-m-d');
        }

        $query = Banding::query()->with('toko');

        // TAMBAH INI: Hanya yang statusditerima = 'OK'
        $query->where('statusditerima', 'OK');

        if ($marketplace && $marketplace !== 'all') {
            $query->where('marketplace', $marketplace);
        }
        if ($startDate) {
            $query->whereDate('tanggal', '>=', $startDate);
        }
        if ($endDate) {
            $query->whereDate('tanggal', '<=', $endDate);
        }
        if ($tokoId && $tokoId !== 'all') {
            $query->where('toko_id', $tokoId);
        }
        if ($statusDiterima && $statusDiterima !== 'all') {
            $query->where('statusditerima', $statusDiterima);
        }

        $bandings = $query->orderBy('tanggal', 'desc')->get();
        $marketplaceOptions = Banding::getMarketplaceOptions();
        $tokoOptions = Toko::pluck('nama', 'id');
        $statusDiterimaOptions = Banding::getStatusDiterimaOptions();

        return view('bandings.ok', compact(
            'bandings',
            'marketplaceOptions',
            'tokoOptions',
            'statusDiterimaOptions',
            'marketplace',
            'startDate',
            'endDate',
            'tokoId',
            'statusDiterima'
        ));
    }

    public function searchOK()
    {
        $tokoOptions = Toko::pluck('nama', 'id');
        return view('bandings.searchok');
    }

    public function searchResultOK(Request $request)
    {
        $request->validate([
            'no_resi' => 'required|string|max:100'
        ]);

        try {
            $banding = Banding::with('toko')->where('no_resi', $request->no_resi)->first();

            if (!$banding) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data tidak ditemukan untuk nomor resi: ' . $request->no_resi
                ], 404);
            }

            // OTOMATIS UPDATE statusditerima ke 'OK'
            $banding->update([
                'statusditerima' => 'OK'
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Data ditemukan dan status diterima diubah menjadi OK!',
                'data' => $banding->fresh() // Reload data setelah update
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }
}
