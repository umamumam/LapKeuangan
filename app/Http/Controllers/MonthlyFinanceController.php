<?php

namespace App\Http\Controllers;

use App\Models\MonthlyFinance;
use App\Models\MonthlySummary;
use Illuminate\Http\Request;
use App\Exports\MonthlyFinanceExport;
use Maatwebsite\Excel\Facades\Excel;

class MonthlyFinanceController extends Controller
{
    public function index()
    {
        // Gunakan eager loading untuk summary
        $finances = MonthlyFinance::withSummary()
            ->orderBy('periode_awal', 'desc')
            ->get();

        return view('monthly-finances.index', compact('finances'));
    }

    public function create()
    {
        // Default: awal bulan ini sampai akhir bulan
        $defaultAwal = now()->startOfMonth()->format('Y-m-d');
        $defaultAkhir = now()->endOfMonth()->format('Y-m-d');
        $defaultNama = MonthlyFinance::generateNamaPeriode($defaultAwal);

        return view('monthly-finances.create', compact('defaultAwal', 'defaultAkhir', 'defaultNama'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'periode_awal' => 'required|date',
            'periode_akhir' => 'required|date|after_or_equal:periode_awal',
            // Pendapatan per marketplace
            'total_pendapatan_shopee' => 'required|integer|min:0',
            'total_pendapatan_tiktok' => 'required|integer|min:0',
            // Operasional
            'operasional' => 'required|integer|min:0',
            // Iklan per marketplace
            'iklan_shopee' => 'required|integer|min:0',
            'iklan_tiktok' => 'required|integer|min:0',
            // Rasio admin layanan per marketplace
            'rasio_admin_layanan_shopee' => 'required|numeric|min:0|max:100',
            'rasio_admin_layanan_tiktok' => 'required|numeric|min:0|max:100',
            'keterangan' => 'nullable|string|max:500',
        ]);

        try {
            // Generate nama periode otomatis
            $namaPeriode = MonthlyFinance::generateNamaPeriode($request->periode_awal);

            // Cek duplikasi periode
            $existing = MonthlyFinance::where('nama_periode', $namaPeriode)->first();
            if ($existing) {
                return redirect()->back()
                    ->with('error', "Data untuk periode {$namaPeriode} sudah ada!")
                    ->withInput();
            }

            MonthlyFinance::create(array_merge($request->all(), [
                'nama_periode' => $namaPeriode
            ]));

            return redirect()->route('monthly-finances.index')
                ->with('success', 'Data keuangan periode berhasil ditambahkan!');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Gagal menambahkan data: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function show(MonthlyFinance $monthlyFinance)
    {
        // Load summary relationship
        $monthlyFinance->load('summary');

        // Load calculated attributes dari accessor
        $calculatedData = [
            // Totals
            'total_pendapatan' => $monthlyFinance->total_pendapatan,
            'total_iklan' => $monthlyFinance->total_iklan,
            'rasio_admin_layanan' => $monthlyFinance->rasio_admin_layanan,

            // Data dari summary
            'total_penghasilan' => $monthlyFinance->total_penghasilan,
            'total_penghasilan_shopee' => $monthlyFinance->total_penghasilan_shopee,
            'total_penghasilan_tiktok' => $monthlyFinance->total_penghasilan_tiktok,
            'hpp' => $monthlyFinance->hpp,
            'hpp_shopee' => $monthlyFinance->hpp_shopee,
            'hpp_tiktok' => $monthlyFinance->hpp_tiktok,

            // Laba/Rugi
            'laba_rugi_kotor' => $monthlyFinance->laba_rugi_kotor,
            'laba_rugi_shopee' => $monthlyFinance->laba_rugi_shopee,
            'laba_rugi_tiktok' => $monthlyFinance->laba_rugi_tiktok,
            'laba_rugi_net_shopee' => $monthlyFinance->laba_rugi_net_shopee,
            'laba_rugi_net_tiktok' => $monthlyFinance->laba_rugi_net_tiktok,
            'laba_rugi' => $monthlyFinance->laba_rugi,

            // Rasio
            'rasio_operasional' => $monthlyFinance->rasio_operasional,
            'rasio_margin_kotor' => $monthlyFinance->rasio_margin_kotor,
            'rasio_margin_shopee' => $monthlyFinance->rasio_margin_shopee,
            'rasio_margin_tiktok' => $monthlyFinance->rasio_margin_tiktok,
            'rasio_laba' => $monthlyFinance->rasio_laba,
            'rasio_laba_net_shopee' => $monthlyFinance->rasio_laba_net_shopee,
            'rasio_laba_net_tiktok' => $monthlyFinance->rasio_laba_net_tiktok,

            // Metrik performance
            'aov_aktual' => $monthlyFinance->aov_aktual,
            'aov_shopee' => $monthlyFinance->aov_shopee,
            'aov_tiktok' => $monthlyFinance->aov_tiktok,
            'basket_size_aktual' => $monthlyFinance->basket_size_aktual,
            'roas_shopee' => $monthlyFinance->roas_shopee,
            'roas_tiktok' => $monthlyFinance->roas_tiktok,
            'roas_aktual' => $monthlyFinance->roas_aktual,
            'acos_shopee' => $monthlyFinance->acos_shopee,
            'acos_tiktok' => $monthlyFinance->acos_tiktok,
            'acos_aktual' => $monthlyFinance->acos_aktual,

            // Quantity
            'total_order_qty' => $monthlyFinance->total_order_qty,
            'total_order_shopee' => $monthlyFinance->total_order_shopee,
            'total_order_tiktok' => $monthlyFinance->total_order_tiktok,
            'net_quantity' => $monthlyFinance->net_quantity,
        ];

        return view('monthly-finances.show', compact('monthlyFinance', 'calculatedData'));
    }

    public function edit(MonthlyFinance $monthlyFinance)
    {
        return view('monthly-finances.edit', compact('monthlyFinance'));
    }

    public function update(Request $request, MonthlyFinance $monthlyFinance)
    {
        $request->validate([
            'periode_awal' => 'required|date',
            'periode_akhir' => 'required|date|after_or_equal:periode_awal',
            // Pendapatan per marketplace
            'total_pendapatan_shopee' => 'required|integer|min:0',
            'total_pendapatan_tiktok' => 'required|integer|min:0',
            // Operasional
            'operasional' => 'required|integer|min:0',
            // Iklan per marketplace
            'iklan_shopee' => 'required|integer|min:0',
            'iklan_tiktok' => 'required|integer|min:0',
            // Rasio admin layanan per marketplace
            'rasio_admin_layanan_shopee' => 'required|numeric|min:0|max:100',
            'rasio_admin_layanan_tiktok' => 'required|numeric|min:0|max:100',
            'keterangan' => 'nullable|string|max:500',
        ]);

        try {
            // Generate nama periode baru jika periode_awal berubah
            $namaPeriodeBaru = MonthlyFinance::generateNamaPeriode($request->periode_awal);

            // Cek duplikasi jika nama periode berubah
            if ($namaPeriodeBaru != $monthlyFinance->nama_periode) {
                $existing = MonthlyFinance::where('nama_periode', $namaPeriodeBaru)->first();
                if ($existing) {
                    return redirect()->back()
                        ->with('error', "Data untuk periode {$namaPeriodeBaru} sudah ada!")
                        ->withInput();
                }
            }

            $monthlyFinance->update(array_merge($request->all(), [
                'nama_periode' => $namaPeriodeBaru
            ]));

            return redirect()->route('monthly-finances.index')
                ->with('success', 'Data keuangan periode berhasil diperbarui!');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Gagal memperbarui data: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function destroy(MonthlyFinance $monthlyFinance)
    {
        try {
            $monthlyFinance->delete();
            return redirect()->route('monthly-finances.index')
                ->with('success', 'Data keuangan periode berhasil dihapus!');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Gagal menghapus data: ' . $e->getMessage());
        }
    }

    public function export()
    {
        return Excel::download(new MonthlyFinanceExport, 'rekap-keuangan-' . date('Y-m-d-H-i-s') . '.xlsx');
    }

    public function rekap()
    {
        $finances = MonthlyFinance::withSummary()
            ->orderBy('periode_awal', 'desc')
            ->get();

        // Hitung total-total
        $totals = [
            // Pendapatan
            'total_pendapatan_shopee' => $finances->sum('total_pendapatan_shopee'),
            'total_pendapatan_tiktok' => $finances->sum('total_pendapatan_tiktok'),
            'total_pendapatan' => $finances->sum('total_pendapatan'),

            // Operasional & Iklan
            'operasional' => $finances->sum('operasional'),
            'iklan_shopee' => $finances->sum('iklan_shopee'),
            'iklan_tiktok' => $finances->sum('iklan_tiktok'),
            'total_iklan' => $finances->sum('total_iklan'),
        ];

        // Hitung calculated totals dari accessor
        $totals['total_penghasilan'] = $finances->sum('total_penghasilan');
        $totals['total_penghasilan_shopee'] = $finances->sum('total_penghasilan_shopee');
        $totals['total_penghasilan_tiktok'] = $finances->sum('total_penghasilan_tiktok');

        $totals['hpp'] = $finances->sum('hpp');
        $totals['hpp_shopee'] = $finances->sum('hpp_shopee');
        $totals['hpp_tiktok'] = $finances->sum('hpp_tiktok');

        $totals['laba_rugi_kotor'] = $finances->sum('laba_rugi_kotor');
        $totals['laba_rugi_shopee'] = $finances->sum('laba_rugi_shopee');
        $totals['laba_rugi_tiktok'] = $finances->sum('laba_rugi_tiktok');
        $totals['laba_rugi'] = $finances->sum('laba_rugi');
        $totals['laba_rugi_net_shopee'] = $finances->sum('laba_rugi_net_shopee');
        $totals['laba_rugi_net_tiktok'] = $finances->sum('laba_rugi_net_tiktok');

        // Hitung rata-rata rasio
        $totals['rata_rata_rasio_admin_shopee'] = $finances->avg('rasio_admin_layanan_shopee');
        $totals['rata_rata_rasio_admin_tiktok'] = $finances->avg('rasio_admin_layanan_tiktok');
        $totals['rata_rata_rasio_admin'] = $finances->avg('rasio_admin_layanan');

        // Hitung rata-rata dari accessor
        $totals['rata_rata_rasio_operasional'] = $finances->sum('total_pendapatan') > 0 ?
            ($finances->sum('operasional') / $finances->sum('total_pendapatan')) * 100 : 0;

        $totals['rata_rata_rasio_laba'] = $finances->sum('total_pendapatan') > 0 ?
            ($finances->sum('laba_rugi') / $finances->sum('total_pendapatan')) * 100 : 0;

        return view('monthly-finances.rekap', compact('finances', 'totals'));
    }

    // Method untuk sync dengan summary
    public function syncWithSummary(MonthlyFinance $monthlyFinance)
    {
        try {
            // Generate summary untuk periode ini
            $summary = MonthlySummary::generateForPeriod($monthlyFinance->periode_awal);

            return redirect()->route('monthly-finances.show', $monthlyFinance)
                ->with('success', "Data berhasil disinkronisasi dengan summary {$summary->nama_periode}!");

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Gagal sinkronisasi data: ' . $e->getMessage());
        }
    }

    // Method untuk generate dari summary yang sudah ada
    public function generateFromSummary(MonthlyFinance $monthlyFinance = null)
    {
        try {
            if ($monthlyFinance) {
                // Generate dari finance tertentu
                $summary = MonthlySummary::where('nama_periode', $monthlyFinance->nama_periode)->first();

                if (!$summary) {
                    return redirect()->back()
                        ->with('error', 'Tidak ada summary untuk periode ini!');
                }

                // Update finance dengan data dari summary
                $monthlyFinance->update([
                    // Tambahkan logika update jika diperlukan
                ]);

                return redirect()->route('monthly-finances.show', $monthlyFinance)
                    ->with('success', 'Data berhasil diperbarui dari summary!');
            } else {
                // Generate semua finance yang belum ada summary
                $summaries = MonthlySummary::all();

                foreach ($summaries as $summary) {
                    $existingFinance = MonthlyFinance::where('nama_periode', $summary->nama_periode)->first();

                    if (!$existingFinance) {
                        MonthlyFinance::create([
                            'periode_awal' => $summary->periode_awal,
                            'periode_akhir' => $summary->periode_akhir,
                            'nama_periode' => $summary->nama_periode,
                            'total_pendapatan_shopee' => 0,
                            'total_pendapatan_tiktok' => 0,
                            'operasional' => 0,
                            'iklan_shopee' => 0,
                            'iklan_tiktok' => 0,
                            'rasio_admin_layanan_shopee' => 0,
                            'rasio_admin_layanan_tiktok' => 0,
                            'keterangan' => 'Dibuat otomatis dari summary',
                        ]);
                    }
                }

                return redirect()->route('monthly-finances.index')
                    ->with('success', 'Data finance berhasil digenerate dari summary!');
            }
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Gagal generate data: ' . $e->getMessage());
        }
    }
}
