<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" href="/favicon.svg" type="image/svg+xml">
    <title>Assistant Dashboard - {{ config('app.name') }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
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
        
        // Attendance Local Storage Management
        function saveAttendanceState() {
            // Save current attendance status to local storage
            const attendanceData = {
                isCheckedIn: @if($todayAttendance && $todayAttendance->check_in && !$todayAttendance->check_out) true @else false @endif,
                checkInTime: @if($todayAttendance && $todayAttendance->check_in) '{{ $todayAttendance->check_in }}' @else null @endif,
                checkOutTime: @if($todayAttendance && $todayAttendance->check_out) '{{ $todayAttendance->check_out }}' @else null @endif,
                savedAt: new Date().toISOString()
            };
            localStorage.setItem('attendanceState', JSON.stringify(attendanceData));
        }

        function loadAttendanceState() {
            // Load attendance state from local storage
            const saved = localStorage.getItem('attendanceState');
            if (saved) {
                try {
                    const attendanceData = JSON.parse(saved);
                    return attendanceData;
                } catch (e) {
                    console.error('Error parsing attendance state:', e);
                    return null;
                }
            }
            return null;
        }

        function showAttendanceReminder() {
            // Show reminder if user was checked in before auto-logout
            const attendanceState = loadAttendanceState();
            if (attendanceState && attendanceState.isCheckedIn) {
                const savedTime = new Date(attendanceState.savedAt);
                const now = new Date();
                const hoursDiff = (now - savedTime) / (1000 * 60 * 60);
                
                // Only show reminder if saved within last 24 hours
                if (hoursDiff < 24) {
                    // Create reminder notification
                    const reminder = document.createElement('div');
                    reminder.className = 'fixed top-4 right-4 bg-yellow-900/90 border border-yellow-600 text-yellow-200 p-4 rounded-lg shadow-lg z-50 max-w-sm';
                    reminder.innerHTML = `
                        <div class="flex items-start gap-3">
                            <div class="flex-shrink-0">
                                <svg class="w-6 h-6 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                                </svg>
                            </div>
                            <div class="flex-1">
                                <p class="font-semibold text-yellow-300">⚠️ Attendance Reminder</p>
                                <p class="text-sm mt-1">You were checked in before. Don't forget to check out!</p>
                                <div class="flex gap-2 mt-2">
                                    <button onclick="this.closest('.fixed').remove()" class="px-2 py-1 bg-yellow-700 hover:bg-yellow-600 rounded text-xs">Dismiss</button>
                                    <button onclick="clearAttendanceState(); this.closest('.fixed').remove()" class="px-2 py-1 bg-red-700 hover:bg-red-600 rounded text-xs">Clear State</button>
                                </div>
                            </div>
                        </div>
                    `;
                    document.body.appendChild(reminder);
                    
                    // Auto-remove after 10 seconds
                    setTimeout(() => {
                        if (reminder.parentNode) {
                            reminder.remove();
                        }
                    }, 10000);
                }
            }
        }

        function clearAttendanceState() {
            localStorage.removeItem('attendanceState');
        }

        // Initialize attendance state management
        document.addEventListener('DOMContentLoaded', function() {
            // Save current state when page loads
            saveAttendanceState();
            
            // Show reminder if needed
            setTimeout(showAttendanceReminder, 2000);
            
            // Auto-save state every 5 minutes
            setInterval(saveAttendanceState, 5 * 60 * 1000);
        });

        // Save state before page unload (including auto-logout)
        window.addEventListener('beforeunload', saveAttendanceState);

        function getLocation(type) {
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(
                    function(position) {
                        document.getElementById(type + '_lat').value = position.coords.latitude;
                        document.getElementById(type + '_lng').value = position.coords.longitude;
                        document.getElementById(type + '_location').value = 'Location: ' + position.coords.latitude.toFixed(6) + ', ' + position.coords.longitude.toFixed(6);
                        document.getElementById(type + 'Form').submit();
                    },
                    function(error) {
                        console.log('Geolocation error:', error.message);
                        // Submit anyway with default/manual values
                        document.getElementById(type + '_location').value = 'Manual Check-' + (type === 'checkin' ? 'in' : 'out');
                        document.getElementById(type + 'Form').submit();
                    }
                );
            } else {
                // Geolocation not supported, submit with default values
                document.getElementById(type + '_location').value = 'Manual Check-' + (type === 'checkin' ? 'in' : 'out');
                document.getElementById(type + 'Form').submit();
            }
            return false;
        }
    </script>
</head>
<body class="bg-gradient-to-br from-black via-gray-900 to-blue-900 min-h-screen text-white">
    <div class="max-w-6xl mx-auto p-6">
        <!-- Header -->
        <header class="flex items-center justify-between mb-8">
            <div>
                <h1 class="text-3xl font-bold text-blue-400">👨‍💼 Assistant Dashboard</h1>
                @auth
                    <p class="text-blue-200 text-sm mt-1">Administrative Assistant Access</p>
                @else
                    <p class="text-blue-200 text-sm mt-1">Preview Mode - <a href="{{ route('login') }}" class="underline">Login</a> to access full features</p>
                @endauth
            </div>
            <div class="flex items-center gap-4">
@auth
                    <span class="text-sm text-blue-200">{{ 'Admin Assistant' }} (Assistant)</span>
                    <a href="{{ route('dashboard.profile') }}" class="px-3 py-2 text-blue-200 hover:bg-blue-900/30 rounded text-sm">
                        <i class="fas fa-user-circle mr-1"></i>Profile
                    </a>
                @else
                    <span class="text-sm text-yellow-300">Preview Mode</span>
                @endauth
                @auth
                    <form method="POST" action="{{ route('logout') }}" class="inline">
                        @csrf
                        <button type="submit" class="px-3 py-2 text-blue-200 hover:bg-blue-900/30 rounded">Logout</button>
                    </form>
                @else
                    <a href="{{ url('/login') }}" class="px-3 py-2 bg-blue-600 hover:bg-blue-500 rounded">Login</a>
                @endauth
            </div>
            
            <!-- Mobile Navigation -->
            <div class="md:hidden">
                <button onclick="toggleMobileMenu()" class="px-3 py-2 bg-blue-600 hover:bg-blue-700 rounded text-white">
                    <i class="fas fa-bars mr-2"></i>Menu
                </button>
                
                <!-- Mobile Menu Dropdown -->
                <div id="mobileMenu" class="hidden absolute right-0 top-16 bg-gray-900 border border-gray-700 rounded-lg shadow-xl z-50 min-w-48">
                    <div class="py-2">
@auth
                        <div class="px-4 py-2 border-b border-gray-700">
                            <span class="text-sm text-blue-200">{{ 'Admin Assistant' }} (Assistant)</span>
                        </div>
                        <a href="{{ route('television-eload.dashboard') }}" class="block px-4 py-3 text-white hover:bg-gray-800 transition-colors">
                            <i class="fas fa-tv mr-2"></i>📺 TV E-Load
                        </a>
                        <a href="{{ route('attendance.records') }}" class="block px-4 py-3 text-white hover:bg-gray-800 transition-colors">
                            <i class="fas fa-users mr-2"></i>📋 Employee Attendance
                        </a>
                        <a href="{{ route('attendance.index') }}" class="block px-4 py-3 text-white hover:bg-gray-800 transition-colors">
                            <i class="fas fa-calendar-day mr-2"></i>📅 Today's Attendance
                        </a>
                        <a href="{{ route('dashboard.profile') }}" class="block px-4 py-3 text-white hover:bg-gray-800 transition-colors">
                            <i class="fas fa-user-circle mr-2"></i>Profile
                        </a>
                    @else
                        <div class="px-4 py-2 border-b border-gray-700">
                            <span class="text-sm text-yellow-300">Preview Mode</span>
                        </div>
                        <a href="{{ url('/login') }}" class="block px-4 py-3 text-white hover:bg-gray-800 transition-colors">
                            <i class="fas fa-sign-in-alt mr-2"></i>Login
                        </a>
                    @endauth
                    @auth
                        <div class="px-4 py-2 border-t border-gray-700">
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="w-full text-left text-white hover:bg-gray-800 transition-colors">
                                    <i class="fas fa-sign-out-alt mr-2"></i>Logout
                                </button>
                            </form>
                        </div>
                    @endauth
                    </div>
                </div>
            </div>
        </header>


        @if (session('success'))
            <div class="mb-6 bg-green-900/50 border border-green-700 rounded-md p-4">
                <p class="text-green-200">{{ session('success') }}</p>
            </div>
        @endif

        @auth
        @else
        <!-- Preview Notice -->
        <div class="mb-6 bg-yellow-900/50 border border-yellow-700 rounded-md p-4">
            <p class="text-yellow-200">📋 This is a preview of the Assistant Dashboard. <a href="{{ route('login') }}" class="underline font-semibold">Click here to login</a> to access the full functionality.</p>
        </div>
        @endauth

        <!-- Quick Stats -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">
            <div class="bg-black/60 p-4 rounded-lg border border-blue-900/30">
                <h3 class="text-blue-400 font-semibold">Total Employees</h3>
                <p class="text-2xl font-bold" id="totalEmployees">{{ $totalEmployees ?? 0 }}</p>
                <p class="text-xs text-blue-200">All employees</p>
            </div>
            <div class="bg-black/60 p-4 rounded-lg border border-blue-900/30">
                <h3 class="text-blue-400 font-semibold">Active Employees</h3>
                <p class="text-2xl font-bold" id="activeEmployees">{{ $activeEmployees ?? 0 }}</p>
                <p class="text-xs text-blue-200">Currently active</p>
            </div>
            <div class="bg-black/60 p-4 rounded-lg border border-blue-900/30">
                <h3 class="text-blue-400 font-semibold">Products</h3>
                <p class="text-2xl font-bold">{{ $totalProducts ?? 0 }}</p>
                <p class="text-xs text-blue-200">Total products</p>
            </div>
            <div class="bg-black/60 p-4 rounded-lg border border-blue-900/30">
                <h3 class="text-blue-400 font-semibold">Sales Today</h3>
                <p class="text-2xl font-bold">₱{{ number_format($todaySales ?? 0, 2) }}</p>
                <p class="text-xs text-blue-200">Today's revenue</p>
            </div>
            <div class="bg-black/60 p-4 rounded-lg border border-blue-900/30">
                <h3 class="text-blue-400 font-semibold">Your Position</h3>
                @auth
                    <p class="text-xl font-bold">{{ Auth::user()->position ?? 'Administrative Assistant' }}</p>
                @else
                    <p class="text-xl font-bold">Admin Assistant</p>
                @endauth
                <p class="text-xs text-blue-200">Current role</p>
            </div>
        </div>

        <!-- Attendance Statistics -->
        <div class="bg-black/60 p-6 rounded-lg border border-blue-900/30 mb-8">
            <h2 class="text-xl font-semibold mb-4 text-blue-400">📊 Your Attendance Statistics</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="bg-gray-900/50 p-4 rounded-lg border border-blue-900/20">
                    <div class="flex items-center justify-between mb-2">
                        <h3 class="text-blue-300 font-semibold text-sm">Days This Month</h3>
                        <div class="w-8 h-8 bg-blue-900/50 rounded-lg flex items-center justify-center">
                            <span class="text-blue-400 text-xs">📅</span>
                        </div>
                    </div>
                    <p class="text-2xl font-bold text-white">{{ $daysThisMonth ?? 0 }}</p>
                    <p class="text-xs text-gray-400">Working days attended</p>
                </div>
                <div class="bg-gray-900/50 p-4 rounded-lg border border-blue-900/20">
                    <div class="flex items-center justify-between mb-2">
                        <h3 class="text-blue-300 font-semibold text-sm">Hours Worked</h3>
                        <div class="w-8 h-8 bg-blue-900/50 rounded-lg flex items-center justify-center">
                            <span class="text-blue-400 text-xs">⏱️</span>
                        </div>
                    </div>
                    <p class="text-2xl font-bold text-white">{{ $hoursWorked ?? '0h 0m' }}</p>
                    <p class="text-xs text-gray-400">Total time this month</p>
                </div>
                <div class="bg-gray-900/50 p-4 rounded-lg border border-blue-900/20">
                    <div class="flex items-center justify-between mb-2">
                        <h3 class="text-blue-300 font-semibold text-sm">Attendance Rate</h3>
                        <div class="w-8 h-8 bg-blue-900/50 rounded-lg flex items-center justify-center">
                            <span class="text-blue-400 text-xs">📈</span>
                        </div>
                    </div>
                    <p class="text-2xl font-bold text-green-400">{{ $attendanceRate ?? 0 }}%</p>
                    <p class="text-xs text-gray-400">Monthly percentage</p>
                </div>
            </div>
        </div>

        <!-- Admin Functions -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
            @auth
            <!-- User Management -->
            <div class="bg-black/60 p-6 rounded-lg border border-blue-900/30">
                <h2 class="text-xl font-semibold mb-4 text-blue-400">👥 User Management</h2>
                <p class="text-sm text-blue-200 mb-4">Manage Admin Assistant and Employee accounts</p>
                <div class="grid grid-cols-1 gap-3">
                    <a href="{{ route('employee.register.show') }}" class="block p-3 bg-gray-800 rounded hover:bg-blue-900/30 transition-colors">
                        <span class="font-medium">➕ Create Employee Account</span>
                        <p class="text-xs text-blue-200">Add new employee</p>
                    </a>
                    <a href="{{ route('employee.list') }}" class="block p-3 bg-gray-800 rounded hover:bg-blue-900/30 transition-colors">
                        <span class="font-medium">👥 Manage Employees</span>
                        <p class="text-xs text-blue-200">View and edit employees</p>
                    </a>
                </div>
            </div>

            <!-- Attendance Management -->
            <div class="bg-black/60 p-6 rounded-lg border border-purple-900/30">
                <h2 class="text-xl font-semibold mb-4 text-purple-400">📊 Attendance Management</h2>
                <p class="text-sm text-purple-200 mb-4">Complete attendance overview and records</p>
                <div class="grid grid-cols-1 gap-3">
                    <a href="{{ route('attendance.records') }}" class="block p-3 bg-gray-800 rounded hover:bg-purple-900/30 transition-colors">
                        <span class="font-medium">📋  Attendance Records</span>
                        <p class="text-xs text-purple-200">Complete yearly attendance overview for all employees</p>
                    </a>
                    <a href="{{ route('attendance.index') }}" class="block p-3 bg-gray-800 rounded hover:bg-purple-900/30 transition-colors">
                        <span class="font-medium">📅 Today's Attendance</span>
                        <p class="text-xs text-purple-200">View current day attendance records</p>
                    </a>
                </div>
            </div>

            <!-- TV E-Load Management -->
            <div class="bg-black/60 p-6 rounded-lg border border-pink-900/30">
                <h2 class="text-xl font-semibold mb-4 text-pink-400">📺 TV E-Load Management</h2>
                <p class="text-sm text-pink-200 mb-4">Satellite TV & streaming load management</p>
                <div class="grid grid-cols-1 gap-3">
                    <a href="{{ route('admin.tv-eload') }}" class="block p-3 bg-gray-800 rounded hover:bg-pink-900/30 transition-colors">
                        <span class="font-medium">📺 Television E-Load</span>
                        <p class="text-xs text-pink-200">Manage TV satellite and streaming loads</p>
                    </a>
                </div>
            </div>

            <!-- Sales Management Section -->
            <div class="bg-black/60 p-4 md:p-6 rounded-lg border border-green-900/30 mb-6 md:mb-8">
                <h2 class="text-xl font-semibold mb-3 md:mb-4 text-green-400">💰 Sales Management</h2>
                <p class="text-sm text-green-200 mb-4">Process sales and manage transactions (Admin Access)</p>
                
                <!-- Sales Quick Actions -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-3 mb-4">
                    <a href="{{ route('admin.sales.create') }}" class="block p-4 bg-green-700/50 rounded-lg hover:bg-green-600/50 transition-colors text-center border border-green-500/30">
                        <span class="font-medium text-lg">💳</span>
                        <p class="text-sm font-semibold mt-1">Process Sale</p>
                        <p class="text-xs text-green-200">Create new transaction</p>
                    </a>
                    <a href="{{ route('admin.sales.history') }}" class="block p-4 bg-gray-800 rounded-lg hover:bg-green-900/30 transition-colors text-center border border-green-900/30">
                        <span class="font-medium text-lg">📜</span>
                        <p class="text-sm font-semibold mt-1">Sales History</p>
                        <p class="text-xs text-green-200">View all transactions</p>
                    </a>
                    <a href="{{ route('products.index') }}" class="block p-4 bg-gray-800 rounded-lg hover:bg-green-900/30 transition-colors text-center border border-green-900/30">
                        <span class="font-medium text-lg">📦</span>
                        <p class="text-sm font-semibold mt-1">Products</p>
                        <p class="text-xs text-green-200">Browse catalog</p>
                    </a>
                    <a href="{{ route('inventory.index') }}" class="block p-4 bg-gray-800 rounded-lg hover:bg-green-900/30 transition-colors text-center border border-green-900/30">
                        <span class="font-medium text-lg">📊</span>
                        <p class="text-sm font-semibold mt-1">Inventory</p>
                        <p class="text-xs text-green-200">Stock levels</p>
                    </a>
                </div>
                
                <!-- Sales Stats Summary -->
                <div class="grid grid-cols-2 md:grid-cols-4 gap-3 mt-4">
                    <div class="bg-gray-900/50 p-3 rounded-lg border border-green-900/20">
                        <p class="text-xs text-green-300">Today's Sales</p>
                        <p class="text-lg font-bold text-green-400">₱{{ number_format($todaySales ?? 0, 2) }}</p>
                    </div>
                    <div class="bg-gray-900/50 p-3 rounded-lg border border-green-900/20">
                        <p class="text-xs text-green-300">Today's Transactions</p>
                        <p class="text-lg font-bold text-green-400">{{ $todayTransactions ?? 0 }}</p>
                    </div>
                    <div class="bg-gray-900/50 p-3 rounded-lg border border-green-900/20">
                        <p class="text-xs text-green-300">Monthly Sales</p>
                        <p class="text-lg font-bold text-green-400">₱{{ number_format($monthlySales ?? 0, 2) }}</p>
                    </div>
                    <div class="bg-gray-900/50 p-3 rounded-lg border border-green-900/20">
                        <p class="text-xs text-green-300">Monthly Transactions</p>
                        <p class="text-lg font-bold text-green-400">{{ $monthlyTransactions ?? 0 }}</p>
                    </div>
                </div>
                
                <!-- Products & E-Load Stats -->
                <div class="grid grid-cols-2 md:grid-cols-4 gap-3 mt-4">
                    <div class="bg-gray-900/50 p-3 rounded-lg border border-green-900/20">
                        <p class="text-xs text-green-300">Products Available</p>
                        <p class="text-lg font-bold text-green-400">{{ $totalProducts ?? 0 }}</p>
                    </div>
                    <div class="bg-gray-900/50 p-3 rounded-lg border border-green-900/20">
                        <p class="text-xs text-green-300">Low Stock Items</p>
                        <p class="text-lg font-bold text-yellow-400">{{ $lowStockCount ?? 0 }}</p>
                    </div>
                    <div class="bg-gray-900/50 p-3 rounded-lg border border-blue-900/20">
                        <p class="text-xs text-blue-300">Today's E-Load</p>
                        <p class="text-lg font-bold text-blue-400">₱{{ number_format($todayEloadSales ?? 0, 2) }}</p>
                    </div>
                    <div class="bg-gray-900/50 p-3 rounded-lg border border-blue-900/20">
                        <p class="text-xs text-blue-300">E-Load Transactions</p>
                        <p class="text-lg font-bold text-blue-400">{{ $todayEloadTransactions ?? 0 }}</p>
                    </div>
                </div>
            </div>


            <!-- E-Load Management -->
            <div class="bg-black/60 p-6 rounded-lg border border-blue-900/30">
                <h2 class="text-xl font-semibold mb-4 text-blue-400">📱 E-Load Management</h2>
                <p class="text-sm text-blue-200 mb-4">Manage electronic load products and transactions</p>
                <div class="grid grid-cols-1 gap-3">
                    <a href="{{ route('eload.index') }}" class="block p-3 bg-gray-800 rounded hover:bg-blue-900/30 transition-colors">
                        <span class="font-medium">📱 E-Load Products</span>
                        <p class="text-xs text-blue-200">Manage load products</p>
                    </a>
                    <a href="{{ route('admin.eload.add-load') }}" class="block p-3 bg-gray-800 rounded hover:bg-blue-900/30 transition-colors">
                        <span class="font-medium">➕ Add Load</span>
                        <p class="text-xs text-blue-200">Process new load transaction</p>
                    </a>
                    <a href="{{ route('admin.eload.transactions.history') }}" class="block p-3 bg-gray-800 rounded hover:bg-blue-900/30 transition-colors">
                        <span class="font-medium">📜 Transaction History</span>
                        <p class="text-xs text-blue-200">View all load transactions</p>
                    </a>
                </div>
                
                <!-- Quick Add Load Form -->
                <div class="mt-4 p-4 bg-gray-800/50 rounded-lg border border-blue-900/20">
                    <h3 class="text-lg font-medium text-blue-300 mb-3">⚡ Quick Add Load</h3>
                    <form method="POST" action="{{ route('admin.eload.process-load') }}" class="space-y-3">
                        @csrf
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                            <div>
                                <label class="block text-blue-200 mb-1 text-sm">Network</label>
                                <input type="text" name="network" class="w-full px-3 py-2 bg-gray-700 border border-blue-900/30 rounded text-white text-sm focus:outline-none focus:border-blue-500" placeholder="DITO, Smart, Globe, TM" required>
                            </div>
                            <div>
                                <label class="block text-blue-200 mb-1 text-sm">Mobile Number</label>
                                <input type="text" name="eload_number" class="w-full px-3 py-2 bg-gray-700 border border-blue-900/30 rounded text-white text-sm focus:outline-none focus:border-blue-500" placeholder="09123456789" maxlength="11" required>
                            </div>
                            <div>
                                <label class="block text-blue-200 mb-1 text-sm">Price (₱)</label>
                                <input type="number" name="price" class="w-full px-3 py-2 bg-gray-700 border border-blue-900/30 rounded text-white text-sm focus:outline-none focus:border-blue-500" placeholder="50.00" step="0.01" min="1" required>
                            </div>
                            <div>
                                <label class="block text-blue-200 mb-1 text-sm">Status</label>
                                <select name="status" class="w-full px-3 py-2 bg-gray-700 border border-blue-900/30 rounded text-white text-sm focus:outline-none focus:border-blue-500" required>
                                    <option value="completed">✅ Completed</option>
                                    <option value="not_completed">⏳ Not Completed</option>
                                </select>
                            </div>
                        </div>
                        <div class="flex gap-2 mt-3">
                            <button type="submit" class="flex-1 px-4 py-2 bg-blue-600 hover:bg-blue-500 rounded text-sm font-medium">
                                ⚡ Process Load
                            </button>
                            <a href="{{ route('admin.eload.add-load') }}" class="px-4 py-2 bg-gray-600 hover:bg-gray-500 rounded text-sm font-medium">
                                📝 Advanced Form
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            @else
            <!-- User Management (Preview) -->
            <div class="bg-black/60 p-6 rounded-lg border border-blue-900/30">
                <h2 class="text-xl font-semibold mb-4 text-blue-400">👥 User Management</h2>
                <p class="text-sm text-blue-200 mb-4">Manage Admin Assistant and Employee accounts</p>
                <div class="grid grid-cols-1 gap-3">
                    <a href="{{ route('login') }}" class="block p-3 bg-gray-800 rounded hover:bg-blue-900/30 transition-colors">
                        <span class="font-medium">➕ Create Employee Account</span>
                        <p class="text-xs text-blue-200">Add new employee</p>
                    </a>
                    <a href="{{ route('login') }}" class="block p-3 bg-gray-800 rounded hover:bg-blue-900/30 transition-colors">
                        <span class="font-medium">👥 Manage Employees</span>
                        <p class="text-xs text-blue-200">View and edit employees</p>
                    </a>
                </div>
            </div>

            <!-- Sales & Products (Preview) -->
            <div class="bg-black/60 p-6 rounded-lg border border-blue-900/30">
                <h2 class="text-xl font-semibold mb-4 text-blue-400">💼 Sales & Products</h2>
                <p class="text-sm text-blue-200 mb-4">Manage products and inventory. Assist in managing products. View inventory status.</p>
                <div class="grid grid-cols-1 gap-3">
                    <a href="{{ route('login') }}" class="block p-3 bg-gray-800 rounded hover:bg-blue-900/30 transition-colors">
                        <span class="font-medium">💳 Process Sale</span>
                        <p class="text-xs text-blue-200">Create new transaction</p>
                    </a>
                    <a href="{{ route('login') }}" class="block p-3 bg-gray-800 rounded hover:bg-blue-900/30 transition-colors">
                        <span class="font-medium">📦 View Products</span>
                        <p class="text-xs text-blue-200">Browse product catalog</p>
                    </a>
                    <a href="{{ route('login') }}" class="block p-3 bg-gray-800 rounded hover:bg-blue-900/30 transition-colors">
                        <span class="font-medium">📜 Sales History</span>
                        <p class="text-xs text-blue-200">View transaction records</p>
                    </a>
                </div>
            </div>
            @endauth
        </div>

        <!-- Attendance Section -->
        <div class="bg-black/60 p-6 rounded-lg border border-blue-900/30">
            <h2 class="text-xl font-semibold mb-4 text-blue-400">⏰ Attendance Management</h2>
            <p class="text-sm text-blue-200 mb-4">View employee attendance records</p>
            @auth
            <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                <a href="{{ route('attendance.index') }}" class="block p-3 bg-gray-800 rounded hover:bg-blue-900/30 transition-colors">
                    <span class="font-medium">🕐 Today's Attendance</span>
                    <p class="text-xs text-blue-200">View current day</p>
                </a>
                <a href="{{ route('attendance.index') }}" class="block p-3 bg-gray-800 rounded hover:bg-blue-900/30 transition-colors">
                    <span class="font-medium">📅 Attendance Records</span>
                    <p class="text-xs text-blue-200">View all records</p>
                </a>
                <div class="grid grid-cols-2 gap-2">
                    @if($canCheckIn)
                        <form method="POST" action="{{ route('attendance.checkin') }}" id="checkinForm" class="block">
                            @csrf
                            <input type="hidden" name="latitude" id="checkin_lat" value="">
                            <input type="hidden" name="longitude" id="checkin_lng" value="">
                            <input type="hidden" name="location_name" id="checkin_location" value="Manual Check-in">
                            <button type="submit" onclick="return getLocation('checkin')" class="w-full p-2 bg-green-700 rounded hover:bg-green-600 transition-colors text-sm">
                                ⏰ Check In
                            </button>
                        </form>
                    @else
                        <button disabled class="w-full p-2 bg-gray-600 rounded text-sm opacity-50 cursor-not-allowed">
                            ⏰ Check In
                        </button>
                    @endif
                    
                    @if($canCheckOut)
                        <form method="POST" action="{{ route('attendance.checkout') }}" id="checkoutForm" class="block">
                            @csrf
                            <input type="hidden" name="latitude" id="checkout_lat" value="">
                            <input type="hidden" name="longitude" id="checkout_lng" value="">
                            <input type="hidden" name="location_name" id="checkout_location" value="Manual Check-out">
                            <button type="submit" onclick="return getLocation('checkout')" class="w-full p-2 bg-red-700 rounded hover:bg-red-600 transition-colors text-sm">
                                ⏱️ Check Out
                            </button>
                        </form>
                    @else
                        <button disabled class="w-full p-2 bg-gray-600 rounded text-sm opacity-50 cursor-not-allowed">
                            ⏱️ Check Out
                        </button>
                    @endif
                </div>
                
                @if($todayAttendance)
                    <div class="mt-2 p-2 bg-blue-900/30 rounded text-sm">
                        @if($todayAttendance->check_in && !$todayAttendance->check_out)
                            <p class="text-blue-300">
                                ✅ Checked in at 
                                {{ \Carbon\Carbon::parse($todayAttendance->check_in)->format('h:i A') }}
                                ({{ ucfirst($todayAttendance->period ?? '') }} Period)
                            </p>
                            <p class="text-xs text-blue-200">You can now check out</p>

                        @elseif($todayAttendance->check_in && $todayAttendance->check_out)
                            <p class="text-green-300">
                                ✅ Completed ({{ ucfirst($todayAttendance->period ?? '') }} Period):
                                In at {{ \Carbon\Carbon::parse($todayAttendance->check_in)->format('h:i A') }},
                                Out at {{ \Carbon\Carbon::parse($todayAttendance->check_out)->format('h:i A') }}
                            </p>
                        @endif
                    </div>
                @else
                    <div class="mt-2 p-2 bg-yellow-900/30 rounded text-sm">
                        <p class="text-xs text-yellow-200">You can check in or check out once a day </p>
                    </div>
                @endif
            </div>
            @else
            <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                <a href="{{ route('login') }}" class="block p-3 bg-gray-800 rounded hover:bg-blue-900/30 transition-colors">
                    <span class="font-medium">🕐 Today's Attendance</span>
                    <p class="text-xs text-blue-200">View current day</p>
                </a>
                <a href="{{ route('login') }}" class="block p-3 bg-gray-800 rounded hover:bg-blue-900/30 transition-colors">
                    <span class="font-medium">📅 Attendance Records</span>
                    <p class="text-xs text-blue-200">View all records</p>
                </a>
                <div class="grid grid-cols-2 gap-2">
                    <a href="{{ route('login') }}" class="block p-2 bg-green-700 rounded hover:bg-green-600 transition-colors text-sm text-center">
                        ⏰ Check In
                    </a>
                    <a href="{{ route('login') }}" class="block p-2 bg-red-700 rounded hover:bg-red-600 transition-colors text-sm text-center">
                        ⏱️ Check Out
                    </a>
                </div>
            </div>
            @endauth
        </div>

        <!-- User Information -->
        <div class="mt-8 bg-black/60 p-6 rounded-lg border border-blue-900/30">
            <h2 class="text-xl font-semibold mb-4 text-blue-400">👤 Your Information</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                @auth
                <div>
                    <h3 class="font-semibold text-blue-300">Personal Details</h3>
                    <p><strong>Name:</strong> {{ Auth::user()->name ?? 'Admin Assistant' }}</p>
                    <p><strong>Email:</strong> {{ Auth::user()->email ?? 'admin@example.com' }}</p>
                    <p><strong>Position:</strong> {{ Auth::user()->position ?? 'Administrative Assistant' }}</p>
                    <p><strong>Salary:</strong> ₱{{ number_format(Auth::user()->salary ?? 25000, 2) }}</p>
                </div>
                @else
                <div>
                    <h3 class="font-semibold text-blue-300">Personal Details</h3>
                    <p><strong>Name:</strong> Demo Assistant</p>
                    <p><strong>Email:</strong> admin@example.com</p>
                    <p><strong>Position:</strong> Administrative Assistant</p>
                    <p><strong>Salary:</strong> ₱25,000.00</p>
                </div>
                @endauth
                <div>
                    <h3 class="font-semibold text-blue-300 mb-2">Account Details</h3>
                    <p><strong>Role:</strong> <span class="text-blue-400">{{ ucfirst(Auth::user()->role) }}</span></p>
                    <p><strong>Account Type:</strong> Admin Assistant</p>
                    <p><strong>Member Since:</strong> {{ Auth::user()->created_at->format('M d, Y') }}</p>
                    <p><a href="{{ route('dashboard.profile') }}" class="text-blue-400 hover:underline"><i class="fas fa-edit mr-1"></i>Change Password</a></p>
                </div>

            </div>
        </div>

        @auth
        @else
        <!-- Login CTA -->
        <div class="mt-8 text-center">
            <a href="{{ route('login') }}" class="inline-block px-6 py-3 bg-blue-600 hover:bg-blue-500 text-white font-semibold rounded-lg transition-colors">
                🔐 Login to Admin Dashboard
            </a>
        </div>
        @endauth
    </div>

    <script>
        // Real-time employee statistics update
        function updateEmployeeStats() {
            fetch('/api/admin/employee-stats')
                .then(response => response.json())
                .then(data => {
                    // Update Total Employees
                    const totalEmployeesEl = document.getElementById('totalEmployees');
                    if (totalEmployeesEl) {
                        totalEmployeesEl.textContent = data.totalEmployees;
                    }
                    
                    // Update Active Employees
                    const activeEmployeesEl = document.getElementById('activeEmployees');
                    if (activeEmployeesEl) {
                        activeEmployeesEl.textContent = data.activeEmployees;
                    }
                })
                .catch(error => {
                    console.error('Error fetching employee stats:', error);
                });
        }

        // Auto-refresh employee stats every 60 seconds
        document.addEventListener('DOMContentLoaded', function() {
            // Initial update after 5 seconds
            setTimeout(updateEmployeeStats, 5000);
            
            // Then update every 60 seconds
            setInterval(updateEmployeeStats, 60000);
        });
    </script>
</body>
</html>

