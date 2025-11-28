<?php

namespace App\Http\Controllers;

use App\Models\MonthlySummary;
use Illuminate\Http\Request;
use Carbon\Carbon;

class MonthlySummaryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $summaries = MonthlySummary::orderBy('periode_awal', 'desc')->get();

        // Data untuk modal generate
        $currentYear = date('Y');
        $years = range($currentYear - 2, $currentYear + 1);
        $months = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];

        return view('monthly-summaries.index', compact('summaries', 'years', 'months'));
    }

    /**
     * Display the specified resource in modal.
     */
    public function show(MonthlySummary $monthlySummary)
    {
        // Load additional calculated data
        $calculatedData = [
            'margin' => $monthlySummary->margin,
            'rasio_margin' => $monthlySummary->rasio_margin,
            'aov' => $monthlySummary->aov,
            'net_quantity' => $monthlySummary->net_quantity,
        ];

        return view('monthly-summaries.modal-show', compact('monthlySummary', 'calculatedData'));
    }

    /**
     * Generate summary for specific period
     */
    public function generate(Request $request)
    {
        $request->validate([
            'month' => 'required|integer|min:1|max:12',
            'year' => 'required|integer|min:2020|max:' . (date('Y') + 1),
        ]);

        try {
            $periodeAwal = "{$request->year}-{$request->month}-01";
            $summary = MonthlySummary::generateForPeriod($periodeAwal);

            return response()->json([
                'success' => true,
                'message' => "Summary untuk {$summary->nama_periode} berhasil digenerate!",
                'data' => $summary
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal generate summary: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Generate current month summary
     */
    public function generateCurrentMonth()
    {
        try {
            $summary = MonthlySummary::generateCurrentMonth();

            return response()->json([
                'success' => true,
                'message' => "Summary untuk {$summary->nama_periode} berhasil digenerate!",
                'data' => $summary
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal generate summary: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Generate previous month summary
     */
    public function generatePreviousMonth()
    {
        try {
            $summary = MonthlySummary::generatePreviousMonth();

            return response()->json([
                'success' => true,
                'message' => "Summary untuk {$summary->nama_periode} berhasil digenerate!",
                'data' => $summary
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal generate summary: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get dashboard data for modal
     */
    public function dashboard()
    {
        // Get current month summary
        $currentMonth = MonthlySummary::whereYear('periode_awal', date('Y'))
            ->whereMonth('periode_awal', date('m'))
            ->first();

        // Get previous month summary
        $previousMonth = MonthlySummary::whereYear('periode_awal', date('Y'))
            ->whereMonth('periode_awal', date('m') - 1)
            ->first();

        // Get last 6 months for chart
        $last6Months = MonthlySummary::orderBy('periode_awal', 'desc')
            ->take(6)
            ->get()
            ->reverse();

        // Calculate growth
        $growth = [
            'penghasilan' => $this->calculateGrowth($currentMonth->total_penghasilan ?? 0, $previousMonth->total_penghasilan ?? 0),
            'laba_rugi' => $this->calculateGrowth($currentMonth->laba_rugi ?? 0, $previousMonth->laba_rugi ?? 0),
            'orders' => $this->calculateGrowth($currentMonth->total_order_qty ?? 0, $previousMonth->total_order_qty ?? 0),
        ];

        return view('monthly-summaries.modal-dashboard', compact(
            'currentMonth',
            'previousMonth',
            'last6Months',
            'growth'
        ));
    }

    /**
     * Delete specific summary
     */
    public function destroy(MonthlySummary $monthlySummary)
    {
        try {
            $namaPeriode = $monthlySummary->nama_periode;
            $monthlySummary->delete();

            return response()->json([
                'success' => true,
                'message' => "Summary {$namaPeriode} berhasil dihapus!"
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus summary: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Calculate growth percentage
     */
    private function calculateGrowth($current, $previous)
    {
        if ($previous == 0) {
            return $current > 0 ? 100 : 0;
        }

        return round((($current - $previous) / $previous) * 100, 2);
    }
}
