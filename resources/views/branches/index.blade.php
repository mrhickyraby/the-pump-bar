<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>The Pump Bar | Branches</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="pump-app">
    @include('layouts.pump-navigation')
    <main class="max-w-7xl mx-auto p-6">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="lg:col-span-2 bg-white rounded-xl shadow p-6">
                <h1 class="text-2xl font-bold mb-4">Branches</h1>
                <div class="space-y-4">
                    @forelse($branches as $branch)
                        <div class="border rounded-lg p-4">
                            <div class="flex justify-between items-center">
                                <div>
                                    <div class="text-lg font-bold">{{ $branch->name }}</div>
                                    <div class="text-sm text-slate-500">{{ $branch->code }} · {{ $branch->address }}</div>
                                </div>
                                <span class="bg-emerald-100 text-emerald-700 px-2 py-1 rounded-full text-xs font-semibold">{{ ucfirst($branch->status) }}</span>
                            </div>
                        </div>
                    @empty
                        <p class="text-slate-500">No branches found.</p>
                    @endforelse
                </div>
            </div>
            @if(Auth::user()->isOwner())
            <div class="bg-white rounded-xl shadow p-6">
                <h2 class="text-xl font-bold mb-4">Add branch</h2>
                <form method="POST" action="{{ route('branches.store') }}" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-sm font-medium text-slate-700">Branch name</label>
                        <input type="text" name="name" class="mt-1 w-full border-slate-300 rounded-md" required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700">Code</label>
                        <input type="text" name="code" class="mt-1 w-full border-slate-300 rounded-md" required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700">Address</label>
                        <input type="text" name="address" class="mt-1 w-full border-slate-300 rounded-md">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700">Phone</label>
                        <input type="text" name="phone" class="mt-1 w-full border-slate-300 rounded-md">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700">Status</label>
                        <select name="status" class="mt-1 w-full border-slate-300 rounded-md">
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                    </div>
                    <button type="submit" class="w-full bg-slate-900 text-white rounded-md px-4 py-2 font-semibold">Save branch</button>
                </form>
            </div>
            @endif
        </div>
    </main>
</body>
</html>
