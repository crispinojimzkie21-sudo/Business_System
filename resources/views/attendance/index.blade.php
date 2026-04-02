<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Today's Attendance - {{ config('app.name') }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gradient-to-br from-black via-gray-900 to-blue-900 min-h-screen text-white">
    <div class="max-w-6xl mx-auto p-6">
        <!-- Header -->
        <header class="flex items-center justify-between mb-8">
            <div>
                <h1 class="text-3xl font-bold text-blue-400">⏰ Today's Attendance</h1>
                <p class="text-blue-200 text-sm mt-1">Check in and check out for today</p>
                <p class="text-xs text-gray-400 mt-1">
                    <i class="fas fa-clock mr-1"></i>
                    Philippine Time: <span id="philippineTime" class="font-mono text-green-400"></span>
                </p>
            </div>
            <div class="flex gap-4">
                @if(Auth::user()->isSuperAdmin())
                    <a href="{{ url('/dashboard/super-admin') }}" class="px-4 py-2 text-blue-200 hover:bg-blue-900/30 rounded">← Dashboard</a>
                @elseif(Auth::user()->isAdmin())
                    <a href="{{ url('/dashboard/admin') }}" class="px-4 py-2 text-blue-200 hover:bg-blue-900/30 rounded">← Dashboard</a>
                @elseif(Auth::user()->isManager())
                    <a href="{{ url('/dashboard/manager') }}" class="px-4 py-2 text-purple-200 hover:bg-purple-900/30 rounded">← Dashboard</a>
                @elseif(Auth::user()->isCashier())
                    <a href="{{ url('/dashboard/cashier') }}" class="px-4 py-2 text-green-200 hover:bg-green-900/30 rounded">← Dashboard</a>
                @else
                    <a href="{{ url('/dashboard/employee') }}" class="px-4 py-2 text-blue-200 hover:bg-blue-900/30 rounded">← Dashboard</a>
                @endif
            </div>
        </header>

        @if (session('success'))
            <div class="mb-6 bg-green-900/50 border border-green-700 rounded-md p-4">
                <p class="text-green-200">{{ session('success') }}</p>
            </div>
        @endif

        @if (session('error'))
            <div class="mb-6 bg-red-900/50 border border-red-700 rounded-md p-4">
                <p class="text-red-200">{{ session('error') }}</p>
            </div>
        @endif

        @if (session('info'))
            <div class="mb-6 bg-blue-900/50 border border-blue-700 rounded-md p-4">
                <p class="text-blue-200">{{ session('info') }}</p>
            </div>
        @endif

        <!-- Real-time Attendance Notifications -->
        <div class="mb-6">
            <div id="notificationContainer" class="space-y-2">
                <!-- Notifications will appear here -->
            </div>
        </div>

        <!-- Check In/Out Forms -->
        <div class="bg-black/60 p-6 rounded-lg border border-blue-900/30 mb-8">
            <h2 class="text-xl font-semibold mb-4 text-blue-400">⏱️ Attendance Actions</h2>
            <div class="flex gap-4">
                <form method="POST" action="{{ route('attendance.checkin') }}" class="inline">
                    @csrf
                    <input type="hidden" name="latitude" value="">
                    <input type="hidden" name="longitude" value="">
                    <input type="hidden" name="location_name" value="Manual Check-in">
                    <button type="submit" class="px-6 py-3 bg-green-600 hover:bg-green-700 text-white font-semibold rounded-md transition-colors">
                        ⏰ Check In
                    </button>
                </form>
                <form method="POST" action="{{ route('attendance.checkout') }}" class="inline">
                    @csrf
                    <input type="hidden" name="latitude" value="">
                    <input type="hidden" name="longitude" value="">
                    <input type="hidden" name="location_name" value="Manual Check-out">
                    <button type="submit" class="px-6 py-3 bg-red-600 hover:bg-red-700 text-white font-semibold rounded-md transition-colors">
                        ⏱️ Check Out
                    </button>
                </form>
                <a href="{{ route('attendance.records') }}" class="px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-md transition-colors">
                    📅 View All Records
                </a>
            </div>
        </div>

        <!-- Today's Attendance List -->
        <div class="bg-black/60 rounded-lg border border-blue-900/30 overflow-hidden">
            <div class="p-6">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-xl font-semibold text-blue-400">Today's Check-ins</h2>
                    <div class="flex items-center gap-3">
                        <span class="text-xs text-gray-400">Today's Date (PHT): {{ Carbon\Carbon::now('Asia/Manila')->format('Y-m-d') }}</span>
                        <span id="lastRefresh" class="text-xs text-gray-400">Last updated: Loading...</span>
                        <button onclick="refreshAttendance()" class="px-3 py-1 bg-blue-600 hover:bg-blue-700 text-white rounded text-sm">
                            🔄 Refresh
                        </button>
                    </div>
                </div>
                
                <!-- Debug Info -->
                <div class="mb-4 p-3 bg-blue-900/20 rounded border border-blue-800/30">
                    <p class="text-xs text-blue-300">
                        Debug Info: Total Records: {{ $allAttendance->count() }} | 
                        Today's Records: {{ $todayAttendance->count() }} |
                        Today's Date: {{ Carbon\Carbon::now('Asia/Manila')->format('Y-m-d') }}
                    </p>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="border-b border-blue-900/30">
                                <th class="text-left py-3 px-4 text-blue-300">Employee</th>
                                <th class="text-left py-3 px-4 text-blue-300">Check In</th>
                                <th class="text-left py-3 px-4 text-blue-300">Check Out</th>
                                <th class="text-left py-3 px-4 text-blue-300">Duration</th>
                                @if(Auth::user()->isSuperAdmin() || Auth::user()->isAdmin())
                                <th class="text-left py-3 px-4 text-blue-300">Actions</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody id="attendanceTableBody">
                            @forelse ($todayAttendance as $userId => $records)
                                @foreach($records as $record)
                                <tr class="border-b border-blue-900/20 hover:bg-blue-900/10 transition-colors">
                                    <td class="py-3 px-4">
                                        <div class="flex items-center gap-2">
                                            <div class="w-8 h-8 bg-blue-900/50 rounded-full flex items-center justify-center">
                                                <span class="text-xs text-blue-300 font-semibold">{{ strtoupper(substr($record->user->name ?? 'Unknown', 0, 1)) }}</span>
                                            </div>
                                            <div>
                                                <p class="text-sm font-medium text-white">{{ $record->user->name ?? 'Unknown' }}</p>
                                                <p class="text-xs text-gray-400">{{ $record->user->email ?? 'N/A' }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="py-3 px-4">
                                        @if($record->check_in)
                                            <div class="flex items-center gap-2">
                                                <span class="text-green-400">{{ \Carbon\Carbon::parse($record->check_in)->format('H:i:s') }}</span>
                                                @if(\Carbon\Carbon::parse($record->check_in)->diffInMinutes(\Carbon\Carbon::now()) < 5)
                                                    <span class="px-2 py-1 bg-green-900/30 text-green-300 rounded text-xs font-medium animate-pulse">
                                                        NEW
                                                    </span>
                                                @endif
                                            </div>
                                        @else
                                            <span class="text-red-400">Not checked in</span>
                                        @endif
                                    </td>
                                    <td class="py-3 px-4">
                                        @if($record->check_out)
                                            {{ \Carbon\Carbon::parse($record->check_out)->format('H:i:s') }}
                                        @else
                                            <span class="text-yellow-400">Still checked in</span>
                                        @endif
                                    </td>
                                    <td class="py-3 px-4">
                                        @if($record->check_in && $record->check_out)
                                            {{ \Carbon\Carbon::parse($record->check_in)->diffForHumans(\Carbon\Carbon::parse($record->check_out), true) }}
                                        @else
                                            -
                                        @endif
                                    </td>
                                    @if(Auth::user()->isSuperAdmin() || Auth::user()->isAdmin())
                                    <td class="py-3 px-4">
                                        @if(!$record->check_out)
                                            <form action="{{ route('attendance.admin-checkout', $record->user_id) }}" method="POST" style="display: inline;">
                                                @csrf
                                                <button type="submit" class="px-3 py-1 bg-red-600 hover:bg-red-700 text-white rounded text-sm" onclick="return confirm('Check out this user?')">
                                                    ⏱️ Check Out
                                                </button>
                                            </form>
                                        @else
                                            <span class="text-green-400 text-sm">✓ Completed</span>
                                        @endif
                                    </td>
                                    @endif
                                </tr>
                                @endforeach
                            @empty
                                <tr>
                                    <td colspan="{{ (Auth::user()->isSuperAdmin() || Auth::user()->isAdmin()) ? '5' : '4' }}" class="py-8 text-center text-gray-400">
                                        No attendance records for today.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- All Attendance Records (for debugging) -->
        @if(Auth::user()->isSuperAdmin())
        <div class="bg-black/60 rounded-lg border border-purple-900/30 overflow-hidden mt-8">
            <div class="p-6">
                <h2 class="text-xl font-semibold text-purple-400 mb-4">🔍 All Attendance Records (Debug)</h2>
                <div class="mb-4 p-3 bg-purple-900/20 rounded border border-purple-800/30">
                    <p class="text-xs text-purple-300">
                        Total Records: {{ $allAttendance->count() }} | 
                        Today's Records: {{ $todayAttendance->count() }} |
                        Today's Date (PHT): {{ Carbon\Carbon::now('Asia/Manila')->format('Y-m-d') }}
                    </p>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="border-b border-purple-900/30">
                                <th class="text-left py-3 px-4 text-purple-300">ID</th>
                                <th class="text-left py-3 px-4 text-purple-300">Date</th>
                                <th class="text-left py-3 px-4 text-purple-300">Employee</th>
                                <th class="text-left py-3 px-4 text-purple-300">Check In</th>
                                <th class="text-left py-3 px-4 text-purple-300">Check Out</th>
                                <th class="text-left py-3 px-4 text-purple-300">Period</th>
                                <th class="text-left py-3 px-4 text-purple-300">Created At</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($allAttendance as $record)
                                <tr class="border-b border-purple-900/20 hover:bg-purple-900/10">
                                    <td class="py-3 px-4 font-mono text-xs">{{ $record->id }}</td>
                                    <td class="py-3 px-4">
                                        <span class="{{ $record->date === Carbon\Carbon::now('Asia/Manila')->format('Y-m-d') ? 'text-green-400 font-bold' : 'text-gray-400' }}">
                                            {{ $record->date ?? 'N/A' }}
                                            @if($record->date === Carbon\Carbon::now('Asia/Manila')->format('Y-m-d'))
                                                <span class="text-xs bg-green-900/30 px-1 rounded">TODAY</span>
                                            @endif
                                        </span>
                                    </td>
                                    <td class="py-3 px-4">{{ $record->user->name ?? 'Unknown' }}</td>
                                    <td class="py-3 px-4">{{ $record->check_in ? \Carbon\Carbon::parse($record->check_in)->format('H:i:s') : 'N/A' }}</td>
                                    <td class="py-3 px-4">{{ $record->check_out ? \Carbon\Carbon::parse($record->check_out)->format('H:i:s') : 'Still In' }}</td>
                                    <td class="py-3 px-4">{{ $record->period ?? 'N/A' }}</td>
                                    <td class="py-3 px-4 font-mono text-xs">{{ $record->created_at ? \Carbon\Carbon::parse($record->created_at)->format('m-d H:i') : 'N/A' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="py-8 text-center text-gray-400">
                                        No attendance records found in database.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        @endif
    </div>

    <!-- Auto-refresh for real-time updates -->
    <script>
        let lastAttendanceCount = 0;
        
        // Update Philippine time display
        function updatePhilippineTime() {
            const now = new Date();
            // Philippine Time is UTC+8
            const philippineTime = new Date(now.getTime() + (8 * 60 * 60 * 1000) + (now.getTimezoneOffset() * 60 * 1000));
            const timeString = philippineTime.toLocaleTimeString('en-US', {
                hour12: false,
                hour: '2-digit',
                minute: '2-digit',
                second: '2-digit'
            });
            const dateString = philippineTime.toLocaleDateString('en-US', {
                weekday: 'short',
                month: 'short',
                day: 'numeric'
            });
            
            const element = document.getElementById('philippineTime');
            if (element) {
                element.textContent = `${dateString} ${timeString} (PHT)`;
            }
        }
        
        // Check for new attendance records
        function checkForNewAttendance() {
            const currentRows = document.querySelectorAll('#attendanceTableBody tr').length;
            if (currentRows > lastAttendanceCount && lastAttendanceCount > 0) {
                showNotification('New employee checked in!', 'success');
            }
            lastAttendanceCount = currentRows;
        }
        
        // Show notification
        function showNotification(message, type = 'info') {
            const container = document.getElementById('notificationContainer');
            const notification = document.createElement('div');
            
            const bgColor = type === 'success' ? 'bg-green-900/50 border-green-700' : 
                           type === 'error' ? 'bg-red-900/50 border-red-700' : 
                           'bg-blue-900/50 border-blue-700';
            
            const textColor = type === 'success' ? 'text-green-200' : 
                            type === 'error' ? 'text-red-200' : 
                            'text-blue-200';
            
            notification.className = `${bgColor} border rounded-md p-4 flex items-center justify-between animate-pulse`;
            notification.innerHTML = `
                <p class="${textColor}">${message}</p>
                <button onclick="this.parentElement.remove()" class="text-gray-400 hover:text-white">×</button>
            `;
            
            container.appendChild(notification);
            
            // Auto-remove after 5 seconds
            setTimeout(() => {
                if (notification.parentElement) {
                    notification.remove();
                }
            }, 5000);
        }
        
        // Auto-refresh every 30 seconds to show new check-ins
        setInterval(function() {
            checkForNewAttendance();
            location.reload();
        }, 30000);
        
        // Manual refresh button
        function refreshAttendance() {
            showNotification('Refreshing attendance data...', 'info');
            setTimeout(() => location.reload(), 500);
        }
        
        // Show last refresh time
        function updateLastRefresh() {
            const now = new Date();
            const timeString = now.toLocaleTimeString();
            const element = document.getElementById('lastRefresh');
            if (element) {
                element.textContent = 'Last updated: ' + timeString;
            }
        }
        
        // Update times every second
        setInterval(function() {
            updatePhilippineTime();
            updateLastRefresh();
        }, 1000);
        
        // Initialize
        document.addEventListener('DOMContentLoaded', function() {
            updatePhilippineTime();
            updateLastRefresh();
            lastAttendanceCount = document.querySelectorAll('#attendanceTableBody tr').length;
            
            // Show success messages from session
            @if(session('success'))
                showNotification('{{ session('success') }}', 'success');
            @endif
            
            @if(session('error'))
                showNotification('{{ session('error') }}', 'error');
            @endif
        });
    </script>
</body>
</html>

