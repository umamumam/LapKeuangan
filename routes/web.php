<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\IncomeController;
use App\Http\Controllers\ProdukController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\MonthlyFinanceController;

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
    Route::resource('produks', ProdukController::class);

    Route::get('/orders/export', [OrderController::class, 'export'])->name('orders.export');
    Route::get('/orders/import', [OrderController::class, 'importForm'])->name('orders.import.form');
    Route::post('/orders/import', [OrderController::class, 'import'])->name('orders.import');
    Route::get('/orders/download-template', [OrderController::class, 'downloadTemplate'])->name('orders.download.template');
    Route::resource('orders', OrderController::class);

    Route::get('/incomes/calculate/{income}', [IncomeController::class, 'calculateTotal'])
        ->name('incomes.calculate');

    Route::get('/incomes/create-from-order/{noPesanan}', [IncomeController::class, 'createFromOrder'])
        ->name('incomes.create-from-order');
    Route::get('/incomes/hasil', [IncomeController::class, 'hasil'])->name('incomes.hasil');
    Route::get('/incomes/export-hasil', [IncomeController::class, 'exportHasil'])->name('incomes.export-hasil');
    Route::get('/incomes/export', [IncomeController::class, 'export'])->name('incomes.export');
    Route::get('/incomes/import/form', [IncomeController::class, 'importForm'])->name('incomes.import.form');
    Route::post('/incomes/import', [IncomeController::class, 'import'])->name('incomes.import');
    Route::get('/incomes/download-template', [IncomeController::class, 'downloadTemplate'])->name('incomes.download-template');
    Route::resource('incomes', IncomeController::class);

    Route::get('/monthly-finances/{monthlyFinance}/calculate', [MonthlyFinanceController::class, 'calculate'])->name('monthly-finances.calculate');
    Route::get('/monthly-finances/rekap', [MonthlyFinanceController::class, 'rekap'])->name('monthly-finances.rekap');
    Route::get('/monthly-finances/export', [MonthlyFinanceController::class, 'export'])->name('monthly-finances.export');
    Route::resource('monthly-finances', MonthlyFinanceController::class);
});

require __DIR__ . '/auth.php';
