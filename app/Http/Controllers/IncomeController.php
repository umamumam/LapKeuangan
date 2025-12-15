<?php

namespace App\Http\Controllers;

use App\Models\Toko;
use App\Models\Order;
use App\Models\Income;
use App\Models\Periode;
use Illuminate\Http\Request;
use App\Exports\IncomeExport;
use App\Imports\IncomeImport;
use Illuminate\Support\Facades\DB;
use App\Exports\IncomeResultExport;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\IncomePerPeriodeExport;

class IncomeController extends Controller
{
    public function index()
    {
        $incomes = Income::with(['orders.produk', 'periode'])
            ->orderBy('id', 'desc')
            ->paginate(200);

        $totalIncomes = Income::count();
        $startOfMonth = now()->startOfMonth();
        $totalIncomeBulanIni = Income::where('created_at', '>=', $startOfMonth)->sum('total_penghasilan');

        // Ambil semua periode untuk dropdown filter
        $periodes = Periode::orderBy('nama_periode', 'desc')->get();

        return view('incomes.index', compact(
            'incomes',
            'totalIncomes',
            'totalIncomeBulanIni',
            'periodes'
        ));
    }

    public function create()
    {
        $orders = Order::select('no_pesanan')->distinct()->get();
        $periodes = Periode::orderBy('nama_periode', 'desc')->get();
        return view('incomes.create', compact('orders', 'periodes'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'no_pesanan' => 'required|string|max:100',
            'no_pengajuan' => 'nullable|string|max:100',
            'total_penghasilan' => 'required|integer|min:0',
            'periode_id' => 'nullable|exists:periodes,id',
        ]);

        try {
            Income::create($request->all());
            return redirect()->route('incomes.index')
                ->with('success', 'Income berhasil ditambahkan!');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Gagal menambahkan income: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function show(Income $income)
    {
        $income->load(['orders.produk', 'periode.toko']);
        return view('incomes.show', compact('income'));
    }

    public function edit(Income $income)
    {
        $orders = Order::select('no_pesanan')->distinct()->get();
        $periodes = Periode::orderBy('nama_periode', 'desc')->get();
        return view('incomes.edit', compact('income', 'orders', 'periodes'));
    }

    public function update(Request $request, Income $income)
    {
        $request->validate([
            'no_pesanan' => 'required|string|max:100' . $income->id,
            'no_pengajuan' => 'nullable|string|max:100',
            'total_penghasilan' => 'required|integer|min:0',
            'periode_id' => 'nullable|exists:periodes,id',
        ]);

        try {
            $income->update($request->all());
            return redirect()->route('incomes.index')
                ->with('success', 'Income berhasil diperbarui!');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Gagal memperbarui income: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function destroy(Income $income)
    {
        try {
            $income->delete();
            return redirect()->route('incomes.index')
                ->with('success', 'Income berhasil dihapus!');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Gagal menghapus income: ' . $e->getMessage());
        }
    }

    public function calculateTotal(Income $income)
    {
        try {
            DB::beginTransaction();

            $total = $income->orders
                ->where('periode_id', $income->periode_id)
                ->sum(function ($order) {
                    $netQuantity = $order->jumlah - $order->returned_quantity;
                    return $netQuantity * $order->produk->hpp_produk;
                });

            $income->update(['total_penghasilan' => $total]);
            DB::commit();

            return redirect()->route('incomes.show', $income)
                ->with('success', 'Total penghasilan berhasil dihitung otomatis dari order dengan periode yang sama: Rp ' . number_format($total));
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Gagal menghitung total: ' . $e->getMessage());
        }
    }

    public function createFromOrder($noPesanan)
    {
        try {
            $existingIncome = Income::where('no_pesanan', $noPesanan)->first();
            if ($existingIncome) {
                return redirect()->route('incomes.show', $existingIncome)
                    ->with('warning', 'Income untuk nomor pesanan ini sudah ada');
            }

            $orders = Order::with('produk')->where('no_pesanan', $noPesanan)->get();

            if ($orders->isEmpty()) {
                return redirect()->back()
                    ->with('error', 'Tidak ada order dengan nomor pesanan: ' . $noPesanan);
            }

            $total = $orders->sum(function ($order) {
                $netQuantity = $order->jumlah - $order->returned_quantity;
                return $netQuantity * $order->produk->hpp_produk;
            });

            $noPengajuan = 'SUB-' . $noPesanan . '-' . date('YmdHis');

            // Ambil periode_id dari order pertama yang punya periode
            $periodeId = $orders->firstWhere('periode_id', '!=', null)?->periode_id ?? null;

            $income = Income::create([
                'no_pesanan' => $noPesanan,
                'no_pengajuan' => $noPengajuan,
                'total_penghasilan' => $total,
                'periode_id' => $periodeId,
            ]);

            return redirect()->route('incomes.show', $income)
                ->with('success', 'Income berhasil dibuat dari order yang ada!');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Gagal membuat income dari order: ' . $e->getMessage());
        }
    }

    public function export()
    {
        return Excel::download(new IncomeExport, 'incomes-' . date('Y-m-d-H-i-s') . '.xlsx');
    }

    public function exportPeriode(Request $request)
    {
        $request->validate([
            'periode_id' => 'required|exists:periodes,id'
        ]);
        $periode = Periode::findOrFail($request->periode_id);
        $filename = 'income-periode-' . str_replace(' ', '-', $periode->nama_periode) . '-' . date('Y-m-d-H-i-s') . '.xlsx';
        return Excel::download(new IncomePerPeriodeExport($request->periode_id), $filename);
    }

    public function importForm()
    {
        $periodes = Periode::orderBy('nama_periode', 'desc')->get();
        return view('incomes.import', compact('periodes'));
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv|max:5120',
            'default_periode_id' => 'nullable|exists:periodes,id',
        ]);

        try {
            $import = new IncomeImport($request->default_periode_id);
            Excel::import($import, $request->file('file'));

            $failures = $import->getFailedOrders();
            $successCount = $import->getSuccessCount();

            // Jika ada data yang berhasil diimport
            if ($successCount > 0) {
                $message = "Berhasil mengimport {$successCount} data income!";

                // Jika ada yang gagal, tampilkan detail no_pesanan yang gagal
                if (count($failures) > 0) {
                    $failedOrderNumbers = collect($failures)
                        ->pluck('no_pesanan')
                        ->filter(function ($value) {
                            return !empty($value) && $value !== 'Tidak diketahui';
                        })
                        ->unique()
                        ->implode(', ');

                    $failedCount = count($failures);
                    $message .= " {$failedCount} data gagal diimport.";

                    if (!empty($failedOrderNumbers)) {
                        $message .= " No. Pesanan yang gagal: " . $failedOrderNumbers;
                    }

                    return redirect()->route('incomes.index')
                        ->with('success', $message)
                        ->with('warning', "{$failedCount} data gagal diimport. No. Pesanan yang gagal: " . $failedOrderNumbers) // UBAH DI SINI
                        ->with('failures', $failures)
                        ->with('failed_order_numbers', $failedOrderNumbers)
                        ->with('failed_count', $failedCount); // TAMBAHKAN
                }

                return redirect()->route('incomes.index')
                    ->with('success', $message);
            }

            // Jika tidak ada yang berhasil sama sekali
            if (count($failures) > 0) {
                $failedOrderNumbers = collect($failures)
                    ->pluck('no_pesanan')
                    ->filter(function ($value) {
                        return !empty($value) && $value !== 'Tidak diketahui';
                    })
                    ->unique()
                    ->implode(', ');

                $failedCount = count($failures); // TAMBAHKAN
                $message = "Tidak ada data yang berhasil diimport. {$failedCount} data gagal."; // GUNAKAN VARIABLE

                if (!empty($failedOrderNumbers)) {
                    $message .= " No. Pesanan yang gagal: " . $failedOrderNumbers;
                }

                return redirect()->route('incomes.import.form')
                    ->with('error', $message)
                    ->with('failures', $failures)
                    ->with('failed_order_numbers', $failedOrderNumbers)
                    ->with('failed_count', $failedCount); // TAMBAHKAN
            }

            // Jika file kosong
            return redirect()->route('incomes.import.form')
                ->with('error', 'File yang diimport tidak mengandung data yang valid.');
        } catch (\Exception $e) {
            \Log::error('Import income error: ' . $e->getMessage());
            \Log::error($e->getTraceAsString());

            return redirect()->route('incomes.import.form')
                ->with('error', 'Gagal mengimport data: ' . $e->getMessage());
        }
    }

    public function downloadTemplate()
    {
        return Excel::download(new IncomeExport, 'template-import-income.xlsx');
    }

    public function hasil(Request $request)
    {
        $periodes = Periode::orderBy('nama_periode', 'desc')->get();

        $query = Income::with(['orders.produk', 'periode.toko'])
            ->orderBy('created_at', 'desc');

        // Filter berdasarkan periode_id
        if ($request->has('periode_id') && $request->periode_id != '') {
            $query->where('periode_id', $request->periode_id);
        }

        // Filter berdasarkan rentang tanggal
        $startDate = $request->start_date;
        $endDate = $request->end_date;

        if (!$request->has('start_date') && !$request->has('end_date')) {
            $startDate = now()->startOfMonth()->toDateString();
            $endDate = now()->endOfMonth()->toDateString();
        }

        if ($startDate) {
            $query->whereDate('created_at', '>=', $startDate);
        }

        if ($endDate) {
            $query->whereDate('created_at', '<=', $endDate);
        }

        $incomes = $query->get()->map(function ($income) {
            $totalHpp = $income->orders->sum(function ($order) {
                $netQuantity = $order->jumlah - $order->returned_quantity;
                return $netQuantity * $order->produk->hpp_produk;
            });

            $laba = $income->total_penghasilan - $totalHpp;

            $income->total_hpp = $totalHpp;
            $income->laba = $laba;
            $income->persentase_laba = $income->total_penghasilan > 0 ? ($laba / $income->total_penghasilan) * 100 : 0;

            return $income;
        });

        return view('incomes.hasil', compact('incomes', 'periodes', 'startDate', 'endDate'));
    }

    public function detailhasil(Request $request)
    {
        $periodes = Periode::with('toko')->orderBy('nama_periode', 'desc')->get();
        $query = Income::with(['periode.toko'])
            ->orderBy('created_at', 'desc');

        if ($request->has('periode_id') && $request->periode_id != '') {
            $query->where('periode_id', $request->periode_id);
        } else {
            // Default: tampilkan data terbaru dengan pagination
            // Bisa juga menampilkan pesan atau memilih periode default
        }

        $incomes = $query->paginate(100);
        $totalPenghasilan = $incomes->sum('total_penghasilan');
        $totalHpp = $incomes->sum(function($income) {
            return $income->orders->sum(function($order) {
                $netQuantity = $order->jumlah - $order->returned_quantity;
                return $netQuantity * $order->produk->hpp_produk;
            });
        });
        $totalLaba = $incomes->sum(function($income) use ($totalHpp) {
            return $income->total_penghasilan -
                $income->orders->sum(function($order) {
                    $netQuantity = $order->jumlah - $order->returned_quantity;
                    return $netQuantity * $order->produk->hpp_produk;
                });
        });

        return view('incomes.detailhasil', compact(
            'incomes',
            'periodes',
            'totalPenghasilan',
            'totalHpp',
            'totalLaba'
        ));
    }

    public function exportHasil()
    {
        return Excel::download(new IncomeResultExport, 'hasil-income-' . date('Y-m-d-H-i-s') . '.xlsx');
    }

    public function deleteAll()
    {
        try {
            $incomeCount = Income::count();

            if ($incomeCount === 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tidak ada data income untuk dihapus.'
                ], 400);
            }

            // Gunakan truncate tanpa transaction
            Income::truncate();

            return response()->json([
                'success' => true,
                'message' => "Semua data income ($incomeCount data) berhasil dihapus!"
            ]);

        } catch (\Exception $e) {
            \Log::error('Delete All Incomes Error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus semua data income: ' . $e->getMessage()
            ], 500);
        }
    }

    public function deleteByPeriode(Request $request)
    {
        $request->validate([
            'periode_id' => 'required|exists:periodes,id'
        ]);

        try {
            $periode = Periode::findOrFail($request->periode_id);
            $incomeCount = Income::where('periode_id', $request->periode_id)->count();

            if ($incomeCount === 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tidak ada data income pada periode ' . $periode->nama_periode
                ], 400);
            }

            DB::transaction(function () use ($request) {
                Income::where('periode_id', $request->periode_id)->delete();
            });

            return response()->json([
                'success' => true,
                'message' => "Berhasil menghapus {$incomeCount} income dari periode {$periode->nama_periode}!"
            ]);

        } catch (\Exception $e) {
            \Log::error('Delete Incomes by Periode Error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus income: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete income by multiple periode IDs (for bulk delete)
     */
    public function deleteByMultiplePeriode(Request $request)
    {
        $request->validate([
            'periode_ids' => 'required|array',
            'periode_ids.*' => 'exists:periodes,id'
        ]);

        try {
            $periodeIds = $request->periode_ids;
            $incomeCount = Income::whereIn('periode_id', $periodeIds)->count();

            if ($incomeCount === 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tidak ada data income pada periode yang dipilih'
                ], 400);
            }

            DB::transaction(function () use ($periodeIds) {
                Income::whereIn('periode_id', $periodeIds)->delete();
            });

            return response()->json([
                'success' => true,
                'message' => "Berhasil menghapus {$incomeCount} income dari periode yang dipilih!"
            ]);

        } catch (\Exception $e) {
            \Log::error('Delete Incomes by Multiple Periode Error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus income: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Bulk attach incomes to periode
     */
    public function bulkAttachToPeriode(Request $request)
    {
        $request->validate([
            'periode_id' => 'required|exists:periodes,id',
            'income_ids' => 'required|array',
            'income_ids.*' => 'exists:incomes,id'
        ]);

        try {
            $periode = Periode::findOrFail($request->periode_id);
            $incomeCount = count($request->income_ids);

            DB::transaction(function () use ($request) {
                Income::whereIn('id', $request->income_ids)
                    ->update(['periode_id' => $request->periode_id]);
            });

            return response()->json([
                'success' => true,
                'message' => "Berhasil menghubungkan {$incomeCount} income ke periode {$periode->nama_periode}!"
            ]);

        } catch (\Exception $e) {
            \Log::error('Bulk Attach Incomes to Periode Error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Gagal menghubungkan income ke periode: ' . $e->getMessage()
            ], 500);
        }
    }
}
