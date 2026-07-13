<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Add Load - Admin Assistant - {{ config('app.name') }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gradient-to-br from-gray-900 via-purple-900 to-violet-900 min-h-screen text-white">
    <div class="max-w-md mx-auto p-6">
        <!-- Header -->
        <header class="mb-6">
            <h1 class="text-2xl font-bold text-purple-400">📱 Add Load - Admin Assistant</h1>
            <p class="text-purple-200 text-sm mt-1">Process electronic load transaction</p>
        </header>

        @if (session('success'))
            <div class="mb-4 bg-green-900/50 border border-green-700 rounded-md p-3">
                <p class="text-green-200 text-sm">{{ session('success') }}</p>
            </div>
        @endif

        @if (session('error'))
            <div class="mb-4 bg-red-900/50 border border-red-700 rounded-md p-3">
                <p class="text-red-200 text-sm">{{ session('error') }}</p>
            </div>
        @endif

        @if ($errors->any())
            <div class="mb-4 bg-red-900/50 border border-red-700 rounded-md p-3">
                <ul class="list-disc list-inside text-red-200 text-sm">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Simple Add Load Form -->
        <form method="POST" action="{{ route('admin.eload.process-load') }}" class="bg-black/60 p-6 rounded-lg border border-purple-900/30">
            @csrf

            <!-- Step 1: Network (Customizable) -->
            <div class="mb-4">
                <label class="block text-purple-200 mb-2 font-medium">1. Network</label>
                <input type="text" name="network" id="networkInput" value="{{ old('network') }}" class="w-full px-4 py-3 bg-gray-800 border border-purple-900/30 rounded text-white focus:outline-none focus:border-purple-500" placeholder="e.g., DITO, Smart, Globe TM, etc." required>
                <p class="text-xs text-purple-300 mt-1">Enter network provider name</p>
            </div>

            <!-- Step 2: Enter E-Load Number (Mobile Number to Receive Load) -->
            <div class="mb-4">
                <label class="block text-purple-200 mb-2 font-medium">2. E-Load Number (Mobile to Receive)</label>
                <input type="text" name="eload_number" id="eloadNumber" value="{{ old('eload_number') }}" class="w-full px-4 py-3 bg-gray-800 border border-purple-900/30 rounded text-white focus:outline-none focus:border-purple-500" placeholder="e.g., 09123456789" required maxlength="11">
                <p class="text-xs text-purple-300 mt-1">Enter mobile number to receive load</p>
            </div>

            <!-- Step 3: Enter Load Price -->
            <div class="mb-4">
                <label class="block text-purple-200 mb-2 font-medium">3. Load Price</label>
                <div class="relative">
                    <span class="absolute left-4 top-3 text-purple-300">₱</span>
                    <input type="number" name="price" id="priceInput" value="{{ old('price') }}" class="w-full pl-8 pr-4 py-3 bg-gray-800 border border-purple-900/30 rounded text-white focus:outline-none focus:border-purple-500" placeholder="0.00" step="0.01" min="1" required>
                </div>
                <p class="text-xs text-purple-300 mt-1">Enter amount of load</p>
            </div>

            <!-- Quick Price Buttons -->
            <div class="mb-4">
                <label class="block text-purple-200 mb-2 text-sm">Quick Select:</label>
                <div class="flex flex-wrap gap-2">
                    <button type="button" onclick="setPrice(30)" class="px-3 py-1 bg-purple-800 hover:bg-purple-700 rounded text-sm">₱30</button>
                    <button type="button" onclick="setPrice(50)" class="px-3 py-1 bg-purple-800 hover:bg-purple-700 rounded text-sm">₱50</button>
                    <button type="button" onclick="setPrice(100)" class="px-3 py-1 bg-purple-800 hover:bg-purple-700 rounded text-sm">₱100</button>
                    <button type="button" onclick="setPrice(150)" class="px-3 py-1 bg-purple-800 hover:bg-purple-700 rounded text-sm">₱150</button>
                    <button type="button" onclick="setPrice(300)" class="px-3 py-1 bg-purple-800 hover:bg-purple-700 rounded text-sm">₱300</button>
                    <button type="button" onclick="setPrice(500)" class="px-3 py-1 bg-purple-800 hover:bg-purple-700 rounded text-sm">₱500</button>
                </div>

            <!-- Step 4: Select Status -->
            <div class="mb-6">
                <label class="block text-purple-200 mb-2 font-medium">4. Status</label>
                <select name="status" class="w-full px-4 py-3 bg-gray-800 border border-purple-900/30 rounded text-white focus:outline-none focus:border-purple-500" required>
                    <option value="pending" {{ old('status', 'pending') === 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="completed" {{ old('status') === 'completed' ? 'selected' : '' }}>Completed</option>
                </select>
            </div>

            <!-- Step 5: Submit -->
            <div class="flex gap-3">
                <button type="submit" class="flex-1 px-6 py-3 bg-purple-600 hover:bg-purple-500 rounded font-medium">Process Load</button>
                <a href="{{ route('admin.eload.transactions.history') }}" class="px-4 py-3 bg-gray-700 hover:bg-gray-600 rounded">History</a>
            </div>
        </form>

        <!-- Auto Date/Time Info -->
        <div class="mt-4 bg-blue-900/30 border border-blue-700 rounded-md p-3">
            <p class="text-blue-200 text-sm">📅 Date & Time will be automatically recorded when you submit</p>
            <p class="text-blue-300 text-xs mt-1">Current Server Time: {{ now()->format('M d, Y h:i A') }}</p>
        </div>

        <!-- Back Link -->
        <div class="mt-4 text-center space-y-2">
            <a href="{{ route('admin.eload.add-load-multiple') }}" class="text-purple-300 hover:text-purple-200 text-sm">📱 Add Multiple Loads Instead</a>
            <br>
            <a href="{{ route('eload.index') }}" class="text-purple-300 hover:text-purple-200 text-sm">← Back to E-Load Products</a>
        </div>

    <script>
        function setPrice(amount) {
            document.getElementById('priceInput').value = amount;
        }
    </script>
</body>
</html>
