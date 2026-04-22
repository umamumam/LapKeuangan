<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\Supplier;
use App\Models\SupplierTransaction;
use App\Models\SupplierTransactionDetail;
use App\Models\SupplierPayment;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class SupplierTransactionController extends Controller
{
    public function batchCreate(Request $request)
    {
        $supplierId = $request->query('supplier_id');
        if (!$supplierId) {
            return redirect()->route('supplier_transactions.index')->with('error', 'Silahkan pilih supplier terlebih dahulu.');
        }

        $supplier = Supplier::findOrFail($supplierId);
        $barangs  = Barang::where('supplier_id', $supplierId)->orderBy('namabarang')->get();

        // Calculate dates starting from 31 Dec 2025
        $baseDate = Carbon::create(2025, 12, 31);
        $dates = [];
        for ($i = 0; $i < 5; $i++) {
            $dates[] = $baseDate->copy()->addWeeks($i);
        }

        return view('supplier_transactions.batch_create', compact('supplier', 'barangs', 'dates'));
    }

    public function batchStore(Request $request)
    {
        $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'data'        => 'required|array', // Structure: data[date][barang_id] = jumlah
        ]);

        try {
            DB::beginTransaction();

            foreach ($request->data as $dateStr => $items) {
                $total_barang = 0;
                $total_uang   = 0;
                $hasItems = false;

                foreach ($items as $barang_id => $jumlah) {
                    if ($jumlah > 0) {
                        $hasItems = true;
                        break;
                    }
                }

                if (!$hasItems) continue;

                $transaction = SupplierTransaction::create([
                    'supplier_id'   => $request->supplier_id,
                    'tgl'           => $dateStr,
                    'total_barang'  => 0,
                    'total_uang'    => 0,
                    'bayar'         => 0,
                    'total_tagihan' => 0,
                ]);

                foreach ($items as $barang_id => $jumlah) {
                    if ($jumlah <= 0) continue;

                    $barang = Barang::find($barang_id);
                    $subtotal = ($barang->hpp ?? 0) * $jumlah;

                    SupplierTransactionDetail::create([
                        'supplier_transaction_id' => $transaction->id,
                        'barang_id'               => $barang_id,
                        'jumlah'                  => $jumlah,
                        'subtotal'                => $subtotal,
                    ]);

                    $total_barang += $jumlah;
                    $total_uang   += $subtotal;
                }

                $transaction->update([
                    'total_barang'  => $total_barang,
                    'total_uang'    => $total_uang,
                    'total_tagihan' => -$total_uang, // Default: unpaid
                ]);
            }

            DB::commit();
            return redirect()->route('supplier_transactions.show_supplier', $request->supplier_id)
                ->with('success', 'Batch transaksi supplier berhasil disimpan!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal menyimpan batch: ' . $e->getMessage())->withInput();
        }
    }

    public function index(Request $request)
    {
        $suppliers = Supplier::with(['transactions', 'payments'])->orderBy('nama')->get();
        
        foreach ($suppliers as $supplier) {
            $totalBelanja = $supplier->transactions->sum('total_uang');
            $totalBayar = $supplier->payments->sum('nominal');
            $supplier->sisa_nota = $totalBelanja + ($supplier->hutang_awal ?? 0) - $totalBayar;
            
            // Get unique barang names for the card preview (from master data)
            $supplier->barang_preview = Barang::where('supplier_id', $supplier->id)
                ->whereNotNull('namabarang')
                ->where('namabarang', '!=', '')
                ->distinct()
                ->limit(3)
                ->pluck('namabarang');
        }

        return view('supplier_transactions.index', compact('suppliers'));
    }

    public function matrix(Request $request)
    {
        $supplierId = $request->query('supplier_id');
        if (!$supplierId) return redirect()->route('supplier_transactions.index');

        $supplier = Supplier::findOrFail($supplierId);
        
        // Filter: only show barangs with names and belonging to this supplier
        $barangs = Barang::where('supplier_id', $supplierId)
            ->whereNotNull('namabarang')
            ->where('namabarang', '!=', '')
            ->orderBy('namabarang')
            ->orderBy('ukuran')
            ->get();

        $periods = $this->getPeriods();
        $periodIndex = $request->query('period_index', 0);
        $startDate = $periods[$periodIndex]['start'];
        $endDate = $startDate->copy()->addDays(34);

        $dates = [];
        for ($i = 0; $i < 35; $i++) {
            $dates[] = $startDate->copy()->addDays($i);
        }

        $transactions = SupplierTransaction::with('details')
            ->where('supplier_id', $supplierId)
            ->whereBetween('tgl', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
            ->get()
            ->keyBy('tgl');

        $payments = SupplierPayment::where('supplier_id', $supplierId)
            ->whereBetween('tgl', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
            ->get();

        return view('supplier_transactions.matrix', compact('supplier', 'barangs', 'dates', 'transactions', 'periods', 'periodIndex', 'startDate', 'payments'));
    }

    private function getPeriods()
    {
        $periods = [];
        $startYear = 2025;
        $baseDate = Carbon::create($startYear, 12, 31);
        
        for ($i = 0; $i < 12; $i++) {
            $start = $baseDate->copy()->addDays($i * 35);
            $periods[] = [
                'index' => $i,
                'label' => $start->translatedFormat('d M Y'),
                'start' => $start,
                'is_current' => Carbon::now()->between($start, $start->copy()->addDays(34))
            ];
        }
        return $periods;
    }

    public function saveMatrix(Request $request)
    {
        $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'data' => 'required|array',
        ]);

        try {
            DB::beginTransaction();
            foreach ($request->data as $dateStr => $items) {
                $total_barang = 0;
                $total_uang = 0;

                $transaction = SupplierTransaction::updateOrCreate(
                    ['supplier_id' => $request->supplier_id, 'tgl' => $dateStr],
                    ['total_barang' => 0, 'total_uang' => 0]
                );

                // Clear existing details for this transaction to overwrite
                SupplierTransactionDetail::where('supplier_transaction_id', $transaction->id)->delete();

                foreach ($items as $barang_id => $jumlah) {
                    $jumlah = (int)$jumlah;
                    if ($jumlah <= 0) continue;

                    $barang = Barang::find($barang_id);
                    $subtotal = ($barang->hpp ?? 0) * $jumlah;

                    SupplierTransactionDetail::create([
                        'supplier_transaction_id' => $transaction->id,
                        'barang_id' => $barang_id,
                        'jumlah' => $jumlah,
                        'subtotal' => $subtotal,
                    ]);

                    $total_barang += $jumlah;
                    $total_uang += $subtotal;
                }

                if ($total_barang > 0) {
                    $transaction->update([
                        'total_barang' => $total_barang,
                        'total_uang' => $total_uang,
                        'total_tagihan' => -$total_uang, // Default unpaid/debt
                    ]);
                } else {
                    $transaction->delete();
                }
            }
            DB::commit();
            return response()->json(['success' => true, 'message' => 'Data tersimpan!']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    public function savePayment(Request $request)
    {
        $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'tgl' => 'required|date',
            'nominal' => 'required|numeric|min:1',
        ]);

        SupplierPayment::create([
            'supplier_id' => $request->supplier_id,
            'tgl' => $request->tgl,
            'nominal' => $request->nominal,
            'keterangan' => $request->keterangan ?? 'Setoran Dana Supplier'
        ]);

        return response()->json(['success' => true, 'message' => 'Pembayaran tersimpan!']);
    }
}
