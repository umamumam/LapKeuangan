<?php

namespace App\Http\Controllers;

use App\Models\PengirimanSampel;
use App\Models\Sampel;
use App\Models\Toko; // Tambahkan ini
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Exports\PengirimanSampelExport;
use App\Imports\PengirimanSampelImport;
use Maatwebsite\Excel\Facades\Excel;

class PengirimanSampelController extends Controller
{
    public function index(Request $request)
    {
        // Tambahkan filter toko
        $tokoId = $request->input('toko_id');
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        // Set tanggal default jika kosong (tanggal 1 dan akhir bulan ini)
        if (!$startDate && !$endDate) {
            $startDate = now()->startOfMonth()->format('Y-m-d');
            $endDate = now()->endOfMonth()->format('Y-m-d');
        }

        $query = PengirimanSampel::with(['sampel1', 'sampel2', 'sampel3', 'sampel4', 'sampel5', 'toko']);

        // Filter toko
        if ($tokoId && $tokoId !== 'all') {
            $query->where('toko_id', $tokoId);
        }

        // Filter tanggal
        if ($startDate) {
            $query->whereDate('tanggal', '>=', $startDate);
        }
        if ($endDate) {
            $query->whereDate('tanggal', '<=', $endDate);
        }

        $pengirimanSampels = $query->orderBy('tanggal', 'desc')->get();
        $tokoOptions = Toko::pluck('nama', 'id'); // Ambil data toko untuk dropdown
        session(['last_sampel_index_url' => $request->fullUrl()]);

        return view('pengiriman-sampels.index', compact('pengirimanSampels', 'tokoOptions', 'tokoId', 'startDate', 'endDate'));
    }

    public function create()
    {
        $sampels = Sampel::all();
        $tokoOptions = Toko::pluck('nama', 'id'); // Tambahkan toko options

        return view('pengiriman-sampels.create', compact('sampels', 'tokoOptions'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'tanggal' => 'required|date',
            'username' => 'required|string|max:255',
            'no_resi' => 'required|string|max:255',
            'ongkir' => 'required|integer|min:0',
            'penerima' => 'required|string|max:255',
            'contact' => 'required|string|max:255',
            'alamat' => 'required|string',
            'toko_id' => 'required|exists:tokos,id', // Tambahkan validasi toko

            // Validasi untuk sampel 1-5
            'sampel1_id' => 'nullable|exists:sampels,id',
            'jumlah1' => 'nullable|integer|min:0',
            'sampel2_id' => 'nullable|exists:sampels,id',
            'jumlah2' => 'nullable|integer|min:0',
            'sampel3_id' => 'nullable|exists:sampels,id',
            'jumlah3' => 'nullable|integer|min:0',
            'sampel4_id' => 'nullable|exists:sampels,id',
            'jumlah4' => 'nullable|integer|min:0',
            'sampel5_id' => 'nullable|exists:sampels,id',
            'jumlah5' => 'nullable|integer|min:0',
        ]);

        try {
            DB::beginTransaction();

            // Hitung totalhpp dari semua sampel
            $totalhpp = 0;
            for ($i = 1; $i <= 5; $i++) {
                $sampelId = $validated["sampel{$i}_id"] ?? null;
                $jumlah = $validated["jumlah{$i}"] ?? 0;

                if ($sampelId && $jumlah > 0) {
                    $sampel = Sampel::find($sampelId);
                    if ($sampel) {
                        $totalhpp += $sampel->harga * $jumlah;
                    }
                }
            }

            $total_biaya = $totalhpp + $validated['ongkir'];

            $pengirimanSampel = PengirimanSampel::create([
                'tanggal' => $validated['tanggal'],
                'username' => $validated['username'],
                'no_resi' => $validated['no_resi'],
                'ongkir' => $validated['ongkir'],
                'toko_id' => $validated['toko_id'], // Tambahkan toko_id

                // Data sampel 1-5
                'sampel1_id' => $validated['sampel1_id'] ?? null,
                'jumlah1' => $validated['jumlah1'] ?? 0,
                'sampel2_id' => $validated['sampel2_id'] ?? null,
                'jumlah2' => $validated['jumlah2'] ?? 0,
                'sampel3_id' => $validated['sampel3_id'] ?? null,
                'jumlah3' => $validated['jumlah3'] ?? 0,
                'sampel4_id' => $validated['sampel4_id'] ?? null,
                'jumlah4' => $validated['jumlah4'] ?? 0,
                'sampel5_id' => $validated['sampel5_id'] ?? null,
                'jumlah5' => $validated['jumlah5'] ?? 0,

                'totalhpp' => $totalhpp,
                'total_biaya' => $total_biaya,
                'penerima' => $validated['penerima'],
                'contact' => $validated['contact'],
                'alamat' => $validated['alamat']
            ]);

            DB::commit();

            return redirect()->route('pengiriman-sampels.index')
                ->with('success', 'Data pengiriman sampel berhasil ditambahkan.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function show(PengirimanSampel $pengirimanSampel)
    {
        $pengirimanSampel->load(['sampel1', 'sampel2', 'sampel3', 'sampel4', 'sampel5', 'toko']);
        $sampelDetails = $pengirimanSampel->getSampelDetails();

        return view('pengiriman-sampels.show', compact('pengirimanSampel', 'sampelDetails'));
    }

    public function edit(PengirimanSampel $pengirimanSampel)
    {
        $sampels = Sampel::all();
        $tokoOptions = Toko::pluck('nama', 'id'); // Tambahkan toko options
        $pengirimanSampel->load(['sampel1', 'sampel2', 'sampel3', 'sampel4', 'sampel5', 'toko']);

        return view('pengiriman-sampels.edit', compact('pengirimanSampel', 'sampels', 'tokoOptions'));
    }

    public function update(Request $request, PengirimanSampel $pengirimanSampel)
    {
        $validated = $request->validate([
            'tanggal' => 'required|date',
            'username' => 'required|string|max:255',
            'no_resi' => 'required|string|max:255',
            'ongkir' => 'required|integer|min:0',
            'penerima' => 'required|string|max:255',
            'contact' => 'required|string|max:255',
            'alamat' => 'required|string',
            'toko_id' => 'required|exists:tokos,id', // Tambahkan validasi toko

            // Validasi untuk sampel 1-5
            'sampel1_id' => 'nullable|exists:sampels,id',
            'jumlah1' => 'nullable|integer|min:0',
            'sampel2_id' => 'nullable|exists:sampels,id',
            'jumlah2' => 'nullable|integer|min:0',
            'sampel3_id' => 'nullable|exists:sampels,id',
            'jumlah3' => 'nullable|integer|min:0',
            'sampel4_id' => 'nullable|exists:sampels,id',
            'jumlah4' => 'nullable|integer|min:0',
            'sampel5_id' => 'nullable|exists:sampels,id',
            'jumlah5' => 'nullable|integer|min:0',
        ]);

        try {
            DB::beginTransaction();

            // Hitung totalhpp dari semua sampel
            $totalhpp = 0;
            for ($i = 1; $i <= 5; $i++) {
                $sampelId = $validated["sampel{$i}_id"] ?? null;
                $jumlah = $validated["jumlah{$i}"] ?? 0;

                if ($sampelId && $jumlah > 0) {
                    $sampel = Sampel::find($sampelId);
                    if ($sampel) {
                        $totalhpp += $sampel->harga * $jumlah;
                    }
                }
            }

            $total_biaya = $totalhpp + $validated['ongkir'];

            $pengirimanSampel->update([
                'tanggal' => $validated['tanggal'],
                'username' => $validated['username'],
                'no_resi' => $validated['no_resi'],
                'ongkir' => $validated['ongkir'],
                'toko_id' => $validated['toko_id'], // Tambahkan toko_id

                // Data sampel 1-5
                'sampel1_id' => $validated['sampel1_id'] ?? null,
                'jumlah1' => $validated['jumlah1'] ?? 0,
                'sampel2_id' => $validated['sampel2_id'] ?? null,
                'jumlah2' => $validated['jumlah2'] ?? 0,
                'sampel3_id' => $validated['sampel3_id'] ?? null,
                'jumlah3' => $validated['jumlah3'] ?? 0,
                'sampel4_id' => $validated['sampel4_id'] ?? null,
                'jumlah4' => $validated['jumlah4'] ?? 0,
                'sampel5_id' => $validated['sampel5_id'] ?? null,
                'jumlah5' => $validated['jumlah5'] ?? 0,

                'totalhpp' => $totalhpp,
                'total_biaya' => $total_biaya,
                'penerima' => $validated['penerima'],
                'contact' => $validated['contact'],
                'alamat' => $validated['alamat']
            ]);

            DB::commit();

            $url = session('last_sampel_index_url', route('pengiriman-sampels.index'));
            return redirect()->to($url)
                ->with('success', 'Data pengiriman sampel berhasil diperbarui.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function destroy(PengirimanSampel $pengirimanSampel)
    {
        try {
            $pengirimanSampel->delete();
            return redirect()->route('pengiriman-sampels.index')
                ->with('success', 'Data pengiriman sampel berhasil dihapus.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function deleteAll()
    {
        try {
            $count = PengirimanSampel::count();
            PengirimanSampel::truncate();

            return redirect()->route('pengiriman-sampels.index')
                ->with('success', 'Semua data pengiriman sampel (' . $count . ' data) berhasil dihapus.');
        } catch (\Exception $e) {
            return redirect()->route('pengiriman-sampels.index')
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function export()
    {
        return Excel::download(new PengirimanSampelExport, 'pengiriman-sampel-' . date('Y-m-d') . '.xlsx');
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv|max:10240',
            'toko_id' => 'required|exists:tokos,id' // Tambahkan validasi toko untuk import
        ]);

        try {
            $tokoId = $request->toko_id; // Ambil toko_id dari form
            $toko = Toko::find($tokoId);

            $import = new PengirimanSampelImport($tokoId); // Kirim toko_id ke import class
            Excel::import($import, $request->file('file'));

            $successMessage = 'Data pengiriman sampel berhasil diimport ke toko "' . ($toko->nama ?? '') . '".';

            if (session('import_errors')) {
                return redirect()->route('pengiriman-sampels.index')
                    ->with('warning', $successMessage . ' Beberapa data memiliki masalah.')
                    ->with('import_errors', session('import_errors'));
            }

            return redirect()->route('pengiriman-sampels.index')
                ->with('success', $successMessage);
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan saat import: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function getTotalHpp(Request $request)
    {
        $totalhpp = 0;

        // Hitung total dari semua sampel yang dikirim
        for ($i = 1; $i <= 5; $i++) {
            $sampelId = $request->input("sampel{$i}_id");
            $jumlah = $request->input("jumlah{$i}", 0);

            if ($sampelId && $jumlah > 0) {
                $sampel = Sampel::find($sampelId);
                if ($sampel) {
                    $totalhpp += $sampel->harga * $jumlah;
                }
            }
        }

        return response()->json(['totalhpp' => $totalhpp]);
    }

    public function getTotalBiaya(Request $request)
    {
        $totalhpp = $request->totalhpp;
        $ongkir = $request->ongkir;

        $total_biaya = $totalhpp + $ongkir;

        return response()->json(['total_biaya' => $total_biaya]);
    }

    public function getSampelHarga(Request $request)
    {
        // Method untuk mendapatkan harga sampel berdasarkan ID (digunakan di form)
        $sampelId = $request->sampel_id;

        if ($sampelId) {
            $sampel = Sampel::find($sampelId);
            if ($sampel) {
                return response()->json([
                    'harga' => $sampel->harga,
                    'harga_formatted' => 'Rp ' . number_format($sampel->harga, 0, ',', '.')
                ]);
            }
        }

        return response()->json(['harga' => 0, 'harga_formatted' => 'Rp 0']);
    }

    public function rekap(Request $request)
    {
        $bulan = $request->get('bulan', date('Y-m'));
        $tokoId = $request->get('toko_id');

        // Query data rekap
        $query = PengirimanSampel::with(['sampel1', 'sampel2', 'sampel3', 'sampel4', 'sampel5', 'toko'])
            ->whereYear('tanggal', date('Y', strtotime($bulan)))
            ->whereMonth('tanggal', date('m', strtotime($bulan)));

        // Filter toko
        if ($tokoId && $tokoId !== 'all') {
            $query->where('toko_id', $tokoId);
        }

        $rekapData = $query->orderBy('tanggal', 'desc')->get();

        // Hitung total-total
        $totalPengiriman = $rekapData->count();
        $totalHpp = $rekapData->sum('totalhpp');
        $totalOngkir = $rekapData->sum('ongkir');
        $totalBiaya = $rekapData->sum('total_biaya');

        // Hitung total jumlah per sampel
        $totalJumlahSampel = 0;
        foreach ($rekapData as $pengiriman) {
            for ($i = 1; $i <= 5; $i++) {
                $totalJumlahSampel += $pengiriman->{"jumlah{$i}"} ?? 0;
            }
        }

        // Rekap per sampel
        $rekapPerSampel = [];
        $sampelTotals = [];

        foreach ($rekapData as $pengiriman) {
            for ($i = 1; $i <= 5; $i++) {
                $sampel = $pengiriman->{"sampel{$i}"};
                $jumlah = $pengiriman->{"jumlah{$i}"} ?? 0;

                if ($sampel && $jumlah > 0) {
                    $sampelId = $sampel->id;

                    if (!isset($sampelTotals[$sampelId])) {
                        $sampelTotals[$sampelId] = [
                            'nama_sampel' => $sampel->nama,
                            'ukuran' => $sampel->ukuran,
                            'harga' => $sampel->harga,
                            'total_jumlah' => 0,
                            'total_hpp' => 0,
                            'jumlah_pengiriman' => 0
                        ];
                    }

                    $sampelTotals[$sampelId]['total_jumlah'] += $jumlah;
                    $sampelTotals[$sampelId]['total_hpp'] += $sampel->harga * $jumlah;
                    $sampelTotals[$sampelId]['jumlah_pengiriman']++;
                }
            }
        }

        $rekapPerSampel = array_values($sampelTotals);

        // Rekap per username
        $rekapPerUser = $rekapData->groupBy('username')->map(function ($items, $username) {
            return [
                'username' => $username,
                'total_hpp' => $items->sum('totalhpp'),
                'total_ongkir' => $items->sum('ongkir'),
                'total_biaya' => $items->sum('total_biaya'),
                'jumlah_pengiriman' => $items->count()
            ];
        })->values();

        // Rekap per toko
        $rekapPerToko = $rekapData->groupBy('toko_id')->map(function ($items, $tokoId) {
            $toko = $items->first()->toko ?? null;
            return [
                'toko_id' => $tokoId,
                'nama_toko' => $toko ? $toko->nama : 'Tidak diketahui',
                'total_hpp' => $items->sum('totalhpp'),
                'total_ongkir' => $items->sum('ongkir'),
                'total_biaya' => $items->sum('total_biaya'),
                'jumlah_pengiriman' => $items->count()
            ];
        })->values();

        $tokoOptions = Toko::pluck('nama', 'id');

        return view('pengiriman-sampels.rekap', compact(
            'rekapData',
            'rekapPerSampel',
            'rekapPerUser',
            'rekapPerToko',
            'totalPengiriman',
            'totalJumlahSampel',
            'totalHpp',
            'totalOngkir',
            'totalBiaya',
            'bulan',
            'tokoId',
            'tokoOptions'
        ));
    }
}
