<?php

/**
 * Check User Accounts
 * This script will check existing user accounts in the system
 */

require_once 'vendor/autoload.php';

use Illuminate\Support\Facades\DB;

// Load Laravel environment
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== RM Manliquid Business System - User Accounts Information ===\n\n";

try {
    echo "Checking existing user accounts...\n";
    echo str_repeat("=", 50) . "\n";
    
    // Get all users
    $users = DB::table('users')
        ->select('id', 'name', 'email', 'role', 'employee_id', 'access_enabled')
        ->orderBy('id')
        ->get();
    
    echo "Total users: {$users->count()}\n\n";
    
    echo "User Accounts:\n";
    echo str_repeat("-", 50) . "\n";
    
    foreach ($users as $user) {
        $status = $user->access_enabled ? 'Enabled' : 'Disabled';
        echo "ID: {$user->id}\n";
        echo "Name: {$user->name}\n";
        echo "Email: {$user->email}\n";
        echo "Role: {$user->role}\n";
        echo "Employee ID: " . ($user->employee_id ?? 'N/A') . "\n";
        echo "Status: {$status}\n";
        echo str_repeat("-", 50) . "\n";
    }
    
    // Check for Gmail accounts specifically
    echo "\nGmail Accounts:\n";
    echo str_repeat("-", 50) . "\n";
    
    $gmailUsers = $users->filter(function($user) {
        return strpos($user->email, '@gmail.com') !== false;
    });
    
    if ($gmailUsers->count() > 0) {
        foreach ($gmailUsers as $gmailUser) {
            echo "ID: {$gmailUser->id}\n";
            echo "Name: {$gmailUser->name}\n";
            echo "Email: {$gmailUser->email}\n";
            echo "Role: {$gmailUser->role}\n";
            echo "Status: " . ($gmailUser->access_enabled ? 'Enabled' : 'Disabled') . "\n";
            echo str_repeat("-", 50) . "\n";
        }
    } else {
        echo "No Gmail accounts found in the system.\n";
    }
    
    // Show admin/super admin accounts
    echo "\nAdmin/Super Admin Accounts:\n";
    echo str_repeat("-", 50) . "\n";
    
    $adminUsers = $users->filter(function($user) {
        return in_array($user->role, ['super_admin', 'admin']);
    });
    
    if ($adminUsers->count() > 0) {
        foreach ($adminUsers as $adminUser) {
            echo "ID: {$adminUser->id}\n";
            echo "Name: {$adminUser->name}\n";
            echo "Email: {$adminUser->email}\n";
            echo "Role: {$adminUser->role}\n";
            echo "Status: " . ($adminUser->access_enabled ? 'Enabled' : 'Disabled') . "\n";
            echo str_repeat("-", 50) . "\n";
        }
    } else {
        echo "No admin accounts found in the system.\n";
    }
    
    echo "\n" . str_repeat("=", 50) . "\n";
    echo "Login Information:\n";
    echo str_repeat("=", 50) . "\n";
    echo "To login to the system, use:\n";
    echo "URL: http://127.0.0.1:8000/login\n";
    echo "Use any of the above email addresses and their corresponding passwords.\n";
    echo "\nNote: For security reasons, passwords are stored encrypted in the database\n";
    echo "and cannot be retrieved. If you need to reset a password, use the\n";
    echo "password reset functionality or contact an administrator.\n";
    
    echo "\nDefault Login Credentials (if available):\n";
    echo str_repeat("-", 50) . "\n";
    
    // Check for default accounts
    $defaultAccounts = [
        'admin@example.com',
        'superadmin@example.com',
        'test@example.com'
    ];
    
    foreach ($defaultAccounts as $defaultEmail) {
        $defaultUser = $users->firstWhere('email', $defaultEmail);
        if ($defaultUser) {
            echo "Email: {$defaultEmail}\n";
            echo "Default Password: (Contact administrator for password)\n";
            echo "Role: {$defaultUser->role}\n";
            echo str_repeat("-", 50) . "\n";
        }
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . "\n";
    echo "Line: " . $e->getLine() . "\n";
}

echo "\n=== Check Complete ===\n";
?>
