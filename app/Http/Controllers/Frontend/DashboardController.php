<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Lead;
use App\Models\Product;
use App\Models\Supplier;
use App\Services\GlobalProductIdService;
use GuzzleHttp\Psr7\Request;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use App\Models\User;

class DashboardController extends Controller
{
    public function index()
    {
        GlobalProductIdService::put();

        $products = Product::all();
        $suppliers = Supplier::all();
        $callers = User::where('role', User::ROLE_CALLER)->get();
        if (Gate::denies('isAdmin')) {
            return redirect()->route('leads.list');
        }

        return view('dashboard.index', compact('products', 'suppliers'));
    }
}
