<?php

/**
 * Check Jash Account Status
 * This script will check the jash@example.com account status and fix login issues
 */

require_once 'vendor/autoload.php';

use Illuminate\Support\Facades\DB;

// Load Laravel environment
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== RM Manliquid Business System - Check Jash Account ===\n\n";

try {
    echo "Checking jash@example.com account status...\n";
    echo str_repeat("=", 50) . "\n";
    
    // Find the jash account
    $jashUser = DB::table('users')
        ->where('email', 'jash@example.com')
        ->first();
    
    if ($jashUser) {
        echo "Found jash@example.com account:\n";
        echo "- ID: {$jashUser->id}\n";
        echo "- Name: {$jashUser->name}\n";
        echo "- Email: {$jashUser->email}\n";
        echo "- Role: {$jashUser->role}\n";
        echo "- Access Enabled: " . ($jashUser->access_enabled ? 'Yes' : 'No') . "\n";
        echo "- Employee ID: " . ($jashUser->employee_id ?? 'N/A') . "\n";
        echo "- Created At: {$jashUser->created_at}\n";
        echo "- Updated At: {$jashUser->updated_at}\n";
        
        // Check if account is enabled
        if (!$jashUser->access_enabled) {
            echo "\nAccount is DISABLED. Enabling now...\n";
            
            $updated = DB::table('users')
                ->where('id', $jashUser->id)
                ->update([
                    'access_enabled' => 1,
                    'updated_at' => now(),
                ]);
            
            if ($updated) {
                echo "SUCCESS: Account has been enabled!\n";
            } else {
                echo "ERROR: Failed to enable account\n";
            }
        } else {
            echo "\nAccount is already enabled.\n";
        }
        
        // Check role permissions
        echo "\nChecking role permissions...\n";
        
        if ($jashUser->role === 'admin') {
            echo "Role: admin - Has admin access\n";
            echo "Can access: Employee management, user registration, etc.\n";
        } elseif ($jashUser->role === 'super_admin') {
            echo "Role: super_admin - Has full system access\n";
        } else {
            echo "Role: {$jashUser->role} - Limited access\n";
        }
        
        // Test login functionality
        echo "\nTesting login functionality...\n";
        
        // Check if password is set
        if ($jashUser->password) {
            echo "Password: Set (encrypted)\n";
        } else {
            echo "Password: Not set - This could be the issue!\n";
            echo "Setting a default password...\n";
            
            $defaultPassword = 'password123';
            $hashedPassword = password_hash($defaultPassword, PASSWORD_DEFAULT);
            
            $passwordUpdated = DB::table('users')
                ->where('id', $jashUser->id)
                ->update([
                    'password' => $hashedPassword,
                    'updated_at' => now(),
                ]);
            
            if ($passwordUpdated) {
                echo "SUCCESS: Default password set\n";
                echo "Default Password: {$defaultPassword}\n";
            } else {
                echo "ERROR: Failed to set default password\n";
            }
        }
        
        // Final status
        echo "\n" . str_repeat("=", 50) . "\n";
        echo "Final Account Status:\n";
        echo str_repeat("=", 50) . "\n";
        
        $finalUser = DB::table('users')->where('id', $jashUser->id)->first();
        
        echo "Email: {$finalUser->email}\n";
        echo "Name: {$finalUser->name}\n";
        echo "Role: {$finalUser->role}\n";
        echo "Access Enabled: " . ($finalUser->access_enabled ? 'Yes' : 'No') . "\n";
        echo "Password Set: " . ($finalUser->password ? 'Yes' : 'No') . "\n";
        
        echo "\nLogin Instructions:\n";
        echo "URL: http://127.0.0.1:8000/login\n";
        echo "Email: jash@example.com\n";
        echo "Password: password123 (if default was set)\n";
        echo "OR use your existing password if you remember it\n";
        
    } else {
        echo "ERROR: jash@example.com account not found in system\n";
        
        // Create the account if it doesn't exist
        echo "\nCreating jash@example.com account...\n";
        
        $newUserId = DB::table('users')->insertGetId([
            'name' => 'jash',
            'email' => 'jash@example.com',
            'password' => password_hash('password123', PASSWORD_DEFAULT),
            'role' => 'admin',
            'access_enabled' => 1,
            'employee_id' => 'JASH001',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        
        if ($newUserId) {
            echo "SUCCESS: Account created with ID: {$newUserId}\n";
            echo "Login Credentials:\n";
            echo "Email: jash@example.com\n";
            echo "Password: password123\n";
            echo "Role: admin\n";
        } else {
            echo "ERROR: Failed to create account\n";
        }
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . "\n";
    echo "Line: " . $e->getLine() . "\n";
}

echo "\n=== Check Complete ===\n";
?>
