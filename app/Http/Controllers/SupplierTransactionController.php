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
    public function index(Request $request)
    {
        $month = $request->input('month', Carbon::now()->format('m'));
        $year  = $request->input('year', Carbon::now()->format('Y'));

        // Rekap 5 minggu global
        $rekapGlobal = [
            'minggu_1' => ['total_uang' => 0, 'bayar' => 0, 'total_tagihan' => 0],
            'minggu_2' => ['total_uang' => 0, 'bayar' => 0, 'total_tagihan' => 0],
            'minggu_3' => ['total_uang' => 0, 'bayar' => 0, 'total_tagihan' => 0],
            'minggu_4' => ['total_uang' => 0, 'bayar' => 0, 'total_tagihan' => 0],
            'minggu_5' => ['total_uang' => 0, 'bayar' => 0, 'total_tagihan' => 0],
        ];

        $allTrx = SupplierTransaction::whereYear('tgl', $year)->whereMonth('tgl', $month)->get();
        foreach ($allTrx as $trx) {
            $day = Carbon::parse($trx->tgl)->day;
            if ($day <= 7)       $week = 'minggu_1';
            elseif ($day <= 14)  $week = 'minggu_2';
            elseif ($day <= 21)  $week = 'minggu_3';
            elseif ($day <= 28)  $week = 'minggu_4';
            else                 $week = 'minggu_5';

            $rekapGlobal[$week]['total_uang']    += $trx->total_uang;
            $rekapGlobal[$week]['bayar']          += $trx->bayar;
            $rekapGlobal[$week]['total_tagihan']  += $trx->total_tagihan;
        }

        // Suppliers with debt this month
        $suppliersWithDebt = Supplier::whereHas('transactions', function ($q) use ($month, $year) {
            $q->whereYear('tgl', $year)->whereMonth('tgl', $month)->where('total_tagihan', '<', 0);
        })->withSum(['transactions as total_uang' => function ($q) use ($month, $year) {
            $q->whereYear('tgl', $year)->whereMonth('tgl', $month);
        }], 'total_uang')
        ->withSum(['transactions as total_tagihan' => function ($q) use ($month, $year) {
            $q->whereYear('tgl', $year)->whereMonth('tgl', $month);
        }], 'total_tagihan')
        ->get();

        // All suppliers cards
        $suppliers = Supplier::with(['barangs'])
            ->withSum(['transactions as total_tagihan' => function ($q) use ($month, $year) {
                $q->whereYear('tgl', $year)->whereMonth('tgl', $month);
            }], 'total_tagihan')
            ->orderBy('nama')
            ->get();

        return view('supplier_transactions.index', compact('suppliers', 'suppliersWithDebt', 'rekapGlobal', 'month', 'year'));
    }

    public function supplierShow(Request $request, Supplier $supplier)
    {
        $month = $request->input('month', Carbon::now()->format('m'));
        $year  = $request->input('year', Carbon::now()->format('Y'));

        $transactions = SupplierTransaction::with('details.barang')
            ->where('supplier_id', $supplier->id)
            ->whereYear('tgl', $year)
            ->whereMonth('tgl', $month)
            ->orderBy('tgl', 'desc')
            ->get();

        $rekap = [
            'minggu_1' => ['total_uang' => 0, 'bayar' => 0, 'total_tagihan' => 0],
            'minggu_2' => ['total_uang' => 0, 'bayar' => 0, 'total_tagihan' => 0],
            'minggu_3' => ['total_uang' => 0, 'bayar' => 0, 'total_tagihan' => 0],
            'minggu_4' => ['total_uang' => 0, 'bayar' => 0, 'total_tagihan' => 0],
            'minggu_5' => ['total_uang' => 0, 'bayar' => 0, 'total_tagihan' => 0],
        ];

        $hasDebt = false;
        foreach ($transactions as $trx) {
            if ($trx->total_tagihan < 0) $hasDebt = true;
            $day = Carbon::parse($trx->tgl)->day;
            if ($day <= 7)       $week = 'minggu_1';
            elseif ($day <= 14)  $week = 'minggu_2';
            elseif ($day <= 21)  $week = 'minggu_3';
            elseif ($day <= 28)  $week = 'minggu_4';
            else                 $week = 'minggu_5';

            $rekap[$week]['total_uang']   += $trx->total_uang;
            $rekap[$week]['bayar']         += $trx->bayar;
            $rekap[$week]['total_tagihan'] += $trx->total_tagihan;
        }

        $payments = SupplierPayment::where('supplier_id', $supplier->id)
            ->whereYear('tgl', $year)
            ->whereMonth('tgl', $month)
            ->orderBy('tgl', 'desc')
            ->get();

        return view('supplier_transactions.supplier_show', compact('supplier', 'transactions', 'rekap', 'month', 'year', 'hasDebt', 'payments'));
    }

    public function create(Request $request)
    {
        $supplierId = $request->query('supplier_id');
        $supplier = Supplier::findOrFail($supplierId);
        $barangs  = Barang::where('supplier_id', $supplierId)->orderBy('namabarang')->get();

        return view('supplier_transactions.create', compact('supplier', 'barangs'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'tgl'         => 'required|date',
            'bayar'       => 'required|numeric|min:0',
            'details'     => 'required|array|min:1',
            'details.*.barang_id' => 'required|exists:barangs,id',
            'details.*.jumlah'    => 'required|integer|min:1',
        ]);

        DB::beginTransaction();
        try {
            $totalUang     = 0;
            $totalBarang   = 0;
            $detailsData   = [];

            foreach ($request->details as $d) {
                $barang    = Barang::findOrFail($d['barang_id']);
                $hpp       = $barang->hpp ?? 0;
                $jumlah    = $d['jumlah'];
                $subtotal  = $hpp * $jumlah;

                $totalUang   += $subtotal;
                $totalBarang += $jumlah;
                $detailsData[] = [
                    'barang_id' => $barang->id,
                    'jumlah'    => $jumlah,
                    'subtotal'  => $subtotal,
                ];
            }

            $bayar        = $request->bayar;
            $totalTagihan = $bayar - $totalUang;

            $buktiPath = null;
            if ($request->hasFile('bukti_tf')) {
                $buktiPath = $request->file('bukti_tf')->store('bukti_tf', 'public');
            }

            $trx = SupplierTransaction::create([
                'supplier_id'  => $request->supplier_id,
                'tgl'          => $request->tgl,
                'total_barang' => $totalBarang,
                'total_uang'   => $totalUang,
                'bayar'        => $bayar,
                'total_tagihan'=> $totalTagihan,
                'retur'        => $request->retur ?? 0,
                'bukti_tf'     => $buktiPath,
            ]);

            foreach ($detailsData as $d) {
                $trx->details()->create($d);
            }

            // If payment is made, record it
            if ($bayar > 0) {
                SupplierPayment::create([
                    'supplier_id' => $request->supplier_id,
                    'tgl'         => $request->tgl,
                    'keterangan'  => 'Pembayaran Awal Transaksi',
                    'nominal'     => $bayar,
                    'bukti_tf'    => $buktiPath,
                ]);
            }

            DB::commit();
            return redirect()->route('supplier_transactions.show_supplier', $request->supplier_id)
                ->with('success', 'Transaksi supplier berhasil ditambahkan.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal menyimpan: ' . $e->getMessage())->withInput();
        }
    }

    public function edit(SupplierTransaction $supplierTransaction)
    {
        $supplier = $supplierTransaction->supplier;
        $barangs  = Barang::where('supplier_id', $supplier->id)->orderBy('namabarang')->get();
        $supplierTransaction->load('details.barang');
        return view('supplier_transactions.edit', compact('supplierTransaction', 'supplier', 'barangs'));
    }

    public function update(Request $request, SupplierTransaction $supplierTransaction)
    {
        $request->validate([
            'tgl'     => 'required|date',
            'bayar'   => 'required|numeric|min:0',
            'details' => 'required|array|min:1',
        ]);

        DB::beginTransaction();
        try {
            $totalUang   = 0;
            $totalBarang = 0;
            $detailsData = [];

            foreach ($request->details as $d) {
                $barang   = Barang::findOrFail($d['barang_id']);
                $hpp      = $barang->hpp ?? 0;
                $jumlah   = $d['jumlah'];
                $subtotal = $hpp * $jumlah;

                $totalUang   += $subtotal;
                $totalBarang += $jumlah;
                $detailsData[] = ['barang_id' => $barang->id, 'jumlah' => $jumlah, 'subtotal' => $subtotal];
            }

            $bayar        = $request->bayar;
            $totalTagihan = $bayar - $totalUang;

            $buktiPath = $supplierTransaction->bukti_tf;
            if ($request->hasFile('bukti_tf')) {
                if ($buktiPath) Storage::disk('public')->delete($buktiPath);
                $buktiPath = $request->file('bukti_tf')->store('bukti_tf', 'public');
            }

            $supplierTransaction->update([
                'tgl'          => $request->tgl,
                'total_barang' => $totalBarang,
                'total_uang'   => $totalUang,
                'bayar'        => $bayar,
                'total_tagihan'=> $totalTagihan,
                'retur'        => $request->retur ?? 0,
                'bukti_tf'     => $buktiPath,
            ]);

            $supplierTransaction->details()->delete();
            foreach ($detailsData as $d) {
                $supplierTransaction->details()->create($d);
            }

            DB::commit();
            return redirect()->route('supplier_transactions.show_supplier', $supplierTransaction->supplier_id)
                ->with('success', 'Transaksi berhasil diperbarui.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal mengubah: ' . $e->getMessage())->withInput();
        }
    }

    public function destroy(SupplierTransaction $supplierTransaction)
    {
        $supplierId = $supplierTransaction->supplier_id;
        if ($supplierTransaction->bukti_tf) {
            Storage::disk('public')->delete($supplierTransaction->bukti_tf);
        }
        $supplierTransaction->delete();
        return redirect()->route('supplier_transactions.show_supplier', $supplierId)
            ->with('success', 'Transaksi berhasil dihapus.');
    }

    public function payDebt(Request $request, Supplier $supplier)
    {
        $request->validate([
            'nominal' => 'required|numeric|min:1',
            'tgl'     => 'required|date',
            'bukti_tf' => 'required|image',
        ]);

        DB::beginTransaction();
        try {
            $nominal = $request->nominal;

            // Store bukti
            $buktiPath = $request->file('bukti_tf')->store('bukti_tf', 'public');

            // Record in payments table
            SupplierPayment::create([
                'supplier_id' => $supplier->id,
                'tgl'         => $request->tgl,
                'keterangan'  => 'Pelunasan Tagihan',
                'nominal'     => $nominal,
                'bukti_tf'    => $buktiPath,
            ]);

            // Auto-apply payment to oldest debts
            $debtTransactions = SupplierTransaction::where('supplier_id', $supplier->id)
                ->where('total_tagihan', '<', 0)
                ->orderBy('tgl', 'asc')
                ->orderBy('id', 'asc')
                ->get();

            foreach ($debtTransactions as $trx) {
                if ($nominal <= 0) break;

                $hutang = abs($trx->total_tagihan);
                if ($nominal >= $hutang) {
                    $trx->bayar += $hutang;
                    $trx->total_tagihan = 0;
                    $nominal -= $hutang;
                } else {
                    $trx->bayar += $nominal;
                    $trx->total_tagihan += $nominal;
                    $nominal = 0;
                }
                $trx->save();
            }

            DB::commit();
            return redirect()->back()->with('success', 'Pembayaran tagihan supplier berhasil dicatat.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal: ' . $e->getMessage());
        }
    }
}
