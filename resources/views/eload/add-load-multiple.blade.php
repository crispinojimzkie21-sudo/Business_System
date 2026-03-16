<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Add Multiple Loads - {{ config('app.name') }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gradient-to-br from-gray-900 via-purple-900 to-violet-900 min-h-screen text-white">
    <div class="max-w-4xl mx-auto p-6">
        <!-- Header -->
        <header class="mb-6">
            <h1 class="text-2xl font-bold text-purple-400">📱 Add Multiple Loads</h1>
            <p class="text-purple-200 text-sm mt-1">Process multiple electronic load transactions in one go</p>
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

        <!-- Multiple Load Form -->
        <form method="POST" action="{{ Auth::user()->isSuperAdmin() ? route('eload.process-multiple-loads') : route('admin.eload.process-multiple-loads') }}" class="bg-black/60 p-6 rounded-lg border border-purple-900/30">
            @csrf
            
            <!-- Load Entries Container -->
            <div id="loadEntries" class="space-y-4 mb-6">
                <!-- Load Entry 1 (Default) -->
                <div class="load-entry bg-gray-800/50 p-4 rounded-lg border border-purple-900/30" data-entry="1">
                    <div class="flex justify-between items-center mb-3">
                        <h3 class="text-purple-300 font-medium">Load Entry #1</h3>
                        <button type="button" onclick="removeLoadEntry(this)" class="text-red-400 hover:text-red-300 text-sm">Remove</button>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-3">
                        <div>
                            <label class="block text-purple-200 mb-1 text-sm">Network</label>
                            <input type="text" name="loads[0][network]" class="w-full px-3 py-2 bg-gray-700 border border-purple-900/30 rounded text-white focus:outline-none focus:border-purple-500" placeholder="Smart, Globe, DITO" required>
                        </div>
                        <div>
                            <label class="block text-purple-200 mb-1 text-sm">Mobile Number</label>
                            <input type="text" name="loads[0][eload_number]" class="w-full px-3 py-2 bg-gray-700 border border-purple-900/30 rounded text-white focus:outline-none focus:border-purple-500" placeholder="09123456789" maxlength="11" required>
                        </div>
                        <div>
                            <label class="block text-purple-200 mb-1 text-sm">Price</label>
                            <div class="relative">
                                <span class="absolute left-2 top-2 text-purple-300 text-sm">₱</span>
                                <input type="number" name="loads[0][price]" class="w-full pl-6 pr-2 py-2 bg-gray-700 border border-purple-900/30 rounded text-white focus:outline-none focus:border-purple-500" placeholder="0.00" step="0.01" min="0.01" required>
                            </div>
                        </div>
                        <div>
                            <label class="block text-purple-200 mb-1 text-sm">Status</label>
                            <select name="loads[0][status]" class="w-full px-3 py-2 bg-gray-700 border border-purple-900/30 rounded text-white focus:outline-none focus:border-purple-500" required>
                                <option value="completed">✅ Completed</option>
                                <option value="not_completed">⏳ Not Completed</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Add More Button -->
            <div class="mb-6">
                <button type="button" onclick="addLoadEntry()" class="px-4 py-2 bg-purple-600 hover:bg-purple-500 rounded text-sm">
                    + Add Another Load Entry
                </button>
            </div>

            <!-- Summary Section -->
            <div class="bg-purple-900/20 border border-purple-700 rounded-lg p-4 mb-6">
                <h3 class="text-purple-300 font-medium mb-2">Summary</h3>
                <div class="grid grid-cols-2 gap-4 text-sm">
                    <div>
                        <span class="text-purple-400">Total Entries:</span>
                        <span id="totalEntries" class="text-white font-medium">1</span>
                    </div>
                    <div>
                        <span class="text-purple-400">Total Amount:</span>
                        <span id="totalAmount" class="text-white font-medium">₱0.00</span>
                    </div>
                </div>
            </div>

            <!-- Submit Buttons -->
            <div class="flex gap-3">
                <button type="submit" class="flex-1 px-6 py-3 bg-purple-600 hover:bg-purple-500 rounded font-medium">Process All Loads</button>
                <a href="{{ Auth::user()->isSuperAdmin() ? route('eload.transactions.history') : route('admin.eload.transactions.history') }}" class="px-4 py-3 bg-gray-700 hover:bg-gray-600 rounded">History</a>
            </div>
        </form>

        <!-- Auto Date/Time Info -->
        <div class="mt-4 bg-blue-900/30 border border-blue-700 rounded-md p-3">
            <p class="text-blue-200 text-sm">📅 Date & Time will be automatically recorded when you submit</p>
            <p class="text-blue-300 text-xs mt-1">Current Server Time: {{ now()->format('M d, Y h:i A') }}</p>
        </div>

        <!-- Back Link -->
        <div class="mt-4 text-center">
            <a href="{{ Auth::user()->isSuperAdmin() ? route('eload.add-load') : route('admin.eload.add-load') }}" class="text-purple-300 hover:text-purple-200 text-sm">← Switch to Single Load Entry</a>
        </div>

    </div>

    <script>
        let entryCount = 1;

        function addLoadEntry() {
            entryCount++;
            const container = document.getElementById('loadEntries');
            
            const newEntry = document.createElement('div');
            newEntry.className = 'load-entry bg-gray-800/50 p-4 rounded-lg border border-purple-900/30';
            newEntry.setAttribute('data-entry', entryCount);
            
            newEntry.innerHTML = `
                <div class="flex justify-between items-center mb-3">
                    <h3 class="text-purple-300 font-medium">Load Entry #${entryCount}</h3>
                    <button type="button" onclick="removeLoadEntry(this)" class="text-red-400 hover:text-red-300 text-sm">Remove</button>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-4 gap-3">
                    <div>
                        <label class="block text-purple-200 mb-1 text-sm">Network</label>
                        <input type="text" name="loads[${entryCount - 1}][network]" class="w-full px-3 py-2 bg-gray-700 border border-purple-900/30 rounded text-white focus:outline-none focus:border-purple-500" placeholder="Smart, Globe, DITO" required>
                    </div>
                    <div>
                        <label class="block text-purple-200 mb-1 text-sm">Mobile Number</label>
                        <input type="text" name="loads[${entryCount - 1}][eload_number]" class="w-full px-3 py-2 bg-gray-700 border border-purple-900/30 rounded text-white focus:outline-none focus:border-purple-500" placeholder="09123456789" maxlength="11" required>
                    </div>
                    <div>
                        <label class="block text-purple-200 mb-1 text-sm">Price</label>
                        <div class="relative">
                            <span class="absolute left-2 top-2 text-purple-300 text-sm">₱</span>
                            <input type="number" name="loads[${entryCount - 1}][price]" class="w-full pl-6 pr-2 py-2 bg-gray-700 border border-purple-900/30 rounded text-white focus:outline-none focus:border-purple-500" placeholder="0.00" step="0.01" min="0.01" required onchange="updateSummary()">
                        </div>
                    </div>
                    <div>
                        <label class="block text-purple-200 mb-1 text-sm">Status</label>
                        <select name="loads[${entryCount - 1}][status]" class="w-full px-3 py-2 bg-gray-700 border border-purple-900/30 rounded text-white focus:outline-none focus:border-purple-500" required>
                            <option value="completed">✅ Completed</option>
                            <option value="not_completed">⏳ Not Completed</option>
                        </select>
                    </div>
                </div>
            `;
            
            container.appendChild(newEntry);
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
                const priceInput = entry.querySelector('input[name*="[price]"]');
                if (priceInput && priceInput.value) {
                    totalAmount += parseFloat(priceInput.value) || 0;
                }
            });
            
            document.getElementById('totalEntries').textContent = totalEntries;
            document.getElementById('totalAmount').textContent = '₱' + totalAmount.toFixed(2);
        }

        // Add event listeners to existing price inputs
        document.addEventListener('DOMContentLoaded', function() {
            const priceInputs = document.querySelectorAll('input[name*="[price]"]');
            priceInputs.forEach(input => {
                input.addEventListener('input', updateSummary);
            });
            updateSummary();
        });
    </script>
</body>
</html>
