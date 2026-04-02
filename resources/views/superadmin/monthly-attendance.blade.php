<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>📊 Monthly Attendance Records - {{ config('app.name') }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gradient-to-br from-black via-gray-900 to-red-900 min-h-screen text-white">
    <div class="max-w-7xl mx-auto p-6">
        <!-- Header -->
        <header class="flex items-center justify-between mb-8">
            <div>
                <h1 class="text-3xl font-bold text-red-400">📊 Monthly Attendance Records</h1>
                <p class="text-red-200 text-sm mt-1">Complete attendance overview for all employees</p>
            </div>
            
            <!-- Desktop Navigation -->
            <div class="hidden md:flex items-center gap-4">
                <span class="text-sm text-red-200">{{ ucfirst($user->role) }} (Admin)</span>
                <a href="{{ route('dashboard.superadmin') }}" class="px-3 py-2 text-red-200 hover:bg-red-900/30 rounded text-sm">
                    <i class="fas fa-arrow-left mr-1"></i>Back to Dashboard
                </a>
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
            
            <!-- Mobile Navigation -->
            <div class="md:hidden">
                <button onclick="toggleMobileMenu()" class="bg-red-900/30 hover:bg-red-900/50 px-4 py-2 rounded-lg text-red-200 transition-colors">
                    <i class="fas fa-bars mr-2"></i>Menu
                </button>
                
                <!-- Mobile Menu Dropdown -->
                <div id="mobileMenu" class="hidden absolute right-0 top-16 bg-gray-900 border border-red-700 rounded-lg shadow-xl z-50 min-w-48">
                    <div class="py-2">
                        <div class="px-4 py-2 border-b border-red-700">
                            <span class="text-red-200 text-sm">{{ ucfirst($user->role) }} (Admin)</span>
                        </div>
                        <a href="{{ route('dashboard.superadmin') }}" class="block px-4 py-3 text-red-200 hover:bg-red-900/30 transition-colors">
                            <i class="fas fa-arrow-left mr-2"></i>Back to Dashboard
                        </a>
                        <a href="{{ route('dashboard.profile') }}" class="block px-4 py-3 text-red-200 hover:bg-red-900/30 transition-colors">
                            <i class="fas fa-user-circle mr-2"></i>Profile
                        </a>
                        @if(Auth::check())
                            <div class="px-4 py-2 border-b border-red-700">
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="w-full text-left text-red-200 hover:bg-red-900/30 transition-colors">
                                        <i class="fas fa-sign-out-alt mr-2"></i>Logout
                                    </button>
                                </form>
                            </div>
                        @else
                            <a href="{{ url('/login') }}" class="block px-4 py-3 text-red-200 hover:bg-red-900/30 transition-colors">
                                <i class="fas fa-sign-in-alt mr-2"></i>Login
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        </header>

        @if (session('success'))
            <div class="mb-6 bg-green-900/50 border border-green-700 rounded-md p-4">
                <p class="text-green-200">{{ session('success') }}</p>
            </div>
        @endif

        <!-- Month Selection and Stats -->
        <div class="bg-black/60 p-6 rounded-lg border border-red-900/30 mb-8">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h2 class="text-xl font-semibold text-red-400">📅 {{ Carbon\Carbon::createFromDate($currentYear, $currentMonth, 1)->format('F Y') }}</h2>
                    <p class="text-sm text-red-200">Monthly attendance statistics</p>
                </div>
                <div class="flex items-center gap-3">
                    <select id="monthFilter" class="px-3 py-2 bg-gray-800 border border-red-900/50 rounded text-white text-sm">
                        @foreach($months as $monthNum => $monthData)
                            <option value="{{ $monthNum }}" {{ $monthNum == $currentMonth ? 'selected' : '' }}>
                                {{ $monthData['name'] }} {{ $currentYear }}
                            </option>
                        @endforeach
                    </select>
                    <select id="yearFilter" class="px-3 py-2 bg-gray-800 border border-red-900/50 rounded text-white text-sm">
                        <option value="{{ $currentYear }}" selected>{{ $currentYear }}</option>
                        <option value="{{ $currentYear - 1 }}">{{ $currentYear - 1 }}</option>
                        <option value="{{ $currentYear - 2 }}">{{ $currentYear - 2 }}</option>
                    </select>
                    <button onclick="filterByMonth()" class="px-3 py-2 bg-red-600 hover:bg-red-700 rounded text-white text-sm">
                        <i class="fas fa-filter mr-1"></i>Filter
                    </button>
                </div>
            </div>

            <!-- Statistics Cards -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                <div class="bg-gray-900/50 p-4 rounded-lg border border-red-900/20">
                    <p class="text-xs text-red-300 mb-1">Total Employees</p>
                    <p class="text-2xl font-bold text-white">{{ $currentMonthStats['total_employees'] ?? 0 }}</p>
                    <p class="text-xs text-gray-400">All staff members</p>
                </div>
                <div class="bg-gray-900/50 p-4 rounded-lg border border-green-900/20">
                    <p class="text-xs text-green-300 mb-1">Present Employees</p>
                    <p class="text-2xl font-bold text-green-400">{{ $currentMonthStats['present_employees'] ?? 0 }}</p>
                    <p class="text-xs text-gray-400">Attended this month</p>
                </div>
                <div class="bg-gray-900/50 p-4 rounded-lg border border-yellow-900/20">
                    <p class="text-xs text-yellow-300 mb-1">Absent Employees</p>
                    <p class="text-2xl font-bold text-yellow-400">{{ $currentMonthStats['absent_employees'] ?? 0 }}</p>
                    <p class="text-xs text-gray-400">No attendance</p>
                </div>
                <div class="bg-gray-900/50 p-4 rounded-lg border border-blue-900/20">
                    <p class="text-xs text-blue-300 mb-1">Attendance Rate</p>
                    <p class="text-2xl font-bold text-blue-400">{{ $currentMonthStats['attendance_rate'] ?? 0 }}%</p>
                    <p class="text-xs text-gray-400">This month</p>
                </div>
            </div>
        </div>

        <!-- Monthly Overview Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
            @foreach($months as $monthNum => $monthData)
                <div class="bg-gradient-to-br from-black/70 to-red-900/30 p-4 rounded-lg border border-red-900/50 hover:border-red-900/70 transition-all cursor-pointer" onclick="filterByMonth({{ $monthNum }})">
                    <div class="flex items-center justify-between mb-3">
                        <h3 class="text-red-300 font-semibold">{{ $monthData['name'] }}</h3>
                        <div class="w-8 h-8 bg-red-900/50 rounded-lg flex items-center justify-center">
                            <span class="text-red-400 text-xs">{{ str_pad($monthNum, 2, '0', STR_PAD_LEFT) }}</span>
                        </div>
                    </div>
                    <div class="space-y-2">
                        <div class="flex justify-between items-center">
                            <span class="text-xs text-gray-400">Present</span>
                            <span class="text-sm font-bold text-green-400">{{ $monthData['stats']['present_employees'] ?? 0 }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-xs text-gray-400">Absent</span>
                            <span class="text-sm font-bold text-red-400">{{ $monthData['stats']['absent_employees'] ?? 0 }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-xs text-gray-400">Late</span>
                            <span class="text-sm font-bold text-yellow-400">{{ $monthData['stats']['late'] ?? 0 }}</span>
                        </div>
                        <div class="flex justify-between items-center pt-2 border-t border-red-900/30">
                            <span class="text-xs text-gray-400">Rate</span>
                            <span class="text-sm font-bold text-white">{{ $monthData['stats']['attendance_rate'] ?? 0 }}%</span>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Real-time Today's Attendance Section -->
        <div class="bg-black/60 rounded-lg border border-red-900/30 mb-8">
            <div class="p-6">
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <h2 class="text-xl font-semibold text-red-400">📋 Employee Attendance Details</h2>
                        <p class="text-sm text-red-200">Complete yearly attendance overview for all employees</p>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="text-xs text-gray-400">Live Updates</span>
                        <span id="liveStatus" class="px-2 py-1 bg-green-900/30 text-green-300 rounded text-xs">
                            ● Connected
                        </span>
                        <button onclick="refreshRealTimeData()" class="px-3 py-1 bg-red-600 hover:bg-red-700 rounded text-white text-sm">
                            🔄 Refresh
                        </button>
                    </div>
                </div>

                <!-- Today's Statistics -->
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                    <div class="bg-gray-900/50 p-4 rounded-lg border border-red-900/20">
                        <p class="text-xs text-red-300 mb-1">Total Employees</p>
                        <p id="totalEmployees" class="text-2xl font-bold text-white">{{ $totalEmployees ?? 0 }}</p>
                        <p class="text-xs text-gray-400">Staff members only</p>
                    </div>
                    <div class="bg-gray-900/50 p-4 rounded-lg border border-green-900/20">
                        <p class="text-xs text-green-300 mb-1">Total Present Today</p>
                        <p id="presentToday" class="text-2xl font-bold text-green-400">{{ $checkedInToday ?? 0 }}</p>
                        <p class="text-xs text-gray-400">Currently working</p>
                    </div>
                    <div class="bg-gray-900/50 p-4 rounded-lg border border-yellow-900/20">
                        <p class="text-xs text-yellow-300 mb-1">Absent Today</p>
                        <p id="absentToday" class="text-2xl font-bold text-yellow-400">{{ $absentToday ?? 0 }}</p>
                        <p class="text-xs text-gray-400">Not present</p>
                    </div>
                    <div class="bg-gray-900/50 p-4 rounded-lg border border-blue-900/20">
                        <p class="text-xs text-blue-300 mb-1">Avg. Attendance Rate</p>
                        <p id="attendanceRate" class="text-2xl font-bold text-blue-400">{{ $attendanceRateToday ?? 0 }}%</p>
                        <p class="text-xs text-gray-400">Today</p>
                    </div>
                </div>

                <!-- Yearly Employee Attendance Table -->
                <div class="bg-black/40 rounded-lg border border-red-900/30 overflow-hidden">
                    <div class="p-4 border-b border-red-900/30">
                        <div class="flex items-center justify-between">
                            <h3 class="text-red-300 font-semibold">📋 Employee Attendance Details</h3>
                            <div class="flex items-center gap-2">
                                <input type="text" id="employeeSearch" placeholder="Search employee..." 
                                       class="px-3 py-1 bg-gray-800 border border-red-900/50 rounded text-white text-sm">
                                <button onclick="exportToPDF()" class="px-3 py-1 bg-blue-600 hover:bg-blue-700 rounded text-white text-sm">
                                    📄 PDF
                                </button>
                                <button onclick="exportToExcel()" class="px-3 py-1 bg-green-600 hover:bg-green-700 rounded text-white text-sm">
                                    📊 Excel
                                </button>
                                <button onclick="printAttendance()" class="px-3 py-1 bg-purple-600 hover:bg-purple-700 rounded text-white text-sm">
                                    🖨️ Print
                                </button>
                            </div>
                        </div>
                    </div>
                    
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead class="bg-red-900/20">
                                <tr>
                                    <th class="text-left py-3 px-4 text-red-300 font-semibold">Employee</th>
                                    <th class="text-left py-3 px-4 text-red-300 font-semibold">Department</th>
                                    <th class="text-center py-3 px-4 text-red-300 font-semibold">Jan</th>
                                    <th class="text-center py-3 px-4 text-red-300 font-semibold">Feb</th>
                                    <th class="text-center py-3 px-4 text-red-300 font-semibold">Mar</th>
                                    <th class="text-center py-3 px-4 text-red-300 font-semibold">Apr</th>
                                    <th class="text-center py-3 px-4 text-red-300 font-semibold">May</th>
                                    <th class="text-center py-3 px-4 text-red-300 font-semibold">Jun</th>
                                    <th class="text-center py-3 px-4 text-red-300 font-semibold">Jul</th>
                                    <th class="text-center py-3 px-4 text-red-300 font-semibold">Aug</th>
                                    <th class="text-center py-3 px-4 text-red-300 font-semibold">Sep</th>
                                    <th class="text-center py-3 px-4 text-red-300 font-semibold">Oct</th>
                                    <th class="text-center py-3 px-4 text-red-300 font-semibold">Nov</th>
                                    <th class="text-center py-3 px-4 text-red-300 font-semibold">Dec</th>
                                    <th class="text-center py-3 px-4 text-red-300 font-semibold">Total</th>
                                    <th class="text-center py-3 px-4 text-red-300 font-semibold">Rate</th>
                                </tr>
                            </thead>
                            <tbody id="attendanceTableBody">
                                @php
                                    $allEmployees = \App\Models\User::whereIn('role', ['employee', 'admin', 'cashier', 'manager'])->orderBy('name')->get();
                                @endphp
                                @foreach($allEmployees as $employee)
                                    @php
                                        $yearlyStats = app('App\Http\Controllers\SuperAdminController')->getYearlyAttendanceStats($employee->id, $currentYear);
                                    @endphp
                                    <tr class="border-b border-red-900/20 hover:bg-red-900/10 transition-colors">
                                        <td class="py-3 px-4">
                                            <div class="flex items-center gap-2">
                                                <div class="w-8 h-8 bg-red-900/50 rounded-full flex items-center justify-center">
                                                    <span class="text-xs text-red-300 font-semibold">{{ strtoupper(substr($employee->name ?? 'Unknown', 0, 1)) }}</span>
                                                </div>
                                                <div>
                                                    <p class="text-sm font-medium text-white">{{ $employee->name ?? 'Unknown' }}</p>
                                                    <p class="text-xs text-gray-400">{{ $employee->email ?? 'N/A' }}</p>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="py-3 px-4">
                                            <span class="px-2 py-1 bg-red-900/30 text-red-300 rounded text-xs">
                                                {{ ucfirst($employee->role ?? '') }}
                                            </span>
                                        </td>
                                        @foreach(range(1, 12) as $month)
                                            @php
                                                $monthStats = app('App\Http\Controllers\SuperAdminController')->getMonthlyAttendanceStats($month, $currentYear);
                                                $employeeAttendance = \App\Models\Attendance::where('user_id', $employee->id)
                                                    ->whereYear('date', $currentYear)
                                                    ->whereMonth('date', $month)
                                                    ->first();
                                                $presentCount = \App\Models\Attendance::where('user_id', $employee->id)
                                                    ->whereYear('date', $currentYear)
                                                    ->whereMonth('date', $month)
                                                    ->whereNotNull('check_in')
                                                    ->count();
                                                $absentCount = 0; // Calculate based on working days
                                                $lateCount = 0; // Calculate based on check-in time
                                            @endphp
                                            <td class="py-3 px-4 text-center">
                                                <span class="text-xs text-gray-400">
                                                    {{ $presentCount }} / {{ $absentCount }} / {{ $lateCount }}
                                                </span>
                                            </td>
                                        @endforeach
                                        <td class="py-3 px-4 text-center">
                                            <span class="text-sm font-bold text-white">{{ $yearlyStats['total_present'] ?? 0 }}</span>
                                        </td>
                                        <td class="py-3 px-4 text-center">
                                            <span class="px-2 py-1 bg-green-900/30 text-green-300 rounded text-xs font-medium">
                                                {{ $yearlyStats['attendance_rate'] ?? 0 }}%
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Real-time data refresh
        function refreshRealTimeData() {
            const liveStatus = document.getElementById('liveStatus');
            liveStatus.innerHTML = '<span class="px-2 py-1 bg-yellow-900/30 text-yellow-300 rounded text-xs">● Refreshing...</span>';
            
            fetch('/superadmin/refresh-attendances')
                .then(response => response.json())
                .then(data => {
                    // Update statistics cards
                    updateStatistics(data);
                    
                    // Update live status
                    liveStatus.innerHTML = '<span class="px-2 py-1 bg-green-900/30 text-green-300 rounded text-xs">● Connected</span>';
                    
                    // Show success message
                    showNotification('Attendance data refreshed successfully!', 'success');
                })
                .catch(error => {
                    console.error('Error refreshing attendance data:', error);
                    liveStatus.innerHTML = '<span class="px-2 py-1 bg-red-900/30 text-red-300 rounded text-xs">● Error</span>';
                    showNotification('Failed to refresh attendance data', 'error');
                });
        }

        function updateStatistics(data) {
            // Update statistics cards with real-time data
            updateStatCard('totalEmployees', data.totalEmployees || 0);
            updateStatCard('presentToday', data.checkedInToday || 0);
            updateStatCard('absentToday', data.absentToday || 0);
            updateStatCard('attendanceRate', (data.attendanceRate || 0) + '%');
        }

        function updateStatCard(elementId, newValue) {
            const element = document.getElementById(elementId);
            if (element && element.textContent != newValue.toString()) {
                const oldValue = element.textContent;
                element.textContent = newValue;
                
                // Add highlight animation
                element.classList.add('text-yellow-400', 'scale-110');
                setTimeout(() => {
                    element.classList.remove('text-yellow-400', 'scale-110');
                    element.classList.add('text-green-400');
                }, 300);
            }
        }

        function showNotification(message, type) {
            // Create notification element
            const notification = document.createElement('div');
            notification.className = `fixed top-4 right-4 px-4 py-3 rounded-lg shadow-lg z-50 ${
                type === 'success' ? 'bg-green-900/90 text-green-300' : 'bg-red-900/90 text-red-300'
            }`;
            notification.innerHTML = `
                <div class="flex items-center gap-2">
                    <span>${type === 'success' ? '✅' : '❌'}</span>
                    <span class="text-sm font-medium">${message}</span>
                </div>
            `;
            
            document.body.appendChild(notification);
            
            // Auto-remove after 3 seconds
            setTimeout(() => {
                notification.style.transition = 'opacity 0.3s ease-out';
                notification.style.opacity = '0';
                setTimeout(() => {
                    document.body.removeChild(notification);
                }, 300);
            }, 3000);
        }

        // Auto-refresh every 30 seconds
        setInterval(refreshRealTimeData, 30000);

        // Initial refresh when page loads
        document.addEventListener('DOMContentLoaded', function() {
            refreshRealTimeData();
        });

        // Refresh when page becomes visible again
        document.addEventListener('visibilitychange', function() {
            if (!document.hidden) {
                refreshRealTimeData();
            }
        });

        function filterByMonth(month) {
            const year = document.getElementById('yearFilter').value;
            const selectedMonth = month || document.getElementById('monthFilter').value;
            window.location.href = `/superadmin/monthly-attendance?month=${selectedMonth}&year=${year}`;
        }

        function exportToPDF() {
            const month = document.getElementById('monthFilter').value;
            const year = document.getElementById('yearFilter').value;
            
            // Show loading state
            const button = event.target;
            const originalText = button.innerHTML;
            button.innerHTML = '📄 Generating...';
            button.disabled = true;
            
            // Open in new window
            window.open(`/attendance/export/pdf?month=${month}&year=${year}`, '_blank');
            
            // Restore button after delay
            setTimeout(() => {
                button.innerHTML = originalText;
                button.disabled = false;
            }, 2000);
        }

        function exportToExcel() {
            const month = document.getElementById('monthFilter').value;
            const year = document.getElementById('yearFilter').value;
            
            // Show loading state
            const button = event.target;
            const originalText = button.innerHTML;
            button.innerHTML = '📊 Generating...';
            button.disabled = true;
            
            // Open in new window
            window.open(`/attendance/export/excel?month=${month}&year=${year}`, '_blank');
            
            // Restore button after delay
            setTimeout(() => {
                button.innerHTML = originalText;
                button.disabled = false;
            }, 2000);
        }

        function printAttendance() {
            const month = document.getElementById('monthFilter').value;
            const year = document.getElementById('yearFilter').value;
            
            // Show loading state
            const button = event.target;
            const originalText = button.innerHTML;
            button.innerHTML = '🖨️ Opening...';
            button.disabled = true;
            
            // Open in new window
            window.open(`/attendance/print?month=${month}&year=${year}`, '_blank');
            
            // Restore button after delay
            setTimeout(() => {
                button.innerHTML = originalText;
                button.disabled = false;
            }, 2000);
        }

        // Search functionality
        document.getElementById('employeeSearch').addEventListener('input', function(e) {
            const searchTerm = e.target.value.toLowerCase();
            const rows = document.querySelectorAll('#attendanceTableBody tr');
            
            rows.forEach(row => {
                const employeeName = row.querySelector('td:first-child p').textContent.toLowerCase();
                const employeeEmail = row.querySelector('td:first-child p:last-child').textContent.toLowerCase();
                
                if (employeeName.includes(searchTerm) || employeeEmail.includes(searchTerm)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        });

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

        // Add CSS animations
        const style = document.createElement('style');
        style.textContent = `
            .scale-110 {
                transform: scale(1.1);
                transition: transform 0.3s ease;
            }
        `;
        document.head.appendChild(style);
    </script>
</body>
</html>
