<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>The Pump Bar | Reports</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="pump-app">
    @include('layouts.pump-navigation')
    <main class="max-w-7xl mx-auto p-6 space-y-6">
        <div class="bg-white rounded-xl shadow p-6">
            <h1 class="text-2xl font-bold mb-4">Sales and Inventory Reports</h1>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <h2 class="font-bold text-lg mb-3">Recent sales</h2>
                    @forelse($sales as $sale)
                        <div class="border-b py-2 flex justify-between">
                            <span>{{ $sale->transaction_number }} <span class="text-slate-500">· {{ $sale->user->name ?? 'Staff' }}</span></span>
                            <span class="font-semibold">₱{{ number_format($sale->total, 2) }}</span>
                        </div>
                    @empty
                        <p class="text-slate-500">No sales found.</p>
                    @endforelse
                </div>
                <div>
                    <h2 class="font-bold text-lg mb-3">Low stock items</h2>
                    @forelse($lowStock as $item)
                        <div class="border-b py-2 flex justify-between">
                            <span>{{ $item->ingredient->name }}</span>
                            <span class="text-red-600 font-semibold">{{ $item->quantity }} left</span>
                        </div>
                    @empty
                        <p class="text-slate-500">No low stock items.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </main>
</body>
</html>
