<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Attendance;
use Carbon\Carbon;

class AdvancedAttendanceController extends Controller
{
    /**
     * Calculate distance between two GPS coordinates (Haversine formula)
     */
    private function calculateDistance($lat1, $lng1, $lat2, $lng2)
    {
        $earthRadius = 6371000; // Earth's radius in meters
        
        $latDiff = deg2rad($lat2 - $lat1);
        $lngDiff = deg2rad($lng2 - $lng1);
        
        $a = sin($latDiff/2) * sin($latDiff/2) +
             cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
             sin($lngDiff/2) * sin($lngDiff/2);
        
        $c = 2 * atan2(sqrt($a), sqrt(1-$a));
        
        return $earthRadius * $c; // Distance in meters
    }

    /**
     * Validate GPS location against allowed areas
     */
    private function validateLocation($latitude, $longitude)
    {
        $allowedLocations = [
            ['name' => 'Kapanggaan, Naga City', 'lat' => 13.6217, 'lng' => 123.1948, 'radius' => 500], // 500 meters
            ['name' => 'Main Office', 'lat' => 13.6220, 'lng' => 123.1950, 'radius' => 200], // 200 meters
            ['name' => 'Branch Office', 'lat' => 13.6300, 'lng' => 123.2000, 'radius' => 300], // 300 meters
        ];

        foreach ($allowedLocations as $location) {
            $distance = $this->calculateDistance($latitude, $longitude, $location['lat'], $location['lng']);
            if ($distance <= $location['radius']) {
                return [
                    'valid' => true,
                    'location' => $location['name'],
                    'distance' => round($distance, 2)
                ];
            }
        }

        return ['valid' => false, 'location' => null, 'distance' => null];
    }

    /**
     * Advanced check-in with GPS validation
     */
    public function advancedCheckIn(Request $request)
    {
        $userId = auth()->id();
        $now = Carbon::now('Asia/Manila');

        // Prevent double check-in within 5 minutes
        $recentCheckIn = Attendance::where('user_id', $userId)
            ->where('check_in', '>=', $now->copy()->subMinutes(5))
            ->whereNull('check_out')
            ->first();

        if ($recentCheckIn) {
            return back()->with('error', 'You recently checked in. Please wait a few minutes before trying again.');
        }

        // GPS Location Validation
        $latitude = $request->input('latitude');
        $longitude = $request->input('longitude');
        $locationValidation = $this->validateLocation($latitude, $longitude);

        if (!$locationValidation['valid']) {
            return back()->with('error', 'You are not within the authorized check-in area. Please check in from an allowed location.');
        }

        try {
            $attendance = Attendance::create([
                'user_id' => $userId,
                'date' => $now->format('Y-m-d'),
                'period' => $this->getPeriod($now),
                'check_in' => $now,
                'check_in_location' => $locationValidation['location'],
                'latitude' => $latitude,
                'longitude' => $longitude,
                'status' => 'Submitted',
                'gps_verified' => true,
            ]);

            return back()->with('success', '✅ GPS Verified! Checked in successfully at ' . $locationValidation['location'] . ' (' . $locationValidation['distance'] . 'm from reference point)');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to check in. Please try again.');
        }
    }

    /**
     * Advanced check-out with GPS validation
     */
    public function advancedCheckOut(Request $request)
    {
        $userId = auth()->id();
        $now = Carbon::now('Asia/Manila');

        // Find open attendance record
        $attendance = Attendance::where('user_id', $userId)
            ->whereNull('check_out')
            ->latest('check_in')
            ->first();

        if (!$attendance) {
            return back()->with('error', 'No active check-in found. Please check in first.');
        }

        // GPS Location Validation
        $latitude = $request->input('latitude');
        $longitude = $request->input('longitude');
        $locationValidation = $this->validateLocation($latitude, $longitude);

        if (!$locationValidation['valid']) {
            return back()->with('error', 'You are not within the authorized check-out area. Please check out from an allowed location.');
        }

        try {
            // Calculate working hours
            $checkInTime = Carbon::parse($attendance->check_in);
            $workingMinutes = $checkInTime->diffInMinutes($now);
            $workingHours = number_format($workingMinutes / 60, 2);

            $attendance->update([
                'check_out' => $now,
                'check_out_location' => $locationValidation['location'],
                'latitude_out' => $latitude,
                'longitude_out' => $longitude,
                'working_hours' => $workingHours,
                'status' => 'Completed',
                'gps_verified_out' => true,
            ]);

            return back()->with('success', '✅ GPS Verified! Checked out successfully from ' . $locationValidation['location'] . '. Working hours: ' . $workingHours . ' hours');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to check out. Please try again.');
        }
    }

    /**
     * Auto-close attendance for users who forgot to check out
     */
    public function autoCloseAttendance()
    {
        $now = Carbon::now('Asia/Manila');
        $cutoffTime = $now->copy()->setTime(23, 59, 59); // 11:59 PM

        // Find users who checked in but didn't check out
        $openAttendances = Attendance::whereNull('check_out')
            ->where('check_in', '<', $cutoffTime)
            ->get();

        $closedCount = 0;
        foreach ($openAttendances as $attendance) {
            $checkInTime = Carbon::parse($attendance->check_in);
            $workingMinutes = $checkInTime->diffInMinutes($cutoffTime);
            $workingHours = number_format($workingMinutes / 60, 2);

            $attendance->update([
                'check_out' => $cutoffTime,
                'check_out_location' => 'Auto-closed by system',
                'working_hours' => $workingHours,
                'status' => 'Auto-closed',
            ]);
            $closedCount++;
        }

        return response()->json([
            'message' => "Auto-closed {$closedCount} attendance records",
            'closed_count' => $closedCount
        ]);
    }

    /**
     * Get daily attendance summary for admin
     */
    public function dailyAttendanceSummary(Request $request)
    {
        $date = $request->get('date', Carbon::today()->format('Y-m-d'));
        
        $attendances = Attendance::with('user')
            ->whereDate('date', $date)
            ->orderBy('check_in')
            ->get();

        $summary = [
            'total_employees' => $attendances->count(),
            'checked_in' => $attendances->where('check_in', '!=', null)->count(),
            'checked_out' => $attendances->where('check_out', '!=', null)->count(),
            'still_working' => $attendances->where('check_in', '!=', null)->where('check_out', null)->count(),
            'total_working_hours' => $attendances->sum('working_hours'),
            'average_working_hours' => $attendances->avg('working_hours'),
            'on_time' => $attendances->filter(function($att) {
                return $att->check_in && Carbon::parse($att->check_in)->format('H:i') <= '09:00';
            })->count(),
            'late' => $attendances->filter(function($att) {
                return $att->check_in && Carbon::parse($att->check_in)->format('H:i') > '09:00';
            })->count(),
        ];

        return view('admin.attendance-summary', compact('attendances', 'summary', 'date'));
    }

    /**
     * Get attendance period based on time
     */
    private function getPeriod($now)
    {
        $hour = $now->hour;
        return ($hour >= 6 && $hour < 18) ? 'morning' : 'evening';
    }

    /**
     * Prevent double check-in validation
     */
    public function preventDoubleCheckIn($userId)
    {
        $today = Carbon::today('Asia/Manila');
        
        $existingCheckIn = Attendance::where('user_id', $userId)
            ->whereDate('check_in', $today)
            ->whereNull('check_out')
            ->first();

        return $existingCheckIn ? true : false; // true if exists (should prevent)
    }

    /**
     * Working hours computation for payroll
     */
    public function computeWorkingHours($startDate, $endDate)
    {
        $attendances = Attendance::with('user')
            ->whereBetween('date', [$startDate, $endDate])
            ->where('status', 'Completed')
            ->get();

        $userHours = [];
        
        foreach ($attendances as $attendance) {
            $userId = $attendance->user_id;
            $userName = $attendance->user->name;
            
            if (!isset($userHours[$userId])) {
                $userHours[$userId] = [
                    'name' => $userName,
                    'total_hours' => 0,
                    'total_days' => 0,
                    'average_hours' => 0,
                    'overtime_hours' => 0,
                ];
            }
            
            $userHours[$userId]['total_hours'] += $attendance->working_hours ?? 0;
            $userHours[$userId]['total_days']++;
            
            // Calculate overtime (more than 8 hours)
            $dailyHours = $attendance->working_hours ?? 0;
            if ($dailyHours > 8) {
                $userHours[$userId]['overtime_hours'] += ($dailyHours - 8);
            }
        }

        // Calculate averages
        foreach ($userHours as $userId => &$data) {
            $data['average_hours'] = $data['total_days'] > 0 ? 
                round($data['total_hours'] / $data['total_days'], 2) : 0;
        }

        return $userHours;
    }
}
