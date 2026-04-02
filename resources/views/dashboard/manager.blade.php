<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" href="/favicon.svg" type="image/svg+xml">
    <title>Manager Dashboard - {{ config('app.name') }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gradient-to-br from-black via-gray-900 to-purple-900 min-h-screen text-white">
    <div class="max-w-6xl mx-auto p-4 md:p-6">
        <!-- Header -->
        <header class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6 md:mb-8">
            <div>
                <h1 class="text-2xl md:text-3xl font-bold text-purple-400">👔 Manager Dashboard</h1>
                <p class="text-purple-200 text-sm mt-1">Manager Access - View Reports & Manage Team</p>
            </div>
            <div class="flex flex-wrap items-center gap-2 md:gap-4">
                <span class="text-sm text-purple-200">{{ Auth::user()->name }} ({{ ucfirst(Auth::user()->role) }})</span>
                <a href="{{ route('dashboard.profile') }}" class="px-3 py-2 text-purple-200 hover:bg-purple-900/30 rounded text-sm">
                    <i class="fas fa-user-circle mr-1"></i>Profile
                </a>
                <form method="POST" action="{{ route('logout') }}" class="inline">
                    @csrf
                    <button type="submit" class="px-3 py-2 text-purple-200 hover:bg-purple-900/30 rounded text-sm">Logout</button>
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
            <div class="bg-black/60 p-3 md:p-4 rounded-lg border border-purple-900/30">
                <h3 class="text-purple-400 font-semibold text-sm">Your Role</h3>
                <p class="text-xl md:text-2xl font-bold">{{ ucfirst(Auth::user()->role) }}</p>
                <p class="text-xs text-purple-200">Manager</p>
            </div>
            <div class="bg-black/60 p-3 md:p-4 rounded-lg border border-purple-900/30">
                <h3 class="text-purple-400 font-semibold text-sm">Position</h3>
                <p class="text-xl md:text-2xl font-bold">{{ Auth::user()->position ?? 'Not Set' }}</p>
            </div>
            <div class="bg-black/60 p-3 md:p-4 rounded-lg border border-purple-900/30">
                <h3 class="text-purple-400 font-semibold text-sm">Monthly Salary</h3>
                <p class="text-xl md:text-2xl font-bold">₱{{ number_format(Auth::user()->salary ?? 0, 0) }}</p>
            </div>
            <div class="bg-black/60 p-3 md:p-4 rounded-lg border border-green-900/30">
                <h3 class="text-green-400 font-semibold text-sm">Status</h3>
                <p class="text-xl md:text-2xl font-bold text-green-400">Active</p>
            </div>

        <!-- Management Actions Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 md:gap-6 mb-6 md:mb-8">
            <!-- View Products -->
            <div class="bg-black/60 p-4 md:p-6 rounded-lg border border-purple-900/30">
                <h2 class="text-xl font-semibold mb-3 text-purple-400">📦 Products</h2>
                <p class="text-sm text-purple-200 mb-4">View and manage product inventory</p>
                <a href="{{ route('products.index') }}" class="block w-full p-2 bg-purple-700 hover:bg-purple-600 rounded transition-colors text-sm font-medium text-center">
                    View Products
                </a>
            </div>

            <!-- View Inventory -->
            <div class="bg-black/60 p-4 md:p-6 rounded-lg border border-purple-900/30">
                <h2 class="text-xl font-semibold mb-3 text-purple-400">📊 Inventory</h2>
                <p class="text-sm text-purple-200 mb-4">Check stock levels and inventory status</p>
                <a href="{{ route('inventory.index') }}" class="block w-full p-2 bg-purple-700 hover:bg-purple-600 rounded transition-colors text-sm font-medium text-center">
                    View Inventory
                </a>
            </div>

            <!-- View Sales Reports -->
            <div class="bg-black/60 p-4 md:p-6 rounded-lg border border-purple-900/30">
                <h2 class="text-xl font-semibold mb-3 text-purple-400">💰 Sales Reports</h2>
                <p class="text-sm text-purple-200 mb-4">View sales history and reports</p>
                <a href="{{ route('employee.sales.history') }}" class="block w-full p-2 bg-purple-700 hover:bg-purple-600 rounded transition-colors text-sm font-medium text-center">
                    View Sales
                </a>
            </div>

            <!-- View Employees -->
            <div class="bg-black/60 p-4 md:p-6 rounded-lg border border-purple-900/30">
                <h2 class="text-xl font-semibold mb-3 text-purple-400">👥 Employees</h2>
                <p class="text-sm text-purple-200 mb-4">View employee list and details</p>
                <a href="{{ route('employee.list') }}" class="block w-full p-2 bg-purple-700 hover:bg-purple-600 rounded transition-colors text-sm font-medium text-center">
                    View Employees
                </a>
            </div>

            <!-- Attendance Records -->
            <div class="bg-black/60 p-4 md:p-6 rounded-lg border border-purple-900/30">
                <h2 class="text-xl font-semibold mb-3 text-purple-400">⏰ Attendance</h2>
                <p class="text-sm text-purple-200 mb-4">View attendance records</p>
                <a href="{{ route('attendance.records') }}" class="block w-full p-2 bg-purple-700 hover:bg-purple-600 rounded transition-colors text-sm font-medium text-center">
                    View Attendance
                </a>
            </div>

            <!-- Your Profile -->
            <div class="bg-black/60 p-4 md:p-6 rounded-lg border border-purple-900/30">
                <h2 class="text-xl font-semibold mb-3 text-purple-400">👤 Profile</h2>
                <p class="text-sm text-purple-200 mb-4">Update your profile and password</p>
                <a href="{{ route('dashboard.profile') }}" class="block w-full p-2 bg-purple-700 hover:bg-purple-600 rounded transition-colors text-sm font-medium text-center">
                    View Profile
                </a>
            </div>

        <!-- Your Attendance Section -->
        <div class="bg-black/60 p-4 md:p-6 rounded-lg border border-purple-900/30 mb-6 md:mb-8">
            <h2 class="text-xl font-semibold mb-3 md:mb-4 text-purple-400">⏰ Your Attendance</h2>
            
            <!-- Check In/Out Buttons -->
            <div class="grid grid-cols-2 gap-2 mb-4">
                @if($canCheckIn)
                    <form method="POST" action="{{ route('attendance.checkin') }}">
                        @csrf
                        <input type="hidden" name="latitude" value="">
                        <input type="hidden" name="longitude" value="">
                        <input type="hidden" name="location_name" value="Manual Check-in">
                        <button type="submit" class="w-full p-2 bg-green-700 hover:bg-green-600 rounded transition-colors text-sm font-medium">
                            ⏰ Check In
                        </button>
                    </form>
                @else
                    <button disabled class="w-full p-2 bg-gray-600 rounded text-sm font-medium opacity-50 cursor-not-allowed">
                        ⏰ Check In
                    </button>
                @endif
                
                @if($canCheckOut)
                    <form method="POST" action="{{ route('attendance.checkout') }}">
                        @csrf
                        <input type="hidden" name="latitude" value="">
                        <input type="hidden" name="longitude" value="">
                        <input type="hidden" name="location_name" value="Manual Check-out">
                        <button type="submit" class="w-full p-2 bg-red-700 hover:bg-red-600 rounded transition-colors text-sm font-medium">
                            ⏱️ Check Out
                        </button>
                    </form>
                @else
                    <button disabled class="w-full p-2 bg-gray-600 rounded text-sm font-medium opacity-50 cursor-not-allowed">
                        ⏱️ Check Out
                    </button>
                @endif
            </div>
            
            @if($todayAttendance)
                <div class="mb-4 p-2 bg-purple-900/30 rounded text-sm">
                    @if($todayAttendance->check_in && !$todayAttendance->check_out)
                        <p class="text-purple-300">✅ Checked in at {{ \Carbon\Carbon::parse($todayAttendance->check_in)->format('h:i A') }}</p>
                    @elseif($todayAttendance->check_in && $todayAttendance->check_out)
                        <p class="text-green-300">✅ Completed: In at {{ \Carbon\Carbon::parse($todayAttendance->check_in)->format('h:i A') }}, Out at {{ \Carbon\Carbon::parse($todayAttendance->check_out)->format('h:i A') }}</p>
                    @endif
                </div>
            @endif

            <!-- Work Summary Stats -->
            <div class="grid grid-cols-3 gap-3 md:gap-4">
                <div class="bg-black/40 p-3 rounded-lg border border-purple-900/20 text-center">
                    <p class="text-2xl md:text-3xl font-bold text-purple-400">{{ $totalDays }}</p>
                    <p class="text-xs md:text-sm text-purple-200">Days This Month</p>
                </div>
                <div class="bg-black/40 p-3 rounded-lg border border-purple-900/20 text-center">
                    @php
    $hours = floor($totalHoursWorked);
    $minutes = round(($totalHoursWorked - $hours) * 60);
    if ($minutes == 60) { $hours += 1; $minutes = 0; }
@endphp
<p class="text-2xl md:text-3xl font-bold text-purple-400">{{ $hours }}h {{ $minutes }}m</p>
                    <p class="text-xs md:text-sm text-purple-200">Hours Worked</p>
                </div>
                <div class="bg-black/40 p-3 rounded-lg border border-purple-900/20 text-center">
                    <p class="text-2xl md:text-3xl font-bold text-purple-400">{{ $attendanceRate }}%</p>
                    <p class="text-xs md:text-sm text-purple-200">Attendance Rate</p>
                </div>
        </div>

        <!-- Recent Attendance -->
        <div class="bg-black/60 p-4 md:p-6 rounded-lg border border-purple-900/30 mb-6 md:mb-8">
            <h2 class="text-xl font-semibold mb-3 md:mb-4 text-purple-400">📋 Recent Attendance</h2>
            
            @if($recentAttendances && $recentAttendances->count() > 0)
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="border-b border-purple-900/30">
                                <th class="text-left py-2 px-2 md:px-4 text-purple-300">Date</th>
                                <th class="text-left py-2 px-2 md:px-4 text-purple-300">Check In</th>
                                <th class="text-left py-2 px-2 md:px-4 text-purple-300">Check Out</th>
                                <th class="text-left py-2 px-2 md:px-4 text-purple-300 hidden sm:table-cell">Duration</th>
                                <th class="text-left py-2 px-2 md:px-4 text-purple-300">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($recentAttendances as $record)
                                <tr class="border-b border-purple-900/20 hover:bg-purple-900/10">
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
                <div class="text-center py-6 text-purple-300">
                    <i class="fas fa-calendar-xmark text-3xl md:text-4xl mb-2"></i>
                    <p class="text-sm">No attendance records found.</p>
                    <p class="text-xs mt-1">Start by checking in for your shift!</p>
                </div>
            @endif
        </div>

        <!-- User Information -->
        <div class="bg-black/60 p-4 md:p-6 rounded-lg border border-purple-900/30">
            <h2 class="text-xl font-semibold mb-3 md:mb-4 text-purple-400">👤 Your Information</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                <div>
                    <h3 class="font-semibold text-purple-300 mb-2">Personal Details</h3>
                    <p><strong>Name:</strong> {{ Auth::user()->name }}</p>
                    <p><strong>Email:</strong> {{ Auth::user()->email }}</p>
                    <p><strong>Position:</strong> {{ Auth::user()->position ?? 'Not set' }}</p>
                    <p><strong>Salary:</strong> ₱{{ number_format(Auth::user()->salary ?? 0, 2) }}</p>
                </div>
                <div>
                    <h3 class="font-semibold text-purple-300 mb-2">Account Details</h3>
                    <p><strong>Role:</strong> <span class="text-purple-400">{{ ucfirst(Auth::user()->role) }}</span></p>
                    <p><strong>Account Type:</strong> Manager</p>
                    <p><strong>Member Since:</strong> {{ Auth::user()->created_at->format('M d, Y') }}</p>
                    <p><a href="{{ route('dashboard.profile') }}" class="text-purple-400 hover:underline"><i class="fas fa-edit mr-1"></i>Change Password</a></p>
                </div>
        </div>

    <script>
        function getLocation(type) {
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(
                    function(position) {
                        document.querySelector('input[name="latitude"]').value = position.coords.latitude;
                        document.querySelector('input[name="longitude"]').value = position.coords.longitude;
                        document.querySelector('input[name="location_name"]').value = type === 'checkin' ? 'GPS Check-in' : 'GPS Check-out';
                        if (type === 'checkin') {
                            document.querySelector('form[action="{{ route("attendance.checkin") }}"]').submit();
                        } else {
                            document.querySelector('form[action="{{ route("attendance.checkout") }}"]').submit();
                        }
                    },
                    function(error) {
                        alert('Location access denied. Using manual check-in/check-out.');
                        if (type === 'checkin') {
                            document.querySelector('form[action="{{ route("attendance.checkin") }}"]').submit();
                        } else {
                            document.querySelector('form[action="{{ route("attendance.checkout") }}"]').submit();
                        }
                    }
                );
                return false;
            } else {
                alert('Geolocation is not supported by this browser.');
                return true;
            }
        }
    </script>
</body>
</html>
