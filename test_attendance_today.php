<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use App\Models\Attendance;
use Carbon\Carbon;

echo "Creating test attendance record for today...\n";

// Get current Philippine date
$todayPhilippine = Carbon::now('Asia/Manila')->format('Y-m-d');
echo "Today's date (PHT): " . $todayPhilippine . "\n";

// Get a test user (employee)
$user = User::where('role', 'employee')->first();
if (!$user) {
    echo "No employee found. Creating test employee...\n";
    $user = User::create([
        'name' => 'Test Employee',
        'email' => 'test@employee.com',
        'password' => bcrypt('password123'),
        'role' => 'employee',
        'position' => 'Sales Clerk',
        'access_enabled' => true,
    ]);
    echo "Created test employee: " . $user->name . " (ID: " . $user->id . ")\n";
}

// Check if attendance already exists for today
$existingAttendance = Attendance::where('user_id', $user->id)
    ->where('date', $todayPhilippine)
    ->first();

if ($existingAttendance) {
    echo "Attendance already exists for today:\n";
    echo "ID: " . $existingAttendance->id . "\n";
    echo "Date: " . $existingAttendance->date . "\n";
    echo "Check In: " . ($existingAttendance->check_in ? Carbon::parse($existingAttendance->check_in)->format('H:i:s') : 'N/A') . "\n";
    echo "Check Out: " . ($existingAttendance->check_out ? Carbon::parse($existingAttendance->check_out)->format('H:i:s') : 'N/A') . "\n";
} else {
    // Create test attendance record
    $attendance = Attendance::create([
        'user_id' => $user->id,
        'date' => $todayPhilippine,
        'period' => 'morning',
        'period_start' => Carbon::now('Asia/Manila')->setTime(6, 0, 0),
        'period_end' => Carbon::now('Asia/Manila')->setTime(17, 59, 59),
        'check_in' => Carbon::now('Asia/Manila'),
        'check_in_location' => 'Test Location',
        'latitude' => 14.5995,
        'longitude' => 120.9842,
    ]);

    echo "Created test attendance record:\n";
    echo "ID: " . $attendance->id . "\n";
    echo "User: " . $attendance->user->name . "\n";
    echo "Date: " . $attendance->date . "\n";
    echo "Check In: " . Carbon::parse($attendance->check_in)->format('H:i:s') . "\n";
    echo "Period: " . $attendance->period . "\n";
}

// Show total attendance records
$totalAttendance = Attendance::count();
$todayAttendance = Attendance::where('date', $todayPhilippine)->count();

echo "\nSummary:\n";
echo "Total attendance records: " . $totalAttendance . "\n";
echo "Today's attendance records: " . $todayAttendance . "\n";
echo "Today's date (PHT): " . $todayPhilippine . "\n";

echo "\nTest completed! Check the attendance page at http://127.0.0.1:8000/attendance\n";
