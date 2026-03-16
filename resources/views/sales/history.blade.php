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
    <title>Sales History - {{ config('app.name') }}</title>
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
                <h1 class="text-3xl font-bold text-blue-400">📜 Sales History</h1>
                <p class="text-blue-200 text-sm mt-1">View all past transactions</p>
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

        <!-- Sales History List -->
        <div class="bg-black/60 rounded-lg border border-blue-900/30 overflow-hidden">
            <div class="p-6">
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="border-b border-blue-900/30">
                                <th class="text-left py-3 px-4 text-blue-300">Transaction ID</th>
                                <th class="text-left py-3 px-4 text-blue-300">Date</th>
                                <th class="text-left py-3 px-4 text-blue-300">Customer</th>
                                <th class="text-left py-3 px-4 text-blue-300">Payment</th>
                                <th class="text-left py-3 px-4 text-blue-300">Total</th>
                                <th class="text-left py-3 px-4 text-blue-300">Processed By</th>
                                <th class="text-left py-3 px-4 text-blue-300">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($sales as $sale)
                                <tr class="border-b border-blue-900/20 hover:bg-blue-900/10">
                                    <td class="py-3 px-4 font-mono text-green-400">{{ $sale->transaction_id ?? 'N/A' }}</td>
                                    <td class="py-3 px-4">{{ $sale->created_at->format('M d, Y H:i') }}</td>
                                    <td class="py-3 px-4">{{ $sale->customer_name ?? 'N/A' }}</td>
                                    <td class="py-3 px-4">{{ ucfirst($sale->payment_method) }}</td>
                                    <td class="py-3 px-4">₱{{ number_format($sale->total_amount, 2) }}</td>
                                    <td class="py-3 px-4">{{ $sale->user->name ?? 'Unknown' }}</td>
                                    <td class="py-3 px-4">
                                        <a href="{{ route($routePrefix . '.receipt', $sale->id) }}" class="px-2 py-1 bg-blue-600 hover:bg-blue-700 text-white text-xs rounded inline-block">
                                            View
                                        </a>
                                        @if(Auth::user()->role === 'super_admin')
                                        <form action="{{ route($routePrefix . '.destroy', $sale->id) }}" method="POST" class="inline-block ml-1" onsubmit="return confirm('Are you sure you want to delete this sale? Stock will be restored.');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="px-2 py-1 bg-red-600 hover:bg-red-700 text-white text-xs rounded">
                                                Delete
                                            </button>
                                        </form>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="py-8 text-center text-gray-400">
                                        No sales history found.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Pagination -->
        <div class="mt-4">
            {{ $sales->links() }}
        </div>
    </div>
</body>
</html>

