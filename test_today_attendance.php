<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);

$kernel->bootstrap();

// Test attendance records for today
$timeData = (new \App\Http\Controllers\SuperAdminController())->getPhilippineTimeAndPeriod();
echo "Period Start: " . $timeData['period_start']->format('Y-m-d H:i:s') . "\n";

// Check for any attendance records today
$attendancesToday = \App\Models\Attendance::where('period_start', $timeData['period_start'])->get();

echo "Found {$attendancesToday->count()} attendance records for today:\n";
foreach ($attendancesToday as $attendance) {
    echo "  User: {$attendance->user->name} - Check In: " . ($attendance->check_in ?? 'NULL') . " - Check Out: " . ($attendance->check_out ?? 'NULL') . "\n";
}

if ($attendancesToday->count() > 0) {
    echo "\n✅ Attendance system is working!\n";
} else {
    echo "\n❌ No attendance records found for today!\n";
}
