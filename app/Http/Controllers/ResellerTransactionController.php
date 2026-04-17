<?php

namespace App\Http\Controllers;

use App\Models\ResellerTransaction;
use App\Models\ResellerTransactionDetail;
use App\Models\Reseller;
use App\Models\Barang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;

class ResellerTransactionController extends Controller
{
    public function rekap(Request $request)
    {
        $month = $request->input('month', Carbon::now()->format('m'));
        $year = $request->input('year', Carbon::now()->format('Y'));

        $transactions = ResellerTransaction::with('reseller')
            ->whereYear('tgl', $year)
            ->whereMonth('tgl', $month)
            ->orderBy('tgl', 'asc')
            ->get();

        $rekap = [
            'minggu_1' => ['total_uang' => 0, 'bayar' => 0, 'sisa_kurang' => 0],
            'minggu_2' => ['total_uang' => 0, 'bayar' => 0, 'sisa_kurang' => 0],
            'minggu_3' => ['total_uang' => 0, 'bayar' => 0, 'sisa_kurang' => 0],
            'minggu_4' => ['total_uang' => 0, 'bayar' => 0, 'sisa_kurang' => 0],
        ];

        foreach ($transactions as $trx) {
            $day = Carbon::parse($trx->tgl)->day;
            if ($day <= 7) {
                $week = 'minggu_1';
            } elseif ($day <= 14) {
                $week = 'minggu_2';
            } elseif ($day <= 21) {
                $week = 'minggu_3';
            } else {
                $week = 'minggu_4';
            }

            $rekap[$week]['total_uang'] += $trx->total_uang;
            $rekap[$week]['bayar'] += $trx->bayar;
            $rekap[$week]['sisa_kurang'] += $trx->sisa_kurang;
        }

        // Summary per reseller for the month
        $resellerSummary = ResellerTransaction::with('reseller')
            ->select(
                'reseller_id',
                DB::raw('SUM(total_uang) as sum_total_uang'),
                DB::raw('SUM(bayar) as sum_bayar'),
                DB::raw('SUM(sisa_kurang) as sum_sisa_kurang')
            )
            ->whereYear('tgl', $year)
            ->whereMonth('tgl', $month)
            ->groupBy('reseller_id')
            ->get();

        return view('reseller_transactions.rekap', compact('transactions', 'rekap', 'month', 'year', 'resellerSummary'));
    }

    public function index(Request $request)
    {
        $month = $request->input('month', Carbon::now()->format('m'));
        $year = $request->input('year', Carbon::now()->format('Y'));

        $resellers = Reseller::with(['barangs'])->orderBy('nama')->get();

        $allTransactions = ResellerTransaction::whereYear('tgl', $year)
            ->whereMonth('tgl', $month)
            ->get();

        // 5 Weeks Global Recap
        $rekapGlobal = [
            'minggu_1' => ['total_uang' => 0, 'bayar' => 0, 'sisa_kurang' => 0],
            'minggu_2' => ['total_uang' => 0, 'bayar' => 0, 'sisa_kurang' => 0],
            'minggu_3' => ['total_uang' => 0, 'bayar' => 0, 'sisa_kurang' => 0],
            'minggu_4' => ['total_uang' => 0, 'bayar' => 0, 'sisa_kurang' => 0],
            'minggu_5' => ['total_uang' => 0, 'bayar' => 0, 'sisa_kurang' => 0],
        ];

        foreach ($allTransactions as $trx) {
            $day = Carbon::parse($trx->tgl)->day;
            if ($day <= 7) {
                $week = 'minggu_1';
            } elseif ($day <= 14) {
                $week = 'minggu_2';
            } elseif ($day <= 21) {
                $week = 'minggu_3';
            } elseif ($day <= 28) {
                $week = 'minggu_4';
            } else {
                $week = 'minggu_5';
            }

            $rekapGlobal[$week]['total_uang'] += $trx->total_uang;
            $rekapGlobal[$week]['bayar'] += $trx->bayar;
            $rekapGlobal[$week]['sisa_kurang'] += $trx->sisa_kurang;
        }

        foreach ($resellers as $reseller) {
            $trx = $allTransactions->where('reseller_id', $reseller->id);
            $reseller->total_uang = $trx->sum('total_uang');
            $reseller->bayar = $trx->sum('bayar');
            $reseller->sisa_kurang = $trx->sum('sisa_kurang');
        }

        // Orang yang Sisa/Kurang < 0 (berhutang/tagihan)
        $resellersWithDebt = $resellers->filter(function($r) {
            return $r->sisa_kurang < 0;
        })->values();

        return view('reseller_transactions.index', compact('resellers', 'rekapGlobal', 'resellersWithDebt', 'month', 'year'));
    }

    public function resellerShow(Request $request, Reseller $reseller)
    {
        $month = $request->input('month', Carbon::now()->format('m'));
        $year = $request->input('year', Carbon::now()->format('Y'));

        $transactions = ResellerTransaction::with('details.barang')
            ->where('reseller_id', $reseller->id)
            ->whereYear('tgl', $year)
            ->whereMonth('tgl', $month)
            ->orderBy('tgl', 'desc')
            ->get();

        $rekap = [
            'minggu_1' => ['total_uang' => 0, 'bayar' => 0, 'sisa_kurang' => 0],
            'minggu_2' => ['total_uang' => 0, 'bayar' => 0, 'sisa_kurang' => 0],
            'minggu_3' => ['total_uang' => 0, 'bayar' => 0, 'sisa_kurang' => 0],
            'minggu_4' => ['total_uang' => 0, 'bayar' => 0, 'sisa_kurang' => 0],
            'minggu_5' => ['total_uang' => 0, 'bayar' => 0, 'sisa_kurang' => 0],
        ];

        $hasDebt = false;

        foreach ($transactions as $trx) {
            if ($trx->sisa_kurang < 0) {
                $hasDebt = true;
            }

            $day = Carbon::parse($trx->tgl)->day;
            if ($day <= 7) {
                $week = 'minggu_1';
            } elseif ($day <= 14) {
                $week = 'minggu_2';
            } elseif ($day <= 21) {
                $week = 'minggu_3';
            } elseif ($day <= 28) {
                $week = 'minggu_4';
            } else {
                $week = 'minggu_5';
            }

            $rekap[$week]['total_uang'] += $trx->total_uang;
            $rekap[$week]['bayar'] += $trx->bayar;
            $rekap[$week]['sisa_kurang'] += $trx->sisa_kurang;
        }

        return view('reseller_transactions.reseller_show', compact('reseller', 'transactions', 'rekap', 'month', 'year', 'hasDebt'));
    }

    public function create(Request $request)
    {
        $resellerId = $request->query('reseller_id');

        if (!$resellerId) {
            return redirect()->route('reseller_transactions.index')->with('error', 'Silahkan pilih reseller terlebih dahulu.');
        }

        $reseller = Reseller::findOrFail($resellerId);

        // Pass all barangs - JS will filter by reseller logic
        $barangs = Barang::orderBy('namabarang')->get();

        return view('reseller_transactions.create', compact('reseller', 'barangs'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'reseller_id' => 'required|exists:resellers,id',
            'tgl' => 'required|date',
            'bayar' => 'required|integer',
            'details' => 'required|array|min:1',
            'details.*.barang_id' => 'required|exists:barangs,id',
            'details.*.jumlah' => 'required|integer|min:1',
        ]);

        try {
            DB::beginTransaction();

            $total_barang = 0;
            $total_uang = 0;

            $buktiPath = null;
            if ($request->hasFile('bukti_tf')) {
                $buktiPath = $request->file('bukti_tf')->store('bukti_tf', 'public');
            }

            // Save parent first to get ID
            $transaction = ResellerTransaction::create([
                'reseller_id' => $request->reseller_id,
                'tgl' => $request->tgl,
                'total_barang' => 0,
                'total_uang' => 0,
                'bayar' => $request->bayar,
                'sisa_kurang' => 0,
                'retur' => $request->retur ?? 0,
                'bukti_tf' => $buktiPath,
            ]);

            foreach ($request->details as $detail) {
                // Use subtotal from form (already calculated by JS based on selected price mode)
                $subtotal = isset($detail['subtotal']) ? (int)$detail['subtotal'] : 0;
                if ($subtotal <= 0) {
                    $barang = Barang::find($detail['barang_id']);
                    $subtotal = ($barang->hpp ?? 0) * $detail['jumlah'];
                }

                ResellerTransactionDetail::create([
                    'reseller_transaction_id' => $transaction->id,
                    'barang_id' => $detail['barang_id'],
                    'jumlah' => $detail['jumlah'],
                    'subtotal' => $subtotal,
                ]);

                $total_barang += $detail['jumlah'];
                $total_uang += $subtotal;
            }

            $sisa_kurang = $request->bayar - $total_uang;

            $transaction->update([
                'total_barang' => $total_barang,
                'total_uang' => $total_uang,
                'sisa_kurang' => $sisa_kurang
            ]);

            DB::commit();

            return redirect()->route('reseller_transactions.show_reseller', $request->reseller_id)->with('success', 'Transaksi reseller berhasil ditambahkan!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal menambahkan transaksi: ' . $e->getMessage())->withInput();
        }
    }

    public function show(ResellerTransaction $resellerTransaction)
    {
        $resellerTransaction->load(['reseller', 'details.barang']);
        return view('reseller_transactions.show', compact('resellerTransaction'));
    }

    public function destroy(ResellerTransaction $resellerTransaction)
    {
        try {
            $resellerTransaction->delete();
            return redirect()->back()->with('success', 'Transaksi berhasil dihapus!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal menghapus transaksi: ' . $e->getMessage());
        }
    }
    public function edit(ResellerTransaction $resellerTransaction)
    {
        $resellerTransaction->load('details');
        $reseller = Reseller::findOrFail($resellerTransaction->reseller_id);
        // Pass all barangs - JS will filter: show reseller's own barangs,
        // or if reseller has none, show umum (null reseller_id AND null supplier_id)
        $barangs = Barang::orderBy('namabarang')->get();

        return view('reseller_transactions.edit', compact('resellerTransaction', 'reseller', 'barangs'));
    }

    public function update(Request $request, ResellerTransaction $resellerTransaction)
    {
        $request->validate([
            'reseller_id' => 'required|exists:resellers,id',
            'tgl' => 'required|date',
            'bayar' => 'required|integer',
            'details' => 'required|array|min:1',
            'details.*.barang_id' => 'required|exists:barangs,id',
            'details.*.jumlah' => 'required|integer|min:1',
        ]);

        try {
            DB::beginTransaction();

            $total_barang = 0;
            $total_uang = 0;

            $buktiPath = $resellerTransaction->bukti_tf;
            if ($request->hasFile('bukti_tf')) {
                if ($buktiPath) Storage::disk('public')->delete($buktiPath);
                $buktiPath = $request->file('bukti_tf')->store('bukti_tf', 'public');
            }

            // Delete old details
            ResellerTransactionDetail::where('reseller_transaction_id', $resellerTransaction->id)->delete();

            foreach ($request->details as $detail) {
                // Use subtotal from form
                $subtotal = isset($detail['subtotal']) ? (int)$detail['subtotal'] : 0;
                if ($subtotal <= 0) {
                    $barang = Barang::find($detail['barang_id']);
                    $subtotal = ($barang->hpp ?? 0) * $detail['jumlah'];
                }

                ResellerTransactionDetail::create([
                    'reseller_transaction_id' => $resellerTransaction->id,
                    'barang_id' => $detail['barang_id'],
                    'jumlah' => $detail['jumlah'],
                    'subtotal' => $subtotal,
                ]);

                $total_barang += $detail['jumlah'];
                $total_uang += $subtotal;
            }

            $sisa_kurang = $request->bayar - $total_uang;

            $resellerTransaction->update([
                'tgl' => $request->tgl,
                'total_barang' => $total_barang,
                'total_uang' => $total_uang,
                'bayar' => $request->bayar,
                'sisa_kurang' => $sisa_kurang,
                'retur' => $request->retur ?? 0,
                'bukti_tf' => $buktiPath,
            ]);

            DB::commit();

            return redirect()->route('reseller_transactions.show_reseller', $resellerTransaction->reseller_id)->with('success', 'Transaksi reseller berhasil diubah!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal mengubah transaksi: ' . $e->getMessage())->withInput();
        }
    }

    public function payDebt(Request $request, Reseller $reseller)
    {
        $request->validate([
            'nominal' => 'required|numeric|min:1'
        ]);

        $nominal = $request->nominal;

        // Ambil semua transaksi reseller ini yang sisa_kurangnya minus (berhutang)
        // Urutkan dari yang tgl terlama
        $debtTransactions = ResellerTransaction::where('reseller_id', $reseller->id)
            ->where('sisa_kurang', '<', 0)
            ->orderBy('tgl', 'asc')
            ->orderBy('id', 'asc')
            ->get();

        foreach ($debtTransactions as $trx) {
            if ($nominal <= 0) {
                break;
            }

            $hutang = abs($trx->sisa_kurang);

            if ($nominal >= $hutang) {
                // Lunas untuk transaksi ini
                $trx->bayar += $hutang;
                $trx->sisa_kurang = 0;
                $nominal -= $hutang;
            } else {
                // Bayar sebagian untuk transaksi ini
                $trx->bayar += $nominal;
                $trx->sisa_kurang += $nominal; // sisa_kurang itu negatif, jadi ditambah jadi mendekati 0
                $nominal = 0;
            }

            $trx->save();
        }

        return redirect()->back()->with('success', 'Pembayaran tagihan berhasil dicatat ke transaksi terawal.');
    }
}
