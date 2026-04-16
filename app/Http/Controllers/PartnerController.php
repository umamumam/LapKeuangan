<?php

namespace App\Http\Controllers;

use App\Models\Reseller;
use App\Models\Supplier;
use Illuminate\Http\Request;

class PartnerController extends Controller
{
    public function index()
    {
        $resellers = Reseller::with(['barangs'])->withCount('barangs')->orderBy('nama')->get();
        $suppliers = Supplier::with(['barangs'])->withCount('barangs')->orderBy('nama')->get();
        return view('partners.index', compact('resellers', 'suppliers'));
    }

    public function showReseller(Reseller $reseller)
    {
        $reseller->load('barangs');
        $resellers = Reseller::orderBy('nama')->get();
        $suppliers = Supplier::orderBy('nama')->get();
        return view('partners.show_reseller', compact('reseller', 'resellers', 'suppliers'));
    }

    public function showSupplier(Supplier $supplier)
    {
        $supplier->load('barangs');
        $resellers = Reseller::orderBy('nama')->get();
        $suppliers = Supplier::orderBy('nama')->get();
        return view('partners.show_supplier', compact('supplier', 'resellers', 'suppliers'));
    }
}
