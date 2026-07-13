<?php

/**
 * Fix Attendance Database Schema
 * This script will add missing columns to the attendances table
 */

require_once 'vendor/autoload.php';

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

// Load Laravel environment
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== RM Manliquid Business System - Fix Attendance Schema ===\n\n";

try {
    // Check current attendances table structure
    echo "Current attendances table structure:\n";
    echo "-----------------------------------\n";
    $columns = DB::select("PRAGMA table_info(attendances)");
    foreach ($columns as $column) {
        echo "- {$column->name}: {$column->type} (Nullable: " . ($column->notnull ? 'No' : 'Yes') . ")\n";
    }
    
    // Check and add missing columns
    $missingColumns = [
        'check_in_location' => 'TEXT NULL',
        'check_out_location' => 'TEXT NULL',
        'latitude' => 'REAL NULL',
        'longitude' => 'REAL NULL',
        'check_in_latitude' => 'REAL NULL',
        'check_in_longitude' => 'REAL NULL',
        'check_out_latitude' => 'REAL NULL',
        'check_out_longitude' => 'REAL NULL',
    ];
    
    echo "\nChecking and adding missing columns:\n";
    foreach ($missingColumns as $column => $definition) {
        if (Schema::hasColumn('attendances', $column)) {
            echo "✓ $column already exists\n";
        } else {
            echo "Adding $column...\n";
            DB::statement("ALTER TABLE attendances ADD COLUMN $column $definition");
            echo "✓ $column added successfully\n";
        }
    }
    
    // Show updated table structure
    echo "\nUpdated attendances table structure:\n";
    echo "-----------------------------------\n";
    $updatedColumns = DB::select("PRAGMA table_info(attendances)");
    foreach ($updatedColumns as $column) {
        echo "- {$column->name}: {$column->type} (Nullable: " . ($column->notnull ? 'No' : 'Yes') . ")\n";
    }
    
    // Check if there are any attendance records
    $attendanceCount = DB::table('attendances')->count();
    echo "\nCurrent attendance records: " . $attendanceCount . "\n";
    
    if ($attendanceCount > 0) {
        echo "\nRecent attendance records:\n";
        $recentAttendances = DB::table('attendances')
            ->select('id', 'user_id', 'date', 'check_in', 'check_out', 'location')
            ->orderBy('created_at', 'desc')
            ->limit(3)
            ->get();
            
        foreach ($recentAttendances as $attendance) {
            echo "- ID: {$attendance->id}, User ID: {$attendance->user_id}, Date: {$attendance->date}\n";
            echo "  Check In: " . ($attendance->check_in ?? 'N/A') . "\n";
            echo "  Check Out: " . ($attendance->check_out ?? 'N/A') . "\n";
            echo "  Location: " . ($attendance->location ?? 'N/A') . "\n\n";
        }
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

echo "\n=== Attendance Schema Fix Complete ===\n";
?>
