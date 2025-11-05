<?php

namespace App\Http\Controllers;

use App\Models\Produk;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Exports\ProdukExport;
use App\Imports\ProdukImport;
use Maatwebsite\Excel\Facades\Excel;

class ProdukController extends Controller
{
    public function index()
    {
        $produks = Produk::orderBy('created_at', 'desc')->get();
        return view('produks.index', compact('produks'));
    }

    public function create()
    {
        return view('produks.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'sku_induk' => 'nullable|string|max:50',
            'nama_produk' => 'required|string|max:255',
            'nomor_referensi_sku' => 'nullable|string|max:50',
            'nama_variasi' => 'nullable|string|max:50',
            'hpp_produk' => 'required|integer|min:0',
        ]);

        try {
            Produk::create($request->all());
            return redirect()->route('produks.index')
                ->with('success', 'Produk berhasil ditambahkan!');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Gagal menambahkan produk: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function show(Produk $produk)
    {
        return view('produks.show', compact('produk'));
    }

    public function edit(Produk $produk)
    {
        return view('produks.edit', compact('produk'));
    }

    public function update(Request $request, Produk $produk)
    {
        $request->validate([
            'sku_induk' => 'nullable|string|max:50',
            'nama_produk' => 'required|string|max:255',
            'nomor_referensi_sku' => 'nullable|string|max:50',
            'nama_variasi' => 'nullable|string|max:50',
            'hpp_produk' => 'required|integer|min:0',
        ]);

        try {
            $produk->update($request->all());
            return redirect()->route('produks.index')
                ->with('success', 'Produk berhasil diperbarui!');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Gagal memperbarui produk: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function destroy(Produk $produk)
    {
        try {
            $produk->delete();
            return redirect()->route('produks.index')
                ->with('success', 'Produk berhasil dihapus!');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Gagal menghapus produk: ' . $e->getMessage());
        }
    }

    public function export()
    {
        return Excel::download(new ProdukExport, 'produks-' . date('Y-m-d-H-i-s') . '.xlsx');
    }


    public function importForm()
    {
        return view('produks.import');
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv|max:5120'
        ]);

        try {
            $import = new ProdukImport;
            Excel::import($import, $request->file('file'));

            if (count($import->failures()) > 0) {
                return redirect()->route('produks.import.form')
                    ->with('warning', 'Beberapa data gagal diimport. Silakan periksa data yang error.')
                    ->with('failures', $import->failures());
            }

            return redirect()->route('produks.index')
                ->with('success', 'Data produk berhasil diimport!');
        } catch (\Exception $e) {
            return redirect()->route('produks.import.form')
                ->with('error', 'Gagal mengimport data: ' . $e->getMessage());
        }
    }

    public function downloadTemplate()
    {
        return Excel::download(new ProdukExport, 'template-import-produk.xlsx');
    }
}
