<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>The Pump Bar | Expenses</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="pump-app">
    @include('layouts.pump-navigation')
    <main class="max-w-7xl mx-auto p-6 grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 bg-white rounded-xl shadow p-6">
            <h1 class="text-2xl font-bold mb-4">Expense Records</h1>
            <div class="space-y-4">
                @forelse($expenses as $expense)
                    <div class="border rounded-lg p-4 flex justify-between items-center">
                        <div>
                            <div class="font-bold">{{ $expense->category }}</div>
                            <div class="text-sm text-slate-500">{{ $expense->branch->name ?? 'Branch' }} · {{ $expense->description ?? 'No notes' }}</div>
                        </div>
                        <div class="text-right">
                            <div class="font-bold text-red-600">₱{{ number_format($expense->amount, 2) }}</div>
                            <div class="text-xs text-slate-500">{{ $expense->occurred_at?->format('M d, Y') }}</div>
                        </div>
                    </div>
                @empty
                    <p class="text-slate-500">No expense records found.</p>
                @endforelse
            </div>
        </div>
        @if(Auth::user()->isOwner() || Auth::user()->isManager())
        <div class="bg-white rounded-xl shadow p-6">
            <h2 class="text-xl font-bold mb-4">Add expense</h2>
            <form method="POST" action="{{ route('expenses.store') }}" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-sm font-medium text-slate-700">Branch</label>
                    @if(Auth::user()->isOwner())
                        <select name="branch_id" class="mt-1 w-full border-slate-300 rounded-md" required>
                            @foreach($branches as $branch)
                                <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                            @endforeach
                        </select>
                    @else
                        <input type="text" value="{{ $branches->first()?->name }}" class="mt-1 w-full border-slate-300 rounded-md" disabled>
                    @endif
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700">Category</label>
                    <input type="text" name="category" class="mt-1 w-full border-slate-300 rounded-md" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700">Amount</label>
                    <input type="number" step="0.01" name="amount" class="mt-1 w-full border-slate-300 rounded-md" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700">Description</label>
                    <textarea name="description" rows="3" class="mt-1 w-full border-slate-300 rounded-md"></textarea>
                </div>
                <button type="submit" class="w-full bg-red-500 text-white rounded-md px-4 py-2 font-semibold">Save expense</button>
            </form>
        </div>
        @endif
    </main>
</body>
</html>
