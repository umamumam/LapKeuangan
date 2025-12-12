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

            // Gunakan helper untuk generate data
            $result = $this->generateOrUpdatePeriodeData($periode, false);

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

            // Cek apakah sudah di-generate sebelumnya
            if (!$periode->is_generated) {
                return response()->json([
                    'success' => false,
                    'message' => 'Periode belum di-generate sebelumnya. Gunakan generate terlebih dahulu.'
                ], 400);
            }

            // Gunakan helper untuk update data (regenerate)
            $result = $this->generateOrUpdatePeriodeData($periode, true);

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

    private function generateOrUpdatePeriodeData(Periode $periode, bool $isRegenerate = false)
    {
        try {
            // 1. Hitung data dari tabel orders dengan JOIN ke produks
            $ordersData = DB::table('orders')
                ->leftJoin('produks', 'orders.produk_id', '=', 'produks.id')
                ->where('orders.periode_id', $periode->id)
                ->selectRaw('
                    COUNT(*) as jumlah_order,
                    SUM(orders.jumlah) as total_jumlah,
                    SUM(orders.returned_quantity) as total_returned,
                    SUM(orders.total_harga_produk) as total_harga_produk,
                    SUM(orders.jumlah * produks.hpp_produk) as total_hpp
                ')
                ->first();

            // 2. Hitung data dari tabel incomes
            $incomesData = DB::table('incomes')
                ->where('periode_id', $periode->id)
                ->selectRaw('
                    COUNT(*) as jumlah_income,
                    SUM(total_penghasilan) as total_penghasilan
                ')
                ->first();

            // 3. Siapkan data untuk update
            $updateData = [
                'jumlah_order' => $ordersData->jumlah_order ?? 0,
                'returned_quantity' => $ordersData->total_returned ?? 0,
                'total_harga_produk' => $ordersData->total_harga_produk ?? 0,
                'total_hpp_produk' => $ordersData->total_hpp ?? 0,

                'jumlah_income' => $incomesData->jumlah_income ?? 0,
                'total_penghasilan' => $incomesData->total_penghasilan ?? 0,
            ];

            // Jika regenerate, update timestamp generated_at
            if ($isRegenerate) {
                $updateData['generated_at'] = now();
            } else {
                // Jika generate pertama kali
                $updateData['is_generated'] = true;
                $updateData['generated_at'] = now();
            }

            // 4. Set data per marketplace berdasarkan marketplace periode
            if ($periode->marketplace === 'Shopee') {
                $updateData['total_penghasilan_shopee'] = $incomesData->total_penghasilan ?? 0;
                $updateData['total_income_count_shopee'] = $incomesData->jumlah_income ?? 0;
                $updateData['total_hpp_shopee'] = $ordersData->total_hpp ?? 0;

                $updateData['total_penghasilan_tiktok'] = 0;
                $updateData['total_income_count_tiktok'] = 0;
                $updateData['total_hpp_tiktok'] = 0;
            } else {
                $updateData['total_penghasilan_tiktok'] = $incomesData->total_penghasilan ?? 0;
                $updateData['total_income_count_tiktok'] = $incomesData->jumlah_income ?? 0;
                $updateData['total_hpp_tiktok'] = $ordersData->total_hpp ?? 0;

                $updateData['total_penghasilan_shopee'] = 0;
                $updateData['total_income_count_shopee'] = 0;
                $updateData['total_hpp_shopee'] = 0;
            }

            // 5. Update periode
            $periode->update($updateData);

            return [
                'success' => true,
                'message' => $isRegenerate ? 'Data berhasil di-update' : 'Data berhasil di-generate'
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => ($isRegenerate ? 'Regenerate' : 'Generate') . ' gagal: ' . $e->getMessage()
            ];
        }
    }

    // /**
    //  * Generate atau regenerate semua periode (pending dan sudah ada)
    //  */
    // public function generateOrRegenerateAll()
    // {
    //     try {
    //         $allPeriodes = Periode::all();
    //         $generatedCount = 0;
    //         $regeneratedCount = 0;
    //         $errors = [];

    //         foreach ($allPeriodes as $periode) {
    //             $isRegenerate = $periode->is_generated;

    //             $result = $this->generateOrUpdatePeriodeData($periode, $isRegenerate);

    //             if ($result['success']) {
    //                 if ($isRegenerate) {
    //                     $regeneratedCount++;
    //                 } else {
    //                     $generatedCount++;
    //                 }
    //             } else {
    //                 $errors[] = $periode->nama_periode . ' (' . $periode->marketplace . '): ' . $result['message'];
    //             }
    //         }

    //         $message = "Berhasil generate $generatedCount periode baru dan regenerate $regeneratedCount periode yang sudah ada";

    //         if (!empty($errors)) {
    //             $message .= " (Dengan beberapa error: " . implode(', ', array_slice($errors, 0, 3)) . ")";
    //             if (count($errors) > 3) {
    //                 $message .= " dan " . (count($errors) - 3) . " error lainnya";
    //             }
    //         }

    //         return response()->json([
    //             'success' => ($generatedCount + $regeneratedCount) > 0,
    //             'message' => $message,
    //             'generated_count' => $generatedCount,
    //             'regenerated_count' => $regeneratedCount,
    //             'total' => $allPeriodes->count(),
    //             'errors' => $errors
    //         ]);
    //     } catch (\Exception $e) {
    //         return response()->json([
    //             'success' => false,
    //             'message' => 'Gagal generate/regenerate semua: ' . $e->getMessage()
    //         ], 500);
    //     }
    // }

    /**
     * Regenerate semua periode yang sudah di-generate
     */
    public function regenerateAll()
    {
        try {
            $generatedPeriodes = Periode::generated()->get();
            $regeneratedCount = 0;
            $errors = [];

            foreach ($generatedPeriodes as $periode) {
                $result = $this->generateOrUpdatePeriodeData($periode, true);

                if ($result['success']) {
                    $regeneratedCount++;
                } else {
                    $errors[] = $periode->nama_periode . ' (' . $periode->marketplace . '): ' . $result['message'];
                }
            }

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
            return response()->json([
                'success' => false,
                'message' => 'Gagal regenerate semua: ' . $e->getMessage()
            ], 500);
        }
    }

    public function generateCurrentMonth()
    {
        try {
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
                        $result = $this->generateOrUpdatePeriodeData($existing, true);
                        if ($result['success']) {
                            $updatedCount++;
                        } else {
                            $errors[] = $result['message'];
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
                    $result = $this->generateOrUpdatePeriodeData($periode, false);
                    if ($result['success']) {
                        $generatedCount++;
                    } else {
                        $errors[] = $result['message'];
                    }
                }
            }

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
            return response()->json([
                'success' => false,
                'message' => 'Gagal generate bulan berjalan: ' . $e->getMessage()
            ], 500);
        }
    }

    public function show($id)
    {
        $periode = Periode::with(['toko', 'orders', 'incomes'])->findOrFail($id);

        $stats = [
            'orders_count' => $periode->orders->count(),
            'incomes_count' => $periode->incomes->count(),
            'total_harga_produk' => $periode->orders->sum('total_harga_produk'),
            'total_penghasilan' => $periode->incomes->sum('total_penghasilan'),
            'total_return' => $periode->orders->sum('returned_quantity'),
        ];

        return view('periodes.show', compact('periode', 'stats'));
    }

    public function destroy($id)
    {
        try {
            $periode = Periode::findOrFail($id);
            $namaPeriode = $periode->nama_periode;

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
            $pendingPeriodes = Periode::notGenerated()->get();
            $generatedCount = 0;
            $errors = [];

            foreach ($pendingPeriodes as $periode) {
                $result = $this->generateOrUpdatePeriodeData($periode, false);
                if ($result['success']) {
                    $generatedCount++;
                } else {
                    $errors[] = $result['message'];
                }
            }

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
            return response()->json([
                'success' => false,
                'message' => 'Gagal generate semua: ' . $e->getMessage()
            ], 500);
        }
    }
}
