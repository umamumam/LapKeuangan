<?php

namespace App\Http\Controllers;

use App\Models\Rekap;
use App\Models\Toko;
use Illuminate\Http\Request;

class RekapController extends Controller
{
    public function index()
    {
        $rekaps = Rekap::with('toko')->orderBy('tahun', 'desc')->orderByRaw("FIELD(nama_periode, 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember')")->get();
        $tokos = Toko::all();
        $bulanList = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];

        return view('rekaps.index', compact('rekaps', 'tokos', 'bulanList'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_periode' => 'required|in:Januari,Februari,Maret,April,Mei,Juni,Juli,Agustus,September,Oktober,November,Desember',
            'tahun' => 'required|integer|min:2000|max:2100',
            'toko_id' => 'required|exists:tokos,id',
            'total_pendapatan_shopee' => 'required|integer|min:0',
            'total_pendapatan_tiktok' => 'required|integer|min:0',
            'total_penghasilan_shopee' => 'required|integer|min:0',
            'total_penghasilan_tiktok' => 'required|integer|min:0',
            'total_hpp_shopee' => 'required|integer|min:0',
            'total_hpp_tiktok' => 'required|integer|min:0',
            'total_iklan_shopee' => 'required|integer|min:0',
            'total_iklan_tiktok' => 'required|integer|min:0',
            'operasional' => 'required|integer|min:0',
            'rasio_admin_layanan_shopee' => 'required|numeric|min:0|max:100',
            'rasio_admin_layanan_tiktok' => 'required|numeric|min:0|max:100',
            'aov_aktual_shopee' => 'required|integer|min:0',
            'aov_aktual_tiktok' => 'required|integer|min:0',
            'basket_size_aktual_shopee' => 'required|numeric|min:0',
            'basket_size_aktual_tiktok' => 'required|numeric|min:0',
        ]);

        // Cek apakah sudah ada rekap dengan periode, tahun, dan toko yang sama
        $existing = Rekap::where('nama_periode', $request->nama_periode)
            ->where('tahun', $request->tahun)
            ->where('toko_id', $request->toko_id)
            ->first();

        if ($existing) {
            return redirect()->back()->with('error', 'Rekap untuk periode, tahun, dan toko ini sudah ada!');
        }

        // Hitung rasio operasional otomatis
        $data = $request->all();
        $total_pendapatan = $data['total_pendapatan_shopee'] + $data['total_pendapatan_tiktok'];
        $data['rasio_operasional'] = $total_pendapatan > 0 ? ($data['operasional'] / $total_pendapatan) * 100 : 0;

        Rekap::create($data);

        return redirect()->route('rekaps.index')->with('success', 'Data rekap berhasil ditambahkan!');
    }

    public function update(Request $request, Rekap $rekap)
    {
        $request->validate([
            'nama_periode' => 'required|in:Januari,Februari,Maret,April,Mei,Juni,Juli,Agustus,September,Oktober,November,Desember',
            'tahun' => 'required|integer|min:2000|max:2100',
            'toko_id' => 'required|exists:tokos,id',
            'total_pendapatan_shopee' => 'required|integer|min:0',
            'total_pendapatan_tiktok' => 'required|integer|min:0',
            'total_penghasilan_shopee' => 'required|integer|min:0',
            'total_penghasilan_tiktok' => 'required|integer|min:0',
            'total_hpp_shopee' => 'required|integer|min:0',
            'total_hpp_tiktok' => 'required|integer|min:0',
            'total_iklan_shopee' => 'required|integer|min:0',
            'total_iklan_tiktok' => 'required|integer|min:0',
            'operasional' => 'required|integer|min:0',
            'rasio_admin_layanan_shopee' => 'required|numeric|min:0|max:100',
            'rasio_admin_layanan_tiktok' => 'required|numeric|min:0|max:100',
            'aov_aktual_shopee' => 'required|integer|min:0',
            'aov_aktual_tiktok' => 'required|integer|min:0',
            'basket_size_aktual_shopee' => 'required|numeric|min:0',
            'basket_size_aktual_tiktok' => 'required|numeric|min:0',
        ]);

        // Cek duplikat untuk periode, tahun, dan toko (kecuali data yang sedang diupdate)
        $existing = Rekap::where('nama_periode', $request->nama_periode)
            ->where('tahun', $request->tahun)
            ->where('toko_id', $request->toko_id)
            ->where('id', '!=', $rekap->id)
            ->first();

        if ($existing) {
            return redirect()->back()->with('error', 'Rekap untuk periode, tahun, dan toko ini sudah ada!');
        }

        // Hitung rasio operasional otomatis
        $data = $request->all();
        $total_pendapatan = $data['total_pendapatan_shopee'] + $data['total_pendapatan_tiktok'];
        $data['rasio_operasional'] = $total_pendapatan > 0 ? ($data['operasional'] / $total_pendapatan) * 100 : 0;

        $rekap->update($data);

        return redirect()->route('rekaps.index')->with('success', 'Data rekap berhasil diperbarui!');
    }

    public function destroy(Rekap $rekap)
    {
        $rekap->delete();

        return redirect()->route('rekaps.index')->with('success', 'Data rekap berhasil dihapus!');
    }

    public function show(Rekap $rekap)
    {
        return response()->json($rekap);
    }

    public function hasil()
    {
        $tahun = request('tahun', date('Y'));
        $toko_id = request('toko_id');
        $tokos = Toko::all();
        $bulanList = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];

        if (!$toko_id) {
            return view('rekaps.hasil', compact('tokos', 'tahun', 'toko_id', 'bulanList'));
        }

        $rekaps = Rekap::where('tahun', $tahun)->where('toko_id', $toko_id)->get()->keyBy('nama_periode');

        $hasil = [];

        foreach ($bulanList as $bulan) {
            $rekap = $rekaps->get($bulan);

            if ($rekap) {
                $total_pendapatan = $rekap->total_pendapatan_shopee + $rekap->total_pendapatan_tiktok;
                $total_penghasilan = $rekap->total_penghasilan_shopee + $rekap->total_penghasilan_tiktok;
                $total_hpp = $rekap->total_hpp_shopee + $rekap->total_hpp_tiktok;
                $total_iklan = $rekap->total_iklan_shopee + $rekap->total_iklan_tiktok;
                $operasional = $rekap->operasional;
                $rasio_admin_layanan = ($rekap->rasio_admin_layanan_shopee + $rekap->rasio_admin_layanan_tiktok); // rata-rata
                $rasio_operasional = $total_pendapatan > 0 ? ($operasional / $total_pendapatan) * 100 : 0;
                $aov_aktual = ($rekap->aov_aktual_shopee + $rekap->aov_aktual_tiktok) / 2; // rata-rata
                $basket_size_aktual = ($rekap->basket_size_aktual_shopee + $rekap->basket_size_aktual_tiktok) / 2; // rata-rata
                $roas_aktual = $total_iklan > 0 ? $total_pendapatan / $total_iklan : 0;
                $acos_aktual = $total_pendapatan > 0 ? ($total_iklan / $total_pendapatan) * 100 : 0;
                $rasio_margin = $total_hpp > 0 ? (($total_pendapatan - $total_hpp) / $total_pendapatan) * 100 : 0; // margin profit
                $laba = $total_penghasilan - $total_hpp - $operasional - $total_iklan;
                $rasio_laba = $total_pendapatan > 0 ? ($laba / $total_pendapatan) * 100 : 0;
            } else {
                $total_pendapatan = 0;
                $total_penghasilan = 0;
                $total_hpp = 0;
                $total_iklan = 0;
                $operasional = 0;
                $rasio_admin_layanan = 0;
                $rasio_operasional = 0;
                $aov_aktual = 0;
                $basket_size_aktual = 0;
                $roas_aktual = 0;
                $acos_aktual = 0;
                $rasio_margin = 0;
                $laba = 0;
                $rasio_laba = 0;
            }

            $hasil[$bulan] = [
                'total_pendapatan' => $total_pendapatan,
                'total_penghasilan' => $total_penghasilan,
                'total_hpp' => $total_hpp,
                'operasional' => $operasional,
                'iklan' => $total_iklan,
                'rasio_admin_layanan' => $rasio_admin_layanan,
                'rasio_operasional' => $rasio_operasional,
                'aov_aktual' => $aov_aktual,
                'basket_size_aktual' => $basket_size_aktual,
                'roas_aktual' => $roas_aktual,
                'acos_aktual' => $acos_aktual,
                'rasio_margin' => $rasio_margin,
                'rasio_laba' => $rasio_laba,
                'laba' => $laba,
            ];
        }

        return view('rekaps.hasil', compact('hasil', 'tokos', 'tahun', 'toko_id', 'bulanList'));
    }
}
