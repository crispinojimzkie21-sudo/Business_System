<?php



namespace App\Http\Controllers;



use Illuminate\Http\Request;

use Illuminate\Support\Facades\Auth;

use Illuminate\Support\Facades\Hash;

use App\Models\Attendance;

use App\Models\Sale;

use App\Models\Product;

use App\Models\EloadTransaction;

use Carbon\Carbon;



class UserController extends Controller

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

        

        // Get current month and year

        $month = $request->get('month', Carbon::now()->month);

        $year = $request->get('year', Carbon::now()->year);

        

        // Get attendance records for the current month

        $attendances = Attendance::where('user_id', $user->id)

            ->whereMonth('date', $month)

            ->whereYear('date', $year)

            ->orderBy('date', 'desc')

            ->get();

        

        // Calculate stats

        $totalDays = $attendances->count();

        $daysPresent = $attendances->whereNotNull('check_in')->count();

        $totalHoursWorked = 0;

        

        foreach ($attendances as $attendance) {

            if ($attendance->check_in && $attendance->check_out) {

                $checkIn = Carbon::parse($attendance->check_in);

                $checkOut = Carbon::parse($attendance->check_out);

                $totalHoursWorked += $checkIn->diffInHours($checkOut);

            }

        }

        

        $attendanceRate = $totalDays > 0 ? round(($daysPresent / $totalDays) * 100) : 0;

        

        // Get recent attendance (last 5 records)

        $recentAttendances = Attendance::where('user_id', $user->id)

            ->orderBy('date', 'desc')

            ->limit(5)

            ->get();

        

        // Get today's attendance status using 12-hour periods

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

        

        // Return view based on role

        if ($user->isEmployee()) {

            // Get products for employee dashboard
            $products = Product::orderBy('name')->get();

            return view('dashboard.employee', compact('attendances', 'totalDays', 'totalHoursWorked', 'attendanceRate', 'recentAttendances', 'month', 'year', 'canCheckIn', 'canCheckOut', 'todayAttendance', 'products'));

        }

        

        if ($user->isManager()) {

            // Get products for manager dashboard
            $products = Product::orderBy('name')->get();

            return view('dashboard.employee', compact('attendances', 'totalDays', 'totalHoursWorked', 'attendanceRate', 'recentAttendances', 'month', 'year', 'canCheckIn', 'canCheckOut', 'todayAttendance', 'products'));

        }

        

        // Fallback - user dashboard

        // Get products for dashboard
        $products = Product::orderBy('name')->get();

        return view('dashboard.employee', compact('attendances', 'totalDays', 'totalHoursWorked', 'attendanceRate', 'recentAttendances', 'month', 'year', 'canCheckIn', 'canCheckOut', 'todayAttendance', 'products'));

    }



    /**

     * Cashier Dashboard - renders cashier.blade.php

     */

    public function cashier(Request $request)

    {

        $user = Auth::user();

        

        // Validate user is actually a cashier - redirect to appropriate dashboard if not

        if (!$user->isCashier()) {

            if ($user->isEmployee()) {

                return redirect()->route('dashboard.employee')->with('error', 'Access denied. Please use your designated dashboard.');

            }

            if ($user->isAdmin()) {

                return redirect()->route('dashboard.admin')->with('error', 'Access denied. Please use your designated dashboard.');

            }

            if ($user->isSuperAdmin()) {

                return redirect()->route('dashboard.superadmin')->with('error', 'Access denied. Please use your designated dashboard.');

            }

            if ($user->isManager()) {

                return redirect()->route('dashboard.manager')->with('error', 'Access denied. Please use your designated dashboard.');

            }

            return redirect('/')->with('error', 'Access denied.');

        }

        

        // Get current month and year

        $month = $request->get('month', Carbon::now()->month);

        $year = $request->get('year', Carbon::now()->year);

        

        // Get attendance records for the current month

        $attendances = Attendance::where('user_id', $user->id)

            ->whereMonth('date', $month)

            ->whereYear('date', $year)

            ->orderBy('date', 'desc')

            ->get();

        

        // Calculate stats

        $totalDays = $attendances->count();

        $daysPresent = $attendances->whereNotNull('check_in')->count();

        $totalHoursWorked = 0;

        

        foreach ($attendances as $attendance) {

            if ($attendance->check_in && $attendance->check_out) {

                $checkIn = Carbon::parse($attendance->check_in);

                $checkOut = Carbon::parse($attendance->check_out);

                $totalHoursWorked += $checkIn->diffInHours($checkOut);

            }

        }

        

        $attendanceRate = $totalDays > 0 ? round(($daysPresent / $totalDays) * 100) : 0;

        

        // Get recent attendance (last 5 records)

        $recentAttendances = Attendance::where('user_id', $user->id)

            ->orderBy('date', 'desc')

            ->limit(5)

            ->get();

        

        // Get today's attendance status using 12-hour periods

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

        

        // Sales-related data for the cashier dashboard

        $todaySales = Sale::whereDate('created_at', Carbon::today())->sum('total_amount');

        $todayTransactions = Sale::whereDate('created_at', Carbon::today())->count();

        $totalProducts = Product::where('stock_quantity', '>', 0)->count();

        

        // Low stock count - use simpler query compatible with SQLite

        $allProducts = Product::all();

        $lowStockCount = $allProducts->filter(function($product) {

            return $product->stock_quantity <= $product->min_stock_level;

        })->count();

        

        // Additional sales stats for cashier

        $monthlySales = Sale::whereMonth('created_at', Carbon::now()->month)

            ->whereYear('created_at', Carbon::now()->year)

            ->sum('total_amount');

        $monthlyTransactions = Sale::whereMonth('created_at', Carbon::now()->month)

            ->whereYear('created_at', Carbon::now()->year)

            ->count();

        

        // E-Load related data for the cashier dashboard

        $todayEloadTransactions = EloadTransaction::whereDate('created_at', Carbon::today())->count();

        $todayEloadSales = EloadTransaction::whereDate('created_at', Carbon::today())

            ->where('status', 'completed')

            ->sum('price');

        

        return view('dashboard.cashier', compact(

            'attendances', 

            'totalDays', 

            'totalHoursWorked', 

            'attendanceRate', 

            'recentAttendances', 

            'month', 

            'year',

            'canCheckIn',

            'canCheckOut',

            'todayAttendance',

            'todaySales',

            'todayTransactions',

            'totalProducts',

            'lowStockCount',

            'monthlySales',

            'monthlyTransactions',

            'todayEloadTransactions',

            'todayEloadSales'

        ));

    }



    /**

     * Manager Dashboard

     */

    public function manager(Request $request)

    {

        $user = Auth::user();

        

        // Validate user is actually a manager

        if (!$user->isManager()) {

            if ($user->isEmployee()) {

                return redirect()->route('dashboard.employee')->with('error', 'Access denied. Please use your designated dashboard.');

            }

            if ($user->isCashier()) {

                return redirect()->route('dashboard.cashier')->with('error', 'Access denied. Please use your designated dashboard.');

            }

            if ($user->isAdmin()) {

                return redirect()->route('dashboard.admin')->with('error', 'Access denied. Please use your designated dashboard.');

            }

            if ($user->isSuperAdmin()) {

                return redirect()->route('dashboard.superadmin')->with('error', 'Access denied. Please use your designated dashboard.');

            }

            return redirect('/')->with('error', 'Access denied.');

        }

        

        // Get current month and year

        $month = $request->get('month', Carbon::now()->month);

        $year = $request->get('year', Carbon::now()->year);

        

        // Get attendance records for the current month

        $attendances = Attendance::where('user_id', $user->id)

            ->whereMonth('date', $month)

            ->whereYear('date', $year)

            ->orderBy('date', 'desc')

            ->get();

        

        // Calculate stats

        $totalDays = $attendances->count();

        $daysPresent = $attendances->whereNotNull('check_in')->count();

        $totalHoursWorked = 0;

        

        foreach ($attendances as $attendance) {

            if ($attendance->check_in && $attendance->check_out) {

                $checkIn = Carbon::parse($attendance->check_in);

                $checkOut = Carbon::parse($attendance->check_out);

                $totalHoursWorked += $checkIn->diffInHours($checkOut);

            }

        }

        

        $attendanceRate = $totalDays > 0 ? round(($daysPresent / $totalDays) * 100) : 0;

        

        // Get recent attendance (last 5 records)

        $recentAttendances = Attendance::where('user_id', $user->id)

            ->orderBy('date', 'desc')

            ->limit(5)

            ->get();

        

        // Get today's attendance status

        $today = Carbon::today()->format('Y-m-d');

        $todayAttendance = Attendance::where('user_id', $user->id)

            ->where('date', $today)

            ->first();

        

        $canCheckIn = false;

        $canCheckOut = false;

        

        if (!$todayAttendance) {

            // No attendance record today - can check in

            $canCheckIn = true;

        } elseif ($todayAttendance->check_in && !$todayAttendance->check_out) {

            // Checked in but not checked out - can check out

            $canCheckOut = true;

        }

        

        return view('dashboard.manager', compact(

            'attendances', 

            'totalDays', 

            'totalHoursWorked', 

            'attendanceRate', 

            'recentAttendances', 

            'month', 

            'year',

            'canCheckIn',

            'canCheckOut',

            'todayAttendance'

        ));

    }



    /**

     * Show user profile page

     */

    public function profile()

    {

        return view('dashboard.profile');

    }



    /**

     * Update user password

     */

    public function updatePassword(Request $request)

    {

        $user = Auth::user();

        

        // Only validate password if it's provided

        if ($request->filled('password')) {

            $request->validate([

                'password' => 'required|string|min:6|confirmed',

            ]);



            $user->update([

                'password' => Hash::make($request->password),

            ]);



            return redirect()->route('dashboard.profile')->with('success', 'Password updated successfully!');

        }



        return redirect()->route('dashboard.profile')->with('error', 'No password changes made.');

    }

}



