<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TokoController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\RekapController;
use App\Http\Controllers\IncomeController;
use App\Http\Controllers\ProdukController;
use App\Http\Controllers\SampelController;
use App\Http\Controllers\BandingController;
use App\Http\Controllers\PeriodeController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\MonthlyFinanceController;
use App\Http\Controllers\MonthlySummaryController;
use App\Http\Controllers\PengirimanSampelController;
use App\Http\Controllers\PengembalianPenukaranController;

Route::get('/', function () {
    return view('auth.login');
});

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/produks/export', [ProdukController::class, 'export'])->name('produks.export');
    Route::get('/produks/import', [ProdukController::class, 'importForm'])->name('produks.import.form');
    Route::post('/produks/import', [ProdukController::class, 'import'])->name('produks.import');
    Route::get('/produks/download-template', [ProdukController::class, 'downloadTemplate'])->name('produks.download.template');
    Route::delete('/produks/delete-all', [ProdukController::class, 'deleteAll'])->name('produks.deleteAll');
    Route::resource('produks', ProdukController::class);

    Route::get('/orders/export', [OrderController::class, 'export'])->name('orders.export');
    Route::post('/orders/export/periode', [OrderController::class, 'exportPeriode'])->name('orders.export.periode');
    Route::get('/orders/import', [OrderController::class, 'importForm'])->name('orders.import.form');
    Route::post('/orders/import', [OrderController::class, 'import'])->name('orders.import');
    Route::get('/orders/download-template', [OrderController::class, 'downloadTemplate'])->name('orders.download.template');
    Route::delete('/orders/delete-all', [OrderController::class, 'deleteAll'])->name('orders.deleteAll');
    Route::post('/orders/delete-by-periode', [OrderController::class, 'deleteByPeriode'])->name('orders.delete.by.periode'); // DITAMBAHKAN
    Route::resource('orders', OrderController::class);
    // API untuk get periode data
    Route::get('/api/periodes', function () {
        $periodes = App\Models\Periode::orderBy('nama_periode', 'desc')->get();
        return response()->json($periodes);
    });

    // API untuk hitung order per periode
    Route::get('/api/orders/count-by-periode/{periodeId}', function ($periodeId) {
        $count = App\Models\Order::where('periode_id', $periodeId)->count();
        return response()->json(['count' => $count]);
    });

    Route::get('/incomes/calculate/{income}', [IncomeController::class, 'calculateTotal'])
        ->name('incomes.calculate');

    Route::get('/incomes/create-from-order/{noPesanan}', [IncomeController::class, 'createFromOrder'])
        ->name('incomes.create-from-order');

    Route::get('/incomes/hasil', [IncomeController::class, 'hasil'])->name('incomes.hasil');
    Route::get('/incomes/detailhasil', [IncomeController::class, 'detailhasil'])->name('incomes.detailhasil');
    Route::get('/incomes/export-hasil', [IncomeController::class, 'exportHasil'])->name('incomes.export-hasil');
    Route::get('/incomes/export', [IncomeController::class, 'export'])->name('incomes.export');
    Route::post('/incomes/export/periode', [IncomeController::class, 'exportPeriode'])->name('incomes.export.periode');
    Route::get('/incomes/import/form', [IncomeController::class, 'importForm'])->name('incomes.import.form');
    Route::post('/incomes/import', [IncomeController::class, 'import'])->name('incomes.import');
    Route::get('/incomes/download-template', [IncomeController::class, 'downloadTemplate'])->name('incomes.download-template');

    // Rute untuk operasi massal (bulk operations)
    Route::delete('/incomes/delete-all', [IncomeController::class, 'deleteAll'])->name('incomes.deleteAll');
    Route::post('/incomes/delete-by-periode', [IncomeController::class, 'deleteByPeriode'])->name('incomes.delete.by.periode');
    Route::post('/incomes/delete-by-multiple-periode', [IncomeController::class, 'deleteByMultiplePeriode'])->name('incomes.delete.by.multiple.periode');
    Route::post('/incomes/bulk-attach-periode', [IncomeController::class, 'bulkAttachToPeriode'])->name('incomes.bulk.attach.periode');

    // Rute resource untuk CRUD standar
    Route::resource('incomes', IncomeController::class);

    Route::get('/monthly-finances/{monthlyFinance}/calculate', [MonthlyFinanceController::class, 'calculate'])->name('monthly-finances.calculate');
    Route::get('/monthly-finances/rekap', [MonthlyFinanceController::class, 'rekap'])->name('monthly-finances.rekap');
    Route::get('/monthly-finances/export', [MonthlyFinanceController::class, 'export'])->name('monthly-finances.export');
    Route::get('/monthly-finances/{monthlyFinance}/sync', [MonthlyFinanceController::class, 'syncWithSummary'])->name('monthly-finances.sync');
    Route::resource('monthly-finances', MonthlyFinanceController::class);
    Route::resource('toko', TokoController::class);

    Route::prefix('monthly-summaries')->group(function () {
        Route::get('/', [MonthlySummaryController::class, 'index'])->name('monthly-summaries.index');
        Route::post('/generate', [MonthlySummaryController::class, 'generate'])->name('monthly-summaries.generate');
        Route::get('/generate/current', [MonthlySummaryController::class, 'generateCurrentMonth'])->name('monthly-summaries.generate.current');
        Route::get('/generate/previous', [MonthlySummaryController::class, 'generatePreviousMonth'])->name('monthly-summaries.generate.previous');
        Route::get('/dashboard', [MonthlySummaryController::class, 'dashboard'])->name('monthly-summaries.dashboard');
        Route::get('/{monthlySummary}', [MonthlySummaryController::class, 'show'])->name('monthly-summaries.show');
        Route::delete('/{monthlySummary}', [MonthlySummaryController::class, 'destroy'])->name('monthly-summaries.destroy');
    });
    Route::post('/bandings/import', [BandingController::class, 'import'])->name('bandings.import');
    Route::get('/bandings/export', [BandingController::class, 'export'])->name('bandings.export');
    Route::get('/bandings/template', [BandingController::class, 'downloadTemplate'])->name('bandings.downloadTemplate');
    Route::delete('/bandings/delete-all', [BandingController::class, 'deleteAll'])->name('bandings.deleteAll');
    Route::get('/bandings/search', [BandingController::class, 'search'])->name('bandings.search');
    Route::post('/bandings/search-result', [BandingController::class, 'searchResult'])->name('bandings.search.result');
    Route::get('/bandings/create-with-resi/{noResi}', [BandingController::class, 'createWithResi'])->name('bandings.create-with-resi');
    Route::post('/bandings/{banding}/update-status', [BandingController::class, 'updateStatus'])->name('bandings.update-status');
    Route::get('/ok', [BandingController::class, 'StatusOk'])->name('bandings.ok');
    Route::get('/belum', [BandingController::class, 'StatusBelum'])->name('bandings.belum');
    Route::get('/bandings/search-ok', [BandingController::class, 'searchOK'])->name('bandings.searchOK');
    Route::post('/bandings/search-result-ok', [BandingController::class, 'searchResultOK'])->name('bandings.search.result.ok');
    Route::resource('bandings', BandingController::class);

    Route::get('/sampels/export', [SampelController::class, 'export'])->name('sampels.export');
    Route::post('/sampels/import', [SampelController::class, 'import'])->name('sampels.import');
    Route::get('/sampels/get-harga/{id}', [SampelController::class, 'getHarga'])->name('sampels.get-harga');
    Route::resource('sampels', SampelController::class);

    Route::get('/get-total-hpp', [PengirimanSampelController::class, 'getTotalHpp'])->name('get-total-hpp');
    Route::get('/get-total-biaya', [PengirimanSampelController::class, 'getTotalBiaya'])->name('get-total-biaya');
    Route::delete('/pengiriman-sampels-delete-all', [PengirimanSampelController::class, 'deleteAll'])->name('pengiriman-sampels.deleteAll');
    Route::get('/pengiriman-sampels-export', [PengirimanSampelController::class, 'export'])->name('pengiriman-sampels.export');
    Route::post('/pengiriman-sampels-import', [PengirimanSampelController::class, 'import'])->name('pengiriman-sampels.import');
    Route::get('/pengiriman-sampels-rekap', [PengirimanSampelController::class, 'rekap'])->name('pengiriman-sampels.rekap');
    Route::resource('pengiriman-sampels', PengirimanSampelController::class);

    Route::prefix('periodes')->name('periodes.')->group(function () {
        Route::get('/', [PeriodeController::class, 'index'])->name('index');
        Route::post('/', [PeriodeController::class, 'store'])->name('store');
        Route::get('/{id}', [PeriodeController::class, 'show'])->name('show');
        Route::delete('/{id}', [PeriodeController::class, 'destroy'])->name('destroy');

        // Route untuk generate/regenerate
        Route::post('/{id}/generate', [PeriodeController::class, 'generate'])->name('generate');
        Route::post('/{id}/regenerate', [PeriodeController::class, 'regenerate'])->name('regenerate');

        Route::post('/generate/current-month', [PeriodeController::class, 'generateCurrentMonth'])->name('generate.current');
        Route::post('/generate/all-pending', [PeriodeController::class, 'generateAllPending'])->name('generate.all');
        Route::post('/regenerate/all', [PeriodeController::class, 'regenerateAll'])->name('regenerate.all');
        Route::post('/generate-or-regenerate/all', [PeriodeController::class, 'generateOrRegenerateAll'])->name('generate.or.regenerate.all');
    });
    Route::get('/rekaps/hasil', [RekapController::class, 'hasil'])->name('rekaps.hasil');
    Route::resource('rekaps', RekapController::class);

    Route::post('/pengembalian-penukaran/import', [PengembalianPenukaranController::class, 'import'])->name('pengembalian-penukaran.import');
    Route::get('/pengembalian-penukaran/export', [PengembalianPenukaranController::class, 'export'])->name('pengembalian-penukaran.export');
    Route::delete('/pengembalian-penukaran/delete-all', [PengembalianPenukaranController::class, 'deleteAll'])->name('pengembalian-penukaran.delete-all');
    Route::get('/scan-ok', [PengembalianPenukaranController::class, 'searchOK'])->name('pengembalian-penukaran.search.ok');
    Route::post('/scan-ok/search', [PengembalianPenukaranController::class, 'searchResultOK'])->name('pengembalian-penukaran.search.result.ok');
    Route::get('/data-ok', [PengembalianPenukaranController::class, 'indexOK'])->name('pengembalian-penukaran.ok');
    Route::get('/data-belum', [PengembalianPenukaranController::class, 'indexBelum'])->name('pengembalian-penukaran.belum');
    Route::put('/pengembalian-penukaran/{id}/update-status', [PengembalianPenukaranController::class, 'updateStatus'])->name('pengembalian-penukaran.update-status');
    Route::get('/pengembalian-penukaran/export/filtered', [PengembalianPenukaranController::class, 'exportFiltered'])->name('pengembalian-penukaran.export.filtered');
    Route::resource('pengembalian-penukaran', PengembalianPenukaranController::class);
});

require __DIR__ . '/auth.php';
