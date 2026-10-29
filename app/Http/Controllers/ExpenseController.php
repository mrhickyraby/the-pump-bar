<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\Expense;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class ExpenseController extends Controller
{
    public function index(): View
    {
        $user = Auth::user();

        return view('expenses.index', [
            'expenses' => Expense::with(['branch', 'user'])
                ->when(! $user->isOwner(), fn ($query) => $query->where('branch_id', $user->branch_id))
                ->latest()
                ->get(),
            'branches' => Branch::query()
                ->when(! $user->isOwner(), fn ($query) => $query->whereKey($user->branch_id))
                ->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $user = Auth::user();

        $validated = $request->validate([
            'branch_id' => ['required', 'exists:branches,id'],
            'category' => ['required', 'string', 'max:100'],
            'amount' => ['required', 'numeric', 'min:0'],
            'description' => ['nullable', 'string', 'max:255'],
            'occurred_at' => ['required', 'date'],
        ]);

        $branchId = $user->isOwner() ? $validated['branch_id'] : $user->branch_id;
        abort_unless($branchId, 403, 'Your account is not assigned to a branch.');

        Expense::create([
            'category' => $validated['category'],
            'amount' => $validated['amount'],
            'description' => $validated['description'] ?? null,
            'branch_id' => $branchId,
            'user_id' => Auth::id(),
            'occurred_at' => Carbon::parse($validated['occurred_at'])->startOfDay(),
        ]);

        return redirect()->route('expenses.index')->with('success', 'Expense recorded successfully.');
    }
}
