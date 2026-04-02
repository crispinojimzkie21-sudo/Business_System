<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Products - {{ config('app.name') }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gradient-to-br from-black via-gray-900 to-green-900 min-h-screen text-white">
    <div class="max-w-7xl mx-auto p-6">
        <!-- Header -->
        <header class="flex items-center justify-between mb-8">
            <div>
                <h1 class="text-3xl font-bold text-green-400">📦 Products Management</h1>
                <p class="text-green-200 text-sm mt-1">Manage product inventory</p>
            </div>
            <div class="flex items-center gap-4">
@if(Auth::user()->isCashier())
                    <a href="{{ route('dashboard.cashier') }}" class="px-4 py-2 text-green-200 hover:bg-green-900/30 rounded">← Dashboard</a>
                @elseif(Auth::user()->isSuperAdmin())
                    <a href="{{ url('/dashboard/super-admin') }}" class="px-4 py-2 text-green-200 hover:bg-green-900/30 rounded">← Dashboard</a>
                @elseif(Auth::user()->isAdmin())
                    <a href="{{ url('/dashboard/admin') }}" class="px-4 py-2 text-green-200 hover:bg-green-900/30 rounded">← Dashboard</a>
                @else
                    <a href="{{ route('dashboard.employee') }}" class="px-4 py-2 text-green-200 hover:bg-green-900/30 rounded">← Dashboard</a>
                @endif
@if(Auth::user()->isAdmin() || Auth::user()->isSuperAdmin())
                    <a href="{{ route('products.create') }}" class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-md transition-colors">
                        ➕ Add Product
                    </a>
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

        <!-- Search and Filter -->
        <div class="bg-black/60 p-4 rounded-lg border border-green-900/30 mb-6">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <input type="text" id="search" placeholder="Search products..." 
                           class="w-full px-4 py-2 border border-gray-700 bg-black/40 rounded-md text-white placeholder-gray-400">
                </div>
                <div>
                    <select id="categoryFilter" class="w-full px-4 py-2 border border-gray-700 bg-black/40 rounded-md text-white">
                        <option value="">All Categories</option>
                        <option value="Electronics">Electronics</option>
                        <option value="Clothing">Clothing</option>
                        <option value="Food">Food</option>
                        <option value="Beverages">Beverages</option>
                        <option value="Other">Other</option>
                    </select>
                </div>
                <div>
                    <select id="stockFilter" class="w-full px-4 py-2 border border-gray-700 bg-black/40 rounded-md text-white">
                        <option value="">All Stock Levels</option>
                        <option value="low">Low Stock</option>
                        <option value="normal">Normal Stock</option>
                        <option value="out">Out of Stock</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Low Stock Alert -->
        <div class="mb-6 bg-yellow-900/50 border border-yellow-700 rounded-md p-4">
            <h3 class="text-yellow-400 font-semibold mb-2">⚠️ Low Stock Alert</h3>
            <p class="text-yellow-200 text-sm">Products that need restocking:</p>
            <div id="lowStockProducts" class="mt-2">
                <!-- Will be populated by JavaScript -->
            </div>
        </div>

        <!-- Products Table -->
        <div class="bg-black/60 rounded-lg border border-green-900/30 overflow-hidden">
            <div class="p-6">
                <div class="overflow-x-auto">
                    <table class="w-full" id="productsTable">
                        <thead>
                            <tr class="border-b border-green-900/30">
                                <th class="text-left py-3 px-4 text-green-300">SKU</th>
                                <th class="text-left py-3 px-4 text-green-300">Product Name</th>
                                <th class="text-left py-3 px-4 text-green-300">Category</th>
                                <th class="text-left py-3 px-4 text-green-300">Price</th>
                                <th class="text-left py-3 px-4 text-green-300">Stock</th>
                                <th class="text-left py-3 px-4 text-green-300">Min Stock</th>
                                <th class="text-left py-3 px-4 text-green-300">Status</th>
                                <th class="text-left py-3 px-4 text-green-300">Actions</th>
                            </tr>
                        </thead>
                        <tbody id="productsTableBody">
                            @forelse ($products as $product)
                                <tr class="border-b border-green-900/20 hover:bg-green-900/10 product-row" 
                                    data-category="{{ $product->category }}"
                                    data-stock="{{ $product->stock_quantity }}"
                                    data-min-stock="{{ $product->min_stock_level }}">
                                    <td class="py-3 px-4 font-mono text-xs">{{ $product->sku }}</td>
                                    <td class="py-3 px-4">
                                        <div>
                                            <div class="font-medium">{{ $product->name }}</div>
                                            @if($product->description)
                                                <div class="text-xs text-gray-400">{{ Str::limit($product->description, 50) }}</div>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="py-3 px-4">
                                        <span class="px-2 py-1 text-xs rounded-full bg-gray-600">
                                            {{ $product->category ?? 'Uncategorized' }}
                                        </span>
                                    </td>
                                    <td class="py-3 px-4">₱{{ number_format($product->price, 2) }}</td>
                                    <td class="py-3 px-4">
                                        <span class="font-medium {{ $product->stock_quantity <= $product->min_stock_level ? 'text-red-400' : 'text-green-400' }}">
                                            {{ $product->stock_quantity }}
                                        </span>
                                    </td>
                                    <td class="py-3 px-4">{{ $product->min_stock_level }}</td>
                                    <td class="py-3 px-4">
                                        @if($product->stock_quantity == 0)
                                            <span class="px-2 py-1 text-xs rounded-full bg-red-600">Out of Stock</span>
                                        @elseif($product->stock_quantity <= $product->min_stock_level)
                                            <span class="px-2 py-1 text-xs rounded-full bg-yellow-600">Low Stock</span>
                                        @else
                                            <span class="px-2 py-1 text-xs rounded-full bg-green-600">In Stock</span>
                                        @endif
                                    </td>
                                    <td class="py-3 px-4">
                                        <div class="flex gap-2">
@if(Auth::user()->isAdmin() || Auth::user()->isSuperAdmin())
                                                <a href="{{ route('products.edit', $product->id) }}" class="px-2 py-1 bg-blue-600 hover:bg-blue-700 text-white text-xs rounded">
                                                    Edit
                                                </a>
                                                <form action="{{ route('products.destroy', $product->id) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this product?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="px-2 py-1 bg-red-600 hover:bg-red-700 text-white text-xs rounded">
                                                        Delete
                                                    </button>
                                                </form>
                                            @else
                                                <button onclick="viewProduct({{ $product->id }})" class="px-2 py-1 bg-gray-600 hover:bg-gray-700 text-white text-xs rounded">
                                                    View
                                                </button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="py-8 text-center text-gray-400">
                                        No products found. 
@if(Auth::user()->isAdmin() || Auth::user()->isSuperAdmin())
                                            <a href="{{ route('products.create') }}" class="text-green-400 hover:underline">Add your first product</a>
                                        @endif
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Stats -->
        @if(Auth::user()->isAdmin() || Auth::user()->isSuperAdmin())
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mt-8">
                <div class="bg-black/60 p-4 rounded-lg border border-green-900/30">
                    <h3 class="text-green-400 font-semibold">Total Products</h3>
                    <p class="text-2xl font-bold">{{ $products->count() }}</p>
                </div>
                <div class="bg-black/60 p-4 rounded-lg border border-green-900/30">
                    <h3 class="text-green-400 font-semibold">Low Stock</h3>
                    <p class="text-2xl font-bold text-yellow-400">{{ $lowStockCount ?? 0 }}</p>
                </div>
                <div class="bg-black/60 p-4 rounded-lg border border-green-900/30">
                    <h3 class="text-green-400 font-semibold">Out of Stock</h3>
                    <p class="text-2xl font-bold text-red-400">{{ $outOfStockCount ?? 0 }}</p>
                </div>
                <div class="bg-black/60 p-4 rounded-lg border border-green-900/30">
                    <h3 class="text-green-400 font-semibold">Total Value</h3>
                    <p class="text-2xl font-bold">₱{{ number_format($totalValue ?? 0, 2) }}</p>
                </div>
            </div>
        @else
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-8">
                <div class="bg-black/60 p-4 rounded-lg border border-green-900/30">
                    <h3 class="text-green-400 font-semibold">Low Stock</h3>
                    <p class="text-2xl font-bold text-yellow-400">{{ $lowStockCount ?? 0 }}</p>
                </div>
                <div class="bg-black/60 p-4 rounded-lg border border-green-900/30">
                    <h3 class="text-green-400 font-semibold">Out of Stock</h3>
                    <p class="text-2xl font-bold text-red-400">{{ $outOfStockCount ?? 0 }}</p>
                </div>
            </div>
        @endif
    </div>

    <script>
        // Search functionality
        document.getElementById('search').addEventListener('input', filterProducts);
        document.getElementById('categoryFilter').addEventListener('change', filterProducts);
        document.getElementById('stockFilter').addEventListener('change', filterProducts);

        function filterProducts() {
            const searchTerm = document.getElementById('search').value.toLowerCase();
            const categoryFilter = document.getElementById('categoryFilter').value;
            const stockFilter = document.getElementById('stockFilter').value;
            const rows = document.querySelectorAll('.product-row');

            rows.forEach(row => {
                const name = row.querySelector('td:nth-child(2)').textContent.toLowerCase();
                const category = row.dataset.category;
                const stock = parseInt(row.dataset.stock);
                const minStock = parseInt(row.dataset.minStock);

                let matchesSearch = name.includes(searchTerm);
                let matchesCategory = !categoryFilter || category === categoryFilter;
                let matchesStock = true;

                if (stockFilter === 'low') {
                    matchesStock = stock <= minStock && stock > 0;
                } else if (stockFilter === 'normal') {
                    matchesStock = stock > minStock;
                } else if (stockFilter === 'out') {
                    matchesStock = stock === 0;
                }

                row.style.display = matchesSearch && matchesCategory && matchesStock ? '' : 'none';
            });
        }

        // Low stock alert
        function updateLowStockAlert() {
            const rows = document.querySelectorAll('.product-row');
            const lowStockContainer = document.getElementById('lowStockProducts');
            const lowStockProducts = [];

            rows.forEach(row => {
                const stock = parseInt(row.dataset.stock);
                const minStock = parseInt(row.dataset.minStock);
                const name = row.querySelector('td:nth-child(2)').textContent.trim();

                if (stock <= minStock && stock > 0) {
                    lowStockProducts.push(name);
                }
            });

            if (lowStockProducts.length > 0) {
                lowStockContainer.innerHTML = lowStockProducts.map(name => 
                    `<span class="inline-block bg-yellow-800 px-2 py-1 rounded text-xs mr-2 mb-2">${name}</span>`
                ).join('');
            } else {
                lowStockContainer.innerHTML = '<p class="text-yellow-200 text-sm">No products need restocking!</p>';
            }
        }

        // Initialize
        updateLowStockAlert();

        function viewProduct(id) {
            // In a real app, this would show product details
            alert('Product details would be shown here');
        }
    </script>
</body>
</html>
