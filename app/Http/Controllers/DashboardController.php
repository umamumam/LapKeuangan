<?php

namespace App\Http\Controllers;

use App\Models\Income;
use App\Models\Order;
use App\Models\Produk;
use App\Models\Toko;
use App\Models\Rekap;
use App\Models\Sampel;
use App\Models\PengirimanSampel;
use App\Models\Banding;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $totalProduk = Produk::count();
        $totalToko = Toko::count();
        $totalOrder = Order::count();
        $totalIncome = Income::count();

        $tokos = Toko::all();

        $tahunList = Rekap::select('tahun')->distinct()->orderBy('tahun', 'desc')->pluck('tahun');

        $tokoId = $request->input('toko_id', 1);
        $tahun = $request->input('tahun', date('Y'));

        $rekapData = Rekap::where('toko_id', $tokoId)
            ->where('tahun', $tahun)
            ->orderByRaw("FIELD(nama_periode, 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember')")
            ->get();

        $chartData = $this->formatChartData($rekapData);

        $chartDataJson = json_encode($chartData);

        $bestSellerProducts = $this->getBestSellerProducts();

        $dataLainnya = $this->getDataLainnya();

        $statistik = [
            'total_produk' => $totalProduk,
            'total_toko' => $totalToko,
            'total_order' => $totalOrder,
            'total_income' => $totalIncome,
        ];

        return view('dashboard', compact('statistik', 'tokos', 'tahunList', 'chartData', 'chartDataJson', 'tokoId', 'tahun', 'bestSellerProducts', 'dataLainnya'));
    }

    private function formatChartData($rekapData)
    {
        $data = [
            'pendapatan_shopee' => array_fill(0, 12, 0),
            'pendapatan_tiktok' => array_fill(0, 12, 0),
            'penghasilan_shopee' => array_fill(0, 12, 0),
            'penghasilan_tiktok' => array_fill(0, 12, 0),
            'bulan_labels' => ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec']
        ];

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

    private function getBestSellerProducts()
    {
        $sixMonthsAgo = now()->subMonths(6);

        $bestSellers = Order::select(
                'produks.id',
                'produks.nama_produk',
                'produks.nama_variasi',
                'produks.sku_induk',
                DB::raw('SUM(orders.jumlah - orders.returned_quantity) as total_terjual'),
                DB::raw('SUM(orders.total_harga_produk) as total_pendapatan')
            )
            ->join('produks', 'orders.produk_id', '=', 'produks.id')
            ->where('orders.created_at', '>=', $sixMonthsAgo)
            ->groupBy('produks.id', 'produks.nama_produk', 'produks.nama_variasi', 'produks.sku_induk')
            ->havingRaw('SUM(orders.jumlah - orders.returned_quantity) > 0')
            ->orderByDesc('total_terjual')
            ->limit(10)
            ->get();

        return $bestSellers;
    }

    private function getDataLainnya()
    {
        $totalSampel = Sampel::count();
        $lastSampel = Sampel::orderBy('id', 'desc')->first();
        $lastUpdateSampel = $lastSampel ? $lastSampel->created_at->format('d M Y, H:i') : 'Belum ada data';

        $totalPengirimanSampel = PengirimanSampel::count();
        $lastPengirimanSampel = PengirimanSampel::orderBy('id', 'desc')->first();
        $lastUpdatePengirimanSampel = $lastPengirimanSampel ? $lastPengirimanSampel->created_at->format('d M Y, H:i') : 'Belum ada data';

        $totalBanding = Banding::count();
        $lastBanding = Banding::orderBy('id', 'desc')->first();
        $lastUpdateBanding = $lastBanding ? $lastBanding->created_at->format('d M Y, H:i') : 'Belum ada data';

        // Hitung total pengiriman yang mengandung sampel affiliate
        $totalPengirimanAffiliate = PengirimanSampel::where(function ($query) {
            // Cek di semua kolom sampel_id
            for ($i = 1; $i <= 5; $i++) {
                $query->orWhereHas("sampel{$i}", function ($subQuery) {
                    $subQuery->where('nama', 'like', '%affiliate%');
                });
            }
        })->count();

        return [
            'total_sampel' => $totalSampel,
            'last_update_sampel' => $lastUpdateSampel,
            'total_pengiriman_sampel' => $totalPengirimanSampel,
            'total_pengiriman_affiliate' => $totalPengirimanAffiliate,
            'last_update_pengiriman_sampel' => $lastUpdatePengirimanSampel,
            'total_banding' => $totalBanding,
            'last_update_banding' => $lastUpdateBanding,
        ];
    }
}
