<?php

use App\Http\Controllers\BranchController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\IngredientController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/', function (Request $request) {
    $destination = $request->user()?->isStaff() ? 'pos.index' : 'dashboard';

    return redirect()->route($destination);
});

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified', 'role:owner,manager'])
    ->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/branches', [BranchController::class, 'index'])->name('branches.index')->middleware('role:owner,manager');
    Route::post('/branches', [BranchController::class, 'store'])->name('branches.store')->middleware('role:owner');

    Route::get('/products', [ProductController::class, 'index'])->name('products.index')->middleware('role:owner,manager,staff');
    Route::post('/products', [ProductController::class, 'store'])->name('products.store')->middleware('role:owner');

    Route::get('/ingredients', [IngredientController::class, 'index'])->name('ingredients.index')->middleware('role:owner');
    Route::post('/ingredients', [IngredientController::class, 'store'])->name('ingredients.store')->middleware('role:owner');

    Route::get('/inventory', [InventoryController::class, 'index'])->name('inventory.index')->middleware('role:owner,manager');

    Route::get('/expenses', [ExpenseController::class, 'index'])->name('expenses.index')->middleware('role:owner,manager');
    Route::post('/expenses', [ExpenseController::class, 'store'])->name('expenses.store')->middleware('role:owner,manager');

    Route::get('/pos', [SaleController::class, 'index'])->name('pos.index')->middleware('role:owner,manager,staff');
    Route::post('/pos', [SaleController::class, 'store'])->name('pos.store')->middleware('role:owner,manager,staff');

    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index')->middleware('role:owner,manager');

    Route::get('/users', [UserController::class, 'index'])->name('users.index')->middleware('role:owner');
    Route::post('/users', [UserController::class, 'store'])->name('users.store')->middleware('role:owner');
});

require __DIR__.'/auth.php';
