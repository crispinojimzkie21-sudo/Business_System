<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Attendance;
use Carbon\Carbon;

class SuperAdminController extends Controller
{
    /**
     * Get current Philippine time and determine the attendance period
     */
    private function getPhilippineTimeAndPeriod()
    {
        // Set timezone to Philippines
        $now = Carbon::now('Asia/Manila');
        $hour = $now->hour;
        
        // Determine attendance period (12-hour cycles)
        // Morning period: 6:00 AM - 5:59 PM (06:00 - 17:59)
        // Evening period: 6:00 PM - 5:59 AM (18:00 - 05:59)
        if ($hour >= 6 && $hour < 18) {
            // Morning period (6 AM to 5:59 PM)
            $period = 'morning';
            $periodStart = $now->copy()->setTime(6, 0, 0);
            $periodEnd = $now->copy()->setTime(17, 59, 59);
        } else {
            // Evening period (6 PM to 5:59 AM next day)
            $period = 'evening';
            if ($hour >= 18) {
                // Same day evening (6 PM onwards)
                $periodStart = $now->copy()->setTime(18, 0, 0);
                $periodEnd = $now->copy()->addDay()->setTime(5, 59, 59);
            } else {
                // Early morning (before 6 AM)
                $periodStart = $now->copy()->subDay()->setTime(18, 0, 0);
                $periodEnd = $now->copy()->setTime(5, 59, 59);
            }
        }
        
        return [
            'now' => $now,
            'period' => $period,
            'period_start' => $periodStart,
            'period_end' => $periodEnd,
            'date_key' => $periodStart->format('Y-m-d H:i') // Unique key for the period
        ];
    }

    /**
     * Get monthly attendance statistics
     */
    public function getMonthlyAttendanceStats($month, $year)
    {
        $startDate = Carbon::create($year, $month, 1)->startOfMonth();
        $endDate = Carbon::create($year, $month, 1)->endOfMonth();
        
        // Get all attendance records for the month
        $attendances = Attendance::whereBetween('date', [$startDate, $endDate])->get();
        
        // Get all employees (excluding super admin)
        $totalEmployees = User::whereIn('role', ['employee', 'admin', 'cashier', 'manager'])->count();
        
        // Count unique employees who attended (present)
        $presentEmployees = $attendances->pluck('user_id')->unique()->count();
        
        // Calculate absent (total - present)
        $absentEmployees = $totalEmployees - $presentEmployees;
        
        // Calculate late (check-in after 9:00 AM)
        $lateCount = 0;
        foreach ($attendances as $attendance) {
            if ($attendance->check_in) {
                $checkInTime = Carbon::parse($attendance->check_in);
                if ($checkInTime->hour > 9 || ($checkInTime->hour == 9 && $checkInTime->minute > 0)) {
                    $lateCount++;
                }
            }
        }
        
        // Calculate attendance rate
        $attendanceRate = $totalEmployees > 0 ? round(($presentEmployees / $totalEmployees) * 100, 1) : 0;
        
        return [
            'total_employees' => $totalEmployees,
            'present_employees' => $presentEmployees,
            'absent_employees' => $absentEmployees,
            'late' => $lateCount,
            'attendance_rate' => $attendanceRate,
            'total_records' => $attendances->count()
        ];
    }
    
    /**
     * Get yearly attendance statistics for an employee
     */
    public function getYearlyAttendanceStats($userId, $year)
    {
        $stats = [];
        $totalPresent = 0;
        
        for ($month = 1; $month <= 12; $month++) {
            $startDate = Carbon::create($year, $month, 1)->startOfMonth();
            $endDate = Carbon::create($year, $month, 1)->endOfMonth();
            
            $monthAttendances = Attendance::where('user_id', $userId)
                ->whereBetween('created_at', [$startDate, $endDate])
                ->whereNotNull('check_in')
                ->get();
            
            $present = $monthAttendances->count();
            $totalPresent += $present;
            
            $stats[$month] = [
                'present' => $present,
                'absent' => 0, // Calculate based on working days
                'late' => 0 // Calculate based on check-in time
            ];
        }
        
        // Calculate attendance rate
        $totalWorkingDays = 250; // Approximate working days in a year
        $attendanceRate = $totalWorkingDays > 0 ? round(($totalPresent / $totalWorkingDays) * 100, 1) : 0;
        
        return [
            'monthly_stats' => $stats,
            'total_present' => $totalPresent,
            'attendance_rate' => $attendanceRate
        ];
    }
    
    /**
     * Get working days in a month (excluding weekends)
     */
    public function getWorkingDaysInMonth($month, $year)
    {
        $startDate = Carbon::create($year, $month, 1);
        $endDate = Carbon::create($year, $month, 1)->endOfMonth();
        
        $workingDays = 0;
        $current = $startDate->copy();
        
        while ($current <= $endDate) {
            if ($current->isWeekday()) {
                $workingDays++;
            }
            $current->addDay();
        }
        
        return $workingDays;
    }
    
    /**
     * Get working days in a year (excluding weekends)
     */
    public function getWorkingDaysInYear($year)
    {
        $startDate = Carbon::create($year, 1, 1);
        $endDate = Carbon::create($year, 12, 31);
        
        $workingDays = 0;
        $current = $startDate->copy();
        
        while ($current <= $endDate) {
            if ($current->isWeekday()) {
                $workingDays++;
            }
            $current->addDay();
        }
        
        return $workingDays;
    }

    public function index(Request $request)
    {
        $user = Auth::user();
        
        // Validate user is super admin - if not, redirect to proper dashboard
        if (!$user->isSuperAdmin()) {
            if ($user->isAdmin()) {
                return redirect()->route('dashboard.admin')->with('error', 'Access denied. Redirected to admin dashboard.');
            } elseif ($user->isCashier()) {
                return redirect()->route('dashboard.cashier')->with('error', 'Access denied. Redirected to cashier dashboard.');
            } else {
                return redirect()->route('dashboard.employee')->with('error', 'Access denied. Redirected to employee dashboard.');
            }
        }
        
        // Attendance logic - use 12-hour periods
        $timeData = $this->getPhilippineTimeAndPeriod();
        $periodStart = $timeData['period_start'];
        
        $todayAttendance = Attendance::where('user_id', $user->id)
            ->where('period_start', $periodStart)
            ->first();
        
        $canCheckIn = false;
        $canCheckOut = false;
        
        if (!$todayAttendance) {
            // No attendance record today - can check in or check out
            $canCheckIn = true;
            $canCheckOut = true; // Allow check-out anytime
        } elseif ($todayAttendance->check_in && !$todayAttendance->check_out) {
            // Checked in but not checked out - can check out
            $canCheckOut = true;
        }
        // If already checked out, both buttons should be disabled (default false)
        
        // Get comprehensive stats for super admin dashboard
        $totalUsers = User::count();
        $totalProducts = \App\Models\Product::count();
        $todaySales = \App\Models\Sale::whereDate('created_at', now()->format('Y-m-d'))->sum('total_amount');
        $todayTransactions = \App\Models\Sale::whereDate('created_at', now()->format('Y-m-d'))->count();
        $lowStockProducts = \App\Models\Product::whereColumn('stock_quantity', '<=', 'min_stock_level')->count();
        
        // Get backup status
        $lastBackupPath = storage_path('app/last_backup.json');
        $backupStatus = [
            'last_backup' => null,
            'last_backup_date' => null,
            'backup_available' => false,
            'backup_directory_exists' => false,
            'backup_count' => 0,
        ];
        
        try {
            if (file_exists($lastBackupPath)) {
                $backupInfo = json_decode(file_get_contents($lastBackupPath), true);
                if ($backupInfo && is_array($backupInfo)) {
                    $backupStatus = array_merge($backupStatus, $backupInfo);
                    $backupStatus['backup_available'] = true;
                }
            }
            
            // Check backup directory
            $backupPath = storage_path('backups');
            $backupStatus['backup_directory_exists'] = is_dir($backupPath);
            
            if ($backupStatus['backup_directory_exists']) {
                $backupFiles = glob($backupPath . '/database_*.sqlite');
                $backupStatus['backup_count'] = count($backupFiles);
            }
        } catch (\Exception $e) {
            // If there's any error getting backup status, set default values
            \Log::error('Error getting backup status: ' . $e->getMessage());
        }
        
        // Get today's attendance data for all users (entire day, not just current period)
        $todayPhilippine = Carbon::now('Asia/Manila')->format('Y-m-d');
        
        $todayAttendances = Attendance::with('user')
            ->whereDate('date', $todayPhilippine)
            ->orderBy('created_at', 'desc')
            ->get();
        
        $checkedInToday = $todayAttendances->where('check_in', '!=', null)->count();
        $checkedOutToday = $todayAttendances->where('check_out', '!=', null)->count();
        $currentlyWorking = $todayAttendances->where('check_in', '!=', null)->where('check_out', null)->count();
        
        $totalDays = Attendance::where('user_id', $user->id)->whereNotNull('check_in')->whereNotNull('check_out')->count();
        $totalHoursWorked = Attendance::where('user_id', $user->id)->whereNotNull('check_in')->whereNotNull('check_out')->sum('total_hours');
        $attendanceRate = ($totalDays / 30) * 100;

        return view('dashboard.super_admin', compact(
            'user', 
            'totalUsers', 
            'totalProducts', 
            'todaySales', 
            'todayTransactions',
            'lowStockProducts',
            'canCheckIn',
            'canCheckOut',
            'todayAttendance',
            'totalDays',
            'totalHoursWorked',
            'attendanceRate',
            'checkedInToday',
            'checkedOutToday',
            'currentlyWorking',
            'backupStatus'
        ));
    }

    /**
     * Super Admin access control panel - show all user accounts
     */
    public function accessControl()
    {
        // Debug: Log current user and role
        $currentUser = Auth::user();
        \Log::info('SuperAdmin accessControl accessed', [
            'user_id' => $currentUser->id,
            'user_email' => $currentUser->email,
            'user_role' => $currentUser->role,
            'is_super_admin' => $currentUser->isSuperAdmin()
        ]);
        
        // Get ALL users except super_admin
        $usersQuery = User::where('role', '!=', 'super_admin')
            ->orderBy('role')
            ->orderBy('name');
        
        $users = $usersQuery->paginate(20);
        
        $disabledCount = User::where('role', '!=', 'super_admin')
            ->where('access_enabled', false)
            ->count();
        
        $totalCount = User::where('role', '!=', 'super_admin')->count();
        $enabledCount = $totalCount - $disabledCount;
        
        // Get users by role for statistics
        $roleStats = User::where('role', '!=', 'super_admin')
            ->selectRaw('role, COUNT(*) as count')
            ->groupBy('role')
            ->pluck('count', 'role')
            ->toArray();
        
        \Log::info('SuperAdmin accessControl data', [
            'total_users' => $totalCount,
            'disabled_count' => $disabledCount,
            'enabled_count' => $enabledCount,
            'role_stats' => $roleStats
        ]);
        
        return view('superadmin.access-control', compact('users', 'disabledCount', 'enabledCount', 'roleStats'));
    }

    
    /**
     * Real-time statistics for monitoring user accounts and attendance
     */
    public function realTimeStats()
    {
        try {
            // Get current user counts by role
            $totalEmployees = User::where('role', '!=', 'super_admin')->count();
            $superAdminCount = User::where('role', 'super_admin')->count();
            $adminCount = User::where('role', 'admin')->count();
            $employeeCount = User::where('role', 'employee')->count();
            $salesClerkCount = User::where('role', 'sales_clerk')->count();
            $cashierCount = User::where('role', 'cashier')->count();
            $managerCount = User::where('role', 'manager')->count();
            
            // Get today's attendance data
            $todayPhilippine = Carbon::now('Asia/Manila')->format('Y-m-d');
            $todayAttendances = Attendance::with('user')
                ->whereDate('date', $todayPhilippine)
                ->orderBy('created_at', 'desc')
                ->get();
            
            $checkedInToday = $todayAttendances->where('check_in', '!=', null)->count();
            $checkedOutToday = $todayAttendances->where('check_out', '!=', null)->count();
            $currentlyWorking = $todayAttendances->where('check_in', '!=', null)->where('check_out', null)->count();
            $absentToday = $totalEmployees - $checkedInToday;
            $attendanceRate = $totalEmployees > 0 ? round(($checkedInToday / $totalEmployees) * 100, 1) : 0;
            
            // Get recently created users (last 24 hours)
            $recentUsers = User::where('created_at', '>=', Carbon::now('Asia/Manila')->subHours(24))
                ->orderBy('created_at', 'desc')
                ->take(5)
                ->get();
            
            // Get attendance records for new employees
            $newEmployeeAttendance = [];
            foreach ($recentUsers as $user) {
                $employeeRoles = ['employee', 'sales_clerk', 'cashier', 'manager', 'admin'];
                if (in_array($user->role, $employeeRoles)) {
                    $attendanceRecords = Attendance::where('user_id', $user->id)
                        ->where('date', '>=', Carbon::now('Asia/Manila')->subDays(7))
                        ->orderBy('date', 'desc')
                        ->take(5)
                        ->get();
                    
                    $newEmployeeAttendance[] = [
                        'user' => [
                            'id' => $user->id,
                            'name' => $user->name,
                            'email' => $user->email,
                            'role' => $user->role,
                            'created_at' => Carbon::parse($user->created_at)->format('M d, H:i'),
                            'time_ago' => Carbon::parse($user->created_at)->diffForHumans(Carbon::now(), true)
                        ],
                        'attendance_records' => $attendanceRecords->map(function($record) {
                            return [
                                'date' => $record->date,
                                'check_in' => $record->check_in ? Carbon::parse($record->check_in)->format('h:i A') : null,
                                'check_out' => $record->check_out ? Carbon::parse($record->check_out)->format('h:i A') : null,
                                'status' => $record->status ?? 'pending',
                                'notes' => $record->notes
                            ];
                        }),
                        'total_records' => $attendanceRecords->count()
                    ];
                }
            }
            
            // Get system statistics
            $totalUsers = User::count();
            $activeUsers = User::whereNotNull('email_verified_at')->count();
            $disabledUsers = User::where('access_enabled', false)->count();
            
            // Get monthly attendance data for the current year
            $currentYear = Carbon::now('Asia/Manila')->year;
            $monthlyData = [];
            
            for ($month = 1; $month <= 12; $month++) {
                $monthAttendances = Attendance::whereYear('date', $currentYear)
                    ->whereMonth('date', $month)
                    ->get();
                    
                $present = $monthAttendances->whereNotNull('check_in')->count();
                $absent = ($totalEmployees * 22) - $present; // Assuming 22 working days per month
                $late = $monthAttendances->whereNotNull('check_in')
                    ->filter(function($attendance) {
                        $checkInTime = Carbon::parse($attendance->check_in);
                        return $checkInTime->hour > 8 || ($checkInTime->hour == 8 && $checkInTime->minute > 0);
                    })->count();
                    
                $rate = $totalEmployees > 0 ? round(($present / ($totalEmployees * 22)) * 100, 1) : 0;
                
                $monthlyData[] = [
                    'month' => $month,
                    'month_name' => Carbon::createFromDate($currentYear, $month, 1)->format('M'),
                    'present' => $present,
                    'absent' => $absent,
                    'late' => $late,
                    'rate' => $rate
                ];
            }
            
            return response()->json([
                'success' => true,
                'timestamp' => Carbon::now('Asia/Manila')->format('Y-m-d H:i:s'),
                'user_stats' => [
                    'total_employees' => $totalEmployees,
                    'super_admin_count' => $superAdminCount,
                    'admin_count' => $adminCount,
                    'employee_count' => $employeeCount,
                    'sales_clerk_count' => $salesClerkCount,
                    'cashier_count' => $cashierCount,
                    'manager_count' => $managerCount,
                    'total_users' => $totalUsers,
                    'active_users' => $activeUsers,
                    'disabled_users' => $disabledUsers
                ],
                'attendance_stats' => [
                    'checked_in_today' => $checkedInToday,
                    'checked_out_today' => $checkedOutToday,
                    'currently_working' => $currentlyWorking,
                    'absent_today' => $absentToday,
                    'attendance_rate' => $attendanceRate,
                    'date' => $todayPhilippine
                ],
                'recent_activity' => [
                    'new_users' => $recentUsers->map(function($user) {
                        return [
                            'id' => $user->id,
                            'name' => $user->name,
                            'email' => $user->email,
                            'role' => $user->role,
                            'created_at' => Carbon::parse($user->created_at)->format('M d, H:i'),
                            'time_ago' => Carbon::parse($user->created_at)->diffForHumans(Carbon::now(), true)
                        ];
                    }),
                    'new_employee_attendance' => $newEmployeeAttendance
                ],
                'monthly_data' => $monthlyData,
                'notifications' => [
                    'new_user_count' => $recentUsers->count(),
                    'has_new_users' => $recentUsers->count() > 0
                ]
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Failed to load real-time statistics: ' . $e->getMessage(),
                'timestamp' => Carbon::now('Asia/Manila')->format('Y-m-d H:i:s')
            ]);
        }
    }

    /**
     * Refresh attendance data for Super Admin dashboard
     */
    public function refreshAttendances()
    {
        try {
            // Get today's attendance data for all users (entire day, not just current period)
            $todayPhilippine = Carbon::now('Asia/Manila')->format('Y-m-d');
            
            $todayAttendances = Attendance::with('user')
                ->whereDate('date', $todayPhilippine)
                ->orderBy('created_at', 'desc')
                ->get();
            
            $checkedInToday = $todayAttendances->where('check_in', '!=', null)->count();
            $checkedOutToday = $todayAttendances->where('check_out', '!=', null)->count();
            $currentlyWorking = $todayAttendances->where('check_in', '!=', null)->where('check_out', null)->count();
            
            // Get total employees (excluding super admin)
            $totalEmployees = User::where('role', '!=', 'super_admin')->count();
            
            // Calculate today's attendance rate
            $attendanceRate = $totalEmployees > 0 ? round(($checkedInToday / $totalEmployees) * 100, 1) : 0;
            
            // Get monthly attendance data for the current year
            $currentYear = Carbon::now('Asia/Manila')->year;
            $monthlyData = [];
            
            for ($month = 1; $month <= 12; $month++) {
                $monthAttendances = Attendance::whereYear('date', $currentYear)
                    ->whereMonth('date', $month)
                    ->get();
                    
                $present = $monthAttendances->whereNotNull('check_in')->count();
                $absent = ($totalEmployees * 22) - $present; // Assuming 22 working days per month
                $late = $monthAttendances->whereNotNull('check_in')
                    ->filter(function($attendance) {
                        $checkInTime = Carbon::parse($attendance->check_in);
                        return $checkInTime->hour > 8 || ($checkInTime->hour == 8 && $checkInTime->minute > 0);
                    })->count();
                    
                $rate = $totalEmployees > 0 ? round(($present / ($totalEmployees * 22)) * 100, 1) : 0;
                
                $monthlyData[] = [
                    'month' => $month,
                    'month_name' => Carbon::createFromDate($currentYear, $month, 1)->format('M'),
                    'present' => $present,
                    'absent' => $absent,
                    'late' => $late,
                    'rate' => $rate
                ];
            }
            
            // Return JSON data for real-time update
            return response()->json([
                'checkedInToday' => $checkedInToday,
                'checkedOutToday' => $checkedOutToday,
                'currentlyWorking' => $currentlyWorking,
                'totalEmployees' => $totalEmployees,
                'attendanceRate' => $attendanceRate,
                'presentToday' => $checkedInToday,
                'absentToday' => $totalEmployees - $checkedInToday,
                'date' => $todayPhilippine,
                'monthlyData' => $monthlyData,
                'attendances' => $todayAttendances->map(function($attendance) {
                    try {
                        return [
                            'user_name' => $attendance->user ? $attendance->user->name : 'Unknown User',
                            'user_email' => $attendance->user ? $attendance->user->email : 'unknown@example.com',
                            'check_in' => $attendance->check_in ? \Carbon\Carbon::parse($attendance->check_in)->format('h:i A') : null,
                            'check_out' => $attendance->check_out ? \Carbon\Carbon::parse($attendance->check_out)->format('h:i A') : null,
                            'location' => $attendance->check_in_location ?? 'Manual Check-in',
                            'time_ago' => $attendance->check_in ? \Carbon\Carbon::parse($attendance->check_in)->diffForHumans(\Carbon\Carbon::now(), true) : null
                        ];
                    } catch (\Exception $e) {
                        return [
                            'user_name' => 'Error',
                            'user_email' => 'error@example.com',
                            'check_in' => null,
                            'check_out' => null,
                            'location' => 'Error loading data',
                            'time_ago' => null
                        ];
                    }
                })
            ]);
            
        } catch (\Exception $e) {
            // Return error response if something goes wrong
            return response()->json([
                'error' => true,
                'message' => 'Failed to load attendance data: ' . $e->getMessage(),
                'checkedInToday' => 0,
                'checkedOutToday' => 0,
                'currentlyWorking' => 0,
                'totalEmployees' => 0,
                'attendanceRate' => 0,
                'presentToday' => 0,
                'absentToday' => 0,
                'date' => Carbon::now('Asia/Manila')->format('Y-m-d'),
                'monthlyData' => [],
                'attendances' => []
            ]);
        }
    }

    /**
     * Export attendance to PDF
     */
    public function exportToPDF(Request $request)
    {
        $year = $request->get('year', date('Y'));
        $month = $request->get('month', date('m'));
        
        // Get employees and their attendance data
        $employees = User::whereIn('role', ['employee', 'admin'])->orderBy('name')->get();
        $months = [
            1 => 'Jan', 2 => 'Feb', 3 => 'Mar', 4 => 'Apr', 5 => 'May', 6 => 'Jun',
            7 => 'Jul', 8 => 'Aug', 9 => 'Sep', 10 => 'Oct', 11 => 'Nov', 12 => 'Dec'
        ];
        
        $attendanceData = [];
        foreach ($employees as $employee) {
            $yearlyStats = $this->getYearlyAttendanceStats($employee->id, $year);
            
            $attendanceData[] = [
                'name' => $employee->name,
                'email' => $employee->email,
                'role' => ucfirst($employee->role),
                'position' => $employee->position ?? 'N/A',
                'monthly_stats' => $yearlyStats
            ];
        }
        
        // Use DomPDF facade
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('exports.attendance-pdf', [
            'attendanceData' => $attendanceData,
            'year' => $year,
            'month' => $month,
            'months' => $months,
            'generatedDate' => now()->format('F j, Y g:i A')
        ]);
        
        return $pdf->download("attendance-{$year}-{$month}.pdf");
    }

    /**
     * Export attendance to Excel (CSV format as fallback)
     */
    public function exportToExcel(Request $request)
    {
        $year = $request->get('year', date('Y'));
        $month = $request->get('month', date('m'));
        
        // Get employees and their attendance data
        $employees = User::whereIn('role', ['employee', 'admin'])->orderBy('name')->get();
        $months = [
            1 => 'Jan', 2 => 'Feb', 3 => 'Mar', 4 => 'Apr', 5 => 'May', 6 => 'Jun',
            7 => 'Jul', 8 => 'Aug', 9 => 'Sep', 10 => 'Oct', 11 => 'Nov', 12 => 'Dec'
        ];
        
        $filename = "attendance-{$year}-{$month}.csv";
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $callback = function() use ($employees, $months, $year) {
            $file = fopen('php://output', 'w');
            
            // CSV Header
            fputcsv($file, [
                'Employee Name', 'Email', 'Role', 'Position',
                'Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun',
                'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec',
                'Total', 'Rate'
            ]);
            
            // CSV Data
            foreach ($employees as $employee) {
                $yearlyStats = $this->getYearlyAttendanceStats($employee->id, $year);
                
                $row = [
                    $employee->name,
                    $employee->email,
                    ucfirst($employee->role),
                    $employee->position ?? 'N/A'
                ];
                
                // Add monthly data
                foreach ($months as $monthNum => $monthName) {
                    $monthData = $yearlyStats[$monthNum] ?? ['present' => 0, 'absent' => 0, 'late' => 0];
                    $row[] = $monthData['present'] . '/' . $monthData['absent'] . '/' . $monthData['late'];
                }
                
                $row[] = $yearlyStats['total_present'] ?? 0;
                $row[] = ($yearlyStats['attendance_rate'] ?? 0) . '%';
                
                fputcsv($file, $row);
            }
            
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Print attendance record (optimized for printing)
     */
    public function printAttendance(Request $request)
    {
        $year = $request->get('year', date('Y'));
        $month = $request->get('month', date('m'));
        
        // Get employees and their attendance data
        $employees = User::whereIn('role', ['employee', 'admin'])->orderBy('name')->get();
        $months = [
            1 => 'Jan', 2 => 'Feb', 3 => 'Mar', 4 => 'Apr', 5 => 'May', 6 => 'Jun',
            7 => 'Jul', 8 => 'Aug', 9 => 'Sep', 10 => 'Oct', 11 => 'Nov', 12 => 'Dec'
        ];
        
        $attendanceData = [];
        foreach ($employees as $employee) {
            $yearlyStats = $this->getYearlyAttendanceStats($employee->id, $year);
            
            $attendanceData[] = [
                'name' => $employee->name,
                'email' => $employee->email,
                'role' => ucfirst($employee->role),
                'position' => $employee->position ?? 'N/A',
                'monthly_stats' => $yearlyStats
            ];
        }
        
        return view('exports.attendance-print', [
            'attendanceData' => $attendanceData,
            'year' => $year,
            'month' => $month,
            'months' => $months,
            'generatedDate' => now()->format('F j, Y g:i A')
        ]);
    }

    /**
     * Monthly Attendance Records page for Super Admin
     */
    public function monthlyAttendance()
    {
        $user = auth()->user();
        
        // Get current Philippine time and date
        $philippineTime = Carbon::now('Asia/Manila');
        $currentYear = $philippineTime->year;
        $currentMonth = $philippineTime->month;
        
        // Get all employees (excluding super admin)
        $employees = User::where('role', '!=', 'super_admin')
            ->orderBy('name')
            ->get();
        
        // Initialize months array for current year
        $months = [];
        for ($month = 1; $month <= 12; $month++) {
            $monthName = Carbon::createFromDate($currentYear, $month, 1)->format('F');
            $monthStats = $this->getMonthlyAttendanceStats($month, $currentYear);
            $months[$month] = [
                'name' => $monthName,
                'stats' => $monthStats
            ];
        }
        
        // Get attendance data for current month
        $currentMonthStats = $this->getMonthlyAttendanceStats($currentMonth, $currentYear);
        
        // Get today's attendance statistics for real-time display
        $todayPhilippine = Carbon::now('Asia/Manila')->format('Y-m-d');
        $todayAttendances = Attendance::with('user')
            ->whereDate('date', $todayPhilippine)
            ->orderBy('created_at', 'desc')
            ->get();
        
        $checkedInToday = $todayAttendances->where('check_in', '!=', null)->count();
        $totalEmployees = User::where('role', '!=', 'super_admin')->count();
        $absentToday = $totalEmployees - $checkedInToday;
        $attendanceRateToday = $totalEmployees > 0 ? round(($checkedInToday / $totalEmployees) * 100, 1) : 0;
        
        // Get detailed attendance records for current month
        $monthlyAttendances = Attendance::with('user')
            ->whereYear('date', $currentYear)
            ->whereMonth('date', $currentMonth)
            ->orderBy('date', 'desc')
            ->orderBy('check_in', 'desc')
            ->get();
        
        return view('superadmin.monthly-attendance', compact(
            'user',
            'employees',
            'months',
            'currentMonth',
            'currentYear',
            'currentMonthStats',
            'monthlyAttendances',
            'totalEmployees',
            'checkedInToday',
            'absentToday',
            'attendanceRateToday'
        ));
    }
}

