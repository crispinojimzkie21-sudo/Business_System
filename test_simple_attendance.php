<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);

$kernel->bootstrap();

// Simple test to check attendance records
echo "Testing attendance records...\n";

// Get current period using same logic as controller
$now = \Carbon\Carbon::now('Asia/Manila');
$hour = $now->hour;

if ($hour >= 6 && $hour < 18) {
    $period = 'morning';
    $periodStart = $now->copy()->setTime(6, 0, 0);
} else {
    $period = 'evening';
    if ($hour >= 18) {
        $periodStart = $now->copy()->setTime(18, 0, 0);
    } else {
        $periodStart = $now->copy()->subDay()->setTime(18, 0, 0);
    }
}

echo "Current Period: {$period}\n";
echo "Period Start: {$periodStart->format('Y-m-d H:i:s')}\n";

// Check for any attendance records for this period
$attendancesToday = \App\Models\Attendance::where('period_start', $periodStart)->get();

echo "Found {$attendancesToday->count()} attendance records for today:\n";

foreach ($attendancesToday as $attendance) {
    echo "  User: {$attendance->user->name} - Check In: " . ($attendance->check_in ?? 'NULL') . " - Check Out: " . ($attendance->check_out ?? 'NULL') . "\n";
}

if ($attendancesToday->count() > 0) {
    echo "\n✅ Attendance system is working!\n";
} else {
    echo "\n❌ No attendance records found for today!\n";
}
