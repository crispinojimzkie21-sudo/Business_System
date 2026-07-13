<?php

/**
 * Fix Database - Add Missing Notes Column
 * This script will add the missing 'notes' column to the users table
 */

require_once 'vendor/autoload.php';

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

// Load Laravel environment
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== RM Manliquid Business System - Fix Database Schema ===\n\n";

try {
    // Check if notes column exists
    if (Schema::hasColumn('users', 'notes')) {
        echo "✓ Notes column already exists in users table\n";
    } else {
        echo "Adding missing 'notes' column to users table...\n";
        
        // Add the notes column
        DB::statement('ALTER TABLE users ADD COLUMN notes TEXT NULL');
        
        echo "✓ Notes column added successfully\n";
    }
    
    // Check if profile_image column exists
    if (Schema::hasColumn('users', 'profile_image')) {
        echo "✓ Profile image column already exists in users table\n";
    } else {
        echo "Adding missing 'profile_image' column to users table...\n";
        
        // Add the profile_image column
        DB::statement('ALTER TABLE users ADD COLUMN profile_image TEXT NULL');
        
        echo "✓ Profile image column added successfully\n";
    }
    
    // Check other potential missing columns
    $requiredColumns = [
        'id', 'name', 'email', 'password', 'phone', 'address', 'hire_date',
        'department', 'employee_id', 'employment_status', 'position', 'salary',
        'role', 'access_enabled', 'profile_image', 'email_verified_at',
        'remember_token', 'created_at', 'updated_at'
    ];
    
    echo "\nChecking all required columns:\n";
    foreach ($requiredColumns as $column) {
        if (Schema::hasColumn('users', $column)) {
            echo "✓ $column\n";
        } else {
            echo "✗ $column - MISSING\n";
        }
    }
    
    // Show current table structure
    echo "\nCurrent users table structure:\n";
    echo "--------------------------------\n";
    $columns = DB::select("PRAGMA table_info(users)");
    foreach ($columns as $column) {
        echo "- {$column->name}: {$column->type} (Nullable: " . ($column->notnull ? 'No' : 'Yes') . ")\n";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "Please check your database connection.\n";
}

echo "\n=== Database Fix Complete ===\n";
?>
