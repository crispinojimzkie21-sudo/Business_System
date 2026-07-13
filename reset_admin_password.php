<?php

/**
 * Reset Admin Password
 * This script will reset the admin@manliquid.com password
 */

require_once 'vendor/autoload.php';

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

// Load Laravel environment
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== RM Manliquid Business System - Reset Admin Password ===\n\n";

try {
    // Reset admin@manliquid.com password
    $updated = DB::table('users')
        ->where('email', 'admin@manliquid.com')
        ->update([
            'password' => Hash::make('admin123'),
            'updated_at' => now()
        ]);
    
    if ($updated) {
        echo "✓ Password reset successful for admin@manliquid.com\n\n";
        
        // Verify the reset
        $admin = DB::table('users')->where('email', 'admin@manliquid.com')->first();
        
        echo "Account Details:\n";
        echo "----------------\n";
        echo "Name: " . $admin->name . "\n";
        echo "Email: " . $admin->email . "\n";
        echo "Role: " . $admin->role . "\n";
        echo "Access Enabled: " . ($admin->access_enabled ? 'Yes' : 'No') . "\n\n";
        
        // Test the new password
        if (Hash::check('admin123', $admin->password)) {
            echo "✓ Password verification successful\n";
        } else {
            echo "✗ Password verification failed\n";
        }
        
        echo "\n=== New Login Credentials ===\n";
        echo "Email: admin@manliquid.com\n";
        echo "Password: admin123\n\n";
        
        echo "⚠️  Remember to change this password after login!\n";
        
    } else {
        echo "✗ Failed to reset password or account not found\n";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "Please check your database connection.\n";
}

echo "\n=== Password Reset Complete ===\n";
?>
