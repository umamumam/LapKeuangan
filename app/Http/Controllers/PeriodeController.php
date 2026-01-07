<?php

namespace App\Http\Controllers;

use App\Models\Toko;
use App\Models\Periode;
use App\Models\Order;
use App\Models\Income;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class PeriodeController extends Controller
{
    public function index()
    {
        $periodes = Periode::with('toko')
            ->orderBy('tanggal_mulai', 'desc')
            ->get();

        $currentYear = date('Y');
        $years = range($currentYear - 2, $currentYear + 2);

        $months = [
            '01' => 'Januari',
            '02' => 'Februari',
            '03' => 'Maret',
            '04' => 'April',
            '05' => 'Mei',
            '06' => 'Juni',
            '07' => 'Juli',
            '08' => 'Agustus',
            '09' => 'September',
            '10' => 'Oktober',
            '11' => 'November',
            '12' => 'Desember',
        ];

        $tokos = Toko::orderBy('nama')->get();

        return view('periodes.index', compact('periodes', 'years', 'months', 'tokos'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'year' => 'required|numeric|min:2020|max:2050',
            'month' => 'required|string|size:2',
            'toko_id' => 'required|exists:tokos,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            DB::beginTransaction();

            $year = $request->year;
            $month = $request->month;
            $tokoId = $request->toko_id;

            $monthNames = [
                '01' => 'Januari',
                '02' => 'Februari',
                '03' => 'Maret',
                '04' => 'April',
                '05' => 'Mei',
                '06' => 'Juni',
                '07' => 'Juli',
                '08' => 'Agustus',
                '09' => 'September',
                '10' => 'Oktober',
                '11' => 'November',
                '12' => 'Desember',
            ];

            $namaPeriode = $monthNames[$month] . ' ' . $year;

            $tanggalMulai = Carbon::create($year, $month, 1)->startOfMonth();
            $tanggalSelesai = Carbon::create($year, $month, 1)->endOfMonth();

            $existingPeriodes = Periode::where('toko_id', $tokoId)
                ->where('nama_periode', $namaPeriode)
                ->get();

            $marketplaces = ['Shopee', 'Tiktok'];
            $createdPeriodes = [];

            foreach ($marketplaces as $marketplace) {
                $existing = $existingPeriodes->firstWhere('marketplace', $marketplace);

                if ($existing) {
                    continue;
                }

                $periode = Periode::create([
                    'nama_periode' => $namaPeriode,
                    'tanggal_mulai' => $tanggalMulai,
                    'tanggal_selesai' => $tanggalSelesai,
                    'toko_id' => $tokoId,
                    'marketplace' => $marketplace,
                    'is_generated' => false,
                    'generated_at' => null,
                ]);

                $createdPeriodes[] = $periode;
            }

            DB::commit();

            $message = count($createdPeriodes) > 0
                ? 'Periode berhasil dibuat untuk ' . count($createdPeriodes) . ' marketplace'
                : 'Periode sudah ada untuk semua marketplace';

            return response()->json([
                'success' => true,
                'message' => $message,
                'data' => $createdPeriodes
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Gagal membuat periode: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Generate data untuk periode tertentu (untuk pertama kali)
     */
    public function generate($id)
    {
        try {
            DB::beginTransaction();

            $periode = Periode::findOrFail($id);

            // Cek apakah sudah di-generate
            if ($periode->is_generated) {
                return response()->json([
                    'success' => false,
                    'message' => 'Periode sudah di-generate sebelumnya. Gunakan regenerate untuk update data.'
                ], 400);
            }

            // Generate data
            $this->calculateAndSavePeriodeData($periode, false);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Data periode berhasil di-generate',
                'data' => $periode->refresh()
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Gagal generate data: ' . $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ], 500);
        }
    }

    /**
     * REGENERATE - Update data untuk periode yang sudah di-generate
     */
    public function regenerate($id)
    {
        try {
            DB::beginTransaction();

            $periode = Periode::findOrFail($id);

            // Regenerate data
            $this->calculateAndSavePeriodeData($periode, true);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Data periode berhasil di-update (regenerate)',
                'data' => $periode->refresh()
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Gagal regenerate data: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Menghitung dan menyimpan data periode
     */

    private function calculateAndSavePeriodeData(Periode $periode, bool $isRegenerate = false)
    {
        // Reset semua data ke 0 sebelum menghitung ulang
        $updateData = [
            // Data dari orders
            'total_harga_produk' => 0,
            'jumlah_order' => 0,
            'returned_quantity' => 0,
            'total_hpp_produk' => 0,

            // Data dari incomes
            'total_penghasilan' => 0,
            'jumlah_income' => 0,

            // Data per marketplace
            'total_penghasilan_shopee' => 0,
            'total_income_count_shopee' => 0,
            'total_hpp_shopee' => 0,

            'total_penghasilan_tiktok' => 0,
            'total_income_count_tiktok' => 0,
            'total_hpp_tiktok' => 0,
        ];

        // 1. HITUNG DATA DARI ORDERS
        $orders = Order::with('produk')
            ->where('periode_id', $periode->id)
            ->get();

        if ($orders->count() > 0) {
            $updateData['jumlah_order'] = $orders->count();
            $updateData['total_harga_produk'] = $orders->sum('total_harga_produk');
            $updateData['returned_quantity'] = $orders->sum('returned_quantity');
        }

        // 2. HITUNG DATA DARI INCOMES (termasuk HPP dengan logika khusus TikTok)
        $incomes = Income::with(['orders.produk'])
            ->where('periode_id', $periode->id)
            ->get();

        if ($incomes->count() > 0) {
            $updateData['jumlah_income'] = $incomes->count();
            $updateData['total_penghasilan'] = $incomes->sum('total_penghasilan');

            // PERHITUNGAN HPP BERDASARKAN INCOME - DENGAN LOGIKA KHUSUS TIKTOK
            $updateData['total_hpp_produk'] = $incomes->sum(function ($income) use ($periode) {
                // Filter orders yang terkait dengan periode ini
                $incomeOrders = $income->orders->where('periode_id', $periode->id);

                // Hitung HPP dengan logika khusus TikTok
                return $incomeOrders->sum(function ($order) use ($periode, $income) {
                    if ($periode->marketplace === 'Tiktok') {
                        // Jika total_penghasilan minus, HPP = 0
                        if ($income->total_penghasilan < 0) {
                            return 0;
                        }
                    }

                    // Hitung HPP normal untuk Shopee atau TikTok non-retur
                    if ($order->produk && $order->produk->hpp_produk) {
                        $netQuantity = $order->jumlah - ($order->returned_quantity ?? 0);
                        return $netQuantity * $order->produk->hpp_produk;
                    }
                    return 0;
                });
            });
        }

        // 3. SET DATA PER MARKETPLACE
        if ($periode->marketplace === 'Shopee') {
            $updateData['total_penghasilan_shopee'] = $updateData['total_penghasilan'];
            $updateData['total_income_count_shopee'] = $updateData['jumlah_income'];
            $updateData['total_hpp_shopee'] = $updateData['total_hpp_produk'];
        } else {
            $updateData['total_penghasilan_tiktok'] = $updateData['total_penghasilan'];
            $updateData['total_income_count_tiktok'] = $updateData['jumlah_income'];
            $updateData['total_hpp_tiktok'] = $updateData['total_hpp_produk'];
        }

        // 4. SET STATUS GENERATE
        if (!$isRegenerate) {
            $updateData['is_generated'] = true;
        }
        $updateData['generated_at'] = now();

        // 5. UPDATE PERIODE
        $periode->update($updateData);

        return $updateData;
    }

    /**
     * Sync data related untuk regenerate (menghubungkan data yang belum terhubung)
     */
    private function syncRelatedData(Periode $periode)
    {
        // Sync orders berdasarkan tanggal dan toko
        Order::where('periode_id', null)
            ->where('toko_id', $periode->toko_id)
            ->whereBetween('created_at', [$periode->tanggal_mulai, $periode->tanggal_selesai])
            ->update(['periode_id' => $periode->id]);

        // Sync incomes berdasarkan no_pesanan dari orders yang sudah di-update
        $orderNos = Order::where('periode_id', $periode->id)
            ->pluck('no_pesanan')
            ->unique()
            ->toArray();

        if (!empty($orderNos)) {
            Income::whereIn('no_pesanan', $orderNos)
                ->update(['periode_id' => $periode->id]);
        }
    }

    /**
     * Regenerate semua periode yang sudah di-generate
     */
    public function regenerateAll()
    {
        try {
            DB::beginTransaction();

            $generatedPeriodes = Periode::where('is_generated', true)->get();
            $regeneratedCount = 0;
            $errors = [];

            foreach ($generatedPeriodes as $periode) {
                try {
                    $this->calculateAndSavePeriodeData($periode, true);
                    $regeneratedCount++;
                } catch (\Exception $e) {
                    $errors[] = $periode->nama_periode . ' (' . $periode->marketplace . '): ' . $e->getMessage();
                }
            }

            DB::commit();

            $message = "Berhasil regenerate $regeneratedCount periode";

            if (!empty($errors)) {
                $message .= " (Dengan beberapa error: " . implode(', ', array_slice($errors, 0, 3)) . ")";
                if (count($errors) > 3) {
                    $message .= " dan " . (count($errors) - 3) . " error lainnya";
                }
            }

            return response()->json([
                'success' => $regeneratedCount > 0,
                'message' => $message,
                'regenerated_count' => $regeneratedCount,
                'total_generated' => $generatedPeriodes->count(),
                'errors' => $errors
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Gagal regenerate semua: ' . $e->getMessage()
            ], 500);
        }
    }

    public function generateCurrentMonth()
    {
        try {
            DB::beginTransaction();

            $currentYear = date('Y');
            $currentMonth = date('m');

            $monthNames = [
                '01' => 'Januari',
                '02' => 'Februari',
                '03' => 'Maret',
                '04' => 'April',
                '05' => 'Mei',
                '06' => 'Juni',
                '07' => 'Juli',
                '08' => 'Agustus',
                '09' => 'September',
                '10' => 'Oktober',
                '11' => 'November',
                '12' => 'Desember',
            ];

            $namaPeriode = $monthNames[$currentMonth] . ' ' . $currentYear;
            $tanggalMulai = Carbon::create($currentYear, $currentMonth, 1)->startOfMonth();
            $tanggalSelesai = Carbon::create($currentYear, $currentMonth, 1)->endOfMonth();

            $tokos = Toko::all();
            $generatedCount = 0;
            $updatedCount = 0;
            $errors = [];

            foreach ($tokos as $toko) {
                $existingPeriodes = Periode::where('toko_id', $toko->id)
                    ->where('nama_periode', $namaPeriode)
                    ->get();

                $marketplaces = ['Shopee', 'Tiktok'];

                foreach ($marketplaces as $marketplace) {
                    $existing = $existingPeriodes->firstWhere('marketplace', $marketplace);

                    if ($existing) {
                        // Jika sudah ada, regenerate (update data)
                        try {
                            $this->calculateAndSavePeriodeData($existing, true);
                            $updatedCount++;
                        } catch (\Exception $e) {
                            $errors[] = $toko->nama . ' ' . $marketplace . ': ' . $e->getMessage();
                        }
                        continue;
                    }

                    // Buat periode baru
                    $periode = Periode::create([
                        'nama_periode' => $namaPeriode,
                        'tanggal_mulai' => $tanggalMulai,
                        'tanggal_selesai' => $tanggalSelesai,
                        'toko_id' => $toko->id,
                        'marketplace' => $marketplace,
                        'is_generated' => false,
                        'generated_at' => null,
                    ]);

                    // Generate data untuk periode baru
                    try {
                        $this->calculateAndSavePeriodeData($periode, false);
                        $generatedCount++;
                    } catch (\Exception $e) {
                        $errors[] = $toko->nama . ' ' . $marketplace . ': ' . $e->getMessage();
                    }
                }
            }

            DB::commit();

            $message = "Berhasil generate $generatedCount periode baru dan update $updatedCount periode yang sudah ada";

            if (!empty($errors)) {
                $message .= " (Dengan beberapa error: " . implode(', ', $errors) . ")";
            }

            return response()->json([
                'success' => ($generatedCount + $updatedCount) > 0,
                'message' => $message,
                'generated_count' => $generatedCount,
                'updated_count' => $updatedCount,
                'errors' => $errors
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Gagal generate bulan berjalan: ' . $e->getMessage()
            ], 500);
        }
    }

    public function show($id)
    {
        $periode = Periode::with(['toko', 'orders.produk', 'incomes'])->findOrFail($id);

        // Hitung ulang untuk verifikasi
        $ordersCount = $periode->orders->count();
        $incomesCount = $periode->incomes->count();

        $totalHargaProduk = $periode->orders->sum('total_harga_produk');
        $totalPenghasilan = $periode->incomes->sum('total_penghasilan');

        $totalReturn = $periode->orders->sum('returned_quantity');

        // Hitung total HPP dengan rumus yang benar
        $totalHpp = $periode->orders->sum(function ($order) {
            $netQuantity = $order->jumlah - $order->returned_quantity;
            return $netQuantity * $order->produk->hpp_produk;
        });

        $stats = [
            'orders_count' => $ordersCount,
            'incomes_count' => $incomesCount,
            'total_harga_produk' => $totalHargaProduk,
            'total_penghasilan' => $totalPenghasilan,
            'total_hpp' => $totalHpp,
            'total_return' => $totalReturn,
            'laba_bersih' => $totalPenghasilan - $totalHpp,
        ];

        return view('periodes.show', compact('periode', 'stats'));
    }

    public function destroy($id)
    {
        try {
            $periode = Periode::findOrFail($id);
            $namaPeriode = $periode->nama_periode;

            // Hapus periode_id dari orders dan incomes terkait
            Order::where('periode_id', $periode->id)->update(['periode_id' => null]);
            Income::where('periode_id', $periode->id)->update(['periode_id' => null]);

            $periode->delete();

            return response()->json([
                'success' => true,
                'message' => "Periode '$namaPeriode' berhasil dihapus"
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus periode: ' . $e->getMessage()
            ], 500);
        }
    }

    public function generateAllPending()
    {
        try {
            DB::beginTransaction();

            $pendingPeriodes = Periode::where('is_generated', false)->get();
            $generatedCount = 0;
            $errors = [];

            foreach ($pendingPeriodes as $periode) {
                try {
                    $this->calculateAndSavePeriodeData($periode, false);
                    $generatedCount++;
                } catch (\Exception $e) {
                    $errors[] = $periode->nama_periode . ' (' . $periode->marketplace . '): ' . $e->getMessage();
                }
            }

            DB::commit();

            $message = "Berhasil generate $generatedCount periode yang pending";

            if (!empty($errors)) {
                $message .= " (Dengan beberapa error: " . implode(', ', array_slice($errors, 0, 3)) . ")";
                if (count($errors) > 3) {
                    $message .= " dan " . (count($errors) - 3) . " error lainnya";
                }
            }

            return response()->json([
                'success' => $generatedCount > 0,
                'message' => $message,
                'generated_count' => $generatedCount,
                'total_pending' => $pendingPeriodes->count(),
                'errors' => $errors
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Gagal generate semua: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Sync all orders and incomes to appropriate periods
     */
    public function syncAllData()
    {
        try {
            DB::beginTransaction();

            $periodes = Periode::all();
            $syncedOrders = 0;
            $syncedIncomes = 0;

            foreach ($periodes as $periode) {
                // Sync orders based on date range and toko
                $ordersUpdated = Order::where('periode_id', null)
                    ->where('toko_id', $periode->toko_id)
                    ->whereBetween('created_at', [$periode->tanggal_mulai, $periode->tanggal_selesai])
                    ->update(['periode_id' => $periode->id]);

                $syncedOrders += $ordersUpdated;

                // Sync incomes based on order numbers
                $orderNos = Order::where('periode_id', $periode->id)
                    ->pluck('no_pesanan')
                    ->unique()
                    ->toArray();

                if (!empty($orderNos)) {
                    $incomesUpdated = Income::whereIn('no_pesanan', $orderNos)
                        ->where('periode_id', null)
                        ->update(['periode_id' => $periode->id]);

                    $syncedIncomes += $incomesUpdated;
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => "Berhasil sync data: $syncedOrders orders dan $syncedIncomes incomes terhubung ke periode",
                'synced_orders' => $syncedOrders,
                'synced_incomes' => $syncedIncomes
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Gagal sync data: ' . $e->getMessage()
            ], 500);
        }
    }
}
