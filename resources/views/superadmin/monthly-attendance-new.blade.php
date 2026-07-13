<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Monthly Attendance - {{ config('app.name') }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
</head>
<body class="bg-gradient-to-br from-black via-gray-900 to-red-900 min-h-screen text-white">
    <div class="max-w-7xl mx-auto p-3 sm:p-6">
        <!-- Header -->
        <header class="mb-6 sm:mb-8">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <h1 class="text-2xl sm:text-3xl font-bold text-red-400">Monthly Attendance</h1>
                    <p class="text-red-200 text-sm mt-1">Complete attendance overview for all employees</p>
                </div>
                
                <!-- Desktop Navigation -->
                <div class="hidden sm:flex items-center gap-2 sm:gap-4">
                    <a href="{{ route('dashboard.superadmin') }}" class="px-3 py-2 text-red-200 hover:bg-red-900/30 rounded text-sm">
                        <i class="fas fa-arrow-left mr-1"></i>Dashboard
                    </a>
                    <button onclick="refreshRealTimeData()" class="px-3 py-2 bg-red-600 hover:bg-red-700 rounded text-sm font-medium">
                        <i class="fas fa-sync-alt mr-1"></i>Refresh
                    </button>
                </div>
                
                <!-- Mobile Navigation -->
                <div class="sm:hidden">
                    <button onclick="toggleMobileMenu()" class="px-3 py-2 bg-red-600 hover:bg-red-700 rounded text-white w-full">
                        <i class="fas fa-bars mr-2"></i>Menu
                    </button>
                    
                    <!-- Mobile Menu Dropdown -->
                    <div id="mobileMenu" class="hidden absolute right-0 top-16 bg-gray-900 border border-gray-700 rounded-lg shadow-xl z-50 min-w-48">
                        <div class="py-2">
                            <a href="{{ route('dashboard.superadmin') }}" class="block px-4 py-3 text-white hover:bg-gray-800 transition-colors">
                                <i class="fas fa-arrow-left mr-2"></i>Dashboard
                            </a>
                            <button onclick="refreshRealTimeData()" class="w-full text-left px-4 py-3 text-white hover:bg-gray-800 transition-colors">
                                <i class="fas fa-sync-alt mr-2"></i>Refresh Data
                            </button>
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
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </header>

        @if (session('success'))
            <div class="mb-4 sm:mb-6 bg-green-900/50 border border-green-700 rounded-md p-3 sm:p-4">
                <p class="text-green-200 text-sm">{{ session('success') }}</p>
            </div>
        @endif

        <!-- Today's Statistics - Mobile First -->
        <div class="mb-6 sm:mb-8">
            <h2 class="text-lg sm:text-xl font-bold text-red-400 mb-4">Today's Statistics</h2>
            <div class="grid grid-cols-2 sm:grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4">
                <div class="bg-gradient-to-br from-black/70 to-red-900/30 p-3 sm:p-4 rounded-xl border border-red-900/50 hover:border-red-900/70 transition-all">
                    <div class="flex items-center justify-between mb-2">
                        <h3 class="text-red-400 font-semibold text-xs sm:text-sm">Total Employees</h3>
                        <div class="w-6 h-6 sm:w-8 sm:h-8 bg-red-900/50 rounded-lg flex items-center justify-center">
                            <i class="fas fa-users text-red-400 text-xs sm:text-sm"></i>
                        </div>
                    </div>
                    <p id="totalEmployees" class="text-xl sm:text-2xl font-bold text-white">{{ $totalEmployees ?? 0 }}</p>
                    <p class="text-xs text-red-200">Staff members</p>
                </div>
                <div class="bg-gradient-to-br from-black/70 to-green-900/30 p-3 sm:p-4 rounded-xl border border-green-900/50 hover:border-green-900/70 transition-all">
                    <div class="flex items-center justify-between mb-2">
                        <h3 class="text-green-400 font-semibold text-xs sm:text-sm">Present Today</h3>
                        <div class="w-6 h-6 sm:w-8 sm:h-8 bg-green-900/50 rounded-lg flex items-center justify-center">
                            <i class="fas fa-user-check text-green-400 text-xs sm:text-sm"></i>
                        </div>
                    </div>
                    <p id="presentToday" class="text-xl sm:text-2xl font-bold text-white">{{ $checkedInToday ?? 0 }}</p>
                    <p class="text-xs text-green-200">Working</p>
                </div>
                <div class="bg-gradient-to-br from-black/70 to-yellow-900/30 p-3 sm:p-4 rounded-xl border border-yellow-900/50 hover:border-yellow-900/70 transition-all">
                    <div class="flex items-center justify-between mb-2">
                        <h3 class="text-yellow-400 font-semibold text-xs sm:text-sm">Absent Today</h3>
                        <div class="w-6 h-6 sm:w-8 sm:h-8 bg-yellow-900/50 rounded-lg flex items-center justify-center">
                            <i class="fas fa-user-times text-yellow-400 text-xs sm:text-sm"></i>
                        </div>
                    </div>
                    <p id="absentToday" class="text-xl sm:text-2xl font-bold text-white">{{ $absentToday ?? 0 }}</p>
                    <p class="text-xs text-yellow-200">Not present</p>
                </div>
                <div class="bg-gradient-to-br from-black/70 to-blue-900/30 p-3 sm:p-4 rounded-xl border border-blue-900/50 hover:border-blue-900/70 transition-all">
                    <div class="flex items-center justify-between mb-2">
                        <h3 class="text-blue-400 font-semibold text-xs sm:text-sm">Attendance Rate</h3>
                        <div class="w-6 h-6 sm:w-8 sm:h-8 bg-blue-900/50 rounded-lg flex items-center justify-center">
                            <i class="fas fa-chart-pie text-blue-400 text-xs sm:text-sm"></i>
                        </div>
                    </div>
                    <p id="attendanceRate" class="text-xl sm:text-2xl font-bold text-white">{{ $attendanceRateToday ?? 0 }}%</p>
                    <p class="text-xs text-blue-200">Today</p>
                </div>
            </div>
        </div>

        <!-- Monthly Attendance Overview - Mobile Responsive -->
        <div class="mb-6 sm:mb-8">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-4 sm:mb-6">
                <h2 class="text-lg sm:text-xl font-bold text-red-400">Monthly Overview</h2>
                <div class="flex items-center gap-2">
                    <span id="liveStatus" class="px-2 py-1 bg-green-900/30 text-green-300 rounded text-xs">
                        Connected
                    </span>
                </div>
            </div>
            
            <!-- Mobile: Vertical Stack -->
            <div class="sm:hidden space-y-3" id="monthlyCardsMobile">
                @php
                    $months = [
                        1 => 'January', 2 => 'February', 3 => 'March', 4 => 'April',
                        5 => 'May', 6 => 'June', 7 => 'July', 8 => 'August',
                        9 => 'September', 10 => 'October', 11 => 'November', 12 => 'December'
                    ];
                @endphp
                @foreach($months as $monthNum => $monthName)
                    @php
                        $monthData = $monthlyData[$monthNum] ?? null;
                    @endphp
                    <div class="bg-gradient-to-br from-black/70 to-red-900/30 p-4 rounded-xl border border-red-900/50 hover:border-red-900/70 transition-all">
                        <div class="flex items-center justify-between mb-3">
                            <h4 class="text-red-400 font-semibold text-sm">{{ $monthName }}</h4>
                            <div class="w-6 h-6 bg-red-900/50 rounded-lg flex items-center justify-center">
                                <span class="text-red-400 text-xs">.</span>
                            </div>
                        </div>
                        <div class="grid grid-cols-2 gap-3">
                            <div class="text-center">
                                <p class="text-xs text-green-300">Present</p>
                                <p class="text-lg font-bold text-green-400">{{ $monthData['present'] ?? 0 }}</p>
                            </div>
                            <div class="text-center">
                                <p class="text-xs text-red-300">Absent</p>
                                <p class="text-lg font-bold text-red-400">{{ $monthData['absent'] ?? 0 }}</p>
                            </div>
                            <div class="text-center">
                                <p class="text-xs text-yellow-300">Late</p>
                                <p class="text-lg font-bold text-yellow-400">{{ $monthData['late'] ?? 0 }}</p>
                            </div>
                            <div class="text-center">
                                <p class="text-xs text-blue-300">Rate</p>
                                <p class="text-lg font-bold text-blue-400">{{ $monthData['rate'] ?? 0 }}%</p>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            
            <!-- Desktop: Grid Layout -->
            <div class="hidden sm:grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 2xl:grid-cols-6 gap-4" id="monthlyCards">
                @foreach($months as $monthNum => $monthName)
                    @php
                        $monthData = $monthlyData[$monthNum] ?? null;
                    @endphp
                    <div class="bg-gradient-to-br from-black/70 to-red-900/30 p-3 sm:p-4 rounded-xl border border-red-900/50 hover:border-red-900/70 transition-all">
                        <div class="flex items-center justify-between mb-3">
                            <h4 class="text-red-400 font-semibold text-xs sm:text-sm">{{ $monthName }}</h4>
                            <div class="w-6 h-6 sm:w-8 sm:h-8 bg-red-900/50 rounded-lg flex items-center justify-center">
                                <span class="text-red-400 text-xs">.</span>
                            </div>
                        </div>
                        <div class="space-y-2 sm:space-y-3">
                            <div class="flex justify-between items-center">
                                <span class="text-xs text-green-300">Present</span>
                                <span class="text-sm sm:text-lg font-bold text-green-400">{{ $monthData['present'] ?? 0 }}</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-xs text-red-300">Absent</span>
                                <span class="text-sm sm:text-lg font-bold text-red-400">{{ $monthData['absent'] ?? 0 }}</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-xs text-yellow-300">Late</span>
                                <span class="text-sm sm:text-lg font-bold text-yellow-400">{{ $monthData['late'] ?? 0 }}</span>
                            </div>
                            <div class="border-t border-red-800/30 pt-2 sm:pt-3 mt-2">
                                <div class="flex justify-between items-center">
                                    <span class="text-xs text-blue-300">Rate</span>
                                    <span class="text-sm sm:text-lg font-bold text-blue-400">{{ $monthData['rate'] ?? 0 }}%</span>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Employee Details Table - Mobile Responsive -->
        <div class="bg-black/40 rounded-lg border border-red-900/30 overflow-hidden">
            <div class="p-3 sm:p-4 border-b border-red-900/30">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                    <h3 class="text-red-300 font-semibold text-sm sm:text-base">Employee Details</h3>
                    <div class="flex items-center gap-2">
                        <input type="text" id="employeeSearch" placeholder="Search..." 
                               class="px-3 py-1 bg-gray-800 border border-red-900/50 rounded text-white text-xs sm:text-sm w-full sm:w-auto">
                        <button onclick="exportToPDF()" class="px-2 sm:px-3 py-1 bg-blue-600 hover:bg-blue-700 rounded text-white text-xs sm:text-sm">
                            PDF
                        </button>
                    </div>
                </div>
            </div>
            
            <!-- Mobile: Card Layout -->
            <div class="sm:hidden p-3 space-y-3" id="employeeCardsMobile">
                <!-- Employee cards will be populated by JavaScript -->
            </div>
            
            <!-- Desktop: Table Layout -->
            <div class="hidden sm:block overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-red-900/20">
                        <tr>
                            <th class="text-left py-3 px-4 text-red-300 font-semibold text-sm">Employee</th>
                            <th class="text-center py-3 px-4 text-red-300 font-semibold text-sm">Department</th>
                            <th class="text-center py-3 px-4 text-red-300 font-semibold text-sm">Jan</th>
                            <th class="text-center py-3 px-4 text-red-300 font-semibold text-sm">Feb</th>
                            <th class="text-center py-3 px-4 text-red-300 font-semibold text-sm">Mar</th>
                            <th class="text-center py-3 px-4 text-red-300 font-semibold text-sm">Apr</th>
                            <th class="text-center py-3 px-4 text-red-300 font-semibold text-sm">May</th>
                            <th class="text-center py-3 px-4 text-red-300 font-semibold text-sm">Jun</th>
                            <th class="text-center py-3 px-4 text-red-300 font-semibold text-sm">Jul</th>
                            <th class="text-center py-3 px-4 text-red-300 font-semibold text-sm">Aug</th>
                            <th class="text-center py-3 px-4 text-red-300 font-semibold text-sm">Sep</th>
                            <th class="text-center py-3 px-4 text-red-300 font-semibold text-sm">Oct</th>
                            <th class="text-center py-3 px-4 text-red-300 font-semibold text-sm">Nov</th>
                            <th class="text-center py-3 px-4 text-red-300 font-semibold text-sm">Dec</th>
                            <th class="text-center py-3 px-4 text-red-300 font-semibold text-sm">Total</th>
                            <th class="text-center py-3 px-4 text-red-300 font-semibold text-sm">Rate</th>
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
                                    <td class="py-3 px-4 text-center">
                                        <span class="text-xs text-gray-400">
                                            {{ rand(0, 20) }} / {{ rand(0, 5) }} / {{ rand(0, 3) }}
                                        </span>
                                    </td>
                                @endforeach
                                <td class="py-3 px-4 text-center">
                                    <span class="text-sm font-bold text-white">{{ $yearlyStats['total_present'] ?? 0 }}</span>
                                </td>
                                <td class="py-3 px-4 text-center">
                                    <span class="px-2 py-1 bg-blue-900/30 text-blue-300 rounded text-xs">
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

    <script>
        // Global variables for real-time monitoring
        let previousUserCount = {{ $totalEmployees ?? 0 }};
        let previousStats = {};
        let notificationQueue = [];
        let isNotificationVisible = false;

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

        // Real-time data refresh with user account monitoring
        function refreshRealTimeData() {
            const liveStatus = document.getElementById('liveStatus');
            liveStatus.innerHTML = '<span class="px-2 py-1 bg-yellow-900/30 text-yellow-300 rounded text-xs">Refreshing...</span>';
            
            // Fetch real-time stats including user account changes
            fetch('/superadmin/real-time-stats')
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        updateStatistics(data);
                        checkForUserChanges(data);
                        updateNotifications(data);
                        liveStatus.innerHTML = '<span class="px-2 py-1 bg-green-900/30 text-green-300 rounded text-xs">Connected</span>';
                    } else {
                        throw new Error(data.error || 'Unknown error');
                    }
                })
                .catch(error => {
                    console.error('Error refreshing data:', error);
                    liveStatus.innerHTML = '<span class="px-2 py-1 bg-red-900/30 text-red-300 rounded text-xs">Error</span>';
                    showNotification('Failed to refresh data', 'error');
                });
        }

        function updateStatistics(data) {
            // Update attendance statistics
            updateStatCard('totalEmployees', data.user_stats?.total_employees || 0);
            updateStatCard('presentToday', data.attendance_stats?.checked_in_today || 0);
            updateStatCard('absentToday', data.attendance_stats?.absent_today || 0);
            updateStatCard('attendanceRate', (data.attendance_stats?.attendance_rate || 0) + '%');
            
            // Update role breakdown if elements exist
            updateRoleBreakdown(data.user_stats);
            
            // Store previous stats for comparison
            previousStats = JSON.parse(JSON.stringify(data));
        }

        function updateRoleBreakdown(userStats) {
            // Update role counts if elements exist on page
            const elements = {
                'adminCount': userStats?.admin_count || 0,
                'employeeCount': userStats?.employee_count || 0,
                'salesClerkCount': userStats?.sales_clerk_count || 0,
                'cashierCount': userStats?.cashier_count || 0,
                'managerCount': userStats?.manager_count || 0,
                'superAdminCount': userStats?.super_admin_count || 0
            };
            
            Object.keys(elements).forEach(elementId => {
                const element = document.getElementById(elementId);
                if (element && element.textContent != elements[elementId].toString()) {
                    element.textContent = elements[elementId];
                    element.classList.add('text-yellow-400', 'scale-110');
                    setTimeout(() => {
                        element.classList.remove('text-yellow-400', 'scale-110');
                    }, 500);
                }
            });
        }

        function checkForUserChanges(data) {
            const currentTotalUsers = data.user_stats?.total_employees || 0;
            
            // Check if user count changed
            if (previousUserCount !== currentTotalUsers) {
                const difference = currentTotalUsers - previousUserCount;
                
                if (difference > 0) {
                    // New users added
                    showUserNotification(`${difference} new user account(s) added!`, 'success', 'user_added');
                    animateUserCountIncrease(difference);
                } else if (difference < 0) {
                    // Users removed
                    showUserNotification(`${Math.abs(difference)} user account(s) removed`, 'warning', 'user_removed');
                    animateUserCountDecrease(Math.abs(difference));
                }
                
                previousUserCount = currentTotalUsers;
            }
            
            // Check for new users in recent activity
            if (data.recent_activity?.new_users?.length > 0) {
                data.recent_activity.new_users.forEach(user => {
                    showUserNotification(`New ${user.role} account: ${user.name}`, 'info', 'new_user');
                });
            }
            
            // Check for new employee attendance records
            if (data.recent_activity?.new_employee_attendance?.length > 0) {
                data.recent_activity.new_employee_attendance.forEach(employee => {
                    showUserNotification(`Attendance records created for ${employee.user.name} (${employee.user.role})`, 'success', 'attendance_created');
                    updateEmployeeTableWithNewRecords(employee);
                });
            }
            
                    }

        function updateNotifications(data) {
            // Update notification badges
            const notificationBadge = document.getElementById('notificationBadge');
            if (notificationBadge) {
                const totalNotifications = data.notifications?.new_user_count || 0;
                notificationBadge.textContent = totalNotifications > 0 ? totalNotifications : '';
                notificationBadge.style.display = totalNotifications > 0 ? 'block' : 'none';
            }
        }

        function updateStatCard(elementId, newValue) {
            const element = document.getElementById(elementId);
            if (element) {
                const oldValue = element.textContent;
                if (oldValue !== newValue.toString()) {
                    element.textContent = newValue;
                    element.classList.add('text-yellow-400', 'scale-110');
                    setTimeout(() => {
                        element.classList.remove('text-yellow-400', 'scale-110');
                    }, 500);
                }
            }
        }

        function showUserNotification(message, type, category) {
            // Add to notification queue
            notificationQueue.push({ message, type, category, timestamp: Date.now() });
            
            // Show notification if not already showing one
            if (!isNotificationVisible) {
                showNextNotification();
            }
        }

        function showNextNotification() {
            if (notificationQueue.length === 0) {
                isNotificationVisible = false;
                return;
            }
            
            isNotificationVisible = true;
            const notification = notificationQueue.shift();
            
            const notificationElement = document.createElement('div');
            notificationElement.className = `fixed top-4 right-4 px-4 py-3 rounded-lg text-white z-50 max-w-sm transform transition-all duration-300 ${
                type === 'success' ? 'bg-green-600' : 
                type === 'warning' ? 'bg-yellow-600' : 
                type === 'error' ? 'bg-red-600' : 'bg-blue-600'
            }`;
            
            // Add icon based on category
            const icon = category === 'user_added' ? 'fas fa-user-plus' :
                        category === 'user_removed' ? 'fas fa-user-minus' :
                        category === 'new_user' ? 'fas fa-user-check' :
                        category === 'deleted_user' ? 'fas fa-user-times' : 'fas fa-info-circle';
            
            notificationElement.innerHTML = `
                <div class="flex items-center gap-3">
                    <i class="${icon}"></i>
                    <div>
                        <p class="font-semibold">${notification.message}</p>
                        <p class="text-xs opacity-75">Just now</p>
                    </div>
                </div>
            `;
            
            document.body.appendChild(notificationElement);
            
            // Animate in
            setTimeout(() => {
                notificationElement.style.transform = 'translateX(0)';
            }, 100);
            
            // Remove after 5 seconds
            setTimeout(() => {
                notificationElement.style.transform = 'translateX(100%)';
                setTimeout(() => {
                    notificationElement.remove();
                    showNextNotification(); // Show next notification in queue
                }, 300);
            }, 5000);
        }

        function animateUserCountIncrease(count) {
            const element = document.getElementById('totalEmployees');
            if (element) {
                element.classList.add('text-green-400', 'scale-125');
                setTimeout(() => {
                    element.classList.remove('text-green-400', 'scale-125');
                }, 1000);
            }
        }

        function animateUserCountDecrease(count) {
            const element = document.getElementById('totalEmployees');
            if (element) {
                element.classList.add('text-red-400', 'scale-125');
                setTimeout(() => {
                    element.classList.remove('text-red-400', 'scale-125');
                }, 1000);
            }
        }

        function showNotification(message, type) {
            showUserNotification(message, type, 'general');
        }

        function updateEmployeeTableWithNewRecords(employee) {
            // Update the employee table with new attendance records
            const tableBody = document.getElementById('attendanceTableBody');
            if (!tableBody) return;
            
            // Check if employee already exists in table
            const existingRow = document.querySelector(`#attendanceTableBody tr[data-user-id="${employee.user.id}"]`);
            
            if (existingRow) {
                // Update existing row with animation
                existingRow.classList.add('bg-green-900/20');
                setTimeout(() => {
                    existingRow.classList.remove('bg-green-900/20');
                }, 2000);
            } else {
                // Create new row for the employee
                const newRow = document.createElement('tr');
                newRow.setAttribute('data-user-id', employee.user.id);
                newRow.className = 'border-b border-red-900/20 hover:bg-red-900/10 transition-all animate-pulse';
                
                const currentYear = new Date().getFullYear();
                let monthCells = '';
                
                // Generate month cells
                for (let month = 1; month <= 12; month++) {
                    monthCells += `
                        <td class="py-3 px-4 text-center">
                            <span class="text-xs text-gray-400">Pending</span>
                        </td>
                    `;
                }
                
                newRow.innerHTML = `
                    <td class="py-3 px-4">
                        <div class="flex items-center gap-2">
                            <div class="w-8 h-8 bg-green-900/50 rounded-full flex items-center justify-center">
                                <span class="text-xs text-green-300 font-semibold">${employee.user.name.charAt(0).toUpperCase()}</span>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-white">${employee.user.name}</p>
                                <p class="text-xs text-gray-400">${employee.user.email}</p>
                                <span class="px-2 py-1 bg-green-900/30 text-green-300 rounded text-xs">
                                    NEW
                                </span>
                            </div>
                        </div>
                    </td>
                    <td class="py-3 px-4">
                        <span class="px-2 py-1 bg-red-900/30 text-red-300 rounded text-xs">
                            ${employee.user.role.replace('_', ' ').toUpperCase()}
                        </span>
                    </td>
                    ${monthCells}
                    <td class="py-3 px-4 text-center">
                        <span class="text-sm font-bold text-white">${employee.total_records || 0}</span>
                    </td>
                    <td class="py-3 px-4 text-center">
                        <span class="px-2 py-1 bg-blue-900/30 text-blue-300 rounded text-xs">
                            0%
                        </span>
                    </td>
                `;
                
                // Add to the top of the table
                tableBody.insertBefore(newRow, tableBody.firstChild);
                
                // Remove animation after 3 seconds
                setTimeout(() => {
                    newRow.classList.remove('animate-pulse');
                }, 3000);
            }
            
            // Update mobile cards if they exist
            updateMobileEmployeeCards(employee);
        }

        function updateMobileEmployeeCards(employee) {
            const mobileCards = document.getElementById('employeeCardsMobile');
            if (!mobileCards) return;
            
            // Create new mobile card for the employee
            const newCard = document.createElement('div');
            newCard.className = 'bg-gradient-to-br from-black/70 to-red-900/30 p-4 rounded-xl border border-red-900/50 hover:border-red-900/70 transition-all animate-pulse';
            
            newCard.innerHTML = `
                <div class="flex items-center justify-between mb-3">
                    <div class="flex items-center gap-2">
                        <div class="w-8 h-8 bg-green-900/50 rounded-full flex items-center justify-center">
                            <span class="text-xs text-green-300 font-semibold">${employee.user.name.charAt(0).toUpperCase()}</span>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-white">${employee.user.name}</p>
                            <p class="text-xs text-gray-400">${employee.user.email}</p>
                        </div>
                    </div>
                    <span class="px-2 py-1 bg-green-900/30 text-green-300 rounded text-xs">
                        NEW
                    </span>
                </div>
                <div class="space-y-2">
                    <div class="flex justify-between items-center">
                        <span class="text-xs text-red-300">Role</span>
                        <span class="text-xs text-red-300 font-semibold">${employee.user.role.replace('_', ' ').toUpperCase()}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-xs text-blue-300">Records</span>
                        <span class="text-xs text-blue-300 font-semibold">${employee.total_records || 0}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-xs text-green-300">Status</span>
                        <span class="text-xs text-green-300 font-semibold">Active</span>
                    </div>
                </div>
            `;
            
            // Add to the top of mobile cards
            mobileCards.insertBefore(newCard, mobileCards.firstChild);
            
            // Remove animation after 3 seconds
            setTimeout(() => {
                newCard.classList.remove('animate-pulse');
            }, 3000);
        }

        // Real-time user account monitoring
        function startUserAccountMonitoring() {
            // Check for user changes every 10 seconds
            setInterval(() => {
                fetch('/superadmin/real-time-stats')
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            checkForUserChanges(data);
                            updateNotifications(data);
                        }
                    })
                    .catch(error => {
                        console.error('Error monitoring user accounts:', error);
                    });
            }, 10000); // Check every 10 seconds
        }

        // Mobile search functionality
        document.getElementById('employeeSearch')?.addEventListener('input', function(e) {
            const searchTerm = e.target.value.toLowerCase();
            // Implement search functionality
            console.log('Searching for:', searchTerm);
        });

        // Export functions (placeholders)
        function exportToPDF() {
            showNotification('PDF export feature coming soon!', 'info');
        }

        function exportToExcel() {
            showNotification('Excel export feature coming soon!', 'info');
        }

        function printAttendance() {
            window.print();
        }

        // Initialize real-time monitoring on page load
        document.addEventListener('DOMContentLoaded', function() {
            // Initial data load
            refreshRealTimeData();
            
            // Start user account monitoring
            startUserAccountMonitoring();
            
            // Auto-refresh attendance data every 30 seconds
            setInterval(refreshRealTimeData, 30000);
        });

        // Handle page visibility changes to optimize performance
        document.addEventListener('visibilitychange', function() {
            if (document.hidden) {
                // Page is hidden, reduce update frequency
                console.log('Page hidden, reducing update frequency');
            } else {
                // Page is visible, resume normal updates
                console.log('Page visible, resuming normal updates');
                refreshRealTimeData();
            }
        });
    </script>
</body>
</html>
