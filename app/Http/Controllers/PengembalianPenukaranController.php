<?php

namespace App\Http\Controllers;

use App\Models\PengembalianPenukaran;
use Illuminate\Http\Request;
use App\Exports\PengembalianPenukaranExport;
use App\Imports\PengembalianPenukaranImport;
use Maatwebsite\Excel\Facades\Excel;

class PengembalianPenukaranController extends Controller
{
    public function index(Request $request)
    {
        $query = PengembalianPenukaran::query();

        if ($request->filled('jenis')) {
            $query->where('jenis', $request->jenis);
        }

        if ($request->filled('marketplace')) {
            $query->where('marketplace', $request->marketplace);
        }

        $startDate = $request->filled('start_date') ? $request->start_date : now()->startOfMonth()->format('Y-m-d');
        $endDate = $request->filled('end_date') ? $request->end_date : now()->endOfMonth()->format('Y-m-d');
        $query->whereBetween('tanggal', [$startDate, $endDate]);
        $pengembalianPenukaran = $query->orderBy('tanggal', 'desc')->get();
        $jenisOptions = PengembalianPenukaran::JENIS;
        $marketplaceOptions = PengembalianPenukaran::MARKETPLACE;

        return view('pengembalian-penukaran.index', compact(
            'pengembalianPenukaran',
            'jenisOptions',
            'marketplaceOptions',
            'startDate',
            'endDate'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'tanggal' => 'required|date',
            'jenis' => 'required|in:' . implode(',', array_keys(PengembalianPenukaran::JENIS)),
            'marketplace' => 'required|in:' . implode(',', array_keys(PengembalianPenukaran::MARKETPLACE)),
            'resi_penerimaan' => 'nullable|string|max:100',
            'resi_pengiriman' => 'nullable|string|max:100',
            'pembayaran' => 'required|in:' . implode(',', array_keys(PengembalianPenukaran::PEMBAYARAN)),
            'nama_pengirim' => 'required|string|max:100',
            'no_hp' => 'required|string|max:20',
            'alamat' => 'required|string',
            'keterangan' => 'nullable|string',
        ]);

        try {
            PengembalianPenukaran::create($request->all());

            return redirect()->route('pengembalian-penukaran.index')
                ->with('success', 'Data pengembalian/penukaran berhasil ditambahkan!');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Gagal menambahkan data: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function update(Request $request, PengembalianPenukaran $pengembalianPenukaran)
    {
        $request->validate([
            'tanggal' => 'required|date',
            'jenis' => 'required|in:' . implode(',', array_keys(PengembalianPenukaran::JENIS)),
            'marketplace' => 'required|in:' . implode(',', array_keys(PengembalianPenukaran::MARKETPLACE)),
            'resi_penerimaan' => 'nullable|string|max:100',
            'resi_pengiriman' => 'nullable|string|max:100',
            'pembayaran' => 'required|in:' . implode(',', array_keys(PengembalianPenukaran::PEMBAYARAN)),
            'nama_pengirim' => 'required|string|max:100',
            'no_hp' => 'required|string|max:20',
            'alamat' => 'required|string',
            'keterangan' => 'nullable|string',
        ]);

        try {
            $pengembalianPenukaran->update($request->all());

            return redirect()->route('pengembalian-penukaran.index')
                ->with('success', 'Data pengembalian/penukaran berhasil diperbarui!');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Gagal memperbarui data: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function destroy(PengembalianPenukaran $pengembalianPenukaran)
    {
        try {
            $pengembalianPenukaran->delete();

            return redirect()->route('pengembalian-penukaran.index')
                ->with('success', 'Data pengembalian/penukaran berhasil dihapus!');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Gagal menghapus data: ' . $e->getMessage());
        }
    }

    public function deleteAll()
    {
        try {
            $count = PengembalianPenukaran::count();

            if ($count === 0) {
                return redirect()->route('pengembalian-penukaran.index')
                    ->with('warning', 'Tidak ada data pengembalian/penukaran untuk dihapus.');
            }

            PengembalianPenukaran::truncate();

            return redirect()->route('pengembalian-penukaran.index')
                ->with('success', "Semua data pengembalian/penukaran ($count data) berhasil dihapus!");
        } catch (\Exception $e) {
            \Log::error('Delete All PengembalianPenukaran Error: ' . $e->getMessage());

            return redirect()->route('pengembalian-penukaran.index')
                ->with('error', 'Gagal menghapus semua data: ' . $e->getMessage());
        }
    }

    public function export()
    {
        $filename = 'data_pengembalian_penukaran_' . date('Y-m-d_H-i-s') . '.xlsx';
        return Excel::download(new PengembalianPenukaranExport(), $filename);
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls,csv|max:10240'
        ]);

        try {
            $import = new PengembalianPenukaranImport();
            Excel::import($import, $request->file('file'));

            $totalRows = $import->getRowCount();
            $errors = $import->getErrors();
            $failures = $import->failures();

            $errorCount = count($errors) + count($failures);
            $successCount = $totalRows - $errorCount;

            $allErrors = [];

            foreach ($errors as $error) {
                $allErrors[] = [
                    'row' => $error['row'],
                    'resi' => $error['resi'],
                    'nama' => $error['nama'],
                    'error' => $error['error'],
                    'data' => $error['data']
                ];
            }

            foreach ($failures as $failure) {
                $row = $failure->row();
                $errorsList = implode(', ', $failure->errors());
                $values = $failure->values();

                $allErrors[] = [
                    'row' => $row,
                    'resi' => $values['resi_penerimaan'] ?? $values['Resi Penerimaan'] ?? $values['Resi_Penerimaan'] ?? '-',
                    'nama' => $values['nama_pengirim'] ?? $values['Nama Pengirim'] ?? $values['Nama_Pengirim'] ?? '-',
                    'error' => $errorsList,
                    'data' => [
                        'tanggal' => $values['tanggal'] ?? $values['Tanggal'] ?? '-',
                        'jenis' => $values['jenis'] ?? $values['Jenis'] ?? '-',
                        'marketplace' => $values['marketplace'] ?? $values['Marketplace'] ?? '-',
                        'no_hp' => $values['no_hp'] ?? $values['No HP'] ?? $values['No_HP'] ?? '-',
                    ]
                ];
            }

            if ($errorCount > 0) {
                return redirect()->back()
                    ->with('warning', "✅ $successCount data berhasil diimport\n❌ $errorCount data gagal diimport")
                    ->with('import_errors', $allErrors)
                    ->withInput();
            }

            return redirect()->route('pengembalian-penukaran.index')
                ->with('success', "✅ $successCount data berhasil diimport!");
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', "❌ Gagal mengimport data: " . $e->getMessage())
                ->withInput();
        }
    }
}
