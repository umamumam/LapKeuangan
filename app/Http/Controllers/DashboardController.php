<?php

namespace App\Http\Controllers;

use App\Models\Income;
use App\Models\Order;
use App\Models\Produk;
use App\Models\Toko;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $totalProduk = Produk::count();
        $totalToko = Toko::count();
        $totalOrder = Order::count();
        $totalIncome = Income::count();
        $statistik = [
            'total_produk' => $totalProduk,
            'total_toko' => $totalToko,
            'total_order' => $totalOrder,
            'total_income' => $totalIncome,
        ];

        return view('dashboard', compact('statistik'));
    }
}
