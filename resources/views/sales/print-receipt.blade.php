<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Print Receipt - {{ config('app.name') }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @media print {
            body { background: white !important; color: black !important; }
            .no-print { display: none !important; }
            .print-only { display: block !important; }
            .receipt-container { border: none !important; box-shadow: none !important; }
        }
    </style>
</head>
<body class="bg-white text-black" onload="window.print()">
    <div class="max-w-md mx-auto p-4 receipt-container">
        <!-- Business Header -->
        <div class="text-center border-b-2 border-black pb-2 mb-4">
<h2 class="text-xl font-bold">Manliquid Store</h2>
            <p class="text-sm">Sales Receipt</p>
        </div>

        <!-- Transaction Info -->
        <div class="mb-4 text-sm">
            <div class="flex justify-between">
                <span>Transaction ID:</span>
                <span class="font-mono">{{ $sale->transaction_id ?? 'N/A' }}</span>
            </div>
            <div class="flex justify-between">
                <span>Date:</span>
                <span>{{ $sale->created_at->format('M d, Y H:i:s') }}</span>
            </div>
            <div class="flex justify-between">
                <span>Cashier:</span>
                <span>{{ $sale->user->name ?? 'Unknown' }}</span>
            </div>
            @if($sale->customer_name)
            <div class="flex justify-between">
                <span>Customer:</span>
                <span>{{ $sale->customer_name }}</span>
            </div>
            @endif
        </div>

        <!-- Items Table -->
        <div class="border-t border-b border-black py-2 mb-4">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-black">
                        <th class="text-left">Item</th>
                        <th class="text-center">Qty</th>
                        <th class="text-right">Price</th>
                        <th class="text-right">Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    @if(is_array($items) || is_object($items))
                        @foreach($items as $item)
                            <tr>
                                <td class="py-1">{{ $item['product_name'] ?? $item['name'] ?? 'Unknown' }}</td>
                                <td class="py-1 text-center">{{ $item['quantity'] }}</td>
                                <td class="py-1 text-right">₱{{ number_format($item['unit_price'], 2) }}</td>
                                <td class="py-1 text-right">₱{{ number_format($item['subtotal'], 2) }}</td>
                            </tr>
                        @endforeach
                    @endif
                </tbody>
            </table>
        </div>

        <!-- Total -->
        <div class="mb-4">
            <div class="flex justify-between text-lg font-bold">
                <span>Total:</span>
                <span>₱{{ number_format($sale->total_amount, 2) }}</span>
            </div>
        </div>

        <!-- Payment Info -->
        <div class="border-t border-black pt-2 text-sm">
            <div class="flex justify-between">
                <span>Payment Method:</span>
                <span>{{ ucfirst($sale->payment_method) }}</span>
            </div>
        </div>

        <!-- Footer -->
        <div class="text-center mt-4 pt-2 border-t border-black text-xs">
            <p>Thank you for your purchase!</p>
        </div>

        <!-- Back Button (No Print) -->
        <div class="mt-4 text-center no-print">
            <a href="{{ route('sales.receipt', $sale->id) }}" class="px-4 py-2 bg-blue-600 text-white rounded">
                ← Back to Receipt
            </a>
        </div>
    </div>
</body>
</html>

