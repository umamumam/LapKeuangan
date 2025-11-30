<?php

namespace App\Http\Controllers;

use App\Models\Sampel;
use Illuminate\Http\Request;
use App\Exports\SampelExport;
use App\Imports\SampelImport;
use Maatwebsite\Excel\Facades\Excel;

class SampelController extends Controller
{
    public function index()
    {
        $sampels = Sampel::orderBy('nama')->get();
        return view('sampels.index', compact('sampels'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:100',
            'ukuran' => 'required|string|max:50',
            'harga' => 'required|integer|min:0'
        ]);

        try {
            Sampel::create($request->all());

            return redirect()->route('sampels.index')
                ->with('success', 'Data sampel berhasil ditambahkan!');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Gagal menambahkan data sampel: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function update(Request $request, Sampel $sampel)
    {
        $request->validate([
            'nama' => 'required|string|max:100',
            'ukuran' => 'required|string|max:50',
            'harga' => 'required|integer|min:0'
        ]);

        try {
            $sampel->update($request->all());

            return redirect()->route('sampels.index')
                ->with('success', 'Data sampel berhasil diperbarui!');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Gagal memperbarui data sampel: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function destroy(Sampel $sampel)
    {
        try {
            $sampel->delete();

            return redirect()->route('sampels.index')
                ->with('success', 'Data sampel berhasil dihapus!');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Gagal menghapus data sampel: ' . $e->getMessage());
        }
    }

    public function export()
    {
        $filename = 'data_sampel_' . date('Y-m-d_H-i-s') . '.xlsx';
        return Excel::download(new SampelExport(), $filename);
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls,csv|max:10240'
        ]);

        try {
            Excel::import(new SampelImport(), $request->file('file'));

            return redirect()->route('sampels.index')
                ->with('success', 'Data sampel berhasil diimport!');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Gagal mengimport data sampel: ' . $e->getMessage());
        }
    }

    public function getHarga($id)
    {
        try {
            $sampel = Sampel::findOrFail($id);
            return response()->json([
                'success' => true,
                'harga' => $sampel->harga
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Sampel tidak ditemukan'
            ], 404);
        }
    }
}
