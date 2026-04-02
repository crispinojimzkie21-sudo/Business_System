<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\User;
use Illuminate\Http\Request;
use Carbon\Carbon;

class AttendanceController extends Controller
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
    public function index()
    {
        // Get current Philippine date
        $philippineTime = Carbon::now('Asia/Manila');
        $today = $philippineTime->format('Y-m-d');
        
        // For debugging: Let's get all attendance records first to see if any exist
        $allAttendance = Attendance::with('user')
            ->orderBy('created_at', 'desc')
            ->get();
            
        // Get today's attendance - use DATE() function to compare date part only
        $todayAttendance = Attendance::with('user')
            ->whereDate('date', $today)
            ->orderBy('created_at', 'desc')
            ->get()
            ->groupBy('user_id');

        // For debugging: Log the counts
        \Log::info("Total attendance records: " . $allAttendance->count());
        \Log::info("Today's attendance records: " . $todayAttendance->count());
        \Log::info("Today's date (Philippine): " . $today);

        return view('attendance.index', compact('todayAttendance', 'allAttendance'));
    }

    public function records(Request $request)
    {
        $user = auth()->user();
        
        // Regular employees, cashiers, and managers can only see their own records
        if ($user->isEmployee() || $user->isCashier() || $user->isManager()) {
            $attendances = Attendance::where('user_id', $user->id)
                ->orderBy('created_at', 'desc')
                ->paginate(50);
            
            return view('attendance.records', compact('attendances'));
        }
        
        // Admins and super admins can see all records
        $attendances = Attendance::with('user')
            ->orderBy('created_at', 'desc')
            ->paginate(50);

        return view('attendance.records', compact('attendances'));
    }

    public function checkIn(Request $request)
    {
        $userId = auth()->id();
        $timeData = $this->getPhilippineTimeAndPeriod();
        $now = $timeData['now'];
        $periodStart = $timeData['period_start'];
        $dateKey = $timeData['date_key'];

        // Check if user already has a check-in for today (once per day only)
        $existingAttendance = Attendance::where('user_id', $userId)
            ->whereDate('check_in', $now->format('Y-m-d'))
            ->first();

        if ($existingAttendance) {
            return back()->with('error', 'You have already checked in today. You can only check in once per day.');
        }

        // Check if user already has a check-in for this 12-hour period
        $existingAttendance = Attendance::where('user_id', $userId)
            ->where('period_start', $periodStart)
            ->first();

        if ($existingAttendance) {
            if ($existingAttendance->check_in && !$existingAttendance->check_out) {
                return back()->with('error', 'You are already checked in for this ' . $timeData['period'] . ' period. Please check out first.');
            } elseif ($existingAttendance->check_out) {
                return back()->with('error', 'You have already completed your attendance for this ' . $timeData['period'] . ' period.');
            }
        }

        // Additional check: Prevent multiple check-ins within 5 minutes
        $recentCheckIn = Attendance::where('user_id', $userId)
            ->where('check_in', '>=', $now->copy()->subMinutes(5))
            ->first();

        if ($recentCheckIn) {
            return back()->with('error', 'You recently checked in. Please wait a few minutes before trying again.');
        }

        // Get location data from request
        $latitude = $request->input('latitude');
        $longitude = $request->input('longitude');
        $locationName = $request->input('location_name', 'Unknown Location');

        // Handle location unavailable cases
        if (in_array($locationName, ['Location unavailable', 'Location denied', 'Position unavailable', 'Timeout', 'Geolocation not supported'])) {
            $locationName = 'Manual Location';
        }

        try {
            $attendance = Attendance::create([
                'user_id' => $userId,
                'date' => $now->format('Y-m-d'),
                'period' => $timeData['period'],
                'period_start' => $periodStart,
                'period_end' => $timeData['period_end'],
                'check_in' => $now,
                'check_in_location' => $locationName,
                'latitude' => $latitude,
                'longitude' => $longitude,
            ]);

            return back()->with('success', 'Checked in successfully at ' . $now->format('h:i A') . ' for ' . ucfirst($timeData['period']) . ' period.');
        } catch (\Exception $e) {
            \Log::error('Check-in failed: ' . $e->getMessage());
            return back()->with('error', 'Failed to check in. Please try again. Error: ' . $e->getMessage());
        }
    }

    /**
     * Check in at Kapanggaan location
     */
    public function checkInKapanggaan(Request $request)
    {
        $userId = auth()->id();
        $timeData = $this->getPhilippineTimeAndPeriod();
        $now = $timeData['now'];
        $periodStart = $timeData['period_start'];

        // Check if user already has a check-in for today (once per day only)
        $existingAttendance = Attendance::where('user_id', $userId)
            ->whereDate('check_in', $now->format('Y-m-d'))
            ->first();

        if ($existingAttendance) {
            return back()->with('error', 'You have already checked in today. You can only check in once per day.');
        }
        
        // Allow check-out even if already checked in from Kapanggaan
        if ($existingAttendance && $existingAttendance->check_in && !$existingAttendance->check_out) {
            return back()->with('success', 'You are already checked in from Kapanggaan. You can check out now.');
        }

        // Create attendance record for Kapanggaan
        try {
            $attendance = Attendance::create([
                'user_id' => $userId,
                'date' => $now->format('Y-m-d'),
                'period' => $timeData['period'],
                'period_start' => $periodStart,
                'period_end' => $timeData['period_end'],
                'check_in' => $now,
                'check_in_location' => 'Kapanggaan, Naga City',
                'latitude' => 13.6217, // Kapanggaan coordinates
                'longitude' => 123.1948,
            ]);

            return back()->with('success', 'Checked in successfully at Kapanggaan, Naga City at ' . $now->format('h:i A') . ' for ' . ucfirst($timeData['period']) . ' period.');
        } catch (\Exception $e) {
            \Log::error('Kapanggaan check-in failed: ' . $e->getMessage());
            return back()->with('error', 'Failed to check in at Kapanggaan. Please try again.');
        }
    }

    /**
     * Check out at Kapanggaan location
     */
    public function checkOutKapanggaan(Request $request)
    {
        $userId = auth()->id();
        $timeData = $this->getPhilippineTimeAndPeriod();
        $now = $timeData['now'];
        $periodStart = $timeData['period_start'];

        // Find today's check-in without check-out (once per day only)
        $attendance = Attendance::where('user_id', $userId)
            ->whereDate('check_in', $now->format('Y-m-d'))
            ->whereNotNull('check_in')
            ->whereNull('check_out')
            ->first();

        if (!$attendance) {
            // Check if already checked out today
            $alreadyCheckedOut = Attendance::where('user_id', $userId)
                ->whereDate('check_out', $now->format('Y-m-d'))
                ->whereNotNull('check_out')
                ->first();
                
            if ($alreadyCheckedOut) {
                return back()->with('error', 'You have already checked out today. You can only check out once per day.');
            }
            
            return back()->with('error', 'You need to check in first before you can check out.');
        }

        try {
            $checkInTime = Carbon::parse($attendance->check_in);
            $hoursWorked = $checkInTime->diffInMinutes($now);
            $hoursWorkedFormatted = floor($hoursWorked / 60) . 'h ' . ($hoursWorked % 60) . 'm';

            $attendance->update([
                'check_out' => $now,
                'check_out_location' => 'Kapanggaan, Naga City',
            ]);

            return back()->with('success', 'Checked out successfully from Kapanggaan at ' . $now->format('h:i A') . '. Hours worked: ' . $hoursWorkedFormatted);
        } catch (\Exception $e) {
            \Log::error('Kapanggaan check-out failed: ' . $e->getMessage());
            return back()->with('error', 'Failed to check out from Kapanggaan. Please try again.');
        }
    }

    public function checkOut(Request $request)
    {
        $userId = auth()->id();
        $timeData = $this->getPhilippineTimeAndPeriod();
        $now = $timeData['now'];
        $periodStart = $timeData['period_start'];

        // Find today's check-in without check-out (once per day only)
        $attendance = Attendance::where('user_id', $userId)
            ->whereDate('check_in', $now->format('Y-m-d'))
            ->whereNotNull('check_in')
            ->whereNull('check_out')
            ->first();

        if (!$attendance) {
            // Check if already checked out today
            $alreadyCheckedOut = Attendance::where('user_id', $userId)
                ->whereDate('check_out', $now->format('Y-m-d'))
                ->whereNotNull('check_out')
                ->first();
                
            if ($alreadyCheckedOut) {
                return back()->with('error', 'You have already checked out today. You can only check out once per day.');
            }
            
            // Check if there was a recent check-out (within 5 minutes)
            $recentCheckOut = Attendance::where('user_id', $userId)
                ->where('check_out', '>=', $now->copy()->subMinutes(5))
                ->first();
                
            if ($recentCheckOut) {
                return back()->with('error', 'You recently checked out. Please wait a few minutes before trying again.');
            }
            
            return back()->with('error', 'You need to check in first before you can check out.');
        }

        // Get location data from request
        $latitude = $request->input('latitude');
        $longitude = $request->input('longitude');
        $locationName = $request->input('location_name', 'Unknown Location');

        // Handle location unavailable cases
        if (in_array($locationName, ['Location unavailable', 'Location denied', 'Position unavailable', 'Timeout', 'Geolocation not supported'])) {
            $locationName = 'Manual Location';
        }

        try {
            $checkInTime = Carbon::parse($attendance->check_in);
            $hoursWorked = $checkInTime->diffInMinutes($now);
            $hoursWorkedFormatted = floor($hoursWorked / 60) . 'h ' . ($hoursWorked % 60) . 'm';

            $attendance->update([
                'check_out' => $now,
                'check_out_location' => $locationName,
            ]);

            return back()->with('success', 'Checked out successfully at ' . $now->format('h:i A') . '. Hours worked: ' . $hoursWorkedFormatted);
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to check out. Please try again.');
        }
    }

    /**
     * Admin check-out for any user (Super Admin and Admin only)
     */
    public function adminCheckOut(Request $request, $userId)
    {
        $user = auth()->user();
        
        // Only admins and super admins can use this feature
        if (!$user->isSuperAdmin() && !$user->isAdmin()) {
            return back()->with('error', 'You do not have permission to check out other users.');
        }

        $targetUser = User::find($userId);
        if (!$targetUser) {
            return back()->with('error', 'User not found.');
        }

        $philippineTime = Carbon::now('Asia/Manila');
        $today = $philippineTime->format('Y-m-d');

        // Find today's check-in without check-out for the target user
        $attendance = Attendance::where('user_id', $userId)
            ->where('date', $today)
            ->whereNotNull('check_in')
            ->whereNull('check_out')
            ->first();

        if (!$attendance) {
            // Check if already checked out today
            $alreadyCheckedOut = Attendance::where('user_id', $userId)
                ->where('date', $today)
                ->whereNotNull('check_in')
                ->whereNotNull('check_out')
                ->first();
                
            if ($alreadyCheckedOut) {
                return back()->with('error', 'This user has already checked out for today.');
            }
            
            // Check if there's an open check-in from a previous day
            // Auto close any open check-ins older than 7 days for target user
            Attendance::where('user_id', $userId)
                ->whereNotNull('check_in')
                ->whereNull('check_out')
                ->whereDate('check_in', '<', Carbon::now()->subDays(7))
                ->update(['check_out' => Carbon::now()]);
            return back()->with('error', 'No check-in record found for this user today.');
        }

        // Get location data from request
        $latitude = $request->input('latitude');
        $longitude = $request->input('longitude');
        $locationName = $request->input('location_name', 'Admin Check-out');

        // Handle location unavailable cases
        if (in_array($locationName, ['Location unavailable', 'Location denied', 'Position unavailable', 'Timeout', 'Geolocation not supported'])) {
            $locationName = 'Admin Check-out';
        }

        try {
            $checkInTime = Carbon::parse($attendance->check_in);
            $checkOutTime = Carbon::now();
            $hoursWorked = $checkInTime->diffInMinutes($checkOutTime);
            $hoursWorkedFormatted = floor($hoursWorked / 60) . 'h ' . ($hoursWorked % 60) . 'm';

            $attendance->update([
                'check_out' => $checkOutTime,
                'check_out_location' => $locationName,
            ]);

            return back()->with('success', 'Checked out ' . $targetUser->name . ' successfully at ' . $checkOutTime->format('h:i A') . '. Hours worked: ' . $hoursWorkedFormatted);
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to check out user. Please try again.');
        }
    }

    public function userReport($userId)
    {
        $user = User::findOrFail($userId);
        $attendances = Attendance::where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->paginate(30);

        return view('attendance.user-report', compact('user', 'attendances'));
    }

    public function monthlyReport()
    {
        $month = request('month', Carbon::now()->month);
        $year = request('year', Carbon::now()->year);

        $attendances = Attendance::with('user')
            ->whereMonth('date', $month)
            ->whereYear('date', $year)
            ->orderBy('created_at', 'desc')
            ->get()
            ->groupBy('user_id');

        return view('attendance.monthly-report', compact('attendances', 'month', 'year'));
    }

    public function exportReport()
    {
        $month = request('month', Carbon::now()->month);
        $year = request('year', Carbon::now()->year);

        $attendances = Attendance::with('user')
            ->whereMonth('date', $month)
            ->whereYear('date', $year)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('attendance.export', compact('attendances', 'month', 'year'));
    }

    /**
     * Delete (soft delete) an attendance record
     */
    public function destroy($id)
    {
        $attendance = Attendance::findOrFail($id);
        $attendance->delete();

        return redirect()->back()->with('success', 'Attendance record deleted successfully.');
    }

    /**
     * Restore a soft deleted attendance record
     */
    public function restore($id)
    {
        $attendance = Attendance::withTrashed()->findOrFail($id);
        $attendance->restore();

        return redirect()->back()->with('success', 'Attendance record restored successfully.');
    }

    /**
     * Show all deleted attendance records (trash)
     * Only accessible by admins and super admins
     */
    public function trashed()
    {
        $user = auth()->user();
        
        // Only admins and super admins can view trashed records
        if (!$user->isSuperAdmin() && !$user->isAdmin()) {
            return redirect()->route('attendance.records')->with('error', 'You do not have permission to view trashed records.');
        }
        
        $trashedAttendances = Attendance::onlyTrashed()
            ->with('user')
            ->orderBy('deleted_at', 'desc')
            ->paginate(50);

        return view('attendance.trashed', compact('trashedAttendances'));
    }

    /**
     * Permanently delete an attendance record
     */
    public function forceDelete($id)
    {
        $attendance = Attendance::withTrashed()->findOrFail($id);
        $attendance->forceDelete();

        return redirect()->back()->with('success', 'Attendance record permanently deleted.');
    }

    /**
     * Bulk delete attendance records
     */
    public function bulkDelete(Request $request)
    {
        $user = auth()->user();
        
        // Only super admins and admins can bulk delete
        if (!$user->isSuperAdmin() && !$user->isAdmin()) {
            $response = ['error' => 'You do not have permission to delete attendance records.'];
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json($response, 403);
            }
            return redirect()->back()->with('error', $response['error']);
        }

        $request->validate([
            'attendance_ids' => 'required|array',
            'attendance_ids.*' => 'integer|exists:attendances,id'
        ]);

        $attendanceIds = $request->input('attendance_ids', []);

        if (empty($attendanceIds)) {
            $response = ['error' => 'No attendance records selected.'];
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json($response, 400);
            }
            return redirect()->back()->with('error', $response['error']);
        }

        $deletedCount = Attendance::whereIn('id', $attendanceIds)->delete();
        $successMessage = "{$deletedCount} attendance record(s) deleted successfully.";

        // Handle AJAX requests
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => $successMessage,
                'deleted_count' => $deletedCount,
                'redirect' => $request->url()
            ]);
        }

        // Handle regular form submissions
        return redirect()->back()->with('success', $successMessage);
    }

}
