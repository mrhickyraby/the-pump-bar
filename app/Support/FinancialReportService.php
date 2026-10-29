<?php

namespace App\Support;

use App\Models\Expense;
use App\Models\Recipe;
use App\Models\SaleItem;
use App\Models\User;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Support\Collection;

class FinancialReportService
{
    /**
     * @var array<int, float>
     */
    private const COMMISSION_BY_CUPS = [
        1 => 0.0,
        2 => 0.0,
        3 => 0.0,
        4 => 0.0,
        5 => 50.0,
        6 => 60.0,
        7 => 70.0,
        8 => 80.0,
        9 => 90.0,
        10 => 100.0,
        11 => 115.0,
        12 => 130.0,
        13 => 145.0,
        14 => 160.0,
        15 => 175.0,
        16 => 190.0,
        17 => 205.0,
        18 => 220.0,
        19 => 235.0,
        20 => 250.0,
        21 => 270.0,
        22 => 290.0,
        23 => 310.0,
        24 => 330.0,
        25 => 350.0,
        26 => 370.0,
        27 => 390.0,
        28 => 410.0,
        29 => 430.0,
        30 => 450.0,
        31 => 470.0,
        32 => 490.0,
        33 => 510.0,
        34 => 530.0,
        35 => 550.0,
        36 => 570.0,
        37 => 590.0,
        38 => 410.0,
        39 => 430.0,
        40 => 450.0,
        41 => 475.0,
        42 => 500.0,
        43 => 525.0,
        44 => 550.0,
        45 => 575.0,
        46 => 600.0,
        47 => 625.0,
        48 => 650.0,
        49 => 675.0,
        50 => 700.0,
        51 => 725.0,
        52 => 750.0,
        53 => 775.0,
        54 => 800.0,
        55 => 825.0,
        56 => 850.0,
        57 => 875.0,
        58 => 900.0,
        59 => 925.0,
        60 => 950.0,
    ];

    /**
     * @return array{rows: Collection<int, array<string, float|string>>, totals: array<string, float>}
     */
    public function summarize(User $user, Carbon $startDate, Carbon $endDate): array
    {
        $dailyRows = $this->buildBaseRows($startDate, $endDate);

        $sales = SaleItem::query()
            ->with([
                'sale:id,branch_id,created_at,status',
                'product:id',
                'product.recipes:id,product_id,ingredient_id,quantity',
                'product.recipes.ingredient:id,cost_per_unit',
            ])
            ->whereHas('sale', function ($query) use ($user, $startDate, $endDate) {
                $query->where('status', 'completed')
                    ->whereBetween('created_at', [$startDate, $endDate])
                    ->when(! $user->isOwner(), fn ($saleQuery) => $saleQuery->where('branch_id', $user->branch_id));
            })
            ->get();

        foreach ($sales as $item) {
            $saleDate = $item->sale?->created_at?->toDateString();

            if (! $saleDate || ! isset($dailyRows[$saleDate])) {
                continue;
            }

            $cupsSold = (int) $item->quantity;
            $grossSales = (float) $item->total;
            $unitCogs = (float) $item->product?->recipes->sum(
                fn (Recipe $recipe): float => (float) $recipe->quantity * (float) ($recipe->ingredient?->cost_per_unit ?? 0)
            );
            $cogs = $unitCogs * $cupsSold;

            $dailyRows[$saleDate]['cups_sold'] += $cupsSold;
            $dailyRows[$saleDate]['gross_sales'] += $grossSales;
            $dailyRows[$saleDate]['cogs'] += $cogs;
        }

        $expenses = Expense::query()
            ->when(! $user->isOwner(), fn ($query) => $query->where('branch_id', $user->branch_id))
            ->where(function ($query) use ($startDate, $endDate) {
                $query->whereBetween('occurred_at', [$startDate, $endDate])
                    ->orWhere(function ($expenseQuery) use ($startDate, $endDate) {
                        $expenseQuery->whereNull('occurred_at')->whereBetween('created_at', [$startDate, $endDate]);
                    });
            })
            ->get(['amount', 'occurred_at', 'created_at']);

        foreach ($expenses as $expense) {
            $expenseDate = ($expense->occurred_at ?? $expense->created_at)?->toDateString();

            if (! $expenseDate || ! isset($dailyRows[$expenseDate])) {
                continue;
            }

            $dailyRows[$expenseDate]['opex'] += (float) $expense->amount;
        }

        $finalRows = collect($dailyRows)->map(function (array $row): array {
            $grossProfit = $row['gross_sales'] - $row['cogs'];
            $commission = $this->commissionForCups((int) $row['cups_sold']);
            $netProfit = $grossProfit - $row['opex'] - $commission;

            return [
                'date' => $row['date'],
                'cups_sold' => $row['cups_sold'],
                'gross_sales' => round($row['gross_sales'], 2),
                'cogs' => round($row['cogs'], 2),
                'gross_profit' => round($grossProfit, 2),
                'opex' => round($row['opex'], 2),
                'commission' => round($commission, 2),
                'net_profit' => round($netProfit, 2),
            ];
        })->values();

        return [
            'rows' => $finalRows,
            'totals' => [
                'cups_sold' => (float) $finalRows->sum('cups_sold'),
                'gross_sales' => round((float) $finalRows->sum('gross_sales'), 2),
                'cogs' => round((float) $finalRows->sum('cogs'), 2),
                'gross_profit' => round((float) $finalRows->sum('gross_profit'), 2),
                'opex' => round((float) $finalRows->sum('opex'), 2),
                'commission' => round((float) $finalRows->sum('commission'), 2),
                'net_profit' => round((float) $finalRows->sum('net_profit'), 2),
            ],
        ];
    }

    /**
     * @return Collection<int, array{cups:int, commission:float}>
     */
    public function commissionMatrix(): Collection
    {
        return collect(range(1, 60))->map(fn (int $cups): array => [
            'cups' => $cups,
            'commission' => self::COMMISSION_BY_CUPS[$cups],
        ]);
    }

    private function commissionForCups(int $cupsSold): float
    {
        if ($cupsSold < 1) {
            return 0.0;
        }

        return self::COMMISSION_BY_CUPS[min($cupsSold, 60)] ?? 0.0;
    }

    /**
     * @return array<string, array<string, float|string>>
     */
    private function buildBaseRows(Carbon $startDate, Carbon $endDate): array
    {
        $rows = [];

        foreach (CarbonPeriod::create($startDate->copy()->startOfDay(), $endDate->copy()->startOfDay()) as $date) {
            $dateKey = $date->toDateString();
            $rows[$dateKey] = [
                'date' => $dateKey,
                'cups_sold' => 0.0,
                'gross_sales' => 0.0,
                'cogs' => 0.0,
                'opex' => 0.0,
            ];
        }

        return $rows;
    }
}
