<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\Inventory;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class InventoryController extends Controller
{
    public function index(): View
    {
        $user = Auth::user();
        $inventories = Inventory::with(['branch', 'ingredient'])
            ->when(! $user->isOwner(), fn ($query) => $query->where('branch_id', $user->branch_id))
            ->orderBy('quantity', 'asc')
            ->get();

        return view('inventory.index', [
            'inventories' => $inventories,
            'branches' => Branch::query()
                ->when(! $user->isOwner(), fn ($query) => $query->whereKey($user->branch_id))
                ->get(),
        ]);
    }
}
