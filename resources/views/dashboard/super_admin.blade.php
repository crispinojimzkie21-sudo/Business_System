<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" href="/favicon.svg" type="image/svg+xml">
    <title>💰 Sales Management</title>
    <script>
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
    </script>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gradient-to-br from-black via-gray-900 to-red-900 min-h-screen text-white">
    <div class="max-w-7xl mx-auto p-6">
        <!-- Header -->
        <header class="flex items-center justify-between mb-8">
            <div>
                <h1 class="text-3xl font-bold text-red-400">🔐 Admin Dashboard</h1>
                <p class="text-red-200 text-sm mt-1">Full System Access Control</p>
            </div>
            
            <!-- Desktop Navigation -->
            <div class="hidden md:flex items-center gap-4">
                <a href="{{ route('superadmin.monthly-attendance') }}" class="px-3 py-2 bg-red-600 hover:bg-red-700 rounded text-sm">
                    <i class="fas fa-chart-bar mr-1"></i>Monthly Attendance
                </a>
                <a href="{{ route('television-eload.dashboard') }}" class="px-3 py-2 bg-purple-600 hover:bg-purple-700 rounded text-sm">
                    <i class="fas fa-tv mr-1"></i>📺 TV E-Load
                </a>
                <span class="text-sm text-red-200">{{ ucfirst($user->role) }} (Admin)</span>
                    <a href="{{ route('dashboard.profile') }}" class="px-3 py-2 text-red-200 hover:bg-red-900/30 rounded text-sm">
                        <i class="fas fa-user-circle mr-1"></i>Profile
                    </a>
                @if(Auth::check())
                    <form method="POST" action="{{ route('logout') }}" class="inline">
                        @csrf
                        <button type="submit" class="px-3 py-2 text-red-200 hover:bg-red-900/30 rounded">Logout</button>
                    </form>
                @else
                    <a href="{{ url('/login') }}" class="px-3 py-2 bg-red-600 hover:bg-red-500 rounded">Login</a>
                @endif
            </div>
            
            <!-- Mobile Navigation -->
            <div class="md:hidden">
                <button onclick="toggleMobileMenu()" class="px-3 py-2 bg-red-600 hover:bg-red-700 rounded text-white">
                    <i class="fas fa-bars mr-2"></i>Menu
                </button>
                
                <!-- Mobile Menu Dropdown -->
                <div id="mobileMenu" class="hidden absolute right-0 top-16 bg-gray-900 border border-gray-700 rounded-lg shadow-xl z-50 min-w-48">
                    <div class="py-2">
                        <a href="{{ route('superadmin.monthly-attendance') }}" class="block px-4 py-3 text-white hover:bg-gray-800 transition-colors">
                            <i class="fas fa-chart-bar mr-2"></i>📊 Monthly Attendance
                        </a>
                        <a href="{{ route('television-eload.dashboard') }}" class="block px-4 py-3 text-white hover:bg-gray-800 transition-colors">
                            <i class="fas fa-tv mr-2"></i>📺 TV E-Load
                        </a>
                        <div class="px-4 py-2 border-b border-gray-700">
                            <span class="text-sm text-red-200">{{ ucfirst($user->role) }} (Admin)</span>
                        </div>
                        <a href="{{ route('dashboard.profile') }}" class="block px-4 py-3 text-white hover:bg-gray-800 transition-colors">
                            <i class="fas fa-user-circle mr-2"></i>Profile
                        </a>
                        @if(Auth::check())
                            <div class="px-4 py-2 border-t border-gray-700">
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="w-full text-left text-white hover:bg-gray-800 transition-colors">
                                        <i class="fas fa-sign-out-alt mr-2"></i>Logout
                                    </button>
                                </form>
                            </div>
                        @else
                            <a href="{{ url('/login') }}" class="block px-4 py-3 text-white hover:bg-gray-800 transition-colors">
                                <i class="fas fa-sign-in-alt mr-2"></i>Login
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        </header>

        <!-- Design Icon Section -->
        <div class="flex items-center justify-center mb-8">
            <div class="bg-gradient-to-br from-red-900/50 to-black/70 p-8 rounded-2xl border border-red-800/30 shadow-2xl">
                <div class="flex items-center gap-6">
                    <!-- Super Admin Icon -->
                    <div class="relative">
                        <div class="w-20 h-20 bg-gradient-to-br from-red-600 to-red-800 rounded-full flex items-center justify-center shadow-lg">
                            <svg class="w-12 h-12 text-white" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 3c1.66 0 3 1.34 3 3s-1.34 3-3 3-3-1.34-3-3 1.34-3 3-3zm0 14.2c-2.5 0-4.71-1.28-6-3.22.03-1.99 4-3.08 6-3.08 1.99 0 5.97 1.09 6 3.08-1.29 1.94-3.5 3.22-6 3.22z"/>
                            </svg>
                        </div>
                        <!-- Crown Badge -->
                        <div class="absolute -top-2 -right-2 w-8 h-8 bg-yellow-500 rounded-full flex items-center justify-center shadow-lg">
                            <svg class="w-5 h-5 text-yellow-900" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M5 16L3 5l5.5 5L12 4l3.5 6L21 5l-2 11H5zm2.86-2h8.28l.96-5.85-2.68 2.54L12 8.5l-2.36 1.69-2.68-2.54.96 5.85z"/>
                            </svg>
                        </div>
                    </div>
                    
                    <!-- Welcome Text -->
                    <div class="text-left">
                        <h2 class="text-2xl font-bold text-red-400"> Admin Panel</h2>
                        <p class="text-red-200 text-sm mt-1">Complete System Control</p>
                        <div class="flex items-center gap-2 mt-2">
                            <span class="px-2 py-1 bg-red-900/50 rounded text-xs text-red-300">Administrator</span>
                            <span class="px-2 py-1 bg-green-900/50 rounded text-xs text-green-300">Full Access</span>
                            <span class="px-2 py-1 bg-blue-900/50 rounded text-xs text-blue-300">System Root</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>


        @if (session('success'))
            <div class="mb-6 bg-green-900/50 border border-green-700 rounded-md p-4">
                <p class="text-green-200">{{ session('success') }}</p>
            </div>
        @endif

        <!-- Quick Stats -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
            <div class="bg-gradient-to-br from-black/70 to-red-900/30 p-4 rounded-xl border border-red-900/50 hover:border-red-900/70 transition-all">
                <div class="flex items-center justify-between mb-2">
                    <h3 class="text-red-400 font-semibold text-sm">Total Users</h3>
                    <div class="w-8 h-8 bg-red-900/50 rounded-lg flex items-center justify-center">
                        <span class="text-red-400 text-xs">👥</span>
                    </div>
                </div>
                <p class="text-2xl font-bold text-white">{{ App\Models\User::count() }}</p>
                <p class="text-xs text-red-200">All registered users</p>
            </div>
            <div class="bg-gradient-to-br from-black/70 to-green-900/30 p-4 rounded-xl border border-green-900/50 hover:border-green-900/70 transition-all">
                <div class="flex items-center justify-between mb-2">
                    <h3 class="text-green-400 font-semibold text-sm">Total Products</h3>
                    <div class="w-8 h-8 bg-green-900/50 rounded-lg flex items-center justify-center">
                        <span class="text-green-400 text-xs">📦</span>
                    </div>
                </div>
                <p class="text-2xl font-bold text-white">{{ $totalProducts ?? 0 }}</p>
                <p class="text-xs text-green-200">Products in inventory</p>
            </div>
            <div class="bg-gradient-to-br from-black/70 to-blue-900/30 p-4 rounded-xl border border-blue-900/50 hover:border-blue-900/70 transition-all">
                <div class="flex items-center justify-between mb-2">
                    <h3 class="text-blue-400 font-semibold text-sm">Sales Today</h3>
                    <div class="w-8 h-8 bg-blue-900/50 rounded-lg flex items-center justify-center">
                        <span class="text-blue-400 text-xs">💰</span>
                    </div>
                </div>
                <p class="text-2xl font-bold text-white">₱{{ number_format($todaySales ?? 0, 2) }}</p>
                <p class="text-xs text-blue-200">Today's revenue</p>
            </div>
            <div class="bg-gradient-to-br from-black/70 to-yellow-900/30 p-4 rounded-xl border border-yellow-900/50 hover:border-yellow-900/70 transition-all">
                <div class="flex items-center justify-between mb-2">
                    <h3 class="text-yellow-400 font-semibold text-sm">Low Stock</h3>
                    <div class="w-8 h-8 bg-yellow-900/50 rounded-lg flex items-center justify-center">
                        <span class="text-yellow-400 text-xs">⚠️</span>
                    </div>
                </div>
                <p class="text-2xl font-bold text-white">{{ $lowStockProducts ?? 0 }}</p>
                <p class="text-xs text-yellow-200">Items need restocking</p>
            </div>
        </div>

        <!-- Main Management Sections -->
        <div class="grid grid-cols-1 xl:grid-cols-3 gap-6 mb-8">
            <!-- User Management - 2 columns wide -->
            <div class="xl:col-span-2 bg-gradient-to-br from-black/70 to-purple-900/20 p-6 rounded-xl border border-purple-900/50 hover:border-purple-900/70 transition-all">
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-10 h-10 bg-purple-900/50 rounded-lg flex items-center justify-center">
                        <span class="text-purple-400">👥</span>
                    </div>
                    <div>
                        <h2 class="text-xl font-semibold text-purple-400">User Management</h2>
                        <p class="text-xs text-purple-200">Manage all users and access control</p>
                    </div>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
                    <a href="{{ route('superadmin.access-control') }}" class="group p-3 bg-purple-900/30 rounded-lg hover:bg-purple-900/50 transition-all border border-purple-800/50 hover:border-purple-700/70">
                        <div class="flex items-center gap-2 mb-1">
                            <span class="text-purple-400">🔐</span>
                            <span class="font-medium text-white group-hover:text-purple-300">Access Control</span>
                        </div>
                        <p class="text-xs text-purple-200">Enable/disable access</p>
                    </a>
                    <a href="{{ route('admin.register.show') }}" class="group p-3 bg-purple-900/30 rounded-lg hover:bg-purple-900/50 transition-all border border-purple-800/50 hover:border-purple-700/70">
                        <div class="flex items-center gap-2 mb-1">
                            <span class="text-purple-400">➕</span>
                            <span class="font-medium text-white group-hover:text-purple-300">Create Admin</span>
                        </div>
                        <p class="text-xs text-purple-200">Add admin account</p>
                    </a>
                    <a href="{{ route('admin.list') }}" class="group p-3 bg-purple-900/30 rounded-lg hover:bg-purple-900/50 transition-all border border-purple-800/50 hover:border-purple-700/70">
                        <div class="flex items-center gap-2 mb-1">
                            <span class="text-purple-400">📋</span>
                            <span class="font-medium text-white group-hover:text-purple-300">Manage Admins</span>
                        </div>
                        <p class="text-xs text-purple-200">View admin accounts</p>
                    </a>
                    <a href="{{ route('employee.register.show') }}" class="group p-3 bg-purple-900/30 rounded-lg hover:bg-purple-900/50 transition-all border border-purple-800/50 hover:border-purple-700/70">
                        <div class="flex items-center gap-2 mb-1">
                            <span class="text-purple-400">👤</span>
                            <span class="font-medium text-white group-hover:text-purple-300">Create Employee</span>
                        </div>
                        <p class="text-xs text-purple-200">Add employee</p>
                    </a>
                    <a href="{{ route('employee.list') }}" class="group p-3 bg-purple-900/30 rounded-lg hover:bg-purple-900/50 transition-all border border-purple-800/50 hover:border-purple-700/70">
                        <div class="flex items-center gap-2 mb-1">
                            <span class="text-purple-400">👥</span>
                            <span class="font-medium text-white group-hover:text-purple-300">Manage Employees</span>
                        </div>
                        <p class="text-xs text-purple-200">View employees</p>
                    </a>
                    <a href="{{ route('profiles.all') }}" class="group p-3 bg-purple-900/30 rounded-lg hover:bg-purple-900/50 transition-all border border-purple-800/50 hover:border-purple-700/70">
                        <div class="flex items-center gap-2 mb-1">
                            <span class="text-purple-400">📊</span>
                            <span class="font-medium text-white group-hover:text-purple-300">All Profiles</span>
                        </div>
                        <p class="text-xs text-purple-200">User profiles</p>
                    </a>
                </div>
            </div>

            <!-- System Info - 1 column wide -->
            <div class="bg-gradient-to-br from-black/70 to-cyan-900/20 p-6 rounded-xl border border-cyan-900/50 hover:border-cyan-900/70 transition-all">
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-10 h-10 bg-cyan-900/50 rounded-lg flex items-center justify-center">
                        <span class="text-cyan-400">🔧</span>
                    </div>
                    <div>
                        <h2 class="text-xl font-semibold text-cyan-400">System Info</h2>
                        <p class="text-xs text-cyan-200">System status & health</p>
                    </div>
                </div>
                <div class="space-y-3">
                    <div class="bg-cyan-900/30 p-3 rounded-lg border border-cyan-800/50">
                        <div class="flex items-center justify-between mb-1">
                            <span class="text-cyan-300 text-sm font-medium">System Status</span>
                            <span class="text-green-400 text-xs">✅ Online</span>
                        </div>
                        <p class="text-xs text-cyan-200">All systems operational</p>
                    </div>
                    <div class="bg-cyan-900/30 p-3 rounded-lg border border-cyan-800/50">
                        <div class="flex items-center justify-between mb-1">
                            <span class="text-cyan-300 text-sm font-medium">Database</span>
                            <span class="text-green-400 text-xs">✅ Connected</span>
                        </div>
                        <p class="text-xs text-cyan-200">MySQL: {{ config('database.default') }}</p>
                    </div>
                    <div class="bg-cyan-900/30 p-3 rounded-lg border border-cyan-800/50">
                        <div class="flex items-center justify-between mb-1">
                            <span class="text-cyan-300 text-sm font-medium">Server Time</span>
                            <span class="text-cyan-400 text-xs">{{ now()->format('H:i') }}</span>
                        </div>
                        <p class="text-xs text-cyan-200">{{ now()->format('M d, Y') }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Product & Sales Row -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
            <!-- Product Management -->
            <div class="bg-gradient-to-br from-black/70 to-emerald-900/20 p-6 rounded-xl border border-emerald-900/50 hover:border-emerald-900/70 transition-all">
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-10 h-10 bg-emerald-900/50 rounded-lg flex items-center justify-center">
                        <span class="text-emerald-400">📦</span>
                    </div>
                    <div>
                        <h2 class="text-xl font-semibold text-emerald-400">Product Management</h2>
                        <p class="text-xs text-emerald-200">Inventory & stock control</p>
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <a href="{{ route('products.create') }}" class="group p-3 bg-emerald-900/30 rounded-lg hover:bg-emerald-900/50 transition-all border border-emerald-800/50 hover:border-emerald-700/70">
                        <div class="flex items-center gap-2 mb-1">
                            <span class="text-emerald-400">➕</span>
                            <span class="font-medium text-white group-hover:text-emerald-300 text-sm">Add Product</span>
                        </div>
                        <p class="text-xs text-emerald-200">Create new item</p>
                    </a>
                    <a href="{{ route('products.index') }}" class="group p-3 bg-emerald-900/30 rounded-lg hover:bg-emerald-900/50 transition-all border border-emerald-800/50 hover:border-emerald-700/70">
                        <div class="flex items-center gap-2 mb-1">
                            <span class="text-emerald-400">📋</span>
                            <span class="font-medium text-white group-hover:text-emerald-300 text-sm">Manage Products</span>
                        </div>
                        <p class="text-xs text-emerald-200">View & edit items</p>
                    </a>
                    <a href="{{ route('inventory.index') }}" class="group p-3 bg-emerald-900/30 rounded-lg hover:bg-emerald-900/50 transition-all border border-emerald-800/50 hover:border-emerald-700/70">
                        <div class="flex items-center gap-2 mb-1">
                            <span class="text-emerald-400">📊</span>
                            <span class="font-medium text-white group-hover:text-emerald-300 text-sm">Inventory</span>
                        </div>
                        <p class="text-xs text-emerald-200">Stock levels</p>
                    </a>
                    <a href="{{ route('products.create') }}" class="group p-3 bg-emerald-900/30 rounded-lg hover:bg-emerald-900/50 transition-all border border-emerald-800/50 hover:border-emerald-700/70">
                        <div class="flex items-center gap-2 mb-1">
                            <span class="text-emerald-400">⚡</span>
                            <span class="font-medium text-white group-hover:text-emerald-300 text-sm">Quick Add</span>
                        </div>
                        <p class="text-xs text-emerald-200">Fast product entry</p>
                    </a>
                </div>
            </div>

            <!-- Sales Management -->
            <div class="bg-gradient-to-br from-black/70 to-orange-900/20 p-6 rounded-xl border border-orange-900/50 hover:border-orange-900/70 transition-all">
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-10 h-10 bg-orange-900/50 rounded-lg flex items-center justify-center">
                        <span class="text-orange-400">💰</span>
                    </div>
                    <div>
                        <h2 class="text-xl font-semibold text-orange-400">Sales Management</h2>
                        <p class="text-xs text-orange-200">Transactions & reports</p>
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <a href="{{ route('superadmin.sales.create') }}" class="group p-3 bg-orange-900/30 rounded-lg hover:bg-orange-900/50 transition-all border border-orange-800/50 hover:border-orange-700/70">
                        <div class="flex items-center gap-2 mb-1">
                            <span class="text-orange-400">💳</span>
                            <span class="font-medium text-white group-hover:text-orange-300 text-sm">Process Sale</span>
                        </div>
                        <p class="text-xs text-orange-200">New transaction</p>
                    </a>
                    <a href="{{ route('superadmin.sales.reports') }}" class="group p-3 bg-orange-900/30 rounded-lg hover:bg-orange-900/50 transition-all border border-orange-800/50 hover:border-orange-700/70">
                        <div class="flex items-center gap-2 mb-1">
                            <span class="text-orange-400">📊</span>
                            <span class="font-medium text-white group-hover:text-orange-300 text-sm">Sales Reports</span>
                        </div>
                        <p class="text-xs text-orange-200">Analytics</p>
                    </a>
                </div>
            </div>

            <!-- E-Load Management -->
            <div class="bg-gradient-to-br from-black/70 to-red-900/20 p-6 rounded-xl border border-red-900/50 hover:border-red-900/70 transition-all">
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-10 h-10 bg-red-900/50 rounded-lg flex items-center justify-center">
                        <span class="text-red-400">📱</span>
                    </div>
                    <div>
                        <h2 class="text-xl font-semibold text-red-400">E-Load Management</h2>
                        <p class="text-xs text-red-200">Mobile loading services</p>
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <a href="{{ route('eload.index') }}" class="group p-3 bg-red-900/30 rounded-lg hover:bg-red-900/50 transition-all border border-red-800/50 hover:border-red-700/70">
                        <div class="flex items-center gap-2 mb-1">
                            <span class="text-red-400">📱</span>
                            <span class="font-medium text-white group-hover:text-red-300 text-sm">E-Load Products</span>
                        </div>
                        <p class="text-xs text-red-200">Manage load products</p>
                    </a>
                    <a href="{{ route('eload.numbers.index') }}" class="group p-3 bg-red-900/30 rounded-lg hover:bg-red-900/50 transition-all border border-red-800/50 hover:border-red-700/70">
                        <div class="flex items-center gap-2 mb-1">
                            <span class="text-red-400">📡</span>
                            <span class="font-medium text-white group-hover:text-red-300 text-sm">E-Load Numbers</span>
                        </div>
                        <p class="text-xs text-red-200">View gateway numbers</p>
                    </a>
                    <a href="{{ route('eload.add-load') }}" class="group p-3 bg-red-900/30 rounded-lg hover:bg-red-900/50 transition-all border border-red-800/50 hover:border-red-700/70">
                        <div class="flex items-center gap-2 mb-1">
                            <span class="text-red-400">➕</span>
                            <span class="font-medium text-white group-hover:text-red-300 text-sm">Add Load</span>
                        </div>
                        <p class="text-xs text-red-200">Process new load transaction</p>
                    </a>
                    <a href="{{ route('eload.add-load-multiple') }}" class="group p-3 bg-red-900/30 rounded-lg hover:bg-red-900/50 transition-all border border-red-800/50 hover:border-red-700/70">
                        <div class="flex items-center gap-2 mb-1">
                            <span class="text-red-400">📋</span>
                            <span class="font-medium text-white group-hover:text-red-300 text-sm">Add Multiple Loads</span>
                        </div>
                        <p class="text-xs text-red-200">Batch load processing</p>
                    </a>
                    <a href="{{ route('eload.transactions.history') }}" class="group p-3 bg-red-900/30 rounded-lg hover:bg-red-900/50 transition-all border border-red-800/50 hover:border-red-700/70">
                        <div class="flex items-center gap-2 mb-1">
                            <span class="text-red-400">📜</span>
                            <span class="font-medium text-white group-hover:text-red-300 text-sm">Transaction History</span>
                        </div>
                        <p class="text-xs text-red-200">View all load transactions</p>
                    </a>
                </div>
                </div>
            </div>
        </div>

        <!-- System Information + User Information (aligned two-column layout) -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
            <!-- System Information -->
            <div class="bg-black/60 p-6 rounded-lg border border-red-900/30">
                <h2 class="text-xl font-semibold mb-4 text-red-400">🔧 System Information</h2>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm mb-4">
                    <div>
                        <h3 class="font-semibold text-red-300">System Status</h3>
                        <p class="text-green-400">✅ All systems operational</p>
                    </div>
                    <div>
                        <h3 class="font-semibold text-red-300">Database</h3>
                        <p class="text-green-400">✅ Connected</p>
                    </div>
                    <div>
                        <h3 class="font-semibold text-red-300">Last Backup</h3>
                        @if($backupStatus['backup_available'])
                            <p class="text-green-400">✅ {{ \Carbon\Carbon::parse($backupStatus['last_backup_date'])->format('M d, Y h:i A') }}</p>
                        @else
                            <p class="text-yellow-400">⚠️ Not configured</p>
                        @endif
                    </div>
                </div>

                <!-- Backup Controls -->
                <div class="border-t border-red-900/30 pt-4">
                    <h3 class="font-semibold text-red-300 mb-3">🗄️ Backup Management</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="bg-gray-900/50 p-3 rounded-lg border border-red-900/20">
                            <p class="text-xs text-red-300 mb-2">Available Backups</p>
                            <p class="text-lg font-bold text-white">{{ $backupStatus['backup_count'] }}</p>
                            <p class="text-xs text-gray-400">Database files</p>
                        </div>
                        <div class="bg-gray-900/50 p-3 rounded-lg border border-red-900/20">
                            <p class="text-xs text-red-300 mb-2">Auto-Backup</p>
                            <p class="text-sm text-green-400">✅ Every 6 hours</p>
                            <p class="text-xs text-gray-400">Scheduled</p>
                        </div>
                    </div>

                    <div class="flex flex-wrap gap-2 mt-4">
                        <form action="{{ route('admin.backup.create') }}" method="POST" class="inline">
                            @csrf
                            <button type="submit" class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded text-sm font-medium transition-colors">
                                💾 Create Backup Now
                            </button>
                        </form>
                        @if($backupStatus['backup_count'] > 0)
                            <a href="{{ route('admin.backup.list') }}" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded text-sm font-medium transition-colors">
                                📋 View Backups
                            </a>
                        @endif
                    </div>

                    @if(!$backupStatus['backup_available'])
                        <div class="mt-3 p-2 bg-yellow-900/30 rounded text-xs text-yellow-300">
                            <p>⚠️ No backup history found. Create your first backup to protect your data.</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- User Information -->
            <div class="bg-black/60 p-6 rounded-lg border border-red-900/30">
                <h2 class="text-xl font-semibold mb-4 text-red-400">👤 Your Information</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                    <div>
                        <h3 class="font-semibold text-red-300 mb-2">Personal Details</h3>
                        <p><strong>Name:</strong> Kenjie </p>
                        <p><strong>Email:</strong> {{ Auth::user()->email }}</p>
                        <p><strong>Position:</strong> {{ Auth::user()->position ?? 'Developer' }}</p>
                        <p><strong>Salary:</strong> ₱{{ number_format(Auth::user()->salary ?? 0, 2) }}</p>
                    </div>
                    <div>
                        <h3 class="font-semibold text-red-300 mb-2">Account Details</h3>
                        <p><strong>Role:</strong> <span class="text-red-400"> Super Admin</span></p>
                        <p><strong>Account Type:</strong> Super Administrator</p>
                        <p><strong>Member Since:</strong> {{ Auth::user()->created_at->format('M d, Y') }}</p>
                        <p><a href="{{ route('dashboard.profile') }}" class="text-red-400 hover:underline"><i class="fas fa-key mr-1"></i>Change Password</a></p>
                        <p class="text-xs text-gray-400 mt-2"><i class="fas fa-info-circle mr-1"></i>Name and email are display-only. Edit through Employee Management.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Super Admin dashboard with location functionality
function getLocation(type) {
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(
            function(position) {
                if (type === 'checkin') {
                    document.getElementById('checkin_lat').value = position.coords.latitude;
                    document.getElementById('checkin_lng').value = position.coords.longitude;
                    document.getElementById('checkin_location').value = 'GPS Check-in';
                    document.getElementById('checkinForm').submit();
                } else {
                    document.getElementById('checkoutForm').submit();
                }
            },
            function(error) {
                alert('Location access denied. Using manual check-in/check-out.');
                if (type === 'checkin') {
                    document.getElementById('checkinForm').submit();
                } else {
                    document.getElementById('checkoutForm').submit();
                }
            }
        );
        return false;
    } else {
        alert('Geolocation is not supported by this browser.');
        return true;
    }
}

document.addEventListener('DOMContentLoaded', function() {
    console.log('Super Admin dashboard loaded');
});
</script>
</body>
</html>
