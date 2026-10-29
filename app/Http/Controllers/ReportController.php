<?php

namespace App\Http\Controllers;

use App\Models\Inventory;
use App\Support\FinancialReportService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class ReportController extends Controller
{
    public function __construct(private FinancialReportService $financialReportService) {}

    public function index(Request $request): View
    {
        $user = Auth::user();
        $validated = $request->validate([
            'from' => ['nullable', 'date'],
            'to' => ['nullable', 'date'],
        ]);

        $fromDate = Carbon::parse($validated['from'] ?? now()->startOfWeek()->toDateString())->startOfDay();
        $toDate = Carbon::parse($validated['to'] ?? now()->toDateString())->endOfDay();

        if ($fromDate->greaterThan($toDate)) {
            [$fromDate, $toDate] = [$toDate->copy()->startOfDay(), $fromDate->copy()->endOfDay()];
        }

        $financialSummary = $this->financialReportService->summarize($user, $fromDate, $toDate);
        $commissionMatrix = $this->financialReportService->commissionMatrix();

        $lowStock = Inventory::with(['branch', 'ingredient'])->when($user->role !== 'owner', fn ($query) => $query->where('branch_id', $user->branch_id))->whereColumn('quantity', '<=', 'reorder_level')->get();

        return view('reports.index', [
            'fromDate' => $fromDate,
            'toDate' => $toDate,
            'financialRows' => $financialSummary['rows'],
            'financialTotals' => $financialSummary['totals'],
            'commissionMatrix' => $commissionMatrix,
            'lowStock' => $lowStock,
        ]);
    }
}
