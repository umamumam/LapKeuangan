<?php

namespace App\Http\Controllers;

use App\Models\MonthlyFinance;
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
            'total_pendapatan' => 'required|integer|min:0',
            'operasional' => 'required|integer|min:0',
            'iklan' => 'required|integer|min:0',
            'rasio_admin_layanan' => 'required|numeric|min:0|max:100',
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
            'total_penghasilan' => $monthlyFinance->total_penghasilan,
            'hpp' => $monthlyFinance->hpp,
            'laba_rugi' => $monthlyFinance->laba_rugi,
            'rasio_operasional' => $monthlyFinance->rasio_operasional,
            'rasio_margin' => $monthlyFinance->rasio_margin,
            'rasio_laba' => $monthlyFinance->rasio_laba,
            'aov_aktual' => $monthlyFinance->aov_aktual,
            'basket_size_aktual' => $monthlyFinance->basket_size_aktual,
            'roas_aktual' => $monthlyFinance->roas_aktual,
            'acos_aktual' => $monthlyFinance->acos_aktual,
            'total_order_qty' => $monthlyFinance->total_order_qty,
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
            'total_pendapatan' => 'required|integer|min:0',
            'operasional' => 'required|integer|min:0',
            'iklan' => 'required|integer|min:0',
            'rasio_admin_layanan' => 'required|numeric|min:0|max:100',
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
            'total_pendapatan' => $finances->sum('total_pendapatan'),
            'operasional' => $finances->sum('operasional'),
            'iklan' => $finances->sum('iklan'),
        ];

        // Hitung calculated totals dari accessor
        $totals['total_penghasilan'] = $finances->sum('total_penghasilan');
        $totals['hpp'] = $finances->sum('hpp');
        $totals['laba_rugi'] = $finances->sum('laba_rugi');

        // Hitung rata-rata rasio
        $totals['rata_rata_rasio_admin'] = $finances->avg('rasio_admin_layanan');
        $totals['rata_rata_rasio_operasional'] = $finances->avg('rasio_operasional');
        $totals['rata_rata_rasio_laba'] = $finances->avg('rasio_laba');

        return view('monthly-finances.rekap', compact('finances', 'totals'));
    }

    // Method untuk sync dengan summary
    public function syncWithSummary(MonthlyFinance $monthlyFinance)
    {
        try {
            // Generate summary untuk periode ini
            $summary = \App\Models\MonthlySummary::generateForPeriod($monthlyFinance->periode_awal);

            return redirect()->route('monthly-finances.show', $monthlyFinance)
                ->with('success', "Data berhasil disinkronisasi dengan summary {$summary->nama_periode}!");

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Gagal sinkronisasi data: ' . $e->getMessage());
        }
    }
}
