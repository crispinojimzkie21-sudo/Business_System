<?php

/**
 * Fix Super Admin Account
 * This script will create or fix the super admin account
 */

require_once 'vendor/autoload.php';

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

// Load Laravel environment
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== RM Manliquid Business System - Super Admin Fix ===\n\n";

try {
    // Check if superadmin@example.com exists
    $existingAdmin = DB::table('users')->where('email', 'superadmin@example.com')->first();
    
    if ($existingAdmin) {
        echo "Found existing superadmin@example.com account\n";
        echo "Current details:\n";
        echo "- ID: " . $existingAdmin->id . "\n";
        echo "- Name: " . $existingAdmin->name . "\n";
        echo "- Role: " . $existingAdmin->role . "\n";
        echo "- Access Enabled: " . ($existingAdmin->access_enabled ? 'Yes' : 'No') . "\n";
        echo "- Employment Status: " . $existingAdmin->employment_status . "\n\n";
        
        // Update the account
        DB::table('users')
            ->where('email', 'superadmin@example.com')
            ->update([
                'name' => 'Super Administrator',
                'password' => Hash::make('password123'),
                'role' => 'super_admin',
                'access_enabled' => 1,
                'employment_status' => 'active',
                'employee_id' => 'SA001',
                'department' => 'IT',
                'position' => 'Super Administrator',
                'updated_at' => now()
            ]);
        
        echo "✓ Super admin account updated successfully\n";
        
    } else {
        echo "Creating new superadmin@example.com account\n";
        
        // Create new super admin account
        $adminId = DB::table('users')->insertGetId([
            'name' => 'Super Administrator',
            'email' => 'superadmin@example.com',
            'password' => Hash::make('password123'),
            'role' => 'super_admin',
            'access_enabled' => 1,
            'employment_status' => 'active',
            'employee_id' => 'SUPERADMIN001',
            'department' => 'IT',
            'position' => 'Super Administrator',
            'created_at' => now(),
            'updated_at' => now()
        ]);
        
        echo "✓ Super admin account created successfully\n";
        echo "✓ Account ID: " . $adminId . "\n";
    }
    
    // Verify the account
    $verifyAdmin = DB::table('users')->where('email', 'superadmin@example.com')->first();
    
    echo "\n=== Account Details ===\n";
    echo "Email: superadmin@example.com\n";
    echo "Password: password123\n";
    echo "Role: " . $verifyAdmin->role . "\n";
    echo "Access Enabled: " . ($verifyAdmin->access_enabled ? 'Yes' : 'No') . "\n";
    echo "Employment Status: " . $verifyAdmin->employment_status . "\n";
    
    // Test password verification
    if (Hash::check('password123', $verifyAdmin->password)) {
        echo "✓ Password verification successful\n";
    } else {
        echo "✗ Password verification failed\n";
    }
    
    echo "\n=== Login Instructions ===\n";
    echo "1. Go to your application login page\n";
    echo "2. Email: superadmin@example.com\n";
    echo "3. Password: password123\n";
    echo "4. Click 'Sign in'\n\n";
    
    echo "⚠️  IMPORTANT: Change the password after first login!\n";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "Please check your database connection and Laravel configuration.\n";
}

echo "\n=== Super Admin Fix Complete ===\n";
?>
