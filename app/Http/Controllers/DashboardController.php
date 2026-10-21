<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\Expense;
use App\Models\Inventory;
use App\Models\Product;
use App\Models\Sale;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $user = Auth::user();

        $branchQuery = $user->role === 'owner'
            ? Branch::query()
            : Branch::whereKey($user->branch_id);

        $branches = $branchQuery->get();
        $salesTotal = Sale::when($user->role !== 'owner', fn ($query) => $query->where('branch_id', $user->branch_id))->sum('total');
        $expenseTotal = Expense::when($user->role !== 'owner', fn ($query) => $query->where('branch_id', $user->branch_id))->sum('amount');
        $products = Product::where('is_active', true)->count();
        $lowStock = Inventory::when($user->role !== 'owner', fn ($query) => $query->where('branch_id', $user->branch_id))->whereColumn('quantity', '<=', 'reorder_level')->count();
        $transactions = Sale::when($user->role !== 'owner', fn ($query) => $query->where('branch_id', $user->branch_id))->count();

        return view('dashboard', compact('branches', 'salesTotal', 'expenseTotal', 'products', 'lowStock', 'transactions'));
    }
}
