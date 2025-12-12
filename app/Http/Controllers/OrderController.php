<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Produk;
use App\Models\Periode; // DITAMBAHKAN
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Exports\OrderExport;
use App\Imports\OrderImport;
use Maatwebsite\Excel\Facades\Excel;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $orders = Order::with('produk', 'periode')->orderBy('id', 'desc')->paginate(200);
        $totalOrders = Order::count();
        return view('orders.index', compact('orders', 'totalOrders'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $produks = Produk::all();
        $periodes = Periode::orderBy('nama_periode', 'desc')->get();
        return view('orders.create', compact('produks', 'periodes'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'no_pesanan' => 'required|string|max:100',
            'no_resi' => 'nullable|string|max:100',
            'produk_id' => 'required|exists:produks,id',
            'jumlah' => 'required|integer|min:1',
            'returned_quantity' => 'nullable|integer|min:0',
            'total_harga_produk' => 'required|integer|min:0',
            'periode_id' => 'nullable|exists:periodes,id', // DITAMBAHKAN
        ]);

        try {
            Order::create($request->all());
            return redirect()->route('orders.index')
                ->with('success', 'Order berhasil ditambahkan!');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Gagal menambahkan order: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Order $order)
    {
        $order->load('produk', 'periode');
        return view('orders.show', compact('order'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Order $order)
    {
        $produks = Produk::all();
        $periodes = Periode::orderBy('nama_periode', 'desc')->get();
        return view('orders.edit', compact('order', 'produks', 'periodes'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Order $order)
    {
        $request->validate([
            'no_pesanan' => 'required|string|max:100',
            'no_resi' => 'nullable|string|max:100',
            'produk_id' => 'required|exists:produks,id',
            'jumlah' => 'required|integer|min:1',
            'returned_quantity' => 'nullable|integer|min:0',
            'total_harga_produk' => 'required|integer|min:0',
            'periode_id' => 'nullable|exists:periodes,id', // DITAMBAHKAN
        ]);

        // Validasi returned_quantity tidak boleh lebih besar dari jumlah
        if ($request->returned_quantity > $request->jumlah) {
            return redirect()->back()
                ->with('error', 'Returned quantity tidak boleh lebih besar dari jumlah')
                ->withInput();
        }

        try {
            $order->update($request->all());
            return redirect()->route('orders.index')
                ->with('success', 'Order berhasil diperbarui!');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Gagal memperbarui order: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Order $order)
    {
        try {
            $order->delete();
            return redirect()->route('orders.index')
                ->with('success', 'Order berhasil dihapus!');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Gagal menghapus order: ' . $e->getMessage());
        }
    }

    /**
     * Export orders to Excel
     */
    public function export()
    {
        return Excel::download(new OrderExport, 'orders-' . date('Y-m-d-H-i-s') . '.xlsx');
    }

    /**
     * Show import form
     */
    public function importForm()
    {
        return view('orders.import');
    }

    /**
     * Import orders from Excel
     */
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv|max:5120'
        ]);

        try {
            $import = new OrderImport;

            // Gunakan try-catch untuk import
            Excel::import($import, $request->file('file'));

            $failures = $import->failures();
            $failedOrders = $import->getFailedOrders();
            $successCount = $import->getRowCount() - count($failures);

            // Gabungkan failures dengan failed orders
            $allFailures = [];

            // Convert failures to array dengan no_pesanan
            foreach ($failures as $failure) {
                $values = $failure->values();
                $allFailures[] = [
                    'no_pesanan' => $values['no_pesanan'] ?? 'Tidak diketahui',
                    'row' => $failure->row(),
                    'attribute' => $failure->attribute(),
                    'errors' => $failure->errors(),
                    'values' => $values,
                    'reason' => implode(', ', $failure->errors())
                ];
            }

            // Tambahkan failed orders dari model
            foreach ($failedOrders as $failedOrder) {
                $allFailures[] = [
                    'no_pesanan' => $failedOrder['no_pesanan'],
                    'row' => $failedOrder['row'] ?? 'Tidak diketahui',
                    'attribute' => 'general',
                    'errors' => [$failedOrder['reason']],
                    'values' => [
                        'no_pesanan' => $failedOrder['no_pesanan'],
                        'nama_produk' => $failedOrder['nama_produk'],
                        'nama_variasi' => $failedOrder['nama_variasi']
                    ],
                    'reason' => $failedOrder['reason']
                ];
            }

            // Jika ada failures, tampilkan pesan warning
            if (count($allFailures) > 0) {
                $failedOrderNumbers = collect($allFailures)->pluck('no_pesanan')->unique()->implode(', ');

                $message = "Berhasil mengimport {$successCount} data. " . count($allFailures) . " data gagal diimport.";
                $message .= " No. Pesanan yang gagal: " . $failedOrderNumbers;

                return redirect()->route('orders.import.form')
                    ->with('warning', $message)
                    ->with('failures', $allFailures)
                    ->with('failed_order_numbers', $failedOrderNumbers);
            }

            // Jika sukses semua, redirect ke index dengan pesan sukses
            return redirect()->route('orders.index')
                ->with('success', "Berhasil mengimport {$successCount} data order!");
        } catch (\Exception $e) {
            \Log::error('Import Error: ' . $e->getMessage());
            \Log::error($e->getTraceAsString());

            return redirect()->route('orders.import.form')
                ->with('error', 'Gagal mengimport data: ' . $e->getMessage());
        }
    }

    /**
     * Download template for import
     */
    public function downloadTemplate()
    {
        return Excel::download(new OrderExport, 'template-import-order.xlsx');
    }

    public function deleteAll()
    {
        try {
            $orderCount = Order::count();

            if ($orderCount === 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tidak ada data order untuk dihapus.'
                ], 400);
            }

            // Gunakan truncate tanpa transaction
            Order::truncate();

            return response()->json([
                'success' => true,
                'message' => "Semua data order ($orderCount data) berhasil dihapus!"
            ]);

        } catch (\Exception $e) {
            \Log::error('Delete All Orders Error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus semua data order: ' . $e->getMessage()
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
            $orderCount = Order::where('periode_id', $request->periode_id)->count();

            if ($orderCount === 0) {
                // Return JSON untuk AJAX
                return response()->json([
                    'success' => false,
                    'message' => 'Tidak ada data order pada periode ' . $periode->nama_periode
                ], 400);
            }

            DB::transaction(function () use ($request) {
                Order::where('periode_id', $request->periode_id)->delete();
            });

            // Return JSON untuk AJAX
            return response()->json([
                'success' => true,
                'message' => "Berhasil menghapus {$orderCount} order dari periode {$periode->nama_periode}!"
            ]);

        } catch (\Exception $e) {
            \Log::error('Delete Orders by Periode Error: ' . $e->getMessage());

            // Return JSON untuk AJAX
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus order: ' . $e->getMessage()
            ], 500);
        }
    }
}
