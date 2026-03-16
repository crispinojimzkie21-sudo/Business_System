<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Edit Product - {{ config('app.name') }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gradient-to-br from-black via-gray-900 to-green-900 min-h-screen text-white">
    <div class="max-w-4xl mx-auto p-6">
        <!-- Header -->
        <header class="flex items-center justify-between mb-8">
            <div>
                <h1 class="text-3xl font-bold text-green-400">✏️ Edit Product</h1>
                <p class="text-green-200 text-sm mt-1">Update product information</p>
            </div>
            <a href="{{ route('products.index') }}" class="px-4 py-2 text-green-200 hover:bg-green-900/30 rounded">← Back to Products</a>
        </header>

        @if (session('success'))
            <div class="mb-6 bg-green-900/50 border border-green-700 rounded-md p-4">
                <p class="text-green-200">{{ session('success') }}</p>
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

        <!-- Product Form -->
        <div class="bg-black/60 p-6 rounded-lg border border-green-900/30">
            <form method="POST" action="{{ route('products.update', $product->id) }}" class="space-y-6">
                @csrf
                @method('PUT')
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Basic Information -->
                    <div class="space-y-4">
                        <h3 class="text-lg font-semibold text-green-300">Basic Information</h3>
                        
                        <div>
                            <label class="block text-sm font-medium text-green-300 mb-2">Product Name *</label>
                            <input name="name" type="text" value="{{ $product->name }}" required 
                                   class="w-full px-4 py-2 border border-gray-700 bg-black/40 rounded-md text-white placeholder-gray-400">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-green-300 mb-2">SKU *</label>
                            <input name="sku" type="text" value="{{ $product->sku }}" required 
                                   class="w-full px-4 py-2 border border-gray-700 bg-black/40 rounded-md text-white placeholder-gray-400">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-green-300 mb-2">Category</label>
                            <select name="category" 
                                    class="w-full px-4 py-2 border border-gray-700 bg-black/40 rounded-md text-white placeholder-gray-400">
                                <option value="" class="bg-gray-800">Select a category</option>
                                <option value="Digital" {{ $product->category == 'Digital' ? 'selected' : '' }} class="bg-gray-800">Digital</option>
                                <option value="SIM Cards" {{ $product->category == 'SIM Cards' ? 'selected' : '' }} class="bg-gray-800">SIM Cards</option>
                                <option value="Case" {{ $product->category == 'Case' ? 'selected' : '' }} class="bg-gray-800">Case</option>
                                <option value="Charger Type" {{ $product->category == 'Charger Type' ? 'selected' : '' }} class="bg-gray-800">Charger Type</option>
                                <option value="Television" {{ $product->category == 'Television' ? 'selected' : '' }} class="bg-gray-800">Television</option>
                                <option value="Audio/Headphones" {{ $product->category == 'Audio/Headphones' ? 'selected' : '' }} class="bg-gray-800">Audio/Headphones</option>
                                <option value="Smartphones" {{ $product->category == 'Smartphones' ? 'selected' : '' }} class="bg-gray-800">Smartphones</option>
                                <option value="Tablets" {{ $product->category == 'Tablets' ? 'selected' : '' }} class="bg-gray-800">Tablets</option>
                                <option value="Laptops" {{ $product->category == 'Laptops' ? 'selected' : '' }} class="bg-gray-800">Laptops</option>
                                <option value="Accessories" {{ $product->category == 'Accessories' ? 'selected' : '' }} class="bg-gray-800">Accessories</option>
                                <option value="Gaming" {{ $product->category == 'Gaming' ? 'selected' : '' }} class="bg-gray-800">Gaming</option>
                                <option value="Smart Home" {{ $product->category == 'Smart Home' ? 'selected' : '' }} class="bg-gray-800">Smart Home</option>
                                <option value="Cameras" {{ $product->category == 'Cameras' ? 'selected' : '' }} class="bg-gray-800">Cameras</option>
                                <option value="Wearable" {{ $product->category == 'Wearable' ? 'selected' : '' }} class="bg-gray-800">Wearable</option>
                                <option value="Storage" {{ $product->category == 'Storage' ? 'selected' : '' }} class="bg-gray-800">Storage</option>
                                <option value="Networking" {{ $product->category == 'Networking' ? 'selected' : '' }} class="bg-gray-800">Networking</option>
                                <option value="Cables" {{ $product->category == 'Cables' ? 'selected' : '' }} class="bg-gray-800">Cables</option>
                                <option value="Power Banks" {{ $product->category == 'Power Banks' ? 'selected' : '' }} class="bg-gray-800">Power Banks</option>
                                <option value="Batteries" {{ $product->category == 'Batteries' ? 'selected' : '' }} class="bg-gray-800">Batteries</option>
                                <option value="Other" {{ $product->category == 'Other' ? 'selected' : '' }} class="bg-gray-800">Other</option>
                            </select>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-green-300 mb-2">Description</label>
                            <textarea name="description" rows="3" 
                                      class="w-full px-4 py-2 border border-gray-700 bg-black/40 rounded-md text-white placeholder-gray-400">{{ $product->description ?? '' }}</textarea>
                        </div>
                    </div>
                    
                    <!-- Pricing & Stock -->
                    <div class="space-y-4">
                        <h3 class="text-lg font-semibold text-green-300">Pricing & Stock</h3>
                        
                        <div>
                            <label class="block text-sm font-medium text-green-300 mb-2">Selling Price *</label>
                            <input name="price" type="number" step="0.01" min="0" value="{{ $product->price }}" required 
                                   class="w-full px-4 py-2 border border-gray-700 bg-black/40 rounded-md text-white placeholder-gray-400">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-green-300 mb-2">Cost Price *</label>
                            <input name="cost" type="number" step="0.01" min="0" value="{{ $product->cost }}" required 
                                   class="w-full px-4 py-2 border border-gray-700 bg-black/40 rounded-md text-white placeholder-gray-400">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-green-300 mb-2">Stock Quantity *</label>
                            <input name="stock_quantity" type="number" min="0" value="{{ $product->stock_quantity }}" required 
                                   class="w-full px-4 py-2 border border-gray-700 bg-black/40 rounded-md text-white placeholder-gray-400">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-green-300 mb-2">Minimum Stock Level *</label>
                            <input name="min_stock_level" type="number" min="0" value="{{ $product->min_stock_level }}" required 
                                   class="w-full px-4 py-2 border border-gray-700 bg-black/40 rounded-md text-white placeholder-gray-400">
                            <p class="text-xs text-green-200 mt-1">Alert when stock falls below this level</p>
                        </div>
                    </div>
                </div>
                
                <!-- Current Status -->
                <div class="bg-gray-800/50 p-4 rounded-lg">
                    <h3 class="text-sm font-semibold text-green-300 mb-2">Current Status</h3>
                    <div class="flex gap-4 text-sm">
                        <span>Current Stock: <strong class="{{ $product->stock_quantity <= $product->min_stock_level ? 'text-red-400' : 'text-green-400' }}">{{ $product->stock_quantity }}</strong></span>
                        <span>Status: 
                            @if($product->stock_quantity == 0)
                                <strong class="text-red-400">Out of Stock</strong>
                            @elseif($product->stock_quantity <= $product->min_stock_level)
                                <strong class="text-yellow-400">Low Stock</strong>
                            @else
                                <strong class="text-green-400">In Stock</strong>
                            @endif
                        </span>
                    </div>
                </div>
                
                <!-- Form Actions -->
                <div class="flex gap-4 pt-4 border-t border-green-900/30">
                    <button type="submit" class="px-6 py-3 bg-green-600 hover:bg-green-700 text-white font-semibold rounded-md transition-colors">
                        Update Product
                    </button>
                    <a href="{{ route('products.index') }}" class="px-6 py-3 bg-gray-700 hover:bg-gray-600 text-white font-semibold rounded-md transition-colors">
                        Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
</body>
</html>

