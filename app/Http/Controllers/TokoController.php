<?php
// app/Http/Controllers/TokoController.php

namespace App\Http\Controllers;

use App\Models\Toko;
use Illuminate\Http\Request;

class TokoController extends Controller
{
    public function index()
    {
        $tokos = Toko::all();
        return view('toko.index', compact('tokos'));
    }

    public function create()
    {
        return view('toko.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:255'
        ]);

        Toko::create($request->all());

        return redirect()->route('toko.index')
            ->with('success', 'Toko berhasil dibuat.');
    }

    public function show(Toko $toko)
    {
        $incomes = $toko->incomes()->latest()->get();

        return view('toko.show', compact('toko', 'incomes'));
    }

    public function edit(Toko $toko)
    {
        return view('toko.edit', compact('toko'));
    }

    public function update(Request $request, Toko $toko)
    {
        $request->validate([
            'nama' => 'required|string|max:255'
        ]);

        $toko->update($request->all());

        return redirect()->route('toko.index')
            ->with('success', 'Toko berhasil diupdate.');
    }

    public function destroy(Toko $toko)
    {
        $toko->delete();

        return redirect()->route('toko.index')
            ->with('success', 'Toko berhasil dihapus.');
    }
}
