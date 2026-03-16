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
    <title>Sales Reports - {{ config('app.name') }}</title>
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
                <h1 class="text-3xl font-bold text-blue-400">📊 Sales Reports</h1>
                <p class="text-blue-200 text-sm mt-1">View detailed sales analytics</p>
            </div>
            <div class="flex gap-4">
                <a href="{{ route($routePrefix . '.index') }}" class="px-4 py-2 text-blue-200 hover:bg-blue-900/30 rounded">← Back to Sales</a>
            </div>
        </header>

        @if (session('success'))
            <div class="mb-6 bg-green-900/50 border border-green-700 rounded-md p-4">
                <p class="text-green-200">{{ session('success') }}</p>
            </div>
        @endif

        <!-- Daily Sales -->
        <div class="bg-black/60 rounded-lg border border-blue-900/30 overflow-hidden mb-6">
            <div class="p-6">
                <h2 class="text-xl font-semibold mb-4 text-blue-400">Daily Sales (Last 30 Days)</h2>
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="border-b border-blue-900/30">
                                <th class="text-left py-3 px-4 text-blue-300">Date</th>
                                <th class="text-left py-3 px-4 text-blue-300">Total Sales</th>
                                <th class="text-left py-3 px-4 text-blue-300">Transactions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($dailySales as $sale)
                                <tr class="border-b border-blue-900/20 hover:bg-blue-900/10">
                                    <td class="py-3 px-4">{{ $sale->date }}</td>
                                    <td class="py-3 px-4">₱{{ number_format($sale->total, 2) }}</td>
                                    <td class="py-3 px-4">{{ $sale->transactions }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="py-8 text-center text-gray-400">
                                        No daily sales data available.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Weekly Sales -->
        <div class="bg-black/60 rounded-lg border border-blue-900/30 overflow-hidden mb-6">
            <div class="p-6">
                <h2 class="text-xl font-semibold mb-4 text-blue-400">Weekly Sales (Last 12 Weeks)</h2>
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="border-b border-blue-900/30">
                                <th class="text-left py-3 px-4 text-blue-300">Week</th>
                                <th class="text-left py-3 px-4 text-blue-300">Total Sales</th>
                                <th class="text-left py-3 px-4 text-blue-300">Transactions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($weeklySales as $sale)
                                <tr class="border-b border-blue-900/20 hover:bg-blue-900/10">
                                    <td class="py-3 px-4">Week {{ $sale->week }}</td>
                                    <td class="py-3 px-4">₱{{ number_format($sale->total, 2) }}</td>
                                    <td class="py-3 px-4">{{ $sale->transactions }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="py-8 text-center text-gray-400">
                                        No weekly sales data available.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Monthly Sales -->
        <div class="bg-black/60 rounded-lg border border-blue-900/30 overflow-hidden">
            <div class="p-6">
                <h2 class="text-xl font-semibold mb-4 text-blue-400">Monthly Sales (Last 12 Months)</h2>
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="border-b border-blue-900/30">
                                <th class="text-left py-3 px-4 text-blue-300">Month</th>
                                <th class="text-left py-3 px-4 text-blue-300">Total Sales</th>
                                <th class="text-left py-3 px-4 text-blue-300">Transactions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($monthlySales as $sale)
                                <tr class="border-b border-blue-900/20 hover:bg-blue-900/10">
                                    <td class="py-3 px-4">{{ $sale->year }}-{{ $sale->month }}</td>
                                    <td class="py-3 px-4">₱{{ number_format($sale->total, 2) }}</td>
                                    <td class="py-3 px-4">{{ $sale->transactions }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="py-8 text-center text-gray-400">
                                        No monthly sales data available.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</body>
</html>

