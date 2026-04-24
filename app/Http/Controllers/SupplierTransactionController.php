<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use App\Models\SupplierTransaction;
use Illuminate\Http\Request;

class SupplierTransactionController extends Controller
{
    public function index(Request $request)
    {
        $supplierId = $request->query('supplier_id');
        $suppliers = Supplier::orderBy('nama')->get();

        if (!$supplierId && $suppliers->count() > 0) {
            $supplierId = $suppliers->first()->id;
        }

        if ($supplierId) {
            $supplier = Supplier::findOrFail($supplierId);
            $transactions = SupplierTransaction::where('supplier_id', $supplierId)
                                ->orderBy('tanggal', 'asc')
                                ->orderBy('id', 'asc')
                                ->get();
                                
            $groupedTransactions = [];
            $runningTagihan = $supplier->hutang_awal;
            
            $transactionsByDate = $transactions->groupBy('tanggal');
            foreach($transactionsByDate as $date => $items) {
                $sumJumlah = $items->sum('jumlah');
                $sumTf = $items->sum('tf');
                $runningTagihan += $sumJumlah - $sumTf;
                
                // Sertakan semua item (Barang & TF) agar bisa dihapus baris per baris
                $groupedTransactions[$date] = [
                    'items' => $items,
                    'sum_tf' => $sumTf,
                    'tagihan' => $runningTagihan
                ];
            }

            return view('supplier_transactions.index', compact('suppliers', 'supplier', 'groupedTransactions', 'supplierId'));
        }

        return view('supplier_transactions.index', compact('suppliers', 'supplierId'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'tanggal' => 'required|date',
            'lusin' => 'nullable|numeric',
            'potong' => 'nullable|numeric',
            'nama_barang' => 'required|string',
            'harga' => 'required|numeric',
        ]);

        $lusin = (float)($request->lusin ?? 0);
        $potong = (float)($request->potong ?? 0);
        $harga = (float)$request->harga;

        $subtotal = floor(($lusin * $harga) + ($potong * ($harga / 12)));

        SupplierTransaction::create([
            'supplier_id' => $request->supplier_id,
            'tanggal' => $request->tanggal,
            'lusin' => $lusin,
            'potong' => $potong,
            'nama_barang' => $request->nama_barang,
            'harga' => $harga,
            'jumlah' => $subtotal,
            'tf' => 0,
        ]);

        return redirect()->route('supplier_transactions.index', ['supplier_id' => $request->supplier_id])
                            ->with('success', 'Data barang berhasil ditambahkan!');
    }

    public function storeTF(Request $request)
    {
        $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'tanggal' => 'required|date',
            'tf' => 'required|numeric|min:1',
        ]);

        SupplierTransaction::create([
            'supplier_id' => $request->supplier_id,
            'tanggal' => $request->tanggal,
            'lusin' => 0,
            'potong' => 0,
            'nama_barang' => 'Pembayaran TF',
            'harga' => 0,
            'jumlah' => 0,
            'tf' => $request->tf,
        ]);

        return redirect()->route('supplier_transactions.index', ['supplier_id' => $request->supplier_id])
                            ->with('success', 'Data TF berhasil ditambahkan!');
    }

    public function updateSisaNota(Request $request)
    {
        $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'sisa_nota' => 'required|numeric',
        ]);

        $supplier = Supplier::findOrFail($request->supplier_id);
        $supplier->hutang_awal = $request->sisa_nota;
        $supplier->save();

        return redirect()->route('supplier_transactions.index', ['supplier_id' => $request->supplier_id])
                            ->with('success', 'Sisa nota sebelumnya berhasil diperbarui!');
    }

    public function destroy($id)
    {
        $transaction = SupplierTransaction::findOrFail($id);
        $supplierId = $transaction->supplier_id;
        $transaction->delete();

        return redirect()->route('supplier_transactions.index', ['supplier_id' => $supplierId])
                            ->with('success', 'Transaksi berhasil dihapus!');
    }
}
