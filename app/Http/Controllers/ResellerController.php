<?php

namespace App\Http\Controllers;

use App\Models\Reseller;
use Illuminate\Http\Request;

class ResellerController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:255|unique:resellers,nama',
            'hutang_awal' => 'nullable|numeric|min:0',
        ]);

        Reseller::create($request->all());

        return redirect()->back()->with('success', 'Reseller berhasil ditambahkan!');
    }

    public function update(Request $request, Reseller $reseller)
    {
        $request->validate([
            'nama' => 'required|string|max:255|unique:resellers,nama,' . $reseller->id,
            'hutang_awal' => 'nullable|numeric|min:0',
        ]);

        $reseller->update($request->all());

        return redirect()->back()->with('success', 'Reseller berhasil diperbarui!');
    }

    public function destroy(Reseller $reseller)
    {
        $reseller->delete();

        return redirect()->back()->with('success', 'Reseller berhasil dihapus!');
    }
}
