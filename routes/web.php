<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TokoController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\IncomeController;
use App\Http\Controllers\ProdukController;
use App\Http\Controllers\BandingController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\MonthlyFinanceController;
use App\Http\Controllers\MonthlySummaryController;

Route::get('/', function () {
    return view('auth.login');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

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
    Route::get('/orders/import', [OrderController::class, 'importForm'])->name('orders.import.form');
    Route::post('/orders/import', [OrderController::class, 'import'])->name('orders.import');
    Route::get('/orders/download-template', [OrderController::class, 'downloadTemplate'])->name('orders.download.template');
    Route::delete('/orders/delete-all', [OrderController::class, 'deleteAll'])->name('orders.deleteAll');
    Route::resource('orders', OrderController::class);

    Route::get('/incomes/calculate/{income}', [IncomeController::class, 'calculateTotal'])
        ->name('incomes.calculate');

    Route::get('/incomes/create-from-order/{noPesanan}', [IncomeController::class, 'createFromOrder'])
        ->name('incomes.create-from-order');
    Route::get('/incomes/hasil', [IncomeController::class, 'hasil'])->name('incomes.hasil');
    Route::get('/incomes/detailhasil', [IncomeController::class, 'detailhasil'])->name('incomes.detailhasil');
    Route::get('/incomes/export-hasil', [IncomeController::class, 'exportHasil'])->name('incomes.export-hasil');
    Route::get('/incomes/export', [IncomeController::class, 'export'])->name('incomes.export');
    Route::get('/incomes/import/form', [IncomeController::class, 'importForm'])->name('incomes.import.form');
    Route::post('/incomes/import', [IncomeController::class, 'import'])->name('incomes.import');
    Route::get('/incomes/download-template', [IncomeController::class, 'downloadTemplate'])->name('incomes.download-template');
    Route::delete('/incomes/delete-all', [IncomeController::class, 'deleteAll'])->name('incomes.deleteAll');
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
    Route::resource('bandings', BandingController::class);
});

require __DIR__ . '/auth.php';
