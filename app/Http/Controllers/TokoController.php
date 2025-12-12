<?php

namespace App\Http\Controllers;

use App\Models\Toko;
use Illuminate\Http\Request;

class TokoController extends Controller
{
    public function index()
    {
        $tokos = Toko::orderBy('id')->get();
        return view('toko.index', compact('tokos'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:255|unique:tokos,nama',
        ]);

        Toko::create([
            'nama' => $request->nama,
        ]);

        return redirect()->route('toko.index')->with('success', 'Toko berhasil ditambahkan!');
    }

    public function update(Request $request, Toko $toko)
    {
        $request->validate([
            'nama' => 'required|string|max:255|unique:tokos,nama,' . $toko->id,
        ]);

        $toko->update([
            'nama' => $request->nama,
        ]);

        return redirect()->route('toko.index')->with('success', 'Toko berhasil diperbarui!');
    }

    public function destroy(Toko $toko)
    {
        $toko->delete();

        return redirect()->route('toko.index')->with('success', 'Toko berhasil dihapus!');
    }

    public function show(Toko $toko)
    {
        return response()->json($toko);
    }
}
