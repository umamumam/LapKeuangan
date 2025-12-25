<?php

namespace App\Http\Controllers;

use App\Models\Income;
use App\Models\Order;
use App\Models\Produk;
use App\Models\Toko;
use App\Models\Rekap;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $totalProduk = Produk::count();
        $totalToko = Toko::count();
        $totalOrder = Order::count();
        $totalIncome = Income::count();

        // Ambil semua toko untuk dropdown filter
        $tokos = Toko::all();

        // Ambil tahun-tahun yang tersedia di tabel rekaps
        $tahunList = Rekap::select('tahun')->distinct()->orderBy('tahun', 'desc')->pluck('tahun');

        // Filter default
        $tokoId = $request->input('toko_id', 1); // Default toko_id = 1
        $tahun = $request->input('tahun', date('Y')); // Default tahun sekarang

        // Ambil data rekap untuk grafik
        $rekapData = Rekap::where('toko_id', $tokoId)
            ->where('tahun', $tahun)
            ->orderByRaw("FIELD(nama_periode, 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember')")
            ->get();

        // Format data untuk chart
        $chartData = $this->formatChartData($rekapData);

        // Konversi ke JSON untuk dikirim ke JavaScript
        $chartDataJson = json_encode($chartData);

        $statistik = [
            'total_produk' => $totalProduk,
            'total_toko' => $totalToko,
            'total_order' => $totalOrder,
            'total_income' => $totalIncome,
        ];

        return view('dashboard', compact('statistik', 'tokos', 'tahunList', 'chartData', 'chartDataJson', 'tokoId', 'tahun'));
    }

    private function formatChartData($rekapData)
    {
        // Initialize arrays dengan nilai 0 untuk 12 bulan
        $data = [
            'pendapatan_shopee' => array_fill(0, 12, 0),
            'pendapatan_tiktok' => array_fill(0, 12, 0),
            'penghasilan_shopee' => array_fill(0, 12, 0),
            'penghasilan_tiktok' => array_fill(0, 12, 0),
            'bulan_labels' => ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec']
        ];

        // Mapping bulan Indonesia ke index
        $bulanMapping = [
            'Januari' => 0, 'Februari' => 1, 'Maret' => 2, 'April' => 3,
            'Mei' => 4, 'Juni' => 5, 'Juli' => 6, 'Agustus' => 7,
            'September' => 8, 'Oktober' => 9, 'November' => 10, 'Desember' => 11
        ];

        foreach ($rekapData as $rekap) {
            $index = $bulanMapping[$rekap->nama_periode] ?? null;

            if ($index !== null) {
                $data['pendapatan_shopee'][$index] = (int) $rekap->total_pendapatan_shopee;
                $data['pendapatan_tiktok'][$index] = (int) $rekap->total_pendapatan_tiktok;
                $data['penghasilan_shopee'][$index] = (int) $rekap->total_penghasilan_shopee;
                $data['penghasilan_tiktok'][$index] = (int) $rekap->total_penghasilan_tiktok;

            }
        }

        return $data;
    }
}
