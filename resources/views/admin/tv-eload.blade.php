<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>📺 Television E-Load - Admin Assistant</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .gradient-bg {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        .tv-gradient {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
        }
        .card-hover {
            transition: all 0.3s ease;
        }
        .card-hover:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.2);
        }
        .pulse-animation {
            animation: pulse 2s infinite;
        }
        @keyframes pulse {
            0% { opacity: 1; }
            50% { opacity: 0.7; }
            100% { opacity: 1; }
        }
        .loading-spinner {
            border: 3px solid #f3f3f3;
            border-top: 3px solid #f5576c;
            border-radius: 50%;
            width: 20px;
            height: 20px;
            animation: spin 1s linear infinite;
        }
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        .provider-card {
            transition: all 0.3s ease;
            cursor: pointer;
        }
        .provider-card:hover {
            transform: scale(1.05);
        }
        .provider-card.selected {
            border-color: #f5576c;
            background: linear-gradient(135deg, #f093fb20 0%, #f5576c20 100%);
        }
        .amount-btn {
            transition: all 0.2s ease;
        }
        .amount-btn:hover {
            transform: scale(1.05);
        }
        .amount-btn.selected {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            color: white;
        }
    </style>
</head>
<body class="bg-gradient-to-br from-black via-gray-900 to-blue-900 min-h-screen text-white">
    <div class="max-w-7xl mx-auto p-6">
        <!-- Header -->
        <header class="flex items-center justify-between mb-8">
            <div>
                <h1 class="text-3xl font-bold text-pink-400">📺 Television E-Load</h1>
                <p class="text-pink-200 text-sm mt-1">Satellite TV & Streaming Load Management</p>
            </div>
            <div class="flex items-center gap-4">
                <div class="text-sm text-blue-200">
                    Welcome, {{ Auth::user()->name }} (Admin Assistant)
                </div>
                <a href="{{ route('dashboard.admin') }}" class="px-4 py-2 text-blue-200 hover:bg-blue-900/30 rounded">
                    <i class="fas fa-arrow-left mr-2"></i>Back to Dashboard
                </a>
                <form method="POST" action="{{ route('logout') }}" class="inline">
                    @csrf
                    <button type="submit" class="px-4 py-2 text-blue-200 hover:bg-blue-900/30 rounded">Logout</button>
                </form>
            </div>
        </header>

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">
            <div class="bg-black/60 p-6 rounded-lg border border-pink-900/30 card-hover">
                <div class="flex items-center justify-between mb-2">
                    <h3 class="text-pink-300 font-semibold">Today</h3>
                    <div class="w-10 h-10 bg-pink-900/50 rounded-lg flex items-center justify-center">
                        <span class="text-pink-400">📺</span>
                    </div>
                </div>
                <p class="text-2xl font-bold text-white" id="todayTransactions">{{ $todayTransactions }}</p>
                <p class="text-xs text-pink-200">Transactions Today</p>
            </div>
            
            <div class="bg-black/60 p-6 rounded-lg border border-pink-900/30 card-hover">
                <div class="flex items-center justify-between mb-2">
                    <h3 class="text-pink-300 font-semibold">Today</h3>
                    <div class="w-10 h-10 bg-pink-900/50 rounded-lg flex items-center justify-center">
                        <span class="text-pink-400">💰</span>
                    </div>
                </div>
                <p class="text-2xl font-bold text-green-400" id="todayAmount">₱{{ number_format($todayAmount, 2) }}</p>
                <p class="text-xs text-pink-200">Amount Today</p>
            </div>
            
            <div class="bg-black/60 p-6 rounded-lg border border-pink-900/30 card-hover">
                <div class="flex items-center justify-between mb-2">
                    <h3 class="text-pink-300 font-semibold">Total</h3>
                    <div class="w-10 h-10 bg-pink-900/50 rounded-lg flex items-center justify-center">
                        <span class="text-pink-400">📊</span>
                    </div>
                </div>
                <p class="text-2xl font-bold text-white" id="totalTransactions">{{ $totalTransactions }}</p>
                <p class="text-xs text-pink-200">All Transactions</p>
            </div>
            
            <div class="bg-black/60 p-6 rounded-lg border border-pink-900/30 card-hover">
                <div class="flex items-center justify-between mb-2">
                    <h3 class="text-pink-300 font-semibold">Total</h3>
                    <div class="w-10 h-10 bg-pink-900/50 rounded-lg flex items-center justify-center">
                        <span class="text-pink-400">💵</span>
                    </div>
                </div>
                <p class="text-2xl font-bold text-green-400" id="totalAmount">₱{{ number_format($totalAmount, 2) }}</p>
                <p class="text-xs text-pink-200">Total Amount</p>
            </div>
        </div>

        <!-- Debug Info (remove in production) -->
        <div class="bg-black/40 p-4 rounded-lg border border-blue-900/30 mb-6 text-xs">
            <p class="text-blue-300">Debug Info: Today = {{ now()->format('Y-m-d') }} | Today's Transactions = {{ $todayTransactions }} | Today's Amount = ₱{{ number_format($todayAmount, 2) }}</p>
        </div>

        <!-- Main Content -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- TV E-Load Form -->
            <div class="lg:col-span-1">
                <div class="bg-black/60 p-6 rounded-lg border border-pink-900/30">
                    <h2 class="text-xl font-semibold mb-4 text-pink-400">
                        📺 Television E-Load
                        <span class="ml-2 text-xs bg-green-600 px-2 py-1 rounded">System Online</span>
                    </h2>
                    
                    <form id="tvEloadForm" class="space-y-4">
                        @csrf
                        
                        <!-- Account Number -->
                        <div>
                            <label class="block text-sm font-medium text-pink-300 mb-2">Account Number</label>
                            <input type="text" id="accountNumber" name="account_number" 
                                   class="w-full px-4 py-2 border border-pink-700 bg-black/40 rounded-md text-white placeholder-pink-300" 
                                   placeholder="Enter TV account number (no limit)" required>
                            <p class="text-xs text-pink-200 mt-1">No character limit</p>
                        </div>
                        
                        <!-- Customer Name -->
                        <div>
                            <label class="block text-sm font-medium text-pink-300 mb-2">Customer Name (Optional)</label>
                            <input type="text" id="customerName" name="customer_name" 
                                   class="w-full px-4 py-2 border border-pink-700 bg-black/40 rounded-md text-white placeholder-pink-300" 
                                   placeholder="Enter customer name">
                        </div>
                        
                        <!-- Contact Number -->
                        <div>
                            <label class="block text-sm font-medium text-pink-300 mb-2">Contact Number (Optional)</label>
                            <input type="text" id="contactNumber" name="contact_number" 
                                   class="w-full px-4 py-2 border border-pink-700 bg-black/40 rounded-md text-white placeholder-pink-300" 
                                   placeholder="Contact number">
                        </div>
                        
                        <!-- TV Provider -->
                        <div>
                            <label class="block text-sm font-medium text-pink-300 mb-2">TV Provider</label>
                            <div class="grid grid-cols-2 gap-2">
                                <div class="provider-card border border-pink-700 rounded-lg p-3 text-center cursor-pointer hover:border-pink-500" data-provider="Cignal">
                                    <div class="text-pink-400 font-semibold">Cignal</div>
                                    <div class="text-xs text-pink-200">Direct-to-Home Satellite TV</div>
                                </div>
                                <div class="provider-card border border-pink-700 rounded-lg p-3 text-center cursor-pointer hover:border-pink-500" data-provider="GMA Pinoy TV">
                                    <div class="text-pink-400 font-semibold">GMA Pinoy TV</div>
                                    <div class="text-xs text-pink-200">International Filipino Channel</div>
                                </div>
                                <div class="provider-card border border-pink-700 rounded-lg p-3 text-center cursor-pointer hover:border-pink-500" data-provider="Kapamilya Channel">
                                    <div class="text-pink-400 font-semibold">Kapamilya Channel</div>
                                    <div class="text-xs text-pink-200">ABS-CBN International</div>
                                </div>
                                <div class="provider-card border border-pink-700 rounded-lg p-3 text-center cursor-pointer hover:border-pink-500" data-provider="Dream Satellite">
                                    <div class="text-pink-400 font-semibold">Dream Satellite</div>
                                    <div class="text-xs text-pink-200">Satellite TV Service</div>
                                </div>
                                <div class="provider-card border border-pink-700 rounded-lg p-3 text-center cursor-pointer hover:border-pink-500" data-provider="Satlite">
                                    <div class="text-pink-400 font-semibold">Satlite</div>
                                    <div class="text-xs text-pink-200">Affordable Satellite TV</div>
                                </div>
                                <div class="provider-card border border-pink-700 rounded-lg p-3 text-center cursor-pointer hover:border-pink-500" data-provider="Sky Direct">
                                    <div class="text-pink-400 font-semibold">Sky Direct</div>
                                    <div class="text-xs text-pink-200">Direct-to-Home Service</div>
                                </div>
                                <div class="provider-card border border-pink-700 rounded-lg p-3 text-center cursor-pointer hover:border-pink-500" data-provider="GSAT">
                                    <div class="text-pink-400 font-semibold">GSAT</div>
                                    <div class="text-xs text-pink-200">Global Satellite TV</div>
                                </div>
                                <div class="provider-card border border-pink-700 rounded-lg p-3 text-center cursor-pointer hover:border-pink-500" data-provider="Cignal Play">
                                    <div class="text-pink-400 font-semibold">Cignal Play</div>
                                    <div class="text-xs text-pink-200">Digital Streaming Service</div>
                                </div>
                            </div>
                            <input type="hidden" id="selectedProvider" name="provider" value="Cignal" required>
                        </div>
                        
                        <!-- Load Amount -->
                        <div>
                            <label class="block text-sm font-medium text-pink-300 mb-2">Load Amount</label>
                            <div class="grid grid-cols-3 gap-2 mb-2">
                                <button type="button" class="amount-btn border border-pink-700 rounded-lg p-2 text-center hover:border-pink-500" data-amount="50">₱50</button>
                                <button type="button" class="amount-btn border border-pink-700 rounded-lg p-2 text-center hover:border-pink-500" data-amount="100">₱100</button>
                                <button type="button" class="amount-btn border border-pink-700 rounded-lg p-2 text-center hover:border-pink-500" data-amount="150">₱150</button>
                                <button type="button" class="amount-btn border border-pink-700 rounded-lg p-2 text-center hover:border-pink-500" data-amount="200">₱200</button>
                                <button type="button" class="amount-btn border border-pink-700 rounded-lg p-2 text-center hover:border-pink-500" data-amount="300">₱300</button>
                                <button type="button" class="amount-btn border border-pink-700 rounded-lg p-2 text-center hover:border-pink-500" data-amount="500">₱500</button>
                                <button type="button" class="amount-btn border border-pink-700 rounded-lg p-2 text-center hover:border-pink-500" data-amount="750">₱750</button>
                                <button type="button" class="amount-btn border border-pink-700 rounded-lg p-2 text-center hover:border-pink-500" data-amount="1000">₱1000</button>
                                <button type="button" class="amount-btn border border-pink-700 rounded-lg p-2 text-center hover:border-pink-500" data-amount="1500">₱1500</button>
                                <button type="button" class="amount-btn border border-pink-700 rounded-lg p-2 text-center hover:border-pink-500" data-amount="2000">₱2000</button>
                            </div>
                            <input type="number" id="customAmount" name="amount" 
                                   class="w-full px-4 py-2 border border-pink-700 bg-black/40 rounded-md text-white placeholder-pink-300" 
                                   placeholder="Enter custom amount" min="50" max="10000" required>
                            <p class="text-xs text-pink-200 mt-1">or enter custom amount (₱50-₱10,000)</p>
                        </div>
                        
                        <!-- Transaction Summary -->
                        <div class="bg-pink-900/20 border border-pink-700 rounded-lg p-4">
                            <h3 class="text-pink-400 font-semibold mb-2">Transaction Summary</h3>
                            <div class="space-y-1 text-sm">
                                <div class="flex justify-between">
                                    <span class="text-pink-200">Account Number:</span>
                                    <span id="summaryAccount" class="text-white">-</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-pink-200">Customer Name:</span>
                                    <span id="summaryCustomer" class="text-white">Walk-in Customer</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-pink-200">Provider:</span>
                                    <span id="summaryProvider" class="text-white">Cignal</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-pink-200">Amount:</span>
                                    <span id="summaryAmount" class="text-white">₱0.00</span>
                                </div>
                                <div class="flex justify-between font-semibold text-lg border-t border-pink-700 pt-1">
                                    <span class="text-pink-200">Total:</span>
                                    <span id="summaryTotal" class="text-green-400">₱0.00</span>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Submit Buttons -->
                        <div class="flex gap-2">
                            <button type="submit" class="flex-1 bg-pink-600 hover:bg-pink-700 text-white font-semibold py-3 px-4 rounded-lg transition-colors">
                                <i class="fas fa-plus-circle mr-2"></i>Add TV E-Load Record
                            </button>
                            <button type="button" onclick="resetForm()" class="bg-gray-600 hover:bg-gray-700 text-white font-semibold py-3 px-4 rounded-lg transition-colors">
                                <i class="fas fa-redo mr-2"></i>Reset
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            
            <!-- Transactions List -->
            <div class="lg:col-span-2">
                <div class="bg-black/60 p-6 rounded-lg border border-pink-900/30">
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-xl font-semibold text-pink-400">Recent TV E-Load Transactions</h2>
                        <div class="flex gap-2">
                            <button onclick="refreshTransactions()" class="bg-pink-600 hover:bg-pink-700 text-white px-3 py-1 rounded text-sm transition-colors">
                                <i class="fas fa-sync-alt mr-1"></i>Refresh
                            </button>
                            <button onclick="cleanupOldData()" class="bg-orange-600 hover:bg-orange-700 text-white px-3 py-1 rounded text-sm transition-colors">
                                <i class="fas fa-trash mr-1"></i>Cleanup Old Data
                            </button>
                            <button onclick="exportTransactions()" class="bg-green-600 hover:bg-green-700 text-white px-3 py-1 rounded text-sm transition-colors">
                                <i class="fas fa-download mr-1"></i>Export
                            </button>
                        </div>
                    </div>
                    
                    <!-- Search -->
                    <div class="mb-4">
                        <div class="relative">
                            <input type="text" id="searchInput" placeholder="Search by account number, customer name, or reference..." 
                                   class="w-full px-4 py-2 pl-10 border border-pink-700 bg-black/40 rounded-md text-white placeholder-pink-300">
                            <i class="fas fa-search absolute left-3 top-3 text-pink-300"></i>
                        </div>
                        <button onclick="clearSearch()" class="mt-2 text-pink-300 hover:text-pink-100 text-sm">
                            <i class="fas fa-times mr-1"></i>Clear
                        </button>
                    </div>
                    
                    <!-- Transactions Table -->
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="border-b border-pink-700">
                                    <th class="text-left p-2 text-pink-300">Reference</th>
                                    <th class="text-left p-2 text-pink-300">Account Number</th>
                                    <th class="text-left p-2 text-pink-300">Customer</th>
                                    <th class="text-left p-2 text-pink-300">Provider</th>
                                    <th class="text-right p-2 text-pink-300">Amount</th>
                                    <th class="text-center p-2 text-pink-300">Status</th>
                                    <th class="text-left p-2 text-pink-300">Date</th>
                                </tr>
                            </thead>
                            <tbody id="transactionsBody">
                                <!-- Transactions will be loaded here -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        let selectedProvider = 'Cignal';
        let selectedAmount = 0;
        
        // Initialize
        document.addEventListener('DOMContentLoaded', function() {
            loadTransactions();
            // Don't call updateStats immediately since server provides initial data
            
            // Set default provider
            document.querySelector('[data-provider="Cignal"]').classList.add('selected');
            
            // Auto-refresh stats every 30 seconds (start after initial page load)
            setTimeout(() => {
                setInterval(updateStats, 30000);
            }, 30000);
            
            // Auto-refresh transactions every 60 seconds (start after initial page load)
            setTimeout(() => {
                setInterval(loadTransactions, 60000);
            }, 60000);
        });
        
        // Provider selection
        document.querySelectorAll('.provider-card').forEach(card => {
            card.addEventListener('click', function() {
                document.querySelectorAll('.provider-card').forEach(c => c.classList.remove('selected'));
                this.classList.add('selected');
                selectedProvider = this.dataset.provider;
                document.getElementById('selectedProvider').value = selectedProvider;
                document.getElementById('summaryProvider').textContent = selectedProvider;
            });
        });
        
        // Amount selection
        document.querySelectorAll('.amount-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                document.querySelectorAll('.amount-btn').forEach(b => b.classList.remove('selected'));
                this.classList.add('selected');
                selectedAmount = parseInt(this.dataset.amount);
                document.getElementById('customAmount').value = selectedAmount;
                updateSummary();
            });
        });
        
        // Custom amount input
        document.getElementById('customAmount').addEventListener('input', function() {
            selectedAmount = parseInt(this.value) || 0;
            document.querySelectorAll('.amount-btn').forEach(b => b.classList.remove('selected'));
            updateSummary();
        });
        
        // Account number input
        document.getElementById('accountNumber').addEventListener('input', updateSummary);
        
        // Customer name input
        document.getElementById('customerName').addEventListener('input', updateSummary);
        
        function updateSummary() {
            const accountNumber = document.getElementById('accountNumber').value;
            const customerName = document.getElementById('customerName').value;
            
            document.getElementById('summaryAccount').textContent = accountNumber || '-';
            document.getElementById('summaryCustomer').textContent = customerName || 'Walk-in Customer';
            document.getElementById('summaryAmount').textContent = selectedAmount > 0 ? `₱${selectedAmount.toLocaleString()}.00` : '₱0.00';
            document.getElementById('summaryTotal').textContent = selectedAmount > 0 ? `₱${selectedAmount.toLocaleString()}.00` : '₱0.00';
        }
        
        // Form submission
        document.getElementById('tvEloadForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            const data = Object.fromEntries(formData);
            
            // Add TV E-Load record
            fetch('/television-eload/process', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify(data)
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('TV E-Load record added successfully!');
                    resetForm();
                    loadTransactions();
                    updateStats();
                } else {
                    alert('Error: ' + (data.message || 'Unknown error'));
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred while processing the request');
            });
        });
        
        function resetForm() {
            document.getElementById('tvEloadForm').reset();
            selectedAmount = 0;
            selectedProvider = 'Cignal';
            
            document.querySelectorAll('.provider-card').forEach(c => c.classList.remove('selected'));
            document.querySelector('[data-provider="Cignal"]').classList.add('selected');
            
            document.querySelectorAll('.amount-btn').forEach(b => b.classList.remove('selected'));
            
            updateSummary();
        }
        
        function loadTransactions() {
            fetch('/television-eload/transactions')
                .then(response => response.json())
                .then(data => {
                    displayTransactions(data.transactions);
                })
                .catch(error => {
                    console.error('Error loading transactions:', error);
                });
        }
        
        function displayTransactions(transactions) {
            const tbody = document.getElementById('transactionsBody');
            tbody.innerHTML = '';
            
            if (transactions.length === 0) {
                tbody.innerHTML = '<tr><td colspan="7" class="text-center p-4 text-pink-200">No transactions found</td></tr>';
                return;
            }
            
            transactions.forEach(transaction => {
                const row = document.createElement('tr');
                row.className = 'border-b border-pink-800 hover:bg-pink-900/10';
                row.innerHTML = `
                    <td class="p-2 font-mono text-xs">${transaction.transaction_id || 'N/A'}</td>
                    <td class="p-2">${transaction.eload_number || 'N/A'}</td>
                    <td class="p-2">
                        <div>${transaction.customer_name || 'Walk-in'}</div>
                        ${transaction.customer_contact ? `<div class="text-xs text-pink-200">${transaction.customer_contact}</div>` : ''}
                    </td>
                    <td class="p-2">${transaction.provider || 'N/A'}</td>
                    <td class="p-2 text-right">₱${parseFloat(transaction.price || 0).toLocaleString()}.00</td>
                    <td class="p-2 text-center">
                        <span class="px-2 py-1 rounded text-xs ${transaction.status === 'completed' ? 'bg-green-600' : 'bg-yellow-600'}">
                            ${transaction.status}
                        </span>
                    </td>
                    <td class="p-2 text-xs text-pink-200">${new Date(transaction.created_at).toLocaleString()}</td>
                `;
                tbody.appendChild(row);
            });
        }
        
        function updateStats() {
            // Fetch real-time stats from server
            fetch('/television-eload/stats')
                .then(response => response.json())
                .then(data => {
                    document.getElementById('todayTransactions').textContent = data.todayTransactions;
                    document.getElementById('todayAmount').textContent = `₱${parseFloat(data.todayAmount).toLocaleString()}.00`;
                    document.getElementById('totalTransactions').textContent = data.totalTransactions;
                    document.getElementById('totalAmount').textContent = `₱${parseFloat(data.totalAmount).toLocaleString()}.00`;
                })
                .catch(error => {
                    console.error('Error fetching stats:', error);
                    // Fallback to calculating from displayed transactions
                    calculateStatsFromDisplay();
                });
        }
        
        function calculateStatsFromDisplay() {
            // Fallback method - calculate from displayed transactions
            const rows = document.querySelectorAll('#transactionsBody tr');
            let todayCount = 0;
            let todayAmount = 0;
            let totalCount = rows.length;
            let totalAmount = 0;
            
            const today = new Date().toDateString();
            
            rows.forEach(row => {
                const cells = row.querySelectorAll('td');
                if (cells.length > 0) {
                    const dateCell = cells[6].textContent;
                    const amountCell = cells[4].textContent.replace('₱', '').replace(',', '');
                    const amount = parseFloat(amountCell) || 0;
                    
                    totalAmount += amount;
                    
                    if (dateCell.includes(today)) {
                        todayCount++;
                        todayAmount += amount;
                    }
                }
            });
            
            document.getElementById('todayTransactions').textContent = todayCount;
            document.getElementById('todayAmount').textContent = `₱${todayAmount.toLocaleString()}.00`;
            document.getElementById('totalTransactions').textContent = totalCount;
            document.getElementById('totalAmount').textContent = `₱${totalAmount.toLocaleString()}.00`;
        }
        
        function refreshTransactions() {
            loadTransactions();
            updateStats();
        }
        
        function clearSearch() {
            document.getElementById('searchInput').value = '';
            loadTransactions();
        }
        
        function cleanupOldData() {
            if (confirm('Are you sure you want to delete old transaction data?')) {
                fetch('/television-eload/cleanup-old', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Old data cleaned up successfully!');
                        loadTransactions();
                        updateStats();
                    } else {
                        alert('Error: ' + (data.message || 'Unknown error'));
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('An error occurred while cleaning up data');
                });
            }
        }
        
        function exportTransactions() {
            window.open('/television-eload/export', '_blank');
        }
    </script>
</body>
</html>
