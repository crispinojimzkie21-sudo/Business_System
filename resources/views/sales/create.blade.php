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
    <title>Create Sale - {{ config('app.name') }}</title>
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
                <h1 class="text-3xl font-bold text-blue-400">💳 Process Sale</h1>
                <p class="text-blue-200 text-sm mt-1">Create a new sales transaction</p>
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

        @if (session('error'))
            <div class="mb-6 bg-red-900/50 border border-red-700 rounded-md p-4">
                <p class="text-red-200">{{ session('error') }}</p>
            </div>
        @endif

        @if ($errors->any())
            <div class="mb-6 bg-red-900/50 border border-red-700 rounded-md p-4">
                <ul class="text-red-200">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Product Selection -->
            <div class="bg-black/60 p-6 rounded-lg border border-blue-900/30">
                <h2 class="text-xl font-semibold mb-4 text-blue-400">Select Products</h2>
                
                <!-- Product List -->
                <div class="mb-4">
                    <label class="block text-sm font-medium text-blue-300 mb-2">Available Products</label>
                    <select id="productSelect" class="w-full px-4 py-2 border border-gray-700 bg-black/40 rounded-md text-white">
                        <option value="">Select a product...</option>
                        @foreach($products as $product)
                            <option value="{{ $product->id }}" data-name="{{ $product->name }}" data-price="{{ $product->price }}" data-stock="{{ $product->stock_quantity }}">
                                {{ $product->name }} - ₱{{ number_format($product->price, 2) }} (Stock: {{ $product->stock_quantity }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-blue-300 mb-2">Quantity</label>
                    <input type="number" id="productQuantity" min="1" value="1" class="w-full px-4 py-2 border border-gray-700 bg-black/40 rounded-md text-white">
                </div>

                <button type="button" id="addItemBtn" class="w-full px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-md transition-colors">
                    Add to Cart
                </button>

                <!-- Cart Table -->
                <div class="mt-6">
                    <h3 class="text-lg font-semibold mb-3 text-blue-400">Cart</h3>
                    <table class="w-full" id="cartTable">
                        <thead>
                            <tr class="border-b border-blue-900/30">
                                <th class="text-left py-2 text-blue-300">Product</th>
                                <th class="text-left py-2 text-blue-300">Qty</th>
                                <th class="text-left py-2 text-blue-300">Price</th>
                                <th class="text-left py-2 text-blue-300">Subtotal</th>
                                <th class="text-left py-2 text-blue-300"></th>
                            </tr>
                        </thead>
                        <tbody id="cartBody">
                            <tr>
                                <td colspan="5" class="py-4 text-center text-gray-400">Cart is empty</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Sale Form -->
            <div class="bg-black/60 p-6 rounded-lg border border-blue-900/30">
                <h2 class="text-xl font-semibold mb-4 text-blue-400">Sale Details</h2>
                
                <form method="POST" action="{{ route($routePrefix . '.store') }}" id="saleForm">
                    @csrf
                    <input type="hidden" name="items" id="itemsInput">
                    
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-blue-300 mb-2">Payment Method</label>
                        <select name="payment_method" class="w-full px-4 py-2 border border-gray-700 bg-black/40 rounded-md text-white" required>
                            <option value="cash">Cash</option>
                            <option value="card">Card</option>
                            <option value="bank_transfer">Bank Transfer</option>
                            <option value="bank_transfer">E Payment</option>
                        </select>
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-blue-300 mb-2">Customer Name (Optional)</label>
                        <input name="customer_name" type="text" class="w-full px-4 py-2 border border-gray-700 bg-black/40 rounded-md text-white placeholder-gray-400" placeholder="Customer name">
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-blue-300 mb-2">Customer Email (Optional)</label>
                        <input name="customer_email" type="email" class="w-full px-4 py-2 border border-gray-700 bg-black/40 rounded-md text-white placeholder-gray-400" placeholder="customer@example.com">
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-blue-300 mb-2">Customer Phone (Optional)</label>
                        <input name="customer_phone" type="text" class="w-full px-4 py-2 border border-gray-700 bg-black/40 rounded-md text-white placeholder-gray-400" placeholder="Phone number">
                    </div>

                    <div class="bg-black/40 p-4 rounded-lg mb-6">
                        <div class="flex justify-between items-center">
                            <span class="text-lg font-semibold text-blue-300">Total:</span>
                            <span class="text-2xl font-bold" id="totalAmount">₱0.00</span>
                        </div>
                    </div>

                    <button type="submit" id="submitBtn" class="w-full px-6 py-3 bg-green-600 hover:bg-green-700 text-white font-semibold rounded-md transition-colors disabled:opacity-50 disabled:cursor-not-allowed" disabled>
                        Complete Sale
                </form>
            </div>
        </div>
    </div>

    <script>
        let cart = [];

        document.getElementById('addItemBtn').addEventListener('click', function() {
            var select = document.getElementById('productSelect');
            var quantity = parseInt(document.getElementById('productQuantity').value);
            
            var selectedOption = select.options[select.selectedIndex];
            if (!selectedOption.value) {
                alert('Please select a product');
                return;
            }

            var productId = selectedOption.value;
            var productName = selectedOption.getAttribute('data-name');
            var productPrice = parseFloat(selectedOption.getAttribute('data-price'));
            var availableStock = parseInt(selectedOption.getAttribute('data-stock'));

            // Check if quantity exceeds stock
            var existingItem = cart.find(function(item) { return item.product_id === productId; });
            var currentQty = existingItem ? existingItem.quantity : 0;
            
            if (currentQty + quantity > availableStock) {
                alert('Insufficient stock. Available: ' + availableStock);
                return;
            }

            // Add or update item in cart
            if (existingItem) {
                existingItem.quantity += quantity;
            } else {
                cart.push({
                    product_id: productId,
                    name: productName,
                    quantity: quantity,
                    unit_price: productPrice
                });
            }

            renderCart();
            
            // Reset inputs
            select.value = '';
            document.getElementById('productQuantity').value = 1;
        });

        function renderCart() {
            var tbody = document.getElementById('cartBody');
            var totalEl = document.getElementById('totalAmount');
            var submitBtn = document.getElementById('submitBtn');
            
            if (cart.length === 0) {
                tbody.innerHTML = '<tr><td colspan="5" class="py-4 text-center text-gray-400">Cart is empty</td></tr>';
                totalEl.textContent = '₱0.00';
                submitBtn.disabled = true;
                document.getElementById('itemsInput').value = '';
                return;
            }

            var html = '';
            var total = 0;

            cart.forEach(function(item, index) {
                var subtotal = item.quantity * item.unit_price;
                total += subtotal;
                html += '<tr class="border-b border-blue-900/20">';
                html += '<td class="py-2">' + item.name + '</td>';
                html += '<td class="py-2">' + item.quantity + '</td>';
                html += '<td class="py-2">₱' + item.unit_price.toFixed(2) + '</td>';
                html += '<td class="py-2">₱' + subtotal.toFixed(2) + '</td>';
                html += '<td class="py-2"><button type="button" onclick="removeItem(' + index + ')" class="text-red-400 hover:text-red-300">✕</button></td>';
                html += '</tr>';
            });

            tbody.innerHTML = html;
            totalEl.textContent = '₱' + total.toFixed(2);
            submitBtn.disabled = false;
            
            // Update hidden input
            document.getElementById('itemsInput').value = JSON.stringify(cart);
        }

        function removeItem(index) {
            cart.splice(index, 1);
            renderCart();
        }
    </script>
</body>
</html>

