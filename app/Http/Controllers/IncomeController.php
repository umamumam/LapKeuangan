<?php

namespace App\Http\Controllers;

use App\Models\Toko;
use App\Models\Order;
use App\Models\Income;
use Illuminate\Http\Request;
use App\Exports\IncomeExport;
use App\Imports\IncomeImport;
use Illuminate\Support\Facades\DB;
use App\Exports\IncomeResultExport;
use Maatwebsite\Excel\Facades\Excel;

class IncomeController extends Controller
{
    public function index()
    {
        $incomes = Income::with(['orders.produk', 'toko'])->orderBy('id', 'desc')->paginate(200);
        $totalIncomes = Order::count();
        return view('incomes.index', compact('incomes', 'totalIncomes'));
    }

    public function create()
    {
        $orders = Order::select('no_pesanan')->distinct()->get();
        $tokos = Toko::all();
        return view('incomes.create', compact('orders', 'tokos'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'no_pesanan' => 'required|string|max:100|unique:incomes,no_pesanan',
            'no_pengajuan' => 'nullable|string|max:100',
            'total_penghasilan' => 'required|integer',
            'toko_id' => 'required|exists:tokos,id',
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
        $income->load(['orders.produk', 'toko']);
        return view('incomes.show', compact('income'));
    }

    public function edit(Income $income)
    {
        $orders = Order::select('no_pesanan')->distinct()->get();
        $tokos = Toko::all();
        return view('incomes.edit', compact('income', 'orders', 'tokos'));
    }

    public function update(Request $request, Income $income)
    {
        $request->validate([
            'no_pesanan' => 'required|string|max:100|unique:incomes,no_pesanan,' . $income->id,
            'no_pengajuan' => 'nullable|string|max:100',
            'total_penghasilan' => 'required|integer',
            'toko_id' => 'required|exists:tokos,id',
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

            $income->load(['orders.produk']);
            $total = $income->orders->sum(function ($order) {
                $netQuantity = $order->jumlah - $order->returned_quantity;
                return $netQuantity * $order->produk->hpp_produk;
            });

            $income->update(['total_penghasilan' => $total]);
            DB::commit();

            return redirect()->route('incomes.show', $income)
                ->with('success', 'Total penghasilan berhasil dihitung otomatis: Rp ' . number_format($total));
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
            // Ambil toko_id dari order pertama atau default ke toko pertama
            $toko_id = $orders->first()->toko_id ?? Toko::first()->id ?? null;

            if (!$toko_id) {
                return redirect()->back()
                    ->with('error', 'Tidak ada toko yang tersedia. Silahkan buat toko terlebih dahulu.');
            }
            $income = Income::create([
                'no_pesanan' => $noPesanan,
                'no_pengajuan' => $noPengajuan,
                'total_penghasilan' => $total,
                'toko_id' => $toko_id,
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

    public function importForm()
    {
        $tokos = Toko::all();
        return view('incomes.import', compact('tokos'));
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv|max:5120',
            'default_toko_id' => 'nullable|exists:tokos,id'
        ]);

        try {
            $import = new IncomeImport($request->default_toko_id);
            Excel::import($import, $request->file('file'));

            $failures = $import->getFailedOrders();
            $successCount = $import->getSuccessCount();
            $totalRows = $import->getRowCount();

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
                        ->with('warning', "Beberapa data gagal diimport. Cek detail untuk informasi lebih lanjut.")
                        ->with('failures', $failures)
                        ->with('failed_order_numbers', $failedOrderNumbers);
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

                $message = "Tidak ada data yang berhasil diimport. " . count($failures) . " data gagal.";

                if (!empty($failedOrderNumbers)) {
                    $message .= " No. Pesanan yang gagal: " . $failedOrderNumbers;
                }

                return redirect()->route('incomes.import.form')
                    ->with('error', $message)
                    ->with('failures', $failures)
                    ->with('failed_order_numbers', $failedOrderNumbers);
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
        $tokos = Toko::all();

        $query = Income::with(['orders.produk', 'toko'])
            ->orderBy('created_at', 'desc');

        if ($request->has('toko_id') && $request->toko_id != '') {
            $query->where('toko_id', $request->toko_id);
        }

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

        return view('incomes.hasil', compact('incomes', 'tokos', 'startDate', 'endDate'));
    }

    public function detailhasil(Request $request)
    {
        $tokos = Toko::all();

        $query = Income::with(['orders.produk', 'toko'])
            ->orderBy('created_at', 'desc');

        if ($request->has('toko_id') && $request->toko_id != '') {
            $query->where('toko_id', $request->toko_id);
        }

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

        return view('incomes.detailhasil', compact('incomes', 'tokos', 'startDate', 'endDate'));
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
                return redirect()->route('incomes.index')
                    ->with('warning', 'Tidak ada data income untuk dihapus.');
            }

            // Gunakan transaction untuk keamanan
            DB::transaction(function () {
                // Hapus semua data income
                Income::query()->delete();
            });

            return redirect()->route('incomes.index')
                ->with('success', "Semua data income ($incomeCount data) berhasil dihapus!");

        } catch (\Exception $e) {
            \Log::error('Delete All Incomes Error: ' . $e->getMessage());

            return redirect()->route('incomes.index')
                ->with('error', 'Gagal menghapus semua data income: ' . $e->getMessage());
        }
    }
}
