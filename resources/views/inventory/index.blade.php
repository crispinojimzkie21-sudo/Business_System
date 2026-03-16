<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Inventory Management - {{ config('app.name') }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gradient-to-br from-black via-gray-900 to-blue-900 min-h-screen text-white">
    <div class="max-w-6xl mx-auto p-6">
        <!-- Header -->
        <header class="flex items-center justify-between mb-8">
            <div>
                <h1 class="text-3xl font-bold text-blue-400">📊 Inventory Management</h1>
                <p class="text-blue-200 text-sm mt-1">Monitor stock levels and inventory status</p>
            </div>
            <div class="flex gap-4">
@if(Auth::user()->isCashier())
                    <a href="{{ route('dashboard.cashier') }}" class="px-4 py-2 text-blue-200 hover:bg-blue-900/30 rounded">← Dashboard</a>
                @elseif(Auth::user()->isSuperAdmin())
                    <a href="{{ url('/dashboard/super-admin') }}" class="px-4 py-2 text-blue-200 hover:bg-blue-900/30 rounded">← Dashboard</a>
                @elseif(Auth::user()->isAdmin())
                    <a href="{{ url('/dashboard/admin') }}" class="px-4 py-2 text-blue-200 hover:bg-blue-900/30 rounded">← Dashboard</a>
                @else
                    <a href="{{ route('dashboard.employee') }}" class="px-4 py-2 text-blue-200 hover:bg-blue-900/30 rounded">← Dashboard</a>
                @endif
@if(Auth::user()->isAdmin() || Auth::user()->isSuperAdmin())
                    <a href="{{ route('products.create') }}" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded">➕ Add Product</a>
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

        <!-- Inventory Stats -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">
            <div class="bg-black/60 p-4 rounded-lg border border-blue-900/30">
                <h3 class="text-blue-400 font-semibold">Total Products</h3>
                <p class="text-2xl font-bold">{{ $allProducts->total() }}</p>
            </div>
            <div class="bg-black/60 p-4 rounded-lg border border-red-900/30">
                <h3 class="text-red-400 font-semibold">Out of Stock</h3>
                <p class="text-2xl font-bold">{{ $outOfStockProducts->count() }}</p>
            </div>
            <div class="bg-black/60 p-4 rounded-lg border border-yellow-900/30">
                <h3 class="text-yellow-400 font-semibold">Low Stock</h3>
                <p class="text-2xl font-bold">{{ $lowStockProducts->count() }}</p>
            </div>
            <div class="bg-black/60 p-4 rounded-lg border border-green-900/30">
                <h3 class="text-green-400 font-semibold">Total Value</h3>
                <p class="text-2xl font-bold">₱{{ number_format($totalValue ?? 0, 2) }}</p>
            </div>

        <!-- Low Stock Alert -->
        @if($lowStockProducts->count() > 0)
        <div class="bg-yellow-900/50 border border-yellow-700 rounded-lg p-4 mb-8">
            <h2 class="text-xl font-semibold mb-4 text-yellow-400">⚠️ Low Stock Alerts</h2>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="border-b border-yellow-900/30">
                            <th class="text-left py-2 px-4 text-yellow-300">Product</th>
                            <th class="text-left py-2 px-4 text-yellow-300">SKU</th>
                            <th class="text-left py-2 px-4 text-yellow-300">Current Stock</th>
                            <th class="text-left py-2 px-4 text-yellow-300">Min Stock Level</th>
                            <th class="text-left py-2 px-4 text-yellow-300">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($lowStockProducts as $product)
                            <tr class="border-b border-yellow-900/20">
                                <td class="py-2 px-4">{{ $product->name }}</td>
                                <td class="py-2 px-4">{{ $product->sku }}</td>
                                <td class="py-2 px-4 text-red-400">{{ $product->stock_quantity }}</td>
                                <td class="py-2 px-4">{{ $product->min_stock_level }}</td>
                                <td class="py-2 px-4">
@if(Auth::user()->isAdmin() || Auth::user()->isSuperAdmin())
                                    <a href="{{ route('products.edit', $product->id) }}" class="text-blue-400 hover:text-blue-300">Edit</a>
                                @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif

        <!-- Out of Stock Alert -->
        @if($outOfStockProducts->count() > 0)
        <div class="bg-red-900/50 border border-red-700 rounded-lg p-4 mb-8">
            <h2 class="text-xl font-semibold mb-4 text-red-400">🚫 Out of Stock</h2>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="border-b border-red-900/30">
                            <th class="text-left py-2 px-4 text-red-300">Product</th>
                            <th class="text-left py-2 px-4 text-red-300">SKU</th>
                            <th class="text-left py-2 px-4 text-red-300">Price</th>
                            <th class="text-left py-2 px-4 text-red-300">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($outOfStockProducts as $product)
                            <tr class="border-b border-red-900/20">
                                <td class="py-2 px-4">{{ $product->name }}</td>
                                <td class="py-2 px-4">{{ $product->sku }}</td>
                                <td class="py-2 px-4">₱{{ number_format($product->price, 2) }}</td>
                                <td class="py-2 px-4">
                                    <a href="{{ route('products.edit', $product->id) }}" class="text-blue-400 hover:text-blue-300">Restock</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif

        <!-- All Products -->
        <div class="bg-black/60 rounded-lg border border-blue-900/30 overflow-hidden">
            <div class="p-6">
                <h2 class="text-xl font-semibold mb-4 text-blue-400">📦 All Products</h2>
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="border-b border-blue-900/30">
                                <th class="text-left py-3 px-4 text-blue-300">Product</th>
                                <th class="text-left py-3 px-4 text-blue-300">SKU</th>
                                <th class="text-left py-3 px-4 text-blue-300">Category</th>
                                <th class="text-left py-3 px-4 text-blue-300">Price</th>
                                <th class="text-left py-3 px-4 text-blue-300">Stock</th>
                                <th class="text-left py-3 px-4 text-blue-300">Status</th>
                                <th class="text-left py-3 px-4 text-blue-300">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($allProducts as $product)
                                <tr class="border-b border-blue-900/20 hover:bg-blue-900/10">
                                    <td class="py-3 px-4">{{ $product->name }}</td>
                                    <td class="py-3 px-4">{{ $product->sku }}</td>
                                    <td class="py-3 px-4">{{ $product->category ?? 'N/A' }}</td>
                                    <td class="py-3 px-4">₱{{ number_format($product->price, 2) }}</td>
                                    <td class="py-3 px-4">{{ $product->stock_quantity }}</td>
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
                                            <a href="{{ route('products.edit', $product->id) }}" class="px-2 py-1 bg-blue-600 hover:bg-blue-700 text-white text-xs rounded">Edit</a>
@if(Auth::user()->isAdmin() || Auth::user()->isSuperAdmin())
                                            <form method="POST" action="{{ route('products.destroy', $product->id) }}" class="inline" onsubmit="return confirm('Are you sure you want to delete this product?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="px-2 py-1 bg-red-600 hover:bg-red-700 text-white text-xs rounded">Delete</button>
                                            </form>
                                        @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="py-8 text-center text-gray-400">
                                        No products found. <a href="{{ route('products.create') }}" class="text-blue-400 hover:underline">Add your first product</a>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
        </div>

        <!-- Pagination -->
        <div class="mt-4">
            {{ $allProducts->links() }}
        </div>
</body>
</html>
