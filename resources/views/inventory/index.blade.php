<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>The Pump Bar | Inventory</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="pump-app">
    @include('layouts.pump-navigation')
    <main class="max-w-7xl mx-auto p-6">
        <h1 class="text-2xl font-bold mb-4">Inventory Overview</h1>
        <div class="bg-white rounded-xl shadow overflow-hidden">
            <table class="min-w-full divide-y divide-slate-200">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-semibold uppercase text-slate-500">Branch</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold uppercase text-slate-500">Ingredient</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold uppercase text-slate-500">Stock</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold uppercase text-slate-500">Reorder</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold uppercase text-slate-500">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200">
                    @forelse($inventories as $inventory)
                        <tr>
                            <td class="px-6 py-4">{{ $inventory->branch->name ?? 'N/A' }}</td>
                            <td class="px-6 py-4">{{ $inventory->ingredient->name ?? 'N/A' }}</td>
                            <td class="px-6 py-4 font-semibold">{{ $inventory->quantity }}</td>
                            <td class="px-6 py-4">{{ $inventory->reorder_level }}</td>
                            <td class="px-6 py-4">
                                @if($inventory->quantity <= $inventory->reorder_level)
                                    <span class="bg-red-100 text-red-600 px-2 py-1 rounded-full text-xs font-semibold">Low stock</span>
                                @else
                                    <span class="bg-emerald-100 text-emerald-700 px-2 py-1 rounded-full text-xs font-semibold">Healthy</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-4 text-slate-500">No inventory yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </main>
</body>
</html>
