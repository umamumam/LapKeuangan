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
            'total_penghasilan_shopee' => 'required|integer|min:0',
            'total_penghasilan_tiktok' => 'required|integer|min:0',
            'total_hpp_shopee' => 'required|integer|min:0',
            'total_hpp_tiktok' => 'required|integer|min:0',
            'total_iklan_shopee' => 'required|integer|min:0',
            'total_iklan_tiktok' => 'required|integer|min:0',
            'operasional' => 'required|integer|min:0',
        ]);

        // Cek apakah sudah ada rekap dengan periode, tahun, dan toko yang sama
        $existing = Rekap::where('nama_periode', $request->nama_periode)
            ->where('tahun', $request->tahun)
            ->where('toko_id', $request->toko_id)
            ->first();

        if ($existing) {
            return redirect()->back()->with('error', 'Rekap untuk periode, tahun, dan toko ini sudah ada!');
        }

        Rekap::create($request->all());

        return redirect()->route('rekaps.index')->with('success', 'Data rekap berhasil ditambahkan!');
    }

    public function update(Request $request, Rekap $rekap)
    {
        $request->validate([
            'nama_periode' => 'required|in:Januari,Februari,Maret,April,Mei,Juni,Juli,Agustus,September,Oktober,November,Desember',
            'tahun' => 'required|integer|min:2000|max:2100',
            'toko_id' => 'required|exists:tokos,id',
            'total_penghasilan_shopee' => 'required|integer|min:0',
            'total_penghasilan_tiktok' => 'required|integer|min:0',
            'total_hpp_shopee' => 'required|integer|min:0',
            'total_hpp_tiktok' => 'required|integer|min:0',
            'total_iklan_shopee' => 'required|integer|min:0',
            'total_iklan_tiktok' => 'required|integer|min:0',
            'operasional' => 'required|integer|min:0',
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

        $rekap->update($request->all());

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
}
