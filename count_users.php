<?php

/**
 * Count Total Users in System
 * This script will show the total number of users and their roles
 */

require_once 'vendor/autoload.php';

use Illuminate\Support\Facades\DB;

// Load Laravel environment
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== RM Manliquid Business System - User Count ===\n\n";

try {
    // Get total users count
    $totalUsers = DB::table('users')->count();
    echo "Total Users: " . $totalUsers . "\n\n";
    
    // Get users by role
    $usersByRole = DB::table('users')
        ->select('role', DB::raw('count(*) as count'))
        ->groupBy('role')
        ->orderBy('count', 'desc')
        ->get();
    
    echo "Users by Role:\n";
    echo "----------------\n";
    foreach ($usersByRole as $role) {
        echo ucfirst(str_replace('_', ' ', $role->role)) . ": " . $role->count . "\n";
    }
    
    // Get active vs inactive users
    $activeUsers = DB::table('users')->where('employment_status', 'active')->count();
    $inactiveUsers = DB::table('users')->where('employment_status', '!=', 'active')->count();
    
    echo "\nUser Status:\n";
    echo "------------\n";
    echo "Active Users: " . $activeUsers . "\n";
    echo "Inactive/Other: " . $inactiveUsers . "\n";
    
    // Get users with access enabled
    $accessEnabled = DB::table('users')->where('access_enabled', 1)->count();
    $accessDisabled = DB::table('users')->where('access_enabled', 0)->count();
    
    echo "\nAccess Status:\n";
    echo "-------------\n";
    echo "Access Enabled: " . $accessEnabled . "\n";
    echo "Access Disabled: " . $accessDisabled . "\n";
    
    // Show recent users (last 5)
    echo "\nRecent Users (Last 5):\n";
    echo "----------------------\n";
    $recentUsers = DB::table('users')
        ->select('name', 'email', 'role', 'created_at')
        ->orderBy('created_at', 'desc')
        ->limit(5)
        ->get();
    
    foreach ($recentUsers as $user) {
        echo "- " . $user->name . " (" . $user->email . ") - " . $user->role . "\n";
        echo "  Created: " . $user->created_at . "\n\n";
    }
    
    // Show all users summary
    if ($totalUsers <= 10) {
        echo "\nAll Users Summary:\n";
        echo "------------------\n";
        $allUsers = DB::table('users')
            ->select('name', 'email', 'role', 'employment_status', 'access_enabled')
            ->orderBy('created_at', 'desc')
            ->get();
        
        foreach ($allUsers as $user) {
            $status = $user->access_enabled ? '✓' : '✗';
            echo $status . " " . $user->name . " (" . $user->email . ")\n";
            echo "  Role: " . $user->role . " | Status: " . $user->employment_status . "\n\n";
        }
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "Please check your database connection.\n";
}

echo "\n=== User Count Complete ===\n";
?>
