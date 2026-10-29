<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>The Pump Bar | Financial Reports</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="pump-app">
    @include('layouts.pump-navigation')
    <main class="max-w-7xl mx-auto p-6 space-y-6">
        <section class="bg-white rounded-xl shadow p-6 space-y-4">
            <div class="flex flex-wrap items-end justify-between gap-3">
                <div>
                    <h1 class="text-2xl font-bold mb-1">Financial performance report</h1>
                    <p class="text-slate-500">Sales, COGS, manual OPEX, commission, and net profit by day.</p>
                </div>
                <form method="GET" action="{{ route('reports.index') }}" class="flex flex-wrap items-end gap-3">
                    <div>
                        <label for="from" class="block text-sm font-medium text-slate-700">From</label>
                        <input id="from" type="date" name="from" value="{{ $fromDate->toDateString() }}">
                    </div>
                    <div>
                        <label for="to" class="block text-sm font-medium text-slate-700">To</label>
                        <input id="to" type="date" name="to" value="{{ $toDate->toDateString() }}">
                    </div>
                    <button type="submit" class="px-4">Apply</button>
                </form>
            </div>

            <div class="overflow-x-auto">
                <table class="pump-financial-table">
                    <thead>
                        <tr>
                            <th class="pump-financial-table__cups">Cups sold</th>
                            <th>Date</th>
                            <th>Gross sales</th>
                            <th>COGS</th>
                            <th>Gross profit</th>
                            <th>OPEX</th>
                            <th>Commission</th>
                            <th>Net profit</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="pump-financial-table__total">
                            <td>{{ number_format($financialTotals['cups_sold'] ?? 0) }}</td>
                            <td>Totals</td>
                            <td>₱{{ number_format($financialTotals['gross_sales'] ?? 0, 2) }}</td>
                            <td>₱{{ number_format($financialTotals['cogs'] ?? 0, 2) }}</td>
                            <td>₱{{ number_format($financialTotals['gross_profit'] ?? 0, 2) }}</td>
                            <td>₱{{ number_format($financialTotals['opex'] ?? 0, 2) }}</td>
                            <td>₱{{ number_format($financialTotals['commission'] ?? 0, 2) }}</td>
                            <td class="{{ ($financialTotals['net_profit'] ?? 0) < 0 ? 'text-red-600' : 'text-emerald-700' }}">₱{{ number_format($financialTotals['net_profit'] ?? 0, 2) }}</td>
                        </tr>
                        @forelse($financialRows as $row)
                            <tr>
                                <td>{{ number_format($row['cups_sold']) }}</td>
                                <td>{{ \Carbon\Carbon::parse($row['date'])->format('m/d/Y') }}</td>
                                <td>₱{{ number_format($row['gross_sales'], 2) }}</td>
                                <td>₱{{ number_format($row['cogs'], 2) }}</td>
                                <td>₱{{ number_format($row['gross_profit'], 2) }}</td>
                                <td>₱{{ number_format($row['opex'], 2) }}</td>
                                <td>₱{{ number_format($row['commission'], 2) }}</td>
                                <td class="{{ $row['net_profit'] < 0 ? 'text-red-600' : 'text-emerald-700' }}">₱{{ number_format($row['net_profit'], 2) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center text-slate-500 py-4">No sales or expenses found for this date range.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>

        <section class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <div class="bg-white rounded-xl shadow p-6">
                <h2 class="font-bold text-lg mb-3">Commission matrix (cups sold)</h2>
                <div class="overflow-x-auto">
                    <table class="pump-commission-table">
                        <thead>
                            <tr>
                                <th>Cups</th>
                                <th>Commission</th>
                                <th>Cups</th>
                                <th>Commission</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach(range(1, 30) as $cupValue)
                                @php
                                    $leftRow = $commissionMatrix[$cupValue - 1];
                                    $rightRow = $commissionMatrix[$cupValue + 29];
                                @endphp
                                <tr>
                                    <td>{{ $leftRow['cups'] }}</td>
                                    <td>₱{{ number_format($leftRow['commission'], 2) }}</td>
                                    <td>{{ $rightRow['cups'] }}</td>
                                    <td>₱{{ number_format($rightRow['commission'], 2) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow p-6">
                <h2 class="font-bold text-lg mb-3">Low stock items</h2>
                <div class="space-y-2">
                    @forelse($lowStock as $item)
                        <div class="border rounded-lg p-3 flex justify-between items-center">
                            <span>{{ $item->ingredient->name }} <span class="text-slate-500 text-sm">· {{ $item->branch->name }}</span></span>
                            <span class="text-red-600 font-semibold">{{ $item->quantity }} left</span>
                        </div>
                    @empty
                        <p class="text-slate-500">No low stock items.</p>
                    @endforelse
                </div>
            </div>
        </section>
    </main>
</body>
</html>
