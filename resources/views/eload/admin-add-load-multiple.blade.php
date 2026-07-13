<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Add Multiple Loads - Admin Assistant - {{ config('app.name') }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gradient-to-br from-gray-900 via-purple-900 to-violet-900 min-h-screen text-white">
    <div class="max-w-4xl mx-auto p-6">
        <!-- Header -->
        <header class="mb-6">
            <h1 class="text-2xl font-bold text-purple-400">📱 Add Multiple Loads - Admin Assistant</h1>
            <p class="text-purple-200 text-sm mt-1">Process multiple electronic load transactions</p>
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

        <!-- Multiple Loads Form -->
        <form method="POST" action="{{ route('admin.eload.process-multiple-loads') }}" class="bg-black/60 p-6 rounded-lg border border-purple-900/30">
            @csrf
            
            <!-- Load Entries Container -->
            <div id="loadEntries" class="space-y-4 mb-6">
                <!-- Initial Load Entry -->
                <div class="load-entry bg-gray-800/50 p-4 rounded border border-purple-900/30">
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        <!-- Network -->
                        <div>
                            <label class="block text-purple-200 mb-2 font-medium text-sm">Network</label>
                            <input type="text" name="loads[0][network]" class="w-full px-3 py-2 bg-gray-800 border border-purple-900/30 rounded text-white text-sm focus:outline-none focus:border-purple-500" placeholder="e.g., Smart, Globe, DITO" required>
                        </div>
                        
                        <!-- Mobile Number -->
                        <div>
                            <label class="block text-purple-200 mb-2 font-medium text-sm">Mobile Number</label>
                            <input type="text" name="loads[0][eload_number]" class="w-full px-3 py-2 bg-gray-800 border border-purple-900/30 rounded text-white text-sm focus:outline-none focus:border-purple-500" placeholder="09123456789" required maxlength="11">
                        </div>
                        
                        <!-- Price -->
                        <div>
                            <label class="block text-purple-200 mb-2 font-medium text-sm">Price</label>
                            <div class="relative">
                                <span class="absolute left-3 top-2 text-purple-300 text-sm">₱</span>
                                <input type="number" name="loads[0][price]" class="w-full pl-8 pr-3 py-2 bg-gray-800 border border-purple-900/30 rounded text-white text-sm focus:outline-none focus:border-purple-500" placeholder="0.00" step="0.01" min="1" required>
                            </div>
                        </div>
                        
                        <!-- Status -->
                        <div>
                            <label class="block text-purple-200 mb-2 font-medium text-sm">Status</label>
                            <select name="loads[0][status]" class="w-full px-3 py-2 bg-gray-800 border border-purple-900/30 rounded text-white text-sm focus:outline-none focus:border-purple-500" required>
                                <option value="pending">Pending</option>
                                <option value="completed">Completed</option>
                            </select>
                        </div>
                    </div>
                    <button type="button" onclick="removeLoadEntry(this)" class="mt-2 text-red-400 hover:text-red-300 text-sm">Remove Entry</button>
                </div>
            </div>

            <!-- Add Another Entry Button -->
            <div class="mb-4">
                <button type="button" onclick="addLoadEntry()" class="px-4 py-2 bg-purple-700 hover:bg-purple-600 rounded text-sm font-medium">
                    ➕ Add Another Load Entry
                </button>
            </div>

            <!-- Summary Section -->
            <div class="bg-gray-800/50 p-4 rounded border border-purple-900/30 mb-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-purple-200 mb-2 font-medium text-sm">Total Entries</label>
                        <div id="totalEntries" class="text-xl font-bold text-purple-400">1</div>
                    </div>
                    <div>
                        <label class="block text-purple-200 mb-2 font-medium text-sm">Total Amount</label>
                        <div id="totalAmount" class="text-xl font-bold text-green-400">₱0.00</div>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex gap-3">
                <button type="submit" class="flex-1 px-6 py-3 bg-purple-600 hover:bg-purple-500 rounded font-medium">
                    🚀 Process All Loads
                </button>
                <a href="{{ route('admin.eload.transactions.history') }}" class="px-6 py-3 bg-gray-700 hover:bg-gray-600 rounded font-medium">
                    📋 History
                </a>
            </div>
        </form>

        <!-- Auto Date/Time Info -->
        <div class="mt-4 bg-blue-900/30 border border-blue-700 rounded-md p-3">
            <p class="text-blue-200 text-sm">📅 Date & Time will be automatically recorded when you submit</p>
            <p class="text-blue-300 text-xs mt-1">Current Server Time: {{ now()->format('M d, Y h:i A') }}</p>
        </div>

        <!-- Back Link -->
        <div class="mt-4 text-center space-y-2">
            <a href="{{ route('admin.eload.add-load') }}" class="text-purple-300 hover:text-purple-200 text-sm">
                📱 Switch to Single Load Entry
            </a>
            <br>
            <a href="{{ route('eload.index') }}" class="text-purple-300 hover:text-purple-200 text-sm">
                ← Back to E-Load Products
            </a>
        </div>

    <script>
        let entryCount = 1;

        function addLoadEntry() {
            const container = document.getElementById('loadEntries');
            const newEntry = document.createElement('div');
            newEntry.className = 'load-entry bg-gray-800/50 p-4 rounded border border-purple-900/30';
            newEntry.innerHTML = `
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div>
                        <label class="block text-purple-200 mb-2 font-medium text-sm">Network</label>
                        <input type="text" name="loads[${entryCount}][network]" class="w-full px-3 py-2 bg-gray-800 border border-purple-900/30 rounded text-white text-sm focus:outline-none focus:border-purple-500" placeholder="e.g., Smart, Globe, DITO" required>
                    </div>
                    <div>
                        <label class="block text-purple-200 mb-2 font-medium text-sm">Mobile Number</label>
                        <input type="text" name="loads[${entryCount}][eload_number]" class="w-full px-3 py-2 bg-gray-800 border border-purple-900/30 rounded text-white text-sm focus:outline-none focus:border-purple-500" placeholder="09123456789" required maxlength="11">
                    </div>
                    <div>
                        <label class="block text-purple-200 mb-2 font-medium text-sm">Price</label>
                        <div class="relative">
                            <span class="absolute left-3 top-2 text-purple-300 text-sm">₱</span>
                            <input type="number" name="loads[${entryCount}][price]" class="w-full pl-8 pr-3 py-2 bg-gray-800 border border-purple-900/30 rounded text-white text-sm focus:outline-none focus:border-purple-500 price-input" placeholder="0.00" step="0.01" min="1" required onchange="updateSummary()">
                        </div>
                    </div>
                    <div>
                        <label class="block text-purple-200 mb-2 font-medium text-sm">Status</label>
                        <select name="loads[${entryCount}][status]" class="w-full px-3 py-2 bg-gray-800 border border-purple-900/30 rounded text-white text-sm focus:outline-none focus:border-purple-500" required>
                            <option value="pending">Pending</option>
                            <option value="completed">Completed</option>
                        </select>
                    </div>
                </div>
                <button type="button" onclick="removeLoadEntry(this)" class="mt-2 text-red-400 hover:text-red-300 text-sm">Remove Entry</button>
            `;
            container.appendChild(newEntry);
            entryCount++;
            updateSummary();
        }

        function removeLoadEntry(button) {
            const entry = button.closest('.load-entry');
            entry.remove();
            updateSummary();
        }

        function updateSummary() {
            const entries = document.querySelectorAll('.load-entry');
            const totalEntries = entries.length;
            let totalAmount = 0;

            entries.forEach(entry => {
                const priceInput = entry.querySelector('.price-input');
                if (priceInput && priceInput.value) {
                    totalAmount += parseFloat(priceInput.value) || 0;
                }
            });

            document.getElementById('totalEntries').textContent = totalEntries;
            document.getElementById('totalAmount').textContent = '₱' + totalAmount.toFixed(2);
        }

        // Initialize summary on page load
        document.addEventListener('DOMContentLoaded', function() {
            // Add onchange event to existing price input
            const existingPriceInput = document.querySelector('.price-input');
            if (existingPriceInput) {
                existingPriceInput.addEventListener('change', updateSummary);
            }
            updateSummary();
        });
    </script>
</body>
</html>
