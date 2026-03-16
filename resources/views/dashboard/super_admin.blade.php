<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>💰 Sales Management</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gradient-to-br from-black via-gray-900 to-red-900 min-h-screen text-white">
    <div class="max-w-7xl mx-auto p-6">
        <!-- Header -->
        <header class="flex items-center justify-between mb-8">
            <div>
                <h1 class="text-3xl font-bold text-red-400">🔐 Super Admin Dashboard</h1>
                <p class="text-red-200 text-sm mt-1">Full System Access Control</p>
            </div>
            <div class="flex items-center gap-4">
                <span class="text-sm text-red-200">{{ Auth::user()->name ?? 'Super Admin' }} (Super Admin)</span>
                    <a href="{{ route('dashboard.profile') }}" class="px-3 py-2 text-red-200 hover:bg-red-900/30 rounded text-sm">
                        <i class="fas fa-user-circle mr-1"></i>Profile
                    </a>
                @if(Auth::check())
                    <form method="POST" action="{{ route('logout') }}" class="inline">
                        @csrf
                        <button type="submit" class="px-3 py-2 text-red-200 hover:bg-red-900/30 rounded">Logout</button>
                    </form>
                @else
                    <a href="{{ url('/login') }}" class="px-3 py-2 text-red-200 hover:bg-red-900/30 rounded">Login</a>
                @endif
            </div>
        </header>


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
                        <span class="font-medium">📱 E-Load Products</span>
                        <p class="text-xs text-red-200">Manage load products</p>
                    </a>
                    <a href="{{ route('eload.numbers.index') }}" class="block p-3 bg-gray-800 rounded hover:bg-red-900/30 transition-colors">
                        <span class="font-medium">📡 E-Load Numbers</span>
                        <p class="text-xs text-red-200">View gateway numbers (View Only)</p>
                    </a>
                    <a href="{{ route('eload.add-load') }}" class="block p-3 bg-gray-800 rounded hover:bg-red-900/30 transition-colors">
                        <span class="font-medium">➕ Add Load</span>
                        <p class="text-xs text-red-200">Process new load transaction</p>
                    </a>
                    <a href="{{ route('eload.add-load-multiple') }}" class="block p-3 bg-gray-800 rounded hover:bg-red-900/30 transition-colors">
                        <span class="font-medium">📋 Add Multiple Loads</span>
                        <p class="text-xs text-red-200">Batch load processing</p>
                    </a>
                    <a href="{{ route('eload.transactions.history') }}" class="block p-3 bg-gray-800 rounded hover:bg-red-900/30 transition-colors">
                        <span class="font-medium">📜 Transaction History</span>
                        <p class="text-xs text-red-200">View all load transactions</p>
                    </a>
                </div>
            </div>
        </div>

        <!-- Attendance Management -->
        <div class="bg-black/60 p-6 rounded-lg border border-red-900/30">
            <h2 class="text-xl font-semibold mb-4 text-red-400">⏰ Attendance Management</h2>
            <p class="text-sm text-red-200 mb-4">Track employee and admin assistant attendance</p>
                <div class="grid grid-cols-1 gap-3">
                    <a href="{{ route('attendance.index') }}" class="block p-3 bg-gray-800 rounded hover:bg-red-900/30 transition-colors">
                        <span class="font-medium">🕐 Today's Attendance</span>
                        <p class="text-xs text-red-200">View current day attendance</p>
                    </a>
                    <a href="{{ route('attendance.records') }}" class="block p-3 bg-gray-800 rounded hover:bg-red-900/30 transition-colors">
                        <span class="font-medium">📅 Attendance Records</span>
                        <p class="text-xs text-red-200">View all attendance history</p>
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
                            <button type="submit" class="w-full p-2 bg-red-700 rounded hover:bg-red-600 transition-colors text-sm">
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
                    <div class="mt-2 p-2 bg-red-900/30 rounded text-sm">
                        @if($todayAttendance->check_in && !$todayAttendance->check_out)
                            <p class="text-red-300">✅ Checked in at {{ \Carbon\Carbon::parse($todayAttendance->check_in)->format('h:i A') }}</p>
                            <p class="text-xs text-red-200">You can now check out</p>
                        @elseif($todayAttendance->check_in && $todayAttendance->check_out)
                            <p class="text-green-300">✅ Completed: In at {{ \Carbon\Carbon::parse($todayAttendance->check_in)->format('h:i A') }}, Out at {{ \Carbon\Carbon::parse($todayAttendance->check_out)->format('h:i A') }}</p>
                        @endif
                    </div>
                @endif
                </div>
            </div>
        </div>

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
        <div class="bg-black/60 p-4 md:p-6 rounded-lg border border-red-900/30">
            <h2 class="text-xl font-semibold mb-3 md:mb-4 text-red-400">👤 Your Information</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                <div>
                    <h3 class="font-semibold text-red-300 mb-2">Personal Details</h3>
                    <p><strong>Name:</strong> {{ Auth::user()->name }}</p>
                    <p><strong>Email:</strong> {{ Auth::user()->email }}</p>
                    <p><strong>Position:</strong> {{ Auth::user()->position ?? 'Not set' }}</p>
                    <p><strong>Salary:</strong> ₱{{ number_format(Auth::user()->salary ?? 0, 2) }}</p>
                </div>
                <div>
                    <h3 class="font-semibold text-red-300 mb-2">Account Details</h3>
                    <p><strong>Role:</strong> <span class="text-red-400">{{ ucfirst(Auth::user()->role) }}</span></p>
                    <p><strong>Account Type:</strong> Super Admin</p>
                    <p><strong>Member Since:</strong> {{ Auth::user()->created_at->format('M d, Y') }}</p>
                    <p><a href="{{ route('dashboard.profile') }}" class="text-red-400 hover:underline"><i class="fas fa-edit mr-1"></i>Change Password</a></p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Super Admin dashboard - simplified without attendance refresh
document.addEventListener('DOMContentLoaded', function() {
    console.log('Super Admin dashboard loaded');
});
</script>
</body>
</html>
