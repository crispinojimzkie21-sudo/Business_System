<?php

/**
 * Check Attendance Email Table
 * This script will check if the attendance_emails table exists and its structure
 */

require_once 'vendor/autoload.php';

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

// Load Laravel environment
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== RM Manliquid Business System - Check Attendance Email Table ===\n\n";

try {
    // Check if attendance_emails table exists
    if (Schema::hasTable('attendance_emails')) {
        echo "✓ attendance_emails table exists\n\n";
        
        // Show table structure
        echo "Table structure:\n";
        echo "----------------\n";
        $columns = DB::select("PRAGMA table_info(attendance_emails)");
        foreach ($columns as $column) {
            echo "- {$column->name}: {$column->type} (Nullable: " . ($column->notnull ? 'No' : 'Yes') . ")\n";
        }
        
        // Check if there's any data
        $count = DB::table('attendance_emails')->count();
        echo "\nCurrent records: " . $count . "\n";
        
        if ($count > 0) {
            echo "\nSample data:\n";
            $records = DB::table('attendance_emails')->limit(3)->get();
            foreach ($records as $record) {
                echo "- ID: {$record->id}, User ID: {$record->user_id}, Email: {$record->email}\n";
            }
        }
        
    } else {
        echo "✗ attendance_emails table does not exist\n";
        echo "Creating attendance_emails table...\n";
        
        // Create the table
        DB::statement('
            CREATE TABLE attendance_emails (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                user_id INTEGER NOT NULL,
                email TEXT NOT NULL,
                name TEXT NOT NULL,
                role TEXT NOT NULL,
                position TEXT NULL,
                is_active INTEGER DEFAULT 1,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                updated_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
            )
        ');
        
        echo "✓ attendance_emails table created successfully\n";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

echo "\n=== Check Complete ===\n";
?>
