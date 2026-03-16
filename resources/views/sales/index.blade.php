@php
    // Dynamic route prefix based on user role
    $routePrefix = 'sales'; // default
    if (Auth::user()->isSuperAdmin()) {
        $routePrefix = 'superadmin.sales';
    } elseif (Auth::user()->isAdmin()) {
        $routePrefix = 'admin.sales';
    } elseif (Auth::user()->isCashier()) {
        $routePrefix = 'cashier.sales';
    } elseif (Auth::user()->isManager() || Auth::user()->isEmployee()) {
        $routePrefix = 'employee.sales';
    }
@endphp

<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Sales Management - {{ config('app.name') }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .glass-card {
            background: rgba(0, 0, 0, 0.4);
            backdrop-filter: blur(10px);
        }
    </style>
</head>
<body class="bg-gradient-to-br from-black via-gray-900 to-blue-900 min-h-screen text-white">
    <div class="max-w-6xl mx-auto p-6">
        <!-- Header -->
        <header class="flex items-center justify-between mb-8">
            <div>
                <h1 class="text-3xl font-bold text-blue-400">💼 Sales Dashboard</h1>
                <p class="text-blue-200 text-sm mt-1">Track and manage sales</p>
            </div>
            <div class="flex gap-4">
                @if(Auth::user()->isSuperAdmin())
                    <a href="{{ url('/dashboard/super-admin') }}" class="px-4 py-2 text-blue-200 hover:bg-blue-900/30 rounded">← Dashboard</a>
                @elseif(Auth::user()->isAdmin())
                    <a href="{{ url('/dashboard/admin') }}" class="px-4 py-2 text-blue-200 hover:bg-blue-900/30 rounded">← Dashboard</a>
                @elseif(Auth::user()->isCashier())
                    <a href="{{ url('/dashboard/cashier') }}" class="px-4 py-2 text-blue-200 hover:bg-blue-900/30 rounded">← Dashboard</a>
                @else
                    <a href="{{ url('/dashboard/user') }}" class="px-4 py-2 text-blue-200 hover:bg-blue-900/30 rounded">← Dashboard</a>
                @endif
            </div>
        </header>

        @if (session('success'))
            <div class="mb-6 bg-green-900/50 border border-green-700 rounded-md p-4">
                <p class="text-green-200">{{ session('success') }}</p>
            </div>
        @endif

        @if (session('error'))
            <div class="mb-6 bg-red-900/50 border border-red-700 rounded-md p-4">
                <p class="text-red-200">{{ session('error') }}</p>
            </div>
        @endif

        <!-- Quick Stats -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8">
            <div class="bg-black/60 p-6 rounded-lg border border-blue-900/30">
                <h3 class="text-blue-400 font-semibold">Today's Sales</h3>
                <p class="text-3xl font-bold">₱{{ number_format($todaySales ?? 0, 2) }}</p>
            </div>
            <div class="bg-black/60 p-6 rounded-lg border border-blue-900/30">
                <h3 class="text-blue-400 font-semibold">Today's Transactions</h3>
                <p class="text-3xl font-bold">{{ $todayTransactions ?? 0 }}</p>
            </div>
            <div class="bg-black/60 p-6 rounded-lg border border-blue-900/30">
                <a href="{{ route($routePrefix . '.create') }}" class="block text-center px-6 py-4 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-md transition-colors">
                    💳 Process New Sale
                </a>
            </div>
        </div>

        <!-- Sales Actions -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <a href="{{ route($routePrefix . '.create') }}" class="block p-6 bg-black/60 rounded-lg border border-blue-900/30 hover:bg-blue-900/30 transition-colors">
                <span class="font-semibold text-lg">💳 New Sale</span>
                <p class="text-sm text-blue-200 mt-1">Process a new transaction</p>
            </a>
            <a href="{{ route($routePrefix . '.history') }}" class="block p-6 bg-black/60 rounded-lg border border-blue-900/30 hover:bg-blue-900/30 transition-colors">
                <span class="font-semibold text-lg">📜 Sales History</span>
                <p class="text-sm text-blue-200 mt-1">View past transactions</p>
            </a>
            @if(Auth::user()->isAdmin() || Auth::user()->isSuperAdmin())
            <a href="{{ route($routePrefix . '.reports') }}" class="block p-6 bg-black/60 rounded-lg border border-blue-900/30 hover:bg-blue-900/30 transition-colors">
                <span class="font-semibold text-lg">📊 Sales Reports</span>
                <p class="text-sm text-blue-200 mt-1">View detailed reports</p>
            </a>
            @endif
        </div>
    </div>
</body>
</html>

