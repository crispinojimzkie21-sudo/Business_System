<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>📺 Television E-Load - {{ $user->name }}</title>
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
            background: rgba(245, 87, 108, 0.1);
        }
    </style>
</head>
<body class="bg-gray-900 text-white min-h-screen">
    <!-- Header -->
    <header class="gradient-bg shadow-lg">
        <div class="container mx-auto px-4 py-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <i class="fas fa-satellite-dish text-3xl text-white"></i>
                    <div>
                        <h1 class="text-2xl font-bold text-white">📺 Television E-Load</h1>
                        <p class="text-purple-200 text-sm">Satellite TV & Streaming Load Management</p>
                    </div>
                </div>
                
                <!-- Desktop Navigation -->
                <div class="hidden md:flex items-center space-x-4">
                    <span class="text-white">Welcome, {{ $user->name }}</span>
                    <a href="{{ route('dashboard.admin') }}" class="bg-white/20 hover:bg-white/30 px-4 py-2 rounded-lg text-white transition-colors">
                        <i class="fas fa-arrow-left mr-2"></i>Back to Dashboard
                    </a>
                    <form action="{{ route('logout') }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" class="bg-red-500 hover:bg-red-600 px-4 py-2 rounded-lg text-white transition-colors">
                            <i class="fas fa-sign-out-alt mr-2"></i>Logout
                        </button>
                    </form>
                </div>
                
                <!-- Mobile Navigation -->
                <div class="md:hidden">
                    <button onclick="toggleMobileMenu()" class="bg-white/20 hover:bg-white/30 px-4 py-2 rounded-lg text-white transition-colors">
                        <i class="fas fa-bars mr-2"></i>Menu
                    </button>
                    
                    <!-- Mobile Menu Dropdown -->
                    <div id="mobileMenu" class="hidden absolute right-0 top-16 bg-gray-900 border border-gray-700 rounded-lg shadow-xl z-50 min-w-48">
                        <div class="py-2">
                            <div class="px-4 py-2 border-b border-gray-700">
                                <span class="text-white text-sm">{{ $user->name }}</span>
                            </div>
                            <a href="{{ route('dashboard.admin') }}" class="block px-4 py-3 text-white hover:bg-gray-800 transition-colors">
                                <i class="fas fa-arrow-left mr-2"></i>Back to Dashboard
                            </a>
                            <div class="px-4 py-2 border-b border-gray-700">
                                <form action="{{ route('logout') }}" method="POST">
                                    @csrf
                                    <button type="submit" class="w-full text-left text-white hover:bg-gray-800 transition-colors">
                                        <i class="fas fa-sign-out-alt mr-2"></i>Logout
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="container mx-auto px-4 py-8">
        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <div class="bg-gray-800 rounded-xl p-6 border border-purple-500/30 card-hover">
                <div class="flex items-center justify-between mb-4">
                    <i class="fas fa-chart-line text-3xl text-purple-400"></i>
                    <span class="text-xs text-purple-300 bg-purple-900/30 px-2 py-1 rounded">Today</span>
                </div>
                <h3 class="text-2xl font-bold text-white">{{ $todayTransactions }}</h3>
                <p class="text-gray-400 text-sm">Transactions Today</p>
            </div>
            
            <div class="bg-gray-800 rounded-xl p-6 border border-green-500/30 card-hover">
                <div class="flex items-center justify-between mb-4">
                    <i class="fas fa-peso-sign text-3xl text-green-400"></i>
                    <span class="text-xs text-green-300 bg-green-900/30 px-2 py-1 rounded">Today</span>
                </div>
                <h3 class="text-2xl font-bold text-white">₱{{ number_format($todayAmount, 2) }}</h3>
                <p class="text-gray-400 text-sm">Amount Today</p>
            </div>
            
            <div class="bg-gray-800 rounded-xl p-6 border border-blue-500/30 card-hover">
                <div class="flex items-center justify-between mb-4">
                    <i class="fas fa-history text-3xl text-blue-400"></i>
                    <span class="text-xs text-blue-300 bg-blue-900/30 px-2 py-1 rounded">Total</span>
                </div>
                <h3 class="text-2xl font-bold text-white">{{ $totalTransactions }}</h3>
                <p class="text-gray-400 text-sm">All Transactions</p>
            </div>
            
            <div class="bg-gray-800 rounded-xl p-6 border border-yellow-500/30 card-hover">
                <div class="flex items-center justify-between mb-4">
                    <i class="fas fa-coins text-3xl text-yellow-400"></i>
                    <span class="text-xs text-yellow-300 bg-yellow-900/30 px-2 py-1 rounded">Total</span>
                </div>
                <h3 class="text-2xl font-bold text-white">₱{{ number_format($totalAmount, 2) }}</h3>
                <p class="text-gray-400 text-sm">Total Amount</p>
            </div>
        </div>

        <!-- TV E-Load Box -->
        <div class="bg-gray-800 rounded-xl p-8 border border-purple-500/30 mb-8">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-2xl font-bold text-white flex items-center">
                    <i class="fas fa-satellite-dish mr-3 text-purple-400"></i>
                    📺 Television E-Load
                </h2>
                <div class="flex items-center space-x-2">
                    <span class="w-3 h-3 bg-green-400 rounded-full pulse-animation"></span>
                    <span class="text-green-400 text-sm">System Online</span>
                </div>
            </div>

            <form id="tvEloadForm" class="space-y-6">
                <!-- Account Number Input -->
                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-2">
                        <i class="fas fa-id-card mr-2"></i>Account Number
                    </label>
                    <input type="text" 
                           id="accountNumber" 
                           name="account_number" 
                           placeholder="Enter TV account number (no limit)"
                           class="w-full px-4 py-3 bg-gray-700 border border-gray-600 rounded-lg text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                    <div id="accountError" class="text-red-400 text-sm mt-1 hidden"></div>
                </div>

                <!-- Customer Information -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-2">
                            <i class="fas fa-user mr-2"></i>Customer Name (Optional)
                        </label>
                        <input type="text" 
                               id="customerName" 
                               name="customer_name" 
                               placeholder="Enter customer name"
                               class="w-full px-4 py-3 bg-gray-700 border border-gray-600 rounded-lg text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-2">
                            <i class="fas fa-phone mr-2"></i>Contact Number (Optional)
                        </label>
                        <input type="text" 
                               id="customerContact" 
                               name="customer_contact" 
                               placeholder="Contact number"
                               class="w-full px-4 py-3 bg-gray-700 border border-gray-600 rounded-lg text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                    </div>
                </div>

                <!-- Provider Selection -->
                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-2">
                        <i class="fas fa-satellite mr-2"></i>TV Provider
                    </label>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                        @foreach($providers as $provider => $data)
                            <label class="relative">
                                <input type="radio" 
                                       name="provider" 
                                       value="{{ $provider }}" 
                                       class="peer sr-only"
                                       onchange="selectProvider('{{ $provider }}')"
                                       {{ $loop->first ? 'checked' : '' }}
                                       required>
                                <div class="provider-card border-2 border-gray-600 rounded-lg p-3 cursor-pointer transition-all peer-checked:border-{{ $data['color'] }}-500 peer-checked:bg-{{ $data['color'] }}-900/20 hover:border-gray-500">
                                    <div class="text-center">
                                        <i class="fas fa-tv text-{{ $data['color'] }}-400 text-xl mb-1"></i>
                                        <p class="text-sm font-medium">{{ $provider }}</p>
                                        <p class="text-xs text-gray-400 mt-1">{{ $data['description'] }}</p>
                                    </div>
                                </div>
                            </label>
                        @endforeach
                    </div>
                    <div id="providerInfo" class="mt-3 p-3 bg-gray-700 rounded-lg hidden">
                        <div id="providerDetails"></div>
                    </div>
                </div>

                <!-- Load Amount Selection -->
                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-2">
                        <i class="fas fa-money-bill-wave mr-2"></i>Load Amount
                    </label>
                    <div class="grid grid-cols-2 md:grid-cols-5 gap-3 mb-4">
                        @foreach($loadAmounts as $amount)
                            <button type="button" 
                                    onclick="selectAmount({{ $amount }})"
                                    class="amount-btn border-2 border-gray-600 rounded-lg p-3 hover:border-purple-500 hover:bg-purple-900/20 transition-all">
                                <div class="text-center">
                                    <p class="text-lg font-bold">₱{{ $amount }}</p>
                                </div>
                            </button>
                        @endforeach
                    </div>
                    
                    <!-- Custom Amount Input -->
                    <div class="flex items-center space-x-3">
                        <input type="number" 
                               id="customAmount" 
                               name="custom_amount" 
                               placeholder="Enter custom amount"
                               min="50" 
                               max="10000"
                               class="flex-1 px-4 py-3 bg-gray-700 border border-gray-600 rounded-lg text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                        <span class="text-gray-400">or enter custom amount (₱50-₱10,000)</span>
                    </div>
                    <div id="amountError" class="text-red-400 text-sm mt-1 hidden"></div>
                </div>

                <!-- Transaction Summary -->
                <div class="bg-gray-700 rounded-lg p-6 border border-gray-600">
                    <h3 class="text-lg font-semibold text-white mb-4">
                        <i class="fas fa-receipt mr-2"></i>Transaction Summary
                    </h3>
                    <div class="space-y-2">
                        <div class="flex justify-between">
                            <span class="text-gray-400">Account Number:</span>
                            <span id="summaryAccount" class="text-white">-</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-400">Customer Name:</span>
                            <span id="summaryCustomer" class="text-white">-</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-400">Provider:</span>
                            <span id="summaryProvider" class="text-white">-</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-400">Amount:</span>
                            <span id="summaryAmount" class="text-white font-bold">₱0.00</span>
                        </div>
                        <div class="flex justify-between pt-2 border-t border-gray-600">
                            <span class="text-gray-400">Total:</span>
                            <span id="summaryTotal" class="text-white font-bold text-xl">₱0.00</span>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex space-x-4">
                    <button type="submit" 
                            id="submitBtn"
                            class="flex-1 bg-purple-600 hover:bg-purple-700 text-white font-bold py-3 px-6 rounded-lg transition-colors flex items-center justify-center">
                        <i class="fas fa-paper-plane mr-2"></i>
                        <span id="submitText">Add TV E-Load Record</span>
                    </button>
                    <button type="button" 
                            onclick="resetForm()"
                            class="flex-1 bg-gray-600 hover:bg-gray-700 text-white font-bold py-3 px-6 rounded-lg transition-colors">
                        <i class="fas fa-redo mr-2"></i>
                        Reset
                    </button>
                </div>
            </form>
        </div>

        <!-- Recent Transactions -->
        <div class="bg-gray-800 rounded-xl p-6 border border-purple-500/30">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-xl font-bold text-white">
                    <i class="fas fa-history mr-2 text-purple-400"></i>
                    Recent TV E-Load Transactions
                </h2>
                <div class="flex space-x-2">
                    <button onclick="refreshTransactions()" class="bg-purple-600 hover:bg-purple-700 px-4 py-2 rounded-lg text-white transition-colors">
                        <i class="fas fa-sync-alt mr-2"></i>Refresh
                    </button>
                    <button onclick="cleanupOldTransactions()" class="bg-red-600 hover:bg-red-700 px-4 py-2 rounded-lg text-white transition-colors">
                        <i class="fas fa-trash mr-2"></i>Cleanup Old Data
                    </button>
                    <button onclick="exportTransactions()" class="bg-green-600 hover:bg-green-700 px-4 py-2 rounded-lg text-white transition-colors">
                        <i class="fas fa-download mr-2"></i>Export
                    </button>
                </div>
            </div>
                
                <!-- Search Bar -->
                <div class="mb-4">
                    <div class="flex space-x-2">
                        <div class="flex-1">
                            <input type="text" 
                                   id="searchInput" 
                                   placeholder="Search by account number, customer name, or reference..."
                                   class="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                        </div>
                        <button onclick="searchTransactions()" class="bg-blue-600 hover:bg-blue-700 px-6 py-2 rounded-lg text-white transition-colors">
                            <i class="fas fa-search mr-2"></i>Search
                        </button>
                        <button onclick="clearSearch()" class="bg-gray-600 hover:bg-gray-700 px-6 py-2 rounded-lg text-white transition-colors">
                            <i class="fas fa-times mr-2"></i>Clear
                        </button>
                    </div>
                </div>
                
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="border-b border-gray-700">
                                <th class="text-left py-3 px-4 text-gray-400 font-medium">Reference</th>
                                <th class="text-left py-3 px-4 text-gray-400 font-medium">Account Number</th>
                                <th class="text-left py-3 px-4 text-gray-400 font-medium">Customer</th>
                                <th class="text-left py-3 px-4 text-gray-400 font-medium">Provider</th>
                                <th class="text-left py-3 px-4 text-gray-400 font-medium">Amount</th>
                                <th class="text-left py-3 px-4 text-gray-400 font-medium">Status</th>
                                <th class="text-left py-3 px-4 text-gray-400 font-medium">Date</th>
                            </tr>
                        </thead>
                        <tbody id="transactionsTable">
                            @foreach($transactions as $transaction)
                                <tr class="border-b border-gray-700 hover:bg-gray-700/50 transition-colors">
                                    <td class="py-3 px-4">
                                        <span class="text-purple-400 font-mono text-sm">{{ $transaction->transaction_id }}</span>
                                    </td>
                                    <td class="py-3 px-4">{{ $transaction->eload_number }}</td>
                                    <td class="py-3 px-4">
                                        <div>
                                            <p class="text-sm">{{ $transaction->customer_name ?? $transaction->user?->name ?? 'Walk-in Customer' }}</p>
                                            <p class="text-xs text-gray-400">{{ $transaction->customer_contact ?? $transaction->user?->email ?? 'N/A' }}</p>
                                        </div>
                                    </td>
                                    <td class="py-3 px-4">
                                        <span class="px-2 py-1 bg-purple-900/30 text-purple-300 rounded text-xs">
                                            @if($transaction->provider)
                                                {{ $transaction->provider }}
                                            @endif
                                        </span>
                                    </td>
                                    <td class="py-3 px-4 font-bold">₱{{ number_format($transaction->price, 2) }}</td>
                                    <td class="py-3 px-4">
                                        <span class="px-2 py-1 bg-green-900/30 text-green-300 rounded text-xs">✅ {{ $transaction->status }}</span>
                                    </td>
                                    <td class="py-3 px-4 text-gray-400 text-sm">
                                        {{ $transaction->created_at->format('M d, Y h:i A') }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>

    <script>
        let selectedAmount = 0;
        let selectedProvider = '';
        
        // Mobile menu toggle
        function toggleMobileMenu() {
            const menu = document.getElementById('mobileMenu');
            menu.classList.toggle('hidden');
        }
        
        // Close mobile menu when clicking outside
        document.addEventListener('click', function(event) {
            const menu = document.getElementById('mobileMenu');
            const button = event.target.closest('button[onclick="toggleMobileMenu()"]');
            
            if (!menu.contains(event.target) && !button) {
                menu.classList.add('hidden');
            }
        });

        // Account number validation
        document.getElementById('accountNumber').addEventListener('input', function(e) {
            const value = e.target.value.trim();
            const errorDiv = document.getElementById('accountError');
            
            if (value && value.length < 1) {
                errorDiv.textContent = 'Account number is required';
                errorDiv.classList.remove('hidden');
            } else {
                errorDiv.classList.add('hidden');
            }
            
            updateSummary();
        });

        // Customer info updates
        document.getElementById('customerName').addEventListener('input', updateSummary);
        document.getElementById('customerContact').addEventListener('input', updateSummary);

        // Provider selection
        function selectProvider(provider) {
            selectedProvider = provider;
            
            // Update card styles
            document.querySelectorAll('.provider-card').forEach(card => {
                card.classList.remove('selected');
            });
            
            // Find clicked provider card
            const clickedCard = document.querySelector(`input[name="provider"][value="${provider}"]`)?.closest('.provider-card');
            if (clickedCard) {
                clickedCard.classList.add('selected');
            }
            
            updateSummary();
        }

        // Amount selection
        function selectAmount(amount) {
            selectedAmount = amount;
            document.getElementById('customAmount').value = '';
            
            // Update button styles
            document.querySelectorAll('.amount-btn').forEach(btn => {
                btn.classList.remove('border-purple-500', 'bg-purple-900/20');
                btn.classList.add('border-gray-600');
            });
            
            // Find clicked amount button
            const clickedBtn = event.target.closest('.amount-btn');
            if (clickedBtn) {
                clickedBtn.classList.remove('border-gray-600');
                clickedBtn.classList.add('border-purple-500', 'bg-purple-900/20');
            }
            
            updateSummary();
        }

        // Custom amount input
        document.getElementById('customAmount').addEventListener('input', function(e) {
            selectedAmount = parseFloat(e.target.value) || 0;
            
            // Reset button styles
            document.querySelectorAll('.amount-btn').forEach(btn => {
                btn.classList.remove('border-purple-500', 'bg-purple-900/20');
                btn.classList.add('border-gray-600');
            });
            
            updateSummary();
        });

        // Update transaction summary
        function updateSummary() {
            const accountNumber = document.getElementById('accountNumber').value;
            const customerName = document.getElementById('customerName').value || 'Walk-in Customer';
            const provider = selectedProvider || document.querySelector('input[name="provider"]:checked')?.value || '-';
            const amount = selectedAmount || parseFloat(document.getElementById('customAmount').value) || 0;
            
            document.getElementById('summaryAccount').textContent = accountNumber || '-';
            document.getElementById('summaryCustomer').textContent = customerName;
            document.getElementById('summaryProvider').textContent = provider;
            document.getElementById('summaryAmount').textContent = `₱${amount.toFixed(2)}`;
            document.getElementById('summaryTotal').textContent = `₱${amount.toFixed(2)}`;
        }

        // TV Load Form submission
        document.getElementById('tvEloadForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const accountNumber = document.getElementById('accountNumber').value.trim();
            const customerName = document.getElementById('customerName').value.trim();
            const customerContact = document.getElementById('customerContact').value.trim();
            const provider = document.querySelector('input[name="provider"]:checked');
            
            // Debug: Log the provider value
            console.log('Provider element:', provider);
            console.log('Provider value:', provider ? provider.value : 'No provider selected');
            
            const amount = selectedAmount || parseFloat(document.getElementById('customAmount').value);
            
            // Basic validation
            if (!accountNumber) {
                alert('Please enter account number');
                return;
            }
            
            if (!provider) {
                alert('Please select a TV provider');
                return;
            }
            
            if (!amount || amount < 50 || amount > 10000) {
                alert('Please enter valid amount (₱50 - ₱10,000)');
                return;
            }
            
            // Show loading
            const submitBtn = document.getElementById('submitBtn');
            const submitText = document.getElementById('submitText');
            submitBtn.disabled = true;
            submitText.innerHTML = '<div class="loading-spinner inline-block mr-2"></div>Adding Record...';
            
            // Submit record
            const requestData = {
                account_number: accountNumber,
                customer_name: customerName,
                customer_contact: customerContact,
                provider: provider.value,
                amount: amount
            };
            
            fetch('/television-eload/process', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify(requestData)
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Add record directly to table
                    addRecordToTable(data.transaction);
                    resetForm();
                    alert('TV E-Load record added successfully!');
                } else {
                    alert('Error: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error adding record. Please try again.');
            })
            .finally(() => {
                submitBtn.disabled = false;
                submitText.innerHTML = 'Add TV E-Load Record';
            });
        });

        // Add record directly to table
        function addRecordToTable(transaction) {
            const tbody = document.getElementById('transactionsTable');
            const newRow = document.createElement('tr');
            newRow.className = 'border-b border-gray-700 hover:bg-gray-700/50 transition-colors';
            newRow.innerHTML = `
                <td class="py-3 px-4">
                    <span class="text-purple-400 font-mono text-sm">${transaction.transaction_id}</span>
                </td>
                <td class="py-3 px-4">${transaction.eload_number}</td>
                <td class="py-3 px-4">
                    <div>
                        <p class="text-sm">${transaction.customer_name || 'Walk-in'}</p>
                        <p class="text-xs text-gray-400">${transaction.customer_contact || 'N/A'}</p>
                    </div>
                </td>
                <td class="py-3 px-4">
                    <span class="px-2 py-1 bg-purple-900/30 text-purple-300 rounded text-xs">
                        ${transaction.provider || transaction.network || ''}
                    </span>
                </td>
                <td class="py-3 px-4 font-bold">₱${parseFloat(transaction.price).toFixed(2)}</td>
                <td class="py-3 px-4">
                    <span class="px-2 py-1 bg-green-900/30 text-green-300 rounded text-xs">✅ ${transaction.status}</span>
                </td>
                <td class="py-3 px-4 text-gray-400 text-sm">
                    ${new Date().toLocaleString()}
                </td>
            `;
            
            // Add to top of table
            tbody.insertBefore(newRow, tbody.firstChild);
            
            // Update statistics
            updateStatistics();
        }

        // Search transactions
        function searchTransactions() {
            const searchTerm = document.getElementById('searchInput').value.trim();
            
            if (!searchTerm) {
                alert('Please enter a search term');
                return;
            }
            
            fetch('/television-eload/search', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    search: searchTerm
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    updateTransactionsTable(data.transactions);
                } else {
                    alert('Search failed: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error searching transactions:', error);
                alert('Error searching transactions. Please try again.');
            });
        }

        // Clear search
        function clearSearch() {
            document.getElementById('searchInput').value = '';
            refreshTransactions();
        }

        // Refresh transactions
        function refreshTransactions() {
            fetch('/television-eload/transactions', {
                method: 'GET',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    updateTransactionsTable(data.transactions);
                } else {
                    alert('Failed to refresh transactions: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error refreshing transactions:', error);
                alert('Error refreshing transactions. Please try again.');
            });
        }

        // Update transactions table for search results
        function updateTransactionsTable(transactions) {
            const tbody = document.getElementById('transactionsTable');
            tbody.innerHTML = transactions.map(transaction => `
                <tr class="border-b border-gray-700 hover:bg-gray-700/50 transition-colors">
                    <td class="py-3 px-4">
                        <span class="text-purple-400 font-mono text-sm">${transaction.transaction_id}</span>
                    </td>
                    <td class="py-3 px-4">${transaction.eload_number}</td>
                    <td class="py-3 px-4">
                        <div>
                            <p class="text-sm">${transaction.customer_name || transaction.user?.name || 'Walk-in Customer'}</p>
                            <p class="text-xs text-gray-400">${transaction.customer_contact || transaction.user?.email || 'N/A'}</p>
                        </div>
                    </td>
                    <td class="py-3 px-4">
                        <span class="px-2 py-1 bg-purple-900/30 text-purple-300 rounded text-xs">
                            ${transaction.provider || ''}
                        </span>
                    </td>
                    <td class="py-3 px-4 font-bold">₱${parseFloat(transaction.price).toFixed(2)}</td>
                    <td class="py-3 px-4">
                        <span class="px-2 py-1 bg-green-900/30 text-green-300 rounded text-xs">✅ ${transaction.status}</span>
                    </td>
                    <td class="py-3 px-4 text-gray-400 text-sm">
                        ${new Date(transaction.created_at).toLocaleString()}
                    </td>
                </tr>
            `).join('');
        }

        // Cleanup old transactions
        function cleanupOldTransactions() {
            if (!confirm('Are you sure you want to delete all TV E-Load transactions older than 1 year? This action cannot be undone.')) {
                return;
            }
            
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
                    alert(`Successfully deleted ${data.deleted_count} old transactions.`);
                    refreshTransactions();
                } else {
                    alert('Cleanup failed: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error cleaning up old transactions:', error);
                alert('Error cleaning up old transactions. Please try again.');
            });
        }

        // Search on Enter key
        document.getElementById('searchInput').addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                searchTransactions();
            }
        });

        // Update statistics
        function updateStatistics() {
            const todayTransactionsCount = document.querySelectorAll('#transactionsTable tr').length;
            const todayTransactionsElement = document.getElementById('todayTransactions');
            if (todayTransactionsElement) {
                todayTransactionsElement.textContent = todayTransactionsCount;
            }
        }

        // Reset form
        function resetForm() {
            document.getElementById('tvEloadForm').reset();
            selectedAmount = 0;
            selectedProvider = '';
            
            document.querySelectorAll('.amount-btn').forEach(btn => {
                btn.classList.remove('border-purple-500', 'bg-purple-900/20');
                btn.classList.add('border-gray-600');
            });
            
            document.querySelectorAll('.provider-card').forEach(card => {
                card.classList.remove('selected');
            });
            
            document.getElementById('accountError').classList.add('hidden');
            document.getElementById('amountError').classList.add('hidden');
            document.getElementById('providerInfo').classList.add('hidden');
            
            updateSummary();
        }

        // Update statistics
        function updateStatistics() {
            const todayTransactionsCount = document.querySelectorAll('#transactionsTable tr').length;
            const todayTransactionsElement = document.getElementById('todayTransactions');
            if (todayTransactionsElement) {
                todayTransactionsElement.textContent = todayTransactionsCount;
            }
        }

        // Export transactions
        function exportTransactions() {
            window.open('/television-eload/export', '_blank');
        }

        // Initialize
        document.addEventListener('DOMContentLoaded', function() {
            updateSummary();
        });
    </script>
</body>
</html>
