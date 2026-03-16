<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Product;
use App\Models\Sale;
use App\Models\Attendance;
use Carbon\Carbon;

class AdminController extends Controller
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
        
        // Validate user is admin - if not, redirect to proper dashboard
        if (!$user->isAdmin()) {
            if ($user->isSuperAdmin()) {
                return redirect()->route('dashboard.superadmin')->with('error', 'Access denied. Redirected to super admin dashboard.');
            } elseif ($user->isCashier()) {
                return redirect()->route('dashboard.cashier')->with('error', 'Access denied. Redirected to cashier dashboard.');
            } else {
                return redirect()->route('dashboard.employee')->with('error', 'Access denied. Redirected to employee dashboard.');
            }
        }
        
        // Get today's attendance for the admin user using 12-hour periods
        $timeData = $this->getPhilippineTimeAndPeriod();
        $now = $timeData['now'];
        $periodStart = $timeData['period_start'];
        
        $todayAttendance = Attendance::where('user_id', $user->id)
            ->where('period_start', $periodStart)
            ->first();
        
        $canCheckIn = false;
        $canCheckOut = false;
        
        if (!$todayAttendance) {
            // No attendance record for this period - can check in or check out
            $canCheckIn = true;
            $canCheckOut = true; // Allow check-out anytime
        } elseif ($todayAttendance->check_in && !$todayAttendance->check_out) {
            // Checked in but not checked out - can check out
            $canCheckOut = true;
        }
        // If already checked out, both buttons should be disabled (default false)
        
        // Debug: Log the attendance state
        \Log::info('Admin attendance state', [
            'user_id' => $user->id,
            'period' => $timeData['period'],
            'period_start' => $periodStart,
            'has_attendance' => $todayAttendance ? 'yes' : 'no',
            'check_in' => $todayAttendance->check_in ?? 'null',
            'check_out' => $todayAttendance->check_out ?? 'null',
            'canCheckIn' => $canCheckIn,
            'canCheckOut' => $canCheckOut
        ]);
        
        // Get basic stats for admin dashboard
        $totalUsers = User::where('role', 'user')->count();
        $totalEmployees = User::where('role', 'user')->count();
        $totalProducts = Product::where('stock_quantity', '>', 0)->count();
        $todaySales = Sale::whereDate('created_at', Carbon::today())->sum('total_amount');
        $todayTransactions = Sale::whereDate('created_at', Carbon::today())->count();
        $monthlySales = Sale::whereMonth('created_at', Carbon::now()->month)
            ->whereYear('created_at', Carbon::now()->year)
            ->sum('total_amount');
        $monthlyTransactions = Sale::whereMonth('created_at', Carbon::now()->month)
            ->whereYear('created_at', Carbon::now()->year)
            ->count();
        $lowStockCount = Product::whereColumn('stock_quantity', '<=', 'min_stock_level')->where('stock_quantity', '>', 0)->count();
        
        // E-Load stats
        $todayEloadSales = \App\Models\EloadTransaction::whereDate('created_at', Carbon::today())
            ->where('status', 'completed')
            ->sum('price');
        $todayEloadTransactions = \App\Models\EloadTransaction::whereDate('created_at', Carbon::today())->count();

        
        // Get recent employees
        $recentEmployees = User::where('role', 'user')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();
        
        // Get today's attendance
        $todayAttendanceAll = Attendance::whereDate('date', today())
            ->with('user')
            ->get();
        
        $checkedInToday = $todayAttendanceAll->whereNotNull('check_in')->count();
        $checkedOutToday = $todayAttendanceAll->whereNotNull('check_out')->count();
        
        return view('dashboard.admin', compact(
            'user', 
            'totalUsers', 
            'totalEmployees',
            'totalProducts', 
            'todaySales',
            'todayTransactions',
            'recentEmployees',
            'checkedInToday',
            'checkedOutToday',
            'canCheckIn',
            'canCheckOut',
            'todayAttendance',
            'monthlySales',
            'monthlyTransactions',
            'lowStockCount',
            'todayEloadSales',
            'todayEloadTransactions'
        ));
    }
}
