<?php

namespace App\Http\Controllers;

use App\Models\Ingredient;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class IngredientController extends Controller
{
    public function index(): View
    {
        return view('ingredients.index', [
            'ingredients' => Ingredient::latest()->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'unit' => ['required', 'string', 'max:50'],
            'cost_per_unit' => ['required', 'numeric', 'min:0'],
            'is_active' => ['boolean'],
        ]);

        Ingredient::create($validated);

        return redirect()->route('ingredients.index')->with('success', 'Ingredient saved successfully.');
    }
}
