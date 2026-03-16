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
        
        // Get today's attendance data for all users using 12-hour periods
        $timeData = $this->getPhilippineTimeAndPeriod();
        $now = $timeData['now'];
        $periodStart = $timeData['period_start'];
        
        $todayAttendances = Attendance::with('user')
            ->where('period_start', $periodStart)
            ->orderBy('created_at', 'desc')
            ->get();
        
        $checkedInToday = $todayAttendances->where('check_in', '!=', null)->count();
        $checkedOutToday = $todayAttendances->where('check_out', '!=', null)->count();
        $currentlyWorking = $todayAttendances->where('check_in', '!=', null)->where('check_out', null)->count();
        
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
            'todayAttendances',
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
     * Refresh attendance data for Super Admin dashboard
     */
    public function refreshAttendances()
    {
        // Get today's attendance data for all users using 12-hour periods
        $timeData = $this->getPhilippineTimeAndPeriod();
        $now = $timeData['now'];
        $periodStart = $timeData['period_start'];
        
        $todayAttendances = Attendance::with('user')
            ->where('period_start', $periodStart)
            ->orderBy('created_at', 'desc')
            ->get();
        
        $checkedInToday = $todayAttendances->where('check_in', '!=', null)->count();
        $checkedOutToday = $todayAttendances->where('check_out', '!=', null)->count();
        $currentlyWorking = $todayAttendances->where('check_in', '!=', null)->where('check_out', null)->count();
        
        // Return JSON data for real-time update
        return response()->json([
            'checkedInToday' => $checkedInToday,
            'checkedOutToday' => $checkedOutToday,
            'currentlyWorking' => $currentlyWorking,
            'attendances' => $todayAttendances->map(function($attendance) {
                return [
                    'user_name' => $attendance->user->name,
                    'user_role' => ucfirst($attendance->user->role),
                    'user_position' => $attendance->user->position ?? 'No position',
                    'check_in' => $attendance->check_in ? \Carbon\Carbon::parse($attendance->check_in)->format('h:i A') : null,
                    'check_out' => $attendance->check_out ? \Carbon\Carbon::parse($attendance->check_out)->format('h:i A') : null,
                    'status' => $attendance->check_in && !$attendance->check_out ? 'working' : 
                               ($attendance->check_out ? 'completed' : 'no_activity'),
                    'location' => $attendance->check_in_location ?? 'Unknown',
                    'time_ago' => $attendance->check_in ? \Carbon\Carbon::parse($attendance->check_in)->diffForHumans(\Carbon\Carbon::now(), true) : null
                ];
            })
        ]);
    }
}

