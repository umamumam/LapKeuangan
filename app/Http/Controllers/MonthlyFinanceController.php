<?php

namespace App\Http\Controllers;

use App\Models\Periode;
use Illuminate\Http\Request;
use App\Models\MonthlyFinance;
use Illuminate\Support\Facades\DB;

class MonthlyFinanceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $monthlyFinances = MonthlyFinance::with('periode.toko')
            ->latest()
            ->get();

        return view('monthly-finances.index', compact('monthlyFinances'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $periodes = Periode::where('is_generated', true)
            ->whereDoesntHave('monthlyFinance')
            ->with('toko')
            ->get();

        return view('monthly-finances.create', compact('periodes'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'periode_id' => 'required|exists:periodes,id|unique:monthly_finances,periode_id',
            'total_pendapatan' => 'required|integer|min:0',
            'operasional' => 'required|integer|min:0',
            'iklan' => 'required|integer|min:0',
            'rasio_admin_layanan' => 'required|numeric|min:0|max:100',
            'keterangan' => 'nullable|string|max:500',
        ]);

        MonthlyFinance::create($validated);

        return redirect()->route('monthly-finances.index')
            ->with('success', 'Data keuangan bulanan berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(MonthlyFinance $monthlyFinance)
    {
        $monthlyFinance->load([
            'periode' => function($query) {
                $query->withCount(['orders as total_jumlah' => function($q) {
                    $q->select(DB::raw('COALESCE(SUM(jumlah), 0)'));
                }]);
            },
            'periode.toko'
        ]);
        $periode = $monthlyFinance->periode;

        $totalBiaya = $monthlyFinance->operasional + $monthlyFinance->iklan;
        if($periode) {
            $labaBersih = $periode->total_penghasilan - $periode->total_hpp_produk - $totalBiaya;
            $rasioLaba = ($labaBersih / $monthlyFinance->total_pendapatan) * 100;
        } else {
            $labaBersih = $monthlyFinance->total_pendapatan - $totalBiaya;
        }

        return view('monthly-finances.show', compact('monthlyFinance', 'totalBiaya', 'labaBersih', 'periode', 'rasioLaba'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(MonthlyFinance $monthlyFinance)
    {
        $periode = $monthlyFinance->periode;

        return view('monthly-finances.edit', compact('monthlyFinance', 'periode'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, MonthlyFinance $monthlyFinance)
    {
        $validated = $request->validate([
            'total_pendapatan' => 'required|integer|min:0',
            'operasional' => 'required|integer|min:0',
            'iklan' => 'required|integer|min:0',
            'rasio_admin_layanan' => 'required|numeric|min:0|max:100',
            'keterangan' => 'nullable|string|max:500',
        ]);

        $monthlyFinance->update($validated);

        return redirect()->route('monthly-finances.index')
            ->with('success', 'Data keuangan bulanan berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(MonthlyFinance $monthlyFinance)
    {
        $monthlyFinance->delete();

        return redirect()->route('monthly-finances.index')
            ->with('success', 'Data keuangan bulanan berhasil dihapus.');
    }
}
