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

        <!-- Real-time Status Indicator -->
        <div class="fixed bottom-4 right-4 bg-green-600/20 border border-green-600/50 rounded-lg px-3 py-2 flex items-center space-x-2">
            <div class="w-2 h-2 bg-green-400 rounded-full animate-pulse"></div>
            <span class="text-green-400 text-xs">Live Updates</span>
        </div>
    </div>

    <script>
        // Real-time sales history updates
        let lastSaleId = @php echo $sales->first()->id ?? 0; @endphp;
        let isUpdating = false;

        function checkForNewSales() {
            if (isUpdating) return;
            
            isUpdating = true;
            
            fetch('/api/cashier/sales/latest?last_id=' + lastSaleId)
                .then(response => response.json())
                .then(data => {
                    if (data.success && data.new_sales && data.new_sales.length > 0) {
                        // Update the table with new sales
                        updateSalesTable(data.new_sales);
                        lastSaleId = data.new_sales[0].id;
                        
                        // Show notification
                        showNotification('New sale recorded!', 'success');
                    }
                })
                .catch(error => {
                    console.error('Error checking for new sales:', error);
                })
                .finally(() => {
                    isUpdating = false;
                });
        }

        function updateSalesTable(newSales) {
            const tbody = document.querySelector('tbody');
            const noSalesRow = tbody.querySelector('td[colspan="7"]');
            
            // Remove "No sales found" row if it exists
            if (noSalesRow) {
                noSalesRow.parentElement.remove();
            }
            
            // Add new sales to the top of the table
            newSales.forEach(sale => {
                const row = createSaleRow(sale);
                tbody.insertBefore(row, tbody.firstChild);
            });
            
            // Limit table to show only latest 50 rows for performance
            const rows = tbody.querySelectorAll('tr');
            if (rows.length > 50) {
                for (let i = 50; i < rows.length; i++) {
                    rows[i].remove();
                }
            }
        }

        function createSaleRow(sale) {
            const row = document.createElement('tr');
            row.className = 'border-b border-blue-900/20 hover:bg-blue-900/10 bg-green-900/20';
            row.innerHTML = `
                <td class="py-3 px-4 font-mono text-green-400">${sale.transaction_id || 'N/A'}</td>
                <td class="py-3 px-4">${new Date(sale.created_at).toLocaleString()}</td>
                <td class="py-3 px-4">${sale.customer_name || 'N/A'}</td>
                <td class="py-3 px-4">${sale.payment_method ? sale.payment_method.charAt(0).toUpperCase() + sale.payment_method.slice(1) : 'N/A'}</td>
                <td class="py-3 px-4">₱${parseFloat(sale.total_amount || 0).toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2})}</td>
                <td class="py-3 px-4">${sale.user_name || 'Unknown'}</td>
                <td class="py-3 px-4">
                    <a href="/cashier/sales/${sale.id}/receipt" class="px-2 py-1 bg-blue-600 hover:bg-blue-700 text-white text-xs rounded inline-block">
                        View
                    </a>
                </td>
            `;
            
            // Highlight effect for new rows
            row.style.animation = 'slideIn 0.5s ease-out';
            
            return row;
        }

        function showNotification(message, type = 'info') {
            const notification = document.createElement('div');
            notification.className = `fixed top-4 right-4 px-4 py-3 rounded-lg shadow-lg z-50 flex items-center space-x-2 ${
                type === 'success' ? 'bg-green-600/90 text-white' : 'bg-blue-600/90 text-white'
            }`;
            notification.innerHTML = `
                <i class="fas fa-${type === 'success' ? 'check-circle' : 'info-circle'}"></i>
                <span>${message}</span>
            `;
            
            document.body.appendChild(notification);
            
            // Auto-remove after 3 seconds
            setTimeout(() => {
                notification.style.opacity = '0';
                notification.style.transform = 'translateX(100%)';
                setTimeout(() => notification.remove(), 300);
            }, 3000);
        }

        // Start checking for new sales every 10 seconds
        setInterval(checkForNewSales, 10000);
        
        // Initial check after page load
        setTimeout(checkForNewSales, 2000);

        // Add CSS animation
        const style = document.createElement('style');
        style.textContent = `
            @keyframes slideIn {
                from {
                    opacity: 0;
                    transform: translateX(-20px);
                    background-color: rgba(34, 197, 94, 0.3);
                }
                to {
                    opacity: 1;
                    transform: translateX(0);
                    background-color: rgba(34, 197, 94, 0.1);
                }
            }
        `;
        document.head.appendChild(style);
    </script>
</body>
</html>

