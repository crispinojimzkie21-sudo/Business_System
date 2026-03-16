<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);

$kernel->bootstrap();

// Test attendance logic
$timeData = (new \App\Http\Controllers\SuperAdminController())->getPhilippineTimeAndPeriod();
echo "Current Time: " . $timeData['now']->format('Y-m-d H:i:s') . "\n";
echo "Period: " . $timeData['period'] . "\n";
echo "Period Start: " . $timeData['period_start']->format('Y-m-d H:i:s') . "\n";
echo "Period End: " . $timeData['period_end']->format('Y-m-d H:i:s') . "\n";

// Check for existing attendance
$userId = 1; // Use Super Admin ID
$existingAttendance = \App\Models\Attendance::where('user_id', $userId)
    ->where('period_start', $timeData['period_start'])
    ->first();

if ($existingAttendance) {
    echo "Found existing attendance:\n";
    echo "  Check In: " . ($existingAttendance->check_in ?? 'NULL') . "\n";
    echo "  Check Out: " . ($existingAttendance->check_out ?? 'NULL') . "\n";
} else {
    echo "No existing attendance found for this period.\n";
}
