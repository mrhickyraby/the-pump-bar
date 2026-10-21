<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>The Pump Bar | Products</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="pump-app">
    @include('layouts.pump-navigation')
    <main class="max-w-7xl mx-auto p-6">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="lg:col-span-2 bg-white rounded-xl shadow p-6">
                <h1 class="text-2xl font-bold mb-4">Products</h1>
                <div class="space-y-4">
                    @forelse($products as $product)
                        <div class="border rounded-lg p-4 flex justify-between items-center">
                            <div>
                                <div class="font-bold text-lg">{{ $product->name }}</div>
                                <div class="text-sm text-slate-500">{{ $product->category }} · {{ $product->size }} · {{ $product->flavor }}</div>
                            </div>
                            <div class="text-right">
                                <div class="font-bold text-emerald-600">₱{{ number_format($product->price, 2) }}</div>
                                <div class="text-xs uppercase text-slate-500">{{ $product->is_active ? 'Active' : 'Inactive' }}</div>
                            </div>
                        </div>
                    @empty
                        <p class="text-slate-500">No products created yet.</p>
                    @endforelse
                </div>
            </div>
            @if(Auth::user()->isOwner())
            <div class="bg-white rounded-xl shadow p-6">
                <h2 class="text-xl font-bold mb-4">Add product</h2>
                <form method="POST" action="{{ route('products.store') }}" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-sm font-medium text-slate-700">Name</label>
                        <input type="text" name="name" class="mt-1 w-full border-slate-300 rounded-md" required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700">Category</label>
                        <input type="text" name="category" class="mt-1 w-full border-slate-300 rounded-md" required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700">Size</label>
                        <input type="text" name="size" class="mt-1 w-full border-slate-300 rounded-md" required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700">Flavor</label>
                        <input type="text" name="flavor" class="mt-1 w-full border-slate-300 rounded-md" required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700">Price</label>
                        <input type="number" step="0.01" name="price" class="mt-1 w-full border-slate-300 rounded-md" required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700">Description</label>
                        <textarea name="description" rows="3" class="mt-1 w-full border-slate-300 rounded-md"></textarea>
                    </div>
                    <button type="submit" class="w-full bg-orange-500 text-white rounded-md px-4 py-2 font-semibold">Save product</button>
                </form>
            </div>
            @endif
        </div>
    </main>
</body>
</html>
