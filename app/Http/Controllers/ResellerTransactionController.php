<?php

namespace App\Http\Controllers;

use App\Models\Reseller;
use App\Models\Barang;
use App\Models\ResellerTransaction;
use App\Models\ResellerTransactionDetail;
use App\Models\ResellerPayment;
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
        
        $baseDate = Carbon::parse('2025-12-31');
        $now = Carbon::now();
        
        // Logic: Show general products (reseller_id IS NULL AND supplier_id IS NULL)
        $barangs = Barang::whereNull('reseller_id')
            ->whereNull('supplier_id')
            ->whereNotNull('namabarang')
            ->where('namabarang', '!=', '')
            ->orderBy('namabarang')
            ->orderBy('ukuran')
            ->get();
        
        // Map price field based on type
        foreach ($barangs as $barang) {
            $barang->display_price = ($type == 'hpp') ? ($barang->hpp ?? 0) : ($barang->harga_grosir ?? 0);
        }
        
        // Calculate which period we are in (every 35 days)
        $diffInDays = $baseDate->diffInDays($now, false);
        $currentPeriodIndex = floor($diffInDays / 35);
        if ($currentPeriodIndex < 0) $currentPeriodIndex = 0;

        $periodIndex = $request->query('period_index', $currentPeriodIndex);
        $startDate = $baseDate->copy()->addDays($periodIndex * 35);
        
        // Generate a list of periods for the dropdown
        $periods = [];
        for ($i = 0; $i <= $currentPeriodIndex + 2; $i++) {
            $pStart = $baseDate->copy()->addDays($i * 35);
            $pEnd = $pStart->copy()->addDays(34);
            $periods[] = [
                'index' => $i,
                'label' => $pStart->translatedFormat('d M Y') . ' - ' . $pEnd->translatedFormat('d M Y'),
                'is_current' => ($i == $currentPeriodIndex)
            ];
        }
        
        $dates = [];
        for ($i = 0; $i < 35; $i++) {
            $dates[] = $startDate->copy()->addDays($i);
        }
        
        // Fetch existing transactions for this period
        $transactions = ResellerTransaction::with('details')
            ->where('reseller_id', $resellerId)
            ->whereBetween('tgl', [$startDate->format('Y-m-d'), $dates[34]->format('Y-m-d')])
            ->get()
            ->keyBy('tgl');

        // Fetch payments for rekap
        $payments = ResellerPayment::where('reseller_id', $resellerId)
            ->whereBetween('tgl', [$startDate->format('Y-m-d'), $dates[34]->format('Y-m-d')])
            ->get();

        return view('reseller_transactions.matrix', compact('reseller', 'barangs', 'dates', 'transactions', 'payments', 'startDate', 'periods', 'periodIndex', 'type'));
    }

    public function saveMatrix(Request $request)
    {
        $resellerId = $request->reseller_id;
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
                    if ($jumlah <= 0) continue;

                    $barang = Barang::find($barangId);
                    $subtotal = $jumlah * ($barang->harga_grosir ?? 0);

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
}
