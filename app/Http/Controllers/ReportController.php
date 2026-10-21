<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\Inventory;
use App\Models\Sale;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class ReportController extends Controller
{
    public function index(): View
    {
        $user = Auth::user();

        $sales = Sale::with(['branch', 'user'])->when($user->role !== 'owner', fn ($query) => $query->where('branch_id', $user->branch_id))->latest()->take(10)->get();
        $expenses = Expense::with('branch')->when($user->role !== 'owner', fn ($query) => $query->where('branch_id', $user->branch_id))->latest()->take(10)->get();
        $lowStock = Inventory::with(['branch', 'ingredient'])->when($user->role !== 'owner', fn ($query) => $query->where('branch_id', $user->branch_id))->whereColumn('quantity', '<=', 'reorder_level')->get();

        return view('reports.index', compact('sales', 'expenses', 'lowStock'));
    }
}
