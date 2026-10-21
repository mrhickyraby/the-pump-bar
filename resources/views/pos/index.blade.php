<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>The Pump Bar | POS</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="pump-app">
    @include('layouts.pump-navigation')
    <main class="max-w-5xl mx-auto p-6">
        <div class="bg-white rounded-xl shadow p-6">
            <h1 class="text-2xl font-bold mb-4">Point of Sale</h1>
            <form method="POST" action="{{ route('pos.store') }}" class="grid grid-cols-1 md:grid-cols-2 gap-6">
                @csrf
                @if(Auth::user()->isOwner())
                <div>
                    <label class="block text-sm font-medium text-slate-700">Branch</label>
                    <select name="branch_id" class="mt-1 w-full border-slate-300 rounded-md" required>
                        @foreach($branches as $branch)
                            <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                        @endforeach
                    </select>
                </div>
                @endif
                <div>
                    <label class="block text-sm font-medium text-slate-700">Product</label>
                    <select name="product_id" class="mt-1 w-full border-slate-300 rounded-md" required>
                        <option value="">Select product</option>
                        @foreach($products as $product)
                            <option value="{{ $product->id }}">{{ $product->name }} — ₱{{ number_format($product->price, 2) }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700">Quantity</label>
                    <input type="number" name="quantity" min="1" value="1" class="mt-1 w-full border-slate-300 rounded-md" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700">Payment method</label>
                    <select name="payment_method" class="mt-1 w-full border-slate-300 rounded-md">
                        <option value="cash">Cash</option>
                        <option value="card">Card</option>
                        <option value="transfer">Transfer</option>
                    </select>
                </div>
                <div class="flex items-end">
                    <button type="submit" class="w-full bg-orange-500 text-white rounded-md px-4 py-3 font-semibold">Process sale</button>
                </div>
            </form>
        </div>
    </main>
</body>
</html>
