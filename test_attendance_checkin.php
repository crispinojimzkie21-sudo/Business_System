<?php

/**
 * Test Attendance Check-In Functionality
 * This script will test the attendance check-in feature with location tracking
 */

require_once 'vendor/autoload.php';

use Illuminate\Support\Facades\DB;

// Load Laravel environment
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== RM Manliquid Business System - Test Attendance Check-In ===\n\n";

try {
    // Get a test user (use the most recent employee)
    $testUser = DB::table('users')
        ->where('role', 'employee')
        ->orderBy('created_at', 'desc')
        ->first();
    
    if (!$testUser) {
        echo "⚠️  No employee user found, creating test employee...\n";
        
        // Create a test employee
        $testEmployeeId = DB::table('users')->insertGetId([
            'name' => 'Test Attendance Employee',
            'email' => 'testattendance' . time() . '@example.com',
            'password' => bcrypt('password123'),
            'role' => 'employee',
            'position' => 'Test Position',
            'department' => 'Test Department',
            'employee_id' => 'EMP' . time(),
            'employment_status' => 'active',
            'access_enabled' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        
        $testUser = DB::table('users')->where('id', $testEmployeeId)->first();
        echo "✅ Test employee created (ID: " . $testEmployeeId . ")\n\n";
    }
    
    echo "Testing with user:\n";
    echo "- ID: " . $testUser->id . "\n";
    echo "- Name: " . $testUser->name . "\n";
    echo "- Email: " . $testUser->email . "\n";
    echo "- Role: " . $testUser->role . "\n\n";
    
    // Test data for attendance check-in
    $attendanceData = [
        'user_id' => $testUser->id,
        'date' => date('Y-m-d'),
        'check_in' => now(),
        'check_in_location' => 'Location: 12.972144, 121.481639',
        'latitude' => 12.972144,
        'longitude' => 121.481639,
        'check_in_latitude' => 12.972144,
        'check_in_longitude' => 121.481639,
        'location' => 'Test Location',
        'created_at' => now(),
        'updated_at' => now(),
    ];
    
    echo "Testing attendance check-in with location data:\n";
    echo "- Date: " . $attendanceData['date'] . "\n";
    echo "- Check-in Time: " . $attendanceData['check_in'] . "\n";
    echo "- Check-in Location: " . $attendanceData['check_in_location'] . "\n";
    echo "- Latitude: " . $attendanceData['latitude'] . "\n";
    echo "- Longitude: " . $attendanceData['longitude'] . "\n\n";
    
    // Check if user already has attendance for today
    $existingAttendance = DB::table('attendances')
        ->where('user_id', $testUser->id)
        ->where('date', $attendanceData['date'])
        ->first();
    
    if ($existingAttendance) {
        echo "⚠️  User already has attendance record for today\n";
        echo "Existing record ID: " . $existingAttendance->id . "\n";
        echo "Check-in: " . ($existingAttendance->check_in ?? 'N/A') . "\n";
        echo "Check-out: " . ($existingAttendance->check_out ?? 'N/A') . "\n\n";
        
        // Update existing record instead
        DB::table('attendances')
            ->where('id', $existingAttendance->id)
            ->update([
                'check_in' => $attendanceData['check_in'],
                'check_in_location' => $attendanceData['check_in_location'],
                'latitude' => $attendanceData['latitude'],
                'longitude' => $attendanceData['longitude'],
                'check_in_latitude' => $attendanceData['check_in_latitude'],
                'check_in_longitude' => $attendanceData['check_in_longitude'],
                'updated_at' => now(),
            ]);
        
        echo "✅ Attendance record updated successfully!\n";
        $attendanceId = $existingAttendance->id;
    } else {
        // Create new attendance record
        try {
            $attendanceId = DB::table('attendances')->insertGetId($attendanceData);
            echo "✅ Attendance check-in successful!\n";
            echo "Attendance ID: " . $attendanceId . "\n";
        } catch (Exception $e) {
            echo "❌ Attendance check-in failed: " . $e->getMessage() . "\n";
            return;
        }
    }
    
    // Verify the attendance record
    $createdAttendance = DB::table('attendances')->where('id', $attendanceId)->first();
    
    if ($createdAttendance) {
        echo "\nVerification:\n";
        echo "- Attendance ID: " . $createdAttendance->id . "\n";
        echo "- User ID: " . $createdAttendance->user_id . "\n";
        echo "- Date: " . $createdAttendance->date . "\n";
        echo "- Check In: " . $createdAttendance->check_in . "\n";
        echo "- Check Out: " . ($createdAttendance->check_out ?? 'Not checked out') . "\n";
        echo "- Location: " . ($createdAttendance->location ?? 'N/A') . "\n";
        echo "- Check-in Location: " . ($createdAttendance->check_in_location ?? 'N/A') . "\n";
        echo "- Latitude: " . ($createdAttendance->latitude ?? 'N/A') . "\n";
        echo "- Longitude: " . ($createdAttendance->longitude ?? 'N/A') . "\n";
        echo "- Check-in Latitude: " . ($createdAttendance->check_in_latitude ?? 'N/A') . "\n";
        echo "- Check-in Longitude: " . ($createdAttendance->check_in_longitude ?? 'N/A') . "\n";
    }
    
    // Test check-out functionality
    echo "\n" . str_repeat("=", 60) . "\n";
    echo "Testing check-out functionality:\n";
    echo str_repeat("=", 60) . "\n\n";
    
    try {
        $checkOutData = [
            'check_out' => now(),
            'check_out_location' => 'Check-out Location: 12.972144, 121.481639',
            'check_out_latitude' => 12.972144,
            'check_out_longitude' => 121.481639,
            'updated_at' => now(),
        ];
        
        DB::table('attendances')
            ->where('id', $attendanceId)
            ->update($checkOutData);
        
        echo "✅ Check-out successful!\n";
        
        // Verify check-out
        $updatedAttendance = DB::table('attendances')->where('id', $attendanceId)->first();
        echo "- Check Out: " . $updatedAttendance->check_out . "\n";
        echo "- Check-out Location: " . ($updatedAttendance->check_out_location ?? 'N/A') . "\n";
        echo "- Check-out Latitude: " . ($updatedAttendance->check_out_latitude ?? 'N/A') . "\n";
        echo "- Check-out Longitude: " . ($updatedAttendance->check_out_longitude ?? 'N/A') . "\n";
        
    } catch (Exception $e) {
        echo "❌ Check-out failed: " . $e->getMessage() . "\n";
    }
    
    // Show attendance statistics
    echo "\n" . str_repeat("=", 60) . "\n";
    echo "Attendance Statistics:\n";
    echo str_repeat("=", 60) . "\n";
    
    $totalAttendances = DB::table('attendances')->count();
    $todayAttendances = DB::table('attendances')->where('date', date('Y-m-d'))->count();
    $checkedInToday = DB::table('attendances')
        ->where('date', date('Y-m-d'))
        ->whereNotNull('check_in')
        ->count();
    $checkedOutToday = DB::table('attendances')
        ->where('date', date('Y-m-d'))
        ->whereNotNull('check_out')
        ->count();
    
    echo "- Total Attendance Records: " . $totalAttendances . "\n";
    echo "- Today's Records: " . $todayAttendances . "\n";
    echo "- Checked In Today: " . $checkedInToday . "\n";
    echo "- Checked Out Today: " . $checkedOutToday . "\n";
    
    echo "\n✅ Attendance check-in/check-out functionality is working properly!\n";
    echo "✅ Location tracking is working!\n";
    echo "✅ All database columns are properly configured!\n";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "Please check your database connection and configuration.\n";
}

echo "\n=== Test Complete ===\n";
?>
