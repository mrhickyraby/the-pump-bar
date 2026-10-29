<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\Expense;
use App\Models\Inventory;
use App\Models\Product;
use App\Models\Sale;
use App\Support\FinancialReportService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __construct(private FinancialReportService $financialReportService) {}

    public function index(Request $request): View
    {
        $user = Auth::user();
        $validated = $request->validate([
            'period' => ['nullable', Rule::in(['day', 'week', 'month'])],
            'date' => ['nullable', 'date'],
        ]);

        $branchQuery = $user->role === 'owner'
            ? Branch::query()
            : Branch::whereKey($user->branch_id);

        $branches = $branchQuery->get();
        $products = Product::where('is_active', true)->count();
        $lowStock = Inventory::when($user->role !== 'owner', fn ($query) => $query->where('branch_id', $user->branch_id))->whereColumn('quantity', '<=', 'reorder_level')->count();
        $transactions = Sale::when($user->role !== 'owner', fn ($query) => $query->where('branch_id', $user->branch_id))->count();
        $salesTotal = Sale::when($user->role !== 'owner', fn ($query) => $query->where('branch_id', $user->branch_id))->sum('total');
        $expenseTotal = Expense::when($user->role !== 'owner', fn ($query) => $query->where('branch_id', $user->branch_id))->sum('amount');

        $selectedPeriod = $validated['period'] ?? 'day';
        $selectedDate = Carbon::parse($validated['date'] ?? now()->toDateString());
        $rangeStart = $selectedDate->copy()->startOfDay();
        $rangeEnd = $selectedDate->copy()->endOfDay();

        if ($selectedPeriod === 'week') {
            $rangeStart = $selectedDate->copy()->startOfWeek();
            $rangeEnd = $selectedDate->copy()->endOfWeek();
        } elseif ($selectedPeriod === 'month') {
            $rangeStart = $selectedDate->copy()->startOfMonth();
            $rangeEnd = $selectedDate->copy()->endOfMonth();
        }

        $financialSummary = $user->isOwner()
            ? $this->financialReportService->summarize($user, $rangeStart, $rangeEnd)
            : ['rows' => collect(), 'totals' => []];

        if ($user->isOwner()) {
            $salesTotal = $financialSummary['totals']['gross_sales'];
            $expenseTotal = $financialSummary['totals']['opex'];
        }

        $rangeLabel = match ($selectedPeriod) {
            'week' => sprintf('%s - %s', $rangeStart->format('M j, Y'), $rangeEnd->format('M j, Y')),
            'month' => $rangeStart->format('F Y'),
            default => $rangeStart->format('M j, Y'),
        };

        return view('dashboard', compact(
            'branches',
            'salesTotal',
            'expenseTotal',
            'products',
            'lowStock',
            'transactions',
            'selectedPeriod',
            'selectedDate',
            'rangeLabel',
            'financialSummary',
        ));
    }
}
