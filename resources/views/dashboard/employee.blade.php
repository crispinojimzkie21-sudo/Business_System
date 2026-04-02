<!doctype html>

<html lang="en">

<head>

    <meta charset="utf-8">

    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link rel="icon" href="/favicon.svg" type="image/svg+xml">

    <title>Employee Dashboard - {{ config('app.name') }}</title>

    <script src="https://cdn.tailwindcss.com"></script>

</head>

<body class="bg-gradient-to-br from-black via-gray-900 to-blue-900 min-h-screen text-white">

    <div class="max-w-6xl mx-auto p-4 md:p-6">

        <!-- Header -->

        <header class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6 md:mb-8">

            <div>

                <h1 class="text-2xl md:text-3xl font-bold text-blue-400">👤 Sales Clerk Dashboard</h1>

                <p class="text-blue-200 text-sm mt-1">Sales Clerk Access</p>

            </div>

            <div class="flex flex-wrap items-center gap-2 md:gap-4">

                <span class="text-sm text-blue-200">{{ Auth::user()->name }} ({{ ucfirst(Auth::user()->role) }})</span>

                <a href="{{ route('dashboard.profile') }}" class="px-3 py-2 text-blue-200 hover:bg-blue-900/30 rounded text-sm">

                    <i class="fas fa-user-circle mr-1"></i>Profile

                </a>

                <form method="POST" action="{{ route('logout') }}" class="inline">

                    @csrf

                    <button type="submit" class="px-3 py-2 text-blue-200 hover:bg-blue-900/30 rounded text-sm">Logout</button>

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

            <div class="bg-black/60 p-3 md:p-4 rounded-lg border border-blue-900/30">

                <h3 class="text-blue-400 font-semibold text-sm">Your Role</h3>

                <p class="text-xl md:text-2xl font-bold">{{ ucfirst(Auth::user()->role) }}</p>

                <p class="text-xs text-blue-200">Sales Clerk</p>

            </div>

            <div class="bg-black/60 p-3 md:p-4 rounded-lg border border-blue-900/30">

                <h3 class="text-blue-400 font-semibold text-sm">Position</h3>

                <p class="text-xl md:text-2xl font-bold">{{ Auth::user()->position ?? 'Not Set' }}</p>

            </div>

            <div class="bg-black/60 p-3 md:p-4 rounded-lg border border-blue-900/30">

                <h3 class="text-blue-400 font-semibold text-sm">Monthly Salary</h3>

                <p class="text-xl md:text-2xl font-bold">₱{{ number_format(Auth::user()->salary ?? 0, 0) }}</p>

            </div>

            <div class="bg-black/60 p-3 md:p-4 rounded-lg border border-green-900/30">

                <h3 class="text-green-400 font-semibold text-sm">Status</h3>

                <p class="text-xl md:text-2xl font-bold text-green-400">Active</p>

            </div>

        </div>



        <!-- Sales & Attendance Grid -->

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 md:gap-6 mb-6 md:mb-8">

            <!-- Attendance Section -->

            <div class="bg-black/60 p-4 md:p-6 rounded-lg border border-blue-900/30">

                <h2 class="text-xl font-semibold mb-3 md:mb-4 text-blue-400">⏰ Attendance</h2>

                <p class="text-sm text-blue-200 mb-3 md:mb-4">Track your daily attendance</p>

                

                <!-- Check In/Out Buttons -->

                <div class="grid grid-cols-2 gap-2 mb-3">

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

                    @elseif(!$canCheckOut)

                        <button disabled class="w-full p-2 bg-gray-600 rounded text-sm font-medium opacity-50 cursor-not-allowed">

                            ⏱️ Check Out

                        </button>

                    @endif

                </div>

                

                @if($todayAttendance)

                    <div class="mb-3 p-2 bg-blue-900/30 rounded text-sm">

                        @if($todayAttendance->check_in && !$todayAttendance->check_out)

                            <p class="text-blue-300">✅ Checked in at {{ \Carbon\Carbon::parse($todayAttendance->check_in)->format('h:i A') }}</p>

                        @elseif($todayAttendance->check_in && $todayAttendance->check_out)

                            <p class="text-green-300">✅ Completed: In at {{ \Carbon\Carbon::parse($todayAttendance->check_in)->format('h:i A') }}, Out at {{ \Carbon\Carbon::parse($todayAttendance->check_out)->format('h:i A') }}</p>

                        @endif

                    </div>

                @endif

            </div>

            <!-- Products Section -->

            <div class="bg-black/60 p-4 md:p-6 rounded-lg border border-blue-900/30">

                <h2 class="text-xl font-semibold mb-3 md:mb-4 text-blue-400">📦 Available Products</h2>

                <p class="text-sm text-blue-200 mb-3 md:mb-4">View current products and prices</p>

                

                @if($products && $products->count() > 0)

                    <div class="space-y-2 max-h-64 overflow-y-auto">

                        @foreach($products as $product)

                            <div class="flex items-center justify-between p-2 bg-gray-800/50 rounded border border-gray-700/50">

                                <div class="flex items-center gap-2">

                                    <div class="w-2 h-2 rounded-full @if($product->stock_quantity > $product->min_stock_level) bg-green-500 @else bg-yellow-500 @endif"></div>

                                    <div>

                                        <p class="text-sm font-medium text-white">{{ $product->name }}</p>

                                        <p class="text-xs text-gray-400">Stock: {{ $product->stock_quantity }}</p>

                                    </div>

                                </div>

                                <div class="text-right">

                                    <p class="text-sm font-bold text-blue-400">₱{{ number_format($product->price, 2) }}</p>

                                    <p class="text-xs text-gray-400">{{ $product->category ?? 'General' }}</p>

                                </div>

                            </div>

                        @endforeach

                    </div>

                @else

                    <div class="text-center py-6 text-blue-300">

                        <i class="fas fa-box-open text-3xl mb-2"></i>

                        <p class="text-sm">No products available</p>

                    </div>

                @endif

                

                <a href="{{ route('products.index') }}" class="block p-2 bg-gray-800 rounded hover:bg-blue-900/30 transition-colors text-sm text-center mt-3">

                    📋 View All Products

                </a>

            </div>

        </div>



        <!-- Work Summary Stats -->

        <div class="grid grid-cols-3 gap-3 md:gap-4 mb-6 md:mb-8">

            <div class="bg-black/60 p-3 md:p-4 rounded-lg border border-blue-900/30 text-center">

                <p class="text-2xl md:text-3xl font-bold text-blue-400">{{ $totalDays }}</p>

                <p class="text-xs md:text-sm text-blue-200">Days This Month</p>

            </div>

            <div class="bg-black/60 p-3 md:p-4 rounded-lg border border-blue-900/30 text-center">

                @php
    $hours = floor($totalHoursWorked);
    $minutes = round(($totalHoursWorked - $hours) * 60);
    if ($minutes == 60) { $hours += 1; $minutes = 0; }
@endphp
<p class="text-2xl md:text-3xl font-bold text-blue-400">{{ $hours }}h {{ $minutes }}m</p>

                <p class="text-xs md:text-sm text-blue-200">Hours Worked</p>

            </div>

            <div class="bg-black/60 p-3 md:p-4 rounded-lg border border-blue-900/30 text-center">

                <p class="text-2xl md:text-3xl font-bold text-blue-400">{{ $attendanceRate }}%</p>

                <p class="text-xs md:text-sm text-blue-200">Attendance Rate</p>

            </div>

        </div>



        <!-- Recent Attendance -->

        <div class="bg-black/60 p-4 md:p-6 rounded-lg border border-blue-900/30 mb-6 md:mb-8">

            <h2 class="text-xl font-semibold mb-3 md:mb-4 text-blue-400">📋 Recent Attendance</h2>

            

            @if($recentAttendances && $recentAttendances->count() > 0)

                <div class="overflow-x-auto">

                    <table class="w-full text-sm">

                        <thead>

                            <tr class="border-b border-blue-900/30">

                                <th class="text-left py-2 px-2 md:px-4 text-blue-300">Date</th>

                                <th class="text-left py-2 px-2 md:px-4 text-blue-300">Check In</th>

                                <th class="text-left py-2 px-2 md:px-4 text-blue-300">Check Out</th>

                                <th class="text-left py-2 px-2 md:px-4 text-blue-300 hidden sm:table-cell">Duration</th>

                                <th class="text-left py-2 px-2 md:px-4 text-blue-300">Status</th>

                            </tr>

                        </thead>

                        <tbody>

                            @foreach($recentAttendances as $record)

                                <tr class="border-b border-blue-900/20 hover:bg-blue-900/10">

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

                <div class="text-center py-6 text-blue-300">

                    <i class="fas fa-calendar-xmark text-3xl md:text-4xl mb-2"></i>

                    <p class="text-sm">No attendance records found.</p>

                    <p class="text-xs mt-1">Start by checking in for your shift!</p>

                </div>

            @endif

        </div>



        <!-- User Information -->

        <div class="bg-black/60 p-4 md:p-6 rounded-lg border border-blue-900/30">

            <h2 class="text-xl font-semibold mb-3 md:mb-4 text-blue-400">👤 Your Information</h2>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">

                <div>

                    <h3 class="font-semibold text-blue-300 mb-2">Personal Details</h3>

                    <p><strong>Name:</strong> {{ Auth::user()->name }}</p>

                    <p><strong>Email:</strong> {{ Auth::user()->email }}</p>

                    <p><strong>Position:</strong> {{ Auth::user()->position ?? 'Not set' }}</p>

                    <p><strong>Salary:</strong> ₱{{ number_format(Auth::user()->salary ?? 0, 2) }}</p>

                </div>

                <div>

                    <h3 class="font-semibold text-blue-300 mb-2">Account Details</h3>

                    <p><strong>Role:</strong> <span class="text-blue-400">{{ ucfirst(Auth::user()->role) }}</span></p>

                    <p><strong>Account Type:</strong> Sales Clerk</p>

                    <p><strong>Member Since:</strong> {{ Auth::user()->created_at->format('M d, Y') }}</p>

                    <p><a href="{{ route('dashboard.profile') }}" class="text-blue-400 hover:underline"><i class="fas fa-edit mr-1"></i>Change Password</a></p>

                </div>

            </div>

        </div>

    </div>



    <script>

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

                        // Set hidden input values

                        document.querySelector('input[name="latitude"]').value = position.coords.latitude;

                        document.querySelector('input[name="longitude"]').value = position.coords.longitude;

                        document.querySelector('input[name="location_name"]').value = type === 'checkin' ? 'GPS Check-in' : 'GPS Check-out';

                        // Submit the form

                        document.querySelector('form[action="{{ route("attendance.checkin") }}"]').submit();

                    },

                    function(error) {

                        alert('Location access denied. Using manual check-in/check-out.');

                        // Continue with manual check-in

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





