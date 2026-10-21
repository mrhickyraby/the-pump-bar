<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Branch;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class SaleController extends Controller
{
    public function index(): View
    {
        $user = Auth::user();

        return view('pos.index', [
            'products' => Product::where('is_active', true)->get(),
            'branches' => $user->isOwner() ? Branch::where('status', 'active')->get() : collect(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'product_id' => ['required', Rule::exists('products', 'id')->where('is_active', true)],
            'quantity' => ['required', 'integer', 'min:1'],
            'payment_method' => ['required', 'in:cash,card,transfer'],
            'branch_id' => [Auth::user()->isOwner() ? 'required' : 'nullable', 'exists:branches,id'],
        ]);

        $user = Auth::user();
        $product = Product::where('is_active', true)->findOrFail($validated['product_id']);
        $branchId = $user->isOwner() ? $validated['branch_id'] : $user->branch_id;
        abort_unless($branchId, 403, 'Your account is not assigned to a branch.');

        $sale = Sale::create([
            'branch_id' => $branchId,
            'user_id' => $user->id,
            'transaction_number' => 'TPB-'.now()->format('YmdHis'),
            'total' => $product->price * $validated['quantity'],
            'payment_method' => $validated['payment_method'],
            'status' => 'completed',
        ]);

        SaleItem::create([
            'sale_id' => $sale->id,
            'product_id' => $product->id,
            'quantity' => $validated['quantity'],
            'unit_price' => $product->price,
            'total' => $product->price * $validated['quantity'],
        ]);

        return redirect()->route('pos.index')->with('success', 'Sale recorded successfully.');
    }
}
