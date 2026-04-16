<?php

namespace App\Http\Controllers;

use App\Models\Reseller;
use App\Models\Supplier;
use Illuminate\Http\Request;

class PartnerController extends Controller
{
    public function index()
    {
        $resellers = Reseller::orderBy('nama')->get();
        $suppliers = Supplier::orderBy('nama')->get();
        return view('partners.index', compact('resellers', 'suppliers'));
    }
}
