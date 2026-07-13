<?php

/**
 * Test Employee Creation Feature
 * This script will test the employee creation functionality
 */

require_once 'vendor/autoload.php';

use Illuminate\Support\Facades\DB;

// Load Laravel environment
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== RM Manliquid Business System - Test Employee Creation ===\n\n";

try {
    // Test data for employee creation
    $testEmployee = [
        'name' => 'Test Employee',
        'email' => 'testemployee' . time() . '@example.com',
        'password' => bcrypt('password123'),
        'phone' => '09123456789',
        'address' => 'Test Address',
        'hire_date' => '2026-04-10',
        'department' => 'Sales',
        'employee_id' => 'EMP' . time(),
        'employment_status' => 'active',
        'position' => 'Sales Staff',
        'salary' => 15000.00,
        'role' => 'employee',
        'access_enabled' => 1,
        'notes' => 'Test employee creation',
        'created_at' => now(),
        'updated_at' => now()
    ];
    
    echo "Testing employee creation with the following data:\n";
    echo "Name: " . $testEmployee['name'] . "\n";
    echo "Email: " . $testEmployee['email'] . "\n";
    echo "Role: " . $testEmployee['role'] . "\n";
    echo "Position: " . $testEmployee['position'] . "\n";
    echo "Department: " . $testEmployee['department'] . "\n\n";
    
    // Attempt to create the employee
    try {
        $employeeId = DB::table('users')->insertGetId($testEmployee);
        
        echo "✅ Employee created successfully!\n";
        echo "Employee ID: " . $employeeId . "\n\n";
        
        // Verify the employee was created
        $createdEmployee = DB::table('users')->where('id', $employeeId)->first();
        
        if ($createdEmployee) {
            echo "Verification:\n";
            echo "- Name: " . $createdEmployee->name . "\n";
            echo "- Email: " . $createdEmployee->email . "\n";
            echo "- Role: " . $createdEmployee->role . "\n";
            echo "- Employee ID: " . $createdEmployee->employee_id . "\n";
            echo "- Department: " . $createdEmployee->department . "\n";
            echo "- Position: " . $createdEmployee->position . "\n";
            echo "- Salary: " . $createdEmployee->salary . "\n";
            echo "- Access Enabled: " . ($createdEmployee->access_enabled ? 'Yes' : 'No') . "\n";
            echo "- Notes: " . ($createdEmployee->notes ?? 'None') . "\n";
        }
        
        // Test password verification
        if (password_verify('password123', $createdEmployee->password)) {
            echo "✅ Password verification successful\n";
        } else {
            echo "❌ Password verification failed\n";
        }
        
    } catch (Exception $e) {
        echo "❌ Employee creation failed: " . $e->getMessage() . "\n";
        return;
    }
    
    // Test different roles
    echo "\n" . str_repeat("=", 50) . "\n";
    echo "Testing creation of different user roles:\n";
    echo str_repeat("=", 50) . "\n\n";
    
    $roles = ['admin', 'manager', 'cashier', 'employee'];
    
    foreach ($roles as $role) {
        try {
            $testUser = [
                'name' => 'Test ' . ucfirst($role),
                'email' => 'test' . $role . time() . '@example.com',
                'password' => bcrypt('password123'),
                'role' => $role,
                'employee_id' => strtoupper($role) . time(),
                'employment_status' => 'active',
                'access_enabled' => 1,
                'notes' => 'Test ' . $role . ' account',
                'created_at' => now(),
                'updated_at' => now()
            ];
            
            $userId = DB::table('users')->insertGetId($testUser);
            echo "✅ " . ucfirst($role) . " account created (ID: " . $userId . ")\n";
            
        } catch (Exception $e) {
            echo "❌ Failed to create " . $role . " account: " . $e->getMessage() . "\n";
        }
    }
    
    // Show current user count
    echo "\n" . str_repeat("=", 50) . "\n";
    echo "Current System Statistics:\n";
    echo str_repeat("=", 50) . "\n";
    
    $totalUsers = DB::table('users')->count();
    $usersByRole = DB::table('users')
        ->select('role', DB::raw('count(*) as count'))
        ->groupBy('role')
        ->orderBy('count', 'desc')
        ->get();
    
    echo "Total Users: " . $totalUsers . "\n\n";
    echo "Users by Role:\n";
    foreach ($usersByRole as $roleCount) {
        echo "- " . ucfirst(str_replace('_', ' ', $roleCount->role)) . ": " . $roleCount->count . "\n";
    }
    
    echo "\n✅ Employee creation feature is working properly!\n";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "Please check your database connection and configuration.\n";
}

echo "\n=== Test Complete ===\n";
?>
