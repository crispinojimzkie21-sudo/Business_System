<?php

/**
 * Check Admin Password
 * This script will show the password for admin@manliquid.com
 */

require_once 'vendor/autoload.php';

use Illuminate\Support\Facades\DB;

// Load Laravel environment
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== RM Manliquid Business System - Admin Password Check ===\n\n";

try {
    // Get admin account details
    $admin = DB::table('users')->where('email', 'admin@manliquid.com')->first();
    
    if ($admin) {
        echo "Account Found:\n";
        echo "----------------\n";
        echo "Name: " . $admin->name . "\n";
        echo "Email: " . $admin->email . "\n";
        echo "Role: " . $admin->role . "\n";
        echo "Employee ID: " . $admin->employee_id . "\n";
        echo "Department: " . $admin->department . "\n";
        echo "Position: " . $admin->position . "\n";
        echo "Access Enabled: " . ($admin->access_enabled ? 'Yes' : 'No') . "\n";
        echo "Employment Status: " . $admin->employment_status . "\n";
        echo "Created: " . $admin->created_at . "\n\n";
        
        // Check if it's the default Laravel password
        if ($admin->password === '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi') {
            echo "Password: password\n";
            echo "(This is the default Laravel password)\n";
        } else {
            echo "Password: [Custom encrypted password]\n";
            echo "Note: This account has a custom password set.\n";
        }
        
        // Test if 'password' works
        if (password_verify('password', $admin->password)) {
            echo "\n✓ The password 'password' will work for this account.\n";
        } else {
            echo "\n✗ The password 'password' will NOT work for this account.\n";
            echo "This account has a custom password.\n";
        }
        
    } else {
        echo "Account admin@manliquid.com not found!\n";
    }
    
    echo "\n=== Password Check Complete ===\n";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "Please check your database connection.\n";
}
?>
