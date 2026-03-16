<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Add E-Load - {{ config('app.name') }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gradient-to-br from-gray-900 via-purple-900 to-violet-900 min-h-screen text-white">
    <div class="max-w-2xl mx-auto p-6">
        <!-- Header -->
        <header class="flex items-center justify-between mb-8">
            <div>
                <h1 class="text-3xl font-bold text-purple-400">➕ Add E-Load Product</h1>
                <p class="text-purple-200 text-sm mt-1">Create new electronic load product</p>
            </div>
            <a href="{{ route('eload.index') }}" class="px-3 py-2 text-purple-200 hover:bg-purple-900/30 rounded">← Back</a>
        </header>

        @if ($errors->any())
            <div class="mb-6 bg-red-900/50 border border-red-700 rounded-md p-4">
                <ul class="list-disc list-inside text-red-200">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Form -->
        <form method="POST" action="{{ route('eload.store') }}" class="bg-black/60 p-6 rounded-lg border border-purple-900/30">
            @csrf

            <div class="mb-4">
                <label class="block text-purple-200 mb-2">Load Name</label>
                <input type="text" name="name" value="{{ old('name') }}" class="w-full px-4 py-2 bg-gray-800 border border-purple-900/30 rounded text-white focus:outline-none focus:border-purple-500" placeholder="e.g., GO+99" required>
            </div>

            <div class="mb-4">
                <label class="block text-purple-200 mb-2">Network Provider</label>
                <input type="text" name="network" value="{{ old('network') }}" class="w-full px-4 py-2 bg-gray-800 border border-purple-900/30 rounded text-white focus:outline-none focus:border-purple-500" placeholder="Enter any network name (e.g., Smart, Globe, DITO, TM, Cherry, etc.)" required>
                <p class="text-xs text-purple-300 mt-1">💡 Type any network provider name - fully customizable text input</p>
                <div class="mt-2 flex flex-wrap gap-2">
                    <button type="button" onclick="setNetwork('Smart')" class="px-3 py-1 bg-purple-800 hover:bg-purple-700 rounded text-xs text-purple-200">Smart</button>
                    <button type="button" onclick="setNetwork('Globe')" class="px-3 py-1 bg-purple-800 hover:bg-purple-700 rounded text-xs text-purple-200">Globe</button>
                    <button type="button" onclick="setNetwork('DITO')" class="px-3 py-1 bg-purple-800 hover:bg-purple-700 rounded text-xs text-purple-200">DITO</button>
                    <button type="button" onclick="setNetwork('TM')" class="px-3 py-1 bg-purple-800 hover:bg-purple-700 rounded text-xs text-purple-200">TM</button>
                    <button type="button" onclick="setNetwork('Cherry')" class="px-3 py-1 bg-purple-800 hover:bg-purple-700 rounded text-xs text-purple-200">Cherry</button>
                    <button type="button" onclick="clearNetwork()" class="px-3 py-1 bg-gray-700 hover:bg-gray-600 rounded text-xs text-gray-300">Clear</button>
                </div>
            </div>

            <div class="mb-4">
                <label class="block text-purple-200 mb-2">Category</label>
                <select name="category_id" class="w-full px-4 py-2 bg-gray-800 border border-purple-900/30 rounded text-white focus:outline-none focus:border-purple-500" required>
                    <option value="">Select Category</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="mb-4">
                <label class="block text-purple-200 mb-2">Load Price (₱)</label>
                <input type="number" name="price" value="{{ old('price') }}" step="0.01" min="0" class="w-full px-4 py-2 bg-gray-800 border border-purple-900/30 rounded text-white focus:outline-none focus:border-purple-500" placeholder="0.00" required>
            </div>

            <div class="mb-6">
                <label class="block text-purple-200 mb-2">Status</label>
                <select name="status" class="w-full px-4 py-2 bg-gray-800 border border-purple-900/30 rounded text-white focus:outline-none focus:border-purple-500" required>
                    <option value="active" {{ old('status') === 'active' ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ old('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                </select>
            </div>

            <div class="flex gap-4">
                <button type="submit" class="px-6 py-2 bg-purple-600 hover:bg-purple-500 rounded">Create E-Load</button>
                <a href="{{ route('eload.index') }}" class="px-6 py-2 bg-gray-700 hover:bg-gray-600 rounded">Cancel</a>
            </div>
        </form>
    </div>

    <script>
        function setNetwork(network) {
            document.querySelector('input[name="network"]').value = network;
        }
        
        function clearNetwork() {
            document.querySelector('input[name="network"]').value = '';
            document.querySelector('input[name="network"]').focus();
        }
    </script>
</body>
</html>

