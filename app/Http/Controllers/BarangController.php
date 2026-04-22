<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\Reseller;
use App\Models\Supplier;
use Illuminate\Http\Request;
use App\Exports\BarangExport;
use App\Imports\BarangImport;
use Maatwebsite\Excel\Facades\Excel;

class BarangController extends Controller
{
    public function index()
    {
        $barangs = Barang::with(['reseller', 'supplier'])->orderBy('id', 'asc')->get();
        $resellers = Reseller::orderBy('nama')->get();
        $suppliers = Supplier::orderBy('nama')->get();
        return view('barangs.index', compact('barangs', 'resellers', 'suppliers'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'reseller_id' => 'nullable|exists:resellers,id',
            'supplier_id' => 'nullable|exists:suppliers,id',
            'namabarang' => 'required|string|max:255',
            'ukuran' => 'nullable|string|max:50',
            'hpp' => 'nullable|integer',
            'harga_grosir' => 'nullable|integer',
        ]);

        try {
            Barang::create($request->all());
            return redirect()->back()->with('success', 'Barang berhasil ditambahkan!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal menambahkan barang: ' . $e->getMessage())->withInput();
        }
    }

    public function update(Request $request, Barang $barang)
    {
        $request->validate([
            'reseller_id' => 'nullable|exists:resellers,id',
            'supplier_id' => 'nullable|exists:suppliers,id',
            'namabarang' => 'required|string|max:255',
            'ukuran' => 'nullable|string|max:50',
            'hpp' => 'nullable|integer',
            'harga_grosir' => 'nullable|integer',
        ]);

        try {
            $barang->update($request->all());
            return redirect()->back()->with('success', 'Barang berhasil diperbarui!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal memperbarui barang: ' . $e->getMessage())->withInput();
        }
    }

    public function destroy(Barang $barang)
    {
        try {
            $barang->delete();
            return redirect()->back()->with('success', 'Barang berhasil dihapus!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal menghapus barang: ' . $e->getMessage());
        }
    }

    public function export()
    {
        $filename = 'data_barang_' . date('Y-m-d_H-i-s') . '.xlsx';
        return Excel::download(new BarangExport, $filename);
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv|max:10240',
        ]);

        try {
            Excel::import(new BarangImport, $request->file('file'));
            return redirect()->back()->with('success', 'Data barang berhasil diimport!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal mengimport data: ' . $e->getMessage());
        }
    }
}
