<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BranchController extends Controller
{
    public function index(): View
    {
        $user = Auth::user();
        $branches = Branch::query()
            ->when(! $user->isOwner(), fn ($query) => $query->whereKey($user->branch_id))
            ->latest()
            ->get();

        return view('branches.index', [
            'branches' => $branches,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'code' => ['required', 'string', 'max:50', 'unique:branches,code'],
            'address' => ['nullable', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'status' => ['required', 'in:active,inactive'],
        ]);

        Branch::create($validated);

        return redirect()->route('branches.index')->with('success', 'Branch created successfully.');
    }
}
