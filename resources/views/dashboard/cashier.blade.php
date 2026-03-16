<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Cashier Dashboard - {{ config('app.name') }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
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
    <div class="max-w-6xl mx-auto p-4 md:p-6">
        <!-- Header -->
        <header class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6 md:mb-8">
            <div>
                <h1 class="text-2xl md:text-3xl font-bold text-green-400">💰 Cashier Dashboard</h1>
                <p class="text-green-200 text-sm mt-1">Cashier Access</p>
            </div>
            <div class="flex flex-wrap items-center gap-2 md:gap-4">
                <span class="text-sm text-green-200">{{ Auth::user()->name }} ({{ ucfirst(Auth::user()->role) }})</span>
                <a href="{{ route('dashboard.profile') }}" class="px-3 py-2 text-green-200 hover:bg-green-900/30 rounded text-sm">
                    <i class="fas fa-user-circle mr-1"></i>Profile
                </a>
                <form method="POST" action="{{ route('logout') }}" class="inline">
                    @csrf
                    <button type="submit" class="px-3 py-2 text-green-200 hover:bg-green-900/30 rounded text-sm">Logout</button>
                </form>
            </div>
        </header>

        @if (session('success'))
            <div class="mb-4 md:mb-6 bg-green-900/50 border border-green-700 rounded-md p-4">
                <p class="text-green-200">{{ session('success') }}</p>
            </div>
        @endif

        @if (session('error'))
            <div class="mb-4 md:mb-6 bg-red-900/50 border border-red-700 rounded-md p-4">
                <p class="text-red-200">{{ session('error') }}</p>
            </div>
        @endif

        <!-- Quick Stats -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-3 md:gap-4 mb-6 md:mb-8">
            <div class="bg-black/60 p-3 md:p-4 rounded-lg border border-green-900/30">
                <h3 class="text-green-400 font-semibold text-sm">Your Role</h3>
                <p class="text-xl md:text-2xl font-bold">{{ ucfirst(Auth::user()->role) }}</p>
            </div>
            <div class="bg-black/60 p-3 md:p-4 rounded-lg border border-green-900/30">
                <h3 class="text-green-400 font-semibold text-sm">Position</h3>
                <p class="text-xl md:text-2xl font-bold">{{ Auth::user()->position ?? 'Not Set' }}</p>
            </div>
            <div class="bg-black/60 p-3 md:p-4 rounded-lg border border-green-900/30">
                <h3 class="text-green-400 font-semibold text-sm">Monthly Salary</h3>
                <p class="text-xl md:text-2xl font-bold">₱{{ number_format(Auth::user()->salary ?? 0, 0) }}</p>
            </div>
            <div class="bg-black/60 p-3 md:p-4 rounded-lg border border-green-900/30">
                <h3 class="text-green-400 font-semibold text-sm">Status</h3>
                <p class="text-xl md:text-2xl font-bold text-green-400">Active</p>
            </div>
        </div>

        <!-- Sales Management Section -->
        <div class="bg-black/60 p-4 md:p-6 rounded-lg border border-green-900/30 mb-6 md:mb-8">
            <h2 class="text-xl font-semibold mb-3 md:mb-4 text-green-400">💰 Sales Management</h2>
            <p class="text-sm text-green-200 mb-4">Process sales and manage transactions</p>
            
            <!-- Sales Quick Actions -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-3 mb-4">
                <a href="{{ route('cashier.sales.create') }}" class="block p-4 bg-green-700/50 rounded-lg hover:bg-green-600/50 transition-colors text-center border border-green-500/30">
                    <span class="font-medium text-lg">💳</span>
                    <p class="text-sm font-semibold mt-1">Process Sale</p>
                    <p class="text-xs text-green-200">Create new transaction</p>
                </a>
                <a href="{{ route('cashier.sales.history') }}" class="block p-4 bg-gray-800 rounded-lg hover:bg-green-900/30 transition-colors text-center border border-green-900/30">
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
            
            <!-- Products & Inventory Stats -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-3 mt-4">
                <div class="bg-gray-900/50 p-3 rounded-lg border border-green-900/20">
                    <p class="text-xs text-green-300">Products Available</p>
                    <p class="text-lg font-bold text-green-400">{{ $totalProducts ?? 0 }}</p>
                </div>
                <div class="bg-gray-900/50 p-3 rounded-lg border border-green-900/20">
                    <p class="text-xs text-green-300">Low Stock Items</p>
                    <p class="text-lg font-bold text-yellow-400">{{ $lowStockCount ?? 0 }}</p>
                </div>
                <div class="bg-gray-900/50 p-3 rounded-lg border border-green-900/20">
                    <p class="text-xs text-green-300">Average Sale</p>
                    <p class="text-lg font-bold text-green-400">₱{{ number_format(($todayTransactions ?? 1) > 0 ? ($todaySales ?? 0) / ($todayTransactions ?? 1) : 0, 2) }}</p>
                </div>
                <div class="bg-gray-900/50 p-3 rounded-lg border border-green-900/20">
                    <p class="text-xs text-green-300">Sales Performance</p>
                    <p class="text-lg font-bold text-green-400">{{ ($monthlyTransactions ?? 0) > 0 ? 'Good' : 'No Sales' }}</p>
                </div>
            </div>
        </div>

        <!-- Attendance Section -->
        <div class="mb-6 md:mb-8">
            <div class="bg-black/60 p-4 md:p-6 rounded-lg border border-green-900/30">
                <h2 class="text-xl font-semibold mb-3 md:mb-4 text-green-400">⏰ Attendance</h2>
                <p class="text-sm text-green-200 mb-3 md:mb-4">Track your daily attendance</p>
                
                <!-- Check In/Out Buttons -->
                <div class="grid grid-cols-2 gap-2 mb-3">
                    @if($canCheckIn)
                        <form method="POST" action="{{ route('attendance.checkin') }}" id="checkinForm">
                            @csrf
                            <input type="hidden" name="latitude" id="checkin_lat" value="">
                            <input type="hidden" name="longitude" id="checkin_lng" value="">
                            <input type="hidden" name="location_name" id="checkin_location" value="Manual Check-in">
                            <button type="submit" onclick="return getLocation('checkin')" class="w-full p-2 bg-green-700 hover:bg-green-600 rounded transition-colors text-sm font-medium">
                                ⏰ Check In
                            </button>
                        </form>
                    @else
                        <button disabled class="w-full p-2 bg-gray-600 rounded text-sm font-medium opacity-50 cursor-not-allowed">
                            ⏰ Check In
                        </button>
                    @endif
                    
                    @if($canCheckOut)
                        <form method="POST" action="{{ route('attendance.checkout') }}" id="checkoutForm">
                            @csrf
                            <input type="hidden" name="latitude" id="checkout_lat" value="">
                            <input type="hidden" name="longitude" id="checkout_lng" value="">
                            <input type="hidden" name="location_name" id="checkout_location" value="Manual Check-out">
                            <button type="submit" onclick="return getLocation('checkout')" class="w-full p-2 bg-red-700 hover:bg-red-600 rounded transition-colors text-sm font-medium">
                                ⏱️ Check Out
                            </button>
                        </form>
                    @elseif(!$canCheckOut)
                        <button class="w-full p-2 bg-gray-600 rounded text-sm font-medium opacity-50 cursor-not-allowed">
                            ⏱️ Check Out
                        </button>
                    @endif
                </div>
                
                @if($todayAttendance)
                    <div class="mb-3 p-2 bg-green-900/30 rounded text-sm">
                        @if($todayAttendance->check_in && !$todayAttendance->check_out)
                            <p class="text-green-300">✅ Checked in at {{ \Carbon\Carbon::parse($todayAttendance->check_in)->format('h:i A') }} ({{ ucfirst($todayAttendance->period ?? '') }} Period)</p>
                            <p class="text-xs text-green-200">You can now check out</p>
                        @elseif($todayAttendance->check_in && $todayAttendance->check_out)
                            <p class="text-blue-300">✅ Completed ({{ ucfirst($todayAttendance->period ?? '') }} Period): In at {{ \Carbon\Carbon::parse($todayAttendance->check_in)->format('h:i A') }}, Out at {{ \Carbon\Carbon::parse($todayAttendance->check_out)->format('h:i A') }}</p>
                        @endif
                    </div>
                @else
                    <div class="mb-3 p-2 bg-yellow-900/30 rounded text-sm">
                        <p class="text-yellow-300">⚠️ No attendance record for current period</p>
                        <p class="text-xs text-yellow-200">anytime check in and check out anytime</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Work Summary Stats -->
        <div class="grid grid-cols-3 gap-3 md:gap-4 mb-6 md:mb-8">
            <div class="bg-black/60 p-3 md:p-4 rounded-lg border border-green-900/30 text-center">
                <p class="text-2xl md:text-3xl font-bold text-green-400">{{ $totalDays }}</p>
                <p class="text-xs md:text-sm text-green-200">Days This Month</p>
            </div>
            <div class="bg-black/60 p-3 md:p-4 rounded-lg border border-green-900/30 text-center">
                <p class="text-2xl md:text-3xl font-bold text-green-400">{{ $totalHoursWorked }}</p>
                <p class="text-xs md:text-sm text-green-200">Hours Worked</p>
            </div>
            <div class="bg-black/60 p-3 md:p-4 rounded-lg border border-green-900/30 text-center">
                <p class="text-2xl md:text-3xl font-bold text-green-400">{{ $attendanceRate }}%</p>
                <p class="text-xs md:text-sm text-green-200">Attendance Rate</p>
            </div>
        </div>

        <!-- Recent Attendance -->
        <div class="bg-black/60 p-4 md:p-6 rounded-lg border border-green-900/30 mb-6 md:mb-8">
            <h2 class="text-xl font-semibold mb-3 md:mb-4 text-green-400">📋 Recent Attendance</h2>
            
            @if($recentAttendances && $recentAttendances->count() > 0)
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="border-b border-green-900/30">
                                <th class="text-left py-2 px-2 md:px-4 text-green-300">Date</th>
                                <th class="text-left py-2 px-2 md:px-4 text-green-300">Check In</th>
                                <th class="text-left py-2 px-2 md:px-4 text-green-300">Check Out</th>
                                <th class="text-left py-2 px-2 md:px-4 text-green-300 hidden sm:table-cell">Duration</th>
                                <th class="text-left py-2 px-2 md:px-4 text-green-300">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($recentAttendances as $record)
                                <tr class="border-b border-green-900/20 hover:bg-green-900/10">
                                    <td class="py-2 px-2 md:px-4">{{ \Carbon\Carbon::parse($record->date)->format('M d, Y') }}</td>
                                    <td class="py-2 px-2 md:px-4">{{ $record->check_in ? \Carbon\Carbon::parse($record->check_in)->format('H:i') : 'N/A' }}</td>
                                    <td class="py-2 px-2 md:px-4">
                                        @if($record->check_out)
                                            {{ \Carbon\Carbon::parse($record->check_out)->format('H:i') }}
                                        @else
                                            <span class="text-yellow-400 text-xs">Still in</span>
                                        @endif
                                    </td>
                                    <td class="py-2 px-2 md:px-4 hidden sm:table-cell">
                                        @if($record->check_in && $record->check_out)
                                            {{ \Carbon\Carbon::parse($record->check_in)->diffForHumans(\Carbon\Carbon::parse($record->check_out), true) }}
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td class="py-2 px-2 md:px-4">
                                        @if($record->check_in && $record->check_out)
                                            <span class="px-2 py-1 bg-green-500/20 text-green-300 rounded-full text-xs">Completed</span>
                                        @elseif($record->check_in)
                                            <span class="px-2 py-1 bg-yellow-500/20 text-yellow-300 rounded-full text-xs">Checked In</span>
                                        @else
                                            <span class="px-2 py-1 bg-red-500/20 text-red-300 rounded-full text-xs">Absent</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-6 text-green-300">
                    <i class="fas fa-calendar-xmark text-3xl md:text-4xl mb-2"></i>
                    <p class="text-sm">No attendance records found.</p>
                    <p class="text-xs mt-1">Start by checking in for your shift!</p>
                </div>
            @endif
        </div>

        <!-- User Information -->
        <div class="bg-black/60 p-4 md:p-6 rounded-lg border border-green-900/30">
            <h2 class="text-xl font-semibold mb-3 md:mb-4 text-green-400">👤 Your Information</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                <div>
                    <h3 class="font-semibold text-green-300 mb-2">Personal Details</h3>
                    <p><strong>Name:</strong> {{ Auth::user()->name }}</p>
                    <p><strong>Email:</strong> {{ Auth::user()->email }}</p>
                    <p><strong>Position:</strong> {{ Auth::user()->position ?? 'Not set' }}</p>
                    <p><strong>Salary:</strong> ₱{{ number_format(Auth::user()->salary ?? 0, 2) }}</p>
                </div>
                <div>
                    <h3 class="font-semibold text-green-300 mb-2">Account Details</h3>
                    <p><strong>Role:</strong> <span class="text-green-400">{{ ucfirst(Auth::user()->role) }}</span></p>
                    <p><strong>Account Type:</strong> Cashier</p>
                    <p><strong>Member Since:</strong> {{ Auth::user()->created_at->format('M d, Y') }}</p>
                    <p><a href="{{ route('dashboard.profile') }}" class="text-green-400 hover:underline"><i class="fas fa-edit mr-1"></i>Change Password</a></p>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Prevent double-clicking on attendance buttons
        let isSubmitting = false;
        
        document.addEventListener('DOMContentLoaded', function() {
            const checkinForm = document.getElementById('checkinForm');
            const checkoutForm = document.getElementById('checkoutForm');
            
            if (checkinForm) {
                checkinForm.addEventListener('submit', function(e) {
                    if (isSubmitting) {
                        e.preventDefault();
                        return false;
                    }
                    isSubmitting = true;
                    
                    // Disable submit button
                    const submitBtn = this.querySelector('button[type="submit"]');
                    if (submitBtn) {
                        submitBtn.disabled = true;
                        submitBtn.textContent = 'Processing...';
                    }
                    
                    // Re-enable after 5 seconds (fallback)
                    setTimeout(() => {
                        isSubmitting = false;
                        if (submitBtn) {
                            submitBtn.disabled = false;
                            submitBtn.textContent = '⏰ Check In';
                        }
                    }, 5000);
                });
            }
            
            if (checkoutForm) {
                checkoutForm.addEventListener('submit', function(e) {
                    if (isSubmitting) {
                        e.preventDefault();
                        return false;
                    }
                    isSubmitting = true;
                    
                    // Disable submit button
                    const submitBtn = this.querySelector('button[type="submit"]');
                    if (submitBtn) {
                        submitBtn.disabled = true;
                        submitBtn.textContent = 'Processing...';
                    }
                    
                    // Re-enable after 5 seconds (fallback)
                    setTimeout(() => {
                        isSubmitting = false;
                        if (submitBtn) {
                            submitBtn.disabled = false;
                            submitBtn.textContent = '⏱️ Check Out';
                        }
                    }, 5000);
                });
            }
        });
    </script>
</body>
</html>

