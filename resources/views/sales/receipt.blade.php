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
    <title>Sales Receipt - {{ config('app.name') }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .glass-card {
            background: rgba(0, 0, 0, 0.4);
            backdrop-filter: blur(10px);
        }
        @media print {
            body { background: white; color: black; }
            .no-print { display: none; }
        }
    </style>
</head>
<body class="bg-gradient-to-br from-black via-gray-900 to-blue-900 min-h-screen text-white">
    <div class="max-w-2xl mx-auto p-6">
        <!-- Header -->
        <header class="flex items-center justify-between mb-8">
            <div>
                <h1 class="text-3xl font-bold text-blue-400">🧾 Sales Receipt</h1>
                <p class="text-blue-200 text-sm mt-1">Transaction Details</p>
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

        <!-- Receipt Details -->
        <div class="bg-black/60 p-6 rounded-lg border border-blue-900/30">
            <!-- Business Header -->
            <div class="text-center border-b border-blue-900/30 pb-4 mb-4">
<h2 class="text-2xl font-bold text-blue-400">Manliquid Store</h2>
                <p class="text-blue-200 text-sm">Sales Receipt</p>
            </div>

            <!-- Transaction Info -->
            <div class="mb-6">
                <div class="flex justify-between mb-2">
                    <span class="text-blue-300">Transaction ID:</span>
                    <span class="font-mono text-green-400">{{ $sale->transaction_id ?? 'N/A' }}</span>
                </div>
                <div class="flex justify-between mb-2">
                    <span class="text-blue-300">Date:</span>
                    <span>{{ $sale->created_at->format('F d, Y H:i:s') }}</span>
                </div>
                <div class="flex justify-between mb-2">
                    <span class="text-blue-300">Cashier:</span>
                    <span>{{ $sale->user->name ?? 'Unknown' }}</span>
                </div>
                @if($sale->customer_name)
                <div class="flex justify-between mb-2">
                    <span class="text-blue-300">Customer:</span>
                    <span>{{ $sale->customer_name }}</span>
                </div>
                @endif
            </div>

            <!-- Items Table -->
            <div class="border-t border-b border-blue-900/30 py-4 mb-4">
                <table class="w-full">
                    <thead>
                        <tr class="border-b border-blue-900/30">
                            <th class="text-left py-2 text-blue-300">Item</th>
                            <th class="text-center py-2 text-blue-300">Qty</th>
                            <th class="text-right py-2 text-blue-300">Price</th>
                            <th class="text-right py-2 text-blue-300">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if(is_array($items) || is_object($items))
                            @foreach($items as $item)
                                <tr class="border-b border-blue-900/20">
                                    <td class="py-2">{{ $item['product_name'] ?? $item['name'] ?? 'Unknown' }}</td>
                                    <td class="py-2 text-center">{{ $item['quantity'] }}</td>
                                    <td class="py-2 text-right">₱{{ number_format($item['unit_price'], 2) }}</td>
                                    <td class="py-2 text-right">₱{{ number_format($item['subtotal'], 2) }}</td>
                                </tr>
                            @endforeach
                        @endif
                    </tbody>
                </table>
            </div>

            <!-- Total -->
            <div class="mb-6">
                <div class="flex justify-between items-center text-xl font-bold">
                    <span class="text-blue-300">Total:</span>
                    <span class="text-green-400">₱{{ number_format($sale->total_amount, 2) }}</span>
                </div>
            </div>

            <!-- Payment Info -->
            <div class="border-t border-blue-900/30 pt-4">
                <div class="flex justify-between mb-2">
                    <span class="text-blue-300">Payment Method:</span>
                    <span>{{ ucfirst($sale->payment_method) }}</span>
                </div>
                @if($sale->customer_email)
                <div class="flex justify-between mb-2">
                    <span class="text-blue-300">Customer Email:</span>
                    <span>{{ $sale->customer_email }}</span>
                </div>
                @endif
                @if($sale->customer_phone)
                <div class="flex justify-between mb-2">
                    <span class="text-blue-300">Customer Phone:</span>
                    <span>{{ $sale->customer_phone }}</span>
                </div>
                @endif
            </div>

            <!-- Actions -->
            <div class="mt-8 flex gap-4 justify-center">
                <a href="{{ route($routePrefix . '.index') }}" class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-md transition-colors">
                    ← Back to Sales
                </a>
                <button onclick="window.print()" class="px-6 py-2 bg-green-600 hover:bg-green-700 text-white rounded-md transition-colors">
                    🖨️ Print Receipt
                </button>
                @if($sale->customer_email)
                <a href="{{ route($routePrefix . '.resend-receipt', $sale->id) }}" class="px-6 py-2 bg-purple-600 hover:bg-purple-700 text-white rounded-md transition-colors">
                    📧 Send Email Receipt
                </a>
                @endif
            </div>
        </div>
    </div>
</body>
</html>

