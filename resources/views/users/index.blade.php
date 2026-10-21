<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>The Pump Bar | Users</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="pump-app">
    @include('layouts.pump-navigation')
    <main class="max-w-7xl mx-auto p-6 grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 bg-white rounded-xl shadow p-6">
            <h1 class="text-2xl font-bold mb-4">Users</h1>
            <div class="space-y-4">
                @forelse($users as $user)
                    <div class="border rounded-lg p-4 flex justify-between items-center">
                        <div>
                            <div class="font-bold">{{ $user->name }}</div>
                            <div class="text-sm text-slate-500">{{ $user->email }} · {{ $user->branch->name ?? 'No branch' }}</div>
                        </div>
                        <span class="bg-slate-200 text-slate-700 px-2 py-1 rounded-full text-xs font-semibold uppercase">{{ $user->role }}</span>
                    </div>
                @empty
                    <p class="text-slate-500">No users created yet.</p>
                @endforelse
            </div>
        </div>
        @if(Auth::user()->isOwner())
        <div class="bg-white rounded-xl shadow p-6">
            <h2 class="text-xl font-bold mb-4">Create user</h2>
            <form method="POST" action="{{ route('users.store') }}" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-sm font-medium text-slate-700">Full name</label>
                    <input type="text" name="name" class="mt-1 w-full border-slate-300 rounded-md" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700">Email</label>
                    <input type="email" name="email" class="mt-1 w-full border-slate-300 rounded-md" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700">Role</label>
                    <select name="role" class="mt-1 w-full border-slate-300 rounded-md">
                        <option value="owner">Owner</option>
                        <option value="manager">Manager</option>
                        <option value="staff">Staff</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700">Branch</label>
                    <select name="branch_id" class="mt-1 w-full border-slate-300 rounded-md">
                        @foreach($branches as $branch)
                            <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700">Password</label>
                    <input type="password" name="password" class="mt-1 w-full border-slate-300 rounded-md" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700">Confirm password</label>
                    <input type="password" name="password_confirmation" class="mt-1 w-full border-slate-300 rounded-md" required>
                </div>
                <button type="submit" class="w-full bg-slate-900 text-white rounded-md px-4 py-2 font-semibold">Save user</button>
            </form>
        </div>
        @endif
    </main>
</body>
</html>
