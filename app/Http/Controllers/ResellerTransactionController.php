<?php

namespace App\Http\Controllers;

use App\Models\Reseller;
use App\Models\Barang;
use App\Models\ResellerTransaction;
use App\Models\ResellerTransactionDetail;
use App\Models\ResellerPayment;
use App\Models\ResellerPeriod;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ResellerTransactionController extends Controller
{
    public function index(Request $request)
    {
        $type = $request->query('type', 'grosir'); // default to grosir
        $resellers = Reseller::with(['transactions', 'payments'])->orderBy('nama')->get();
        
        foreach ($resellers as $reseller) {
            $totalUang = $reseller->transactions->sum('total_uang');
            $totalBayar = $reseller->payments->sum('nominal');
            $reseller->sisa_nota = $totalUang + ($reseller->hutang_awal ?? 0) - $totalBayar;
            
            // Get unique barang names for the card preview
            $reseller->barang_preview = DB::table('reseller_transaction_details')
                ->join('reseller_transactions', 'reseller_transaction_details.reseller_transaction_id', '=', 'reseller_transactions.id')
                ->join('barangs', 'reseller_transaction_details.barang_id', '=', 'barangs.id')
                ->where('reseller_transactions.reseller_id', $reseller->id)
                ->distinct()
                ->limit(3)
                ->pluck('barangs.namabarang');
        }

        return view('reseller_transactions.index', compact('resellers', 'type'));
    }

    public function matrix(Request $request)
    {
        $resellerId = $request->query('reseller_id');
        $type = $request->query('type', 'grosir');

        if (!$resellerId) {
            return redirect()->route('reseller_transactions.index', ['type' => $type])->with('error', 'Silahkan pilih reseller terlebih dahulu.');
        }

        $reseller = Reseller::findOrFail($resellerId);
        
        $barangs = Barang::where(function($q) use ($resellerId) {
                $q->where(function($q2) {
                    $q2->whereNull('reseller_id')->whereNull('supplier_id');
                })->orWhere('reseller_id', $resellerId);
            })
            ->whereNotNull('namabarang')
            ->where('namabarang', '!=', '')
            ->orderBy('id', 'asc')
            ->get();
        
        foreach ($barangs as $barang) {
            $barang->display_price = ($type == 'hpp') ? ($barang->hpp ?? 0) : ($barang->harga_grosir ?? 0);
        }
        
        $periods = ResellerPeriod::orderBy('id', 'asc')->get();
        
        $periodId = $request->query('period_id');
        $selectedPeriod = null;

        if ($periodId) {
            $selectedPeriod = ResellerPeriod::find($periodId);
        }

        if (!$selectedPeriod && $periods->count() > 0) {
            $selectedPeriod = $periods->first();
            $periodId = $selectedPeriod->id;
        }

        if ($selectedPeriod) {
            $startDate = Carbon::parse($selectedPeriod->start_date);
            $endDate = Carbon::parse($selectedPeriod->end_date);
            $diffInDays = $startDate->diffInDays($endDate) + 1;
        } else {
            // Fallback to default 35 days logic if no periods exist
            $startDate = Carbon::parse('2025-12-31');
            $endDate = $startDate->copy()->addDays(34);
            $diffInDays = 35;
        }
        
        $dates = [];
        for ($i = 0; $i < $diffInDays; $i++) {
            $dates[] = $startDate->copy()->addDays($i);
        }
        
        $transactions = ResellerTransaction::with('details')
            ->where('reseller_id', $resellerId)
            ->whereBetween('tgl', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
            ->get()
            ->keyBy('tgl');

        $payments = ResellerPayment::where('reseller_id', $resellerId)
            ->whereBetween('tgl', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
            ->get();

        // Global Totals for Summary
        $totalUangGlobal = ResellerTransaction::where('reseller_id', $resellerId)->sum('total_uang');
        $totalBayarGlobal = ResellerPayment::where('reseller_id', $resellerId)->sum('nominal');
        $globalSisa = ($reseller->hutang_awal ?? 0) + $totalUangGlobal - $totalBayarGlobal;

        // Calculate Previous Balance (Sisa Sebelum Periode Ini)
        $uangSebelumnya = ResellerTransaction::where('reseller_id', $resellerId)
            ->where('tgl', '<', $startDate->format('Y-m-d'))
            ->sum('total_uang');
        $bayarSebelumnya = ResellerPayment::where('reseller_id', $resellerId)
            ->where('tgl', '<', $startDate->format('Y-m-d'))
            ->sum('nominal');
        $sisaSebelumnya = ($reseller->hutang_awal ?? 0) + $uangSebelumnya - $bayarSebelumnya;

        return view('reseller_transactions.matrix', compact(
            'reseller', 'barangs', 'dates', 'transactions', 'payments', 
            'startDate', 'endDate', 'periods', 'periodId', 'type',
            'globalSisa', 'sisaSebelumnya', 'totalUangGlobal', 'totalBayarGlobal'
        ));
    }

    public function storePeriod(Request $request)
    {
        $request->validate([
            'title' => 'required',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        // Check for overlaps
        $overlap = ResellerPeriod::where(function($q) use ($request) {
            $q->whereBetween('start_date', [$request->start_date, $request->end_date])
              ->orWhereBetween('end_date', [$request->start_date, $request->end_date])
              ->orWhere(function($q2) use ($request) {
                  $q2->where('start_date', '<=', $request->start_date)
                     ->where('end_date', '>=', $request->end_date);
              });
        })->exists();

        if ($overlap) {
            return back()->with('error', 'Tanggal tersebut sudah masuk dalam range periode lain.');
        }

        ResellerPeriod::create($request->all());

        return back()->with('success', 'Periode berhasil ditambahkan.');
    }

    public function updatePeriod(Request $request, $id)
    {
        $request->validate([
            'title' => 'required',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        // Check for overlaps (excluding current ID)
        $overlap = ResellerPeriod::where('id', '!=', $id)
            ->where(function($q) use ($request) {
                $q->whereBetween('start_date', [$request->start_date, $request->end_date])
                  ->orWhereBetween('end_date', [$request->start_date, $request->end_date])
                  ->orWhere(function($q2) use ($request) {
                      $q2->where('start_date', '<=', $request->start_date)
                         ->where('end_date', '>=', $request->end_date);
                  });
            })->exists();

        if ($overlap) {
            return back()->with('error', 'Tanggal tersebut sudah masuk dalam range periode lain.');
        }

        $period = ResellerPeriod::findOrFail($id);
        $period->update($request->all());

        return back()->with('success', 'Periode berhasil diperbarui.');
    }

    public function destroyPeriod($id)
    {
        ResellerPeriod::findOrFail($id)->delete();
        return back()->with('success', 'Periode berhasil dihapus.');
    }

    public function saveMatrix(Request $request)
    {
        $resellerId = $request->reseller_id;
        $type = $request->input('type', 'grosir');
        $data = $request->input('data', []); // [date][barang_id] = jumlah

        try {
            DB::beginTransaction();

            foreach ($data as $date => $items) {
                // Check if any items have jumlah > 0
                $hasItems = false;
                foreach ($items as $qty) {
                    if ($qty > 0) { $hasItems = true; break; }
                }

                $transaction = ResellerTransaction::where('reseller_id', $resellerId)
                    ->where('tgl', $date)
                    ->first();

                if (!$hasItems) {
                    if ($transaction) {
                        $transaction->details()->delete();
                        $transaction->delete();
                    }
                    continue;
                }

                if (!$transaction) {
                    $transaction = ResellerTransaction::create([
                        'reseller_id' => $resellerId,
                        'tgl' => $date,
                    ]);
                }

                $totalBarang = 0;
                $totalUang = 0;

                // Sync details
                $transaction->details()->delete();
                foreach ($items as $barangId => $jumlah) {
                    $jumlah = (int) $jumlah;
                    if ($jumlah <= 0) continue;

                    $barang = Barang::find($barangId);
                    if (!$barang) continue;

                    // If HPP is used, use hpp field, otherwise use harga_grosir
                    $price = ($type == 'hpp') ? ($barang->hpp ?? 0) : ($barang->harga_grosir ?? 0);
                    $subtotal = $jumlah * $price;

                    ResellerTransactionDetail::create([
                        'reseller_transaction_id' => $transaction->id,
                        'barang_id' => $barangId,
                        'jumlah' => $jumlah,
                        'subtotal' => $subtotal,
                    ]);

                    $totalBarang += $jumlah;
                    $totalUang += $subtotal;
                }

                $transaction->update([
                    'total_barang' => $totalBarang,
                    'total_uang' => $totalUang,
                    'sisa_kurang' => $transaction->bayar - $totalUang,
                ]);
            }

            DB::commit();
            return response()->json(['success' => true, 'message' => 'Data berhasil disimpan!']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function savePayment(Request $request)
    {
        $request->validate([
            'reseller_id' => 'required',
            'tgl' => 'required|date',
            'nominal' => 'required|numeric',
        ]);

        ResellerPayment::create($request->all());

        return response()->json(['success' => true]);
    }

    public function updateSisaNota(Request $request)
    {
        $request->validate([
            'reseller_id' => 'required|exists:resellers,id',
            'sisa_nota' => 'required|numeric',
        ]);

        $reseller = Reseller::findOrFail($request->reseller_id);
        $reseller->update([
            'hutang_awal' => $request->sisa_nota
        ]);

        return back()->with('success', 'Hutang awal berhasil diperbarui.');
    }

    public function resetTransactions(Request $request)
    {
        $request->validate([
            'reseller_id' => 'required|exists:resellers,id',
        ]);

        $resellerId = $request->reseller_id;

        try {
            DB::beginTransaction();

            // Delete details first
            DB::table('reseller_transaction_details')
                ->whereIn('reseller_transaction_id', function($query) use ($resellerId) {
                    $query->select('id')
                        ->from('reseller_transactions')
                        ->where('reseller_id', $resellerId);
                })->delete();

            // Delete transactions
            ResellerTransaction::where('reseller_id', $resellerId)->delete();

            DB::commit();
            return response()->json(['success' => true, 'message' => 'Semua transaksi berhasil direset.']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}
