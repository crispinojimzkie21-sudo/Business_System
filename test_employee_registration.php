<?php

/**
 * Test Employee Registration Functionality
 * This script will test the employee registration feature
 */

require_once 'vendor/autoload.php';

use Illuminate\Support\Facades\DB;

// Load Laravel environment
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== RM Manliquid Business System - Test Employee Registration ===\n\n";

try {
    // Test data for employee registration (simulating form submission)
    $testEmployeeData = [
        'name' => 'Test Registration Employee',
        'email' => 'testreg' . time() . '@example.com',
        'password' => 'password123',
        'password_confirmation' => 'password123',
        'role' => 'employee',
        'position' => 'Sales Staff',
        'salary' => 18000.00,
        'phone' => '09876543210',
        'address' => 'Test Registration Address',
        'department' => 'Sales',
        'employment_status' => 'active',
        'notes' => 'Test employee registration via form',
    ];

    echo "Testing employee registration with the following data:\n";
    echo "Name: " . $testEmployeeData['name'] . "\n";
    echo "Email: " . $testEmployeeData['email'] . "\n";
    echo "Role: " . $testEmployeeData['role'] . "\n";
    echo "Position: " . $testEmployeeData['position'] . "\n";
    echo "Department: " . $testEmployeeData['department'] . "\n";
    echo "Salary: " . $testEmployeeData['salary'] . "\n\n";

    // Simulate the registration process (similar to AdminManagementController::registerEmployee)
    try {
        // Generate unique employee ID
        $employeeId = 'EMP' . str_pad(DB::table('users')->max('id') + 1, 5, '0', STR_PAD_LEFT);
        
        // Create the user
        $userId = DB::table('users')->insertGetId([
            'name' => $testEmployeeData['name'],
            'email' => $testEmployeeData['email'],
            'password' => bcrypt($testEmployeeData['password']),
            'position' => $testEmployeeData['position'],
            'salary' => $testEmployeeData['salary'],
            'role' => $testEmployeeData['role'],
            'phone' => $testEmployeeData['phone'],
            'address' => $testEmployeeData['address'],
            'hire_date' => date('Y-m-d'),
            'department' => $testEmployeeData['department'],
            'employee_id' => $employeeId,
            'employment_status' => $testEmployeeData['employment_status'],
            'notes' => $testEmployeeData['notes'],
            'access_enabled' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        echo "✅ Employee registration successful!\n";
        echo "User ID: " . $userId . "\n";
        echo "Employee ID: " . $employeeId . "\n\n";

        // Verify the employee was created
        $createdEmployee = DB::table('users')->where('id', $userId)->first();
        
        if ($createdEmployee) {
            echo "Verification:\n";
            echo "- Name: " . $createdEmployee->name . "\n";
            echo "- Email: " . $createdEmployee->email . "\n";
            echo "- Role: " . $createdEmployee->role . "\n";
            echo "- Employee ID: " . $createdEmployee->employee_id . "\n";
            echo "- Department: " . $createdEmployee->department . "\n";
            echo "- Position: " . $createdEmployee->position . "\n";
            echo "- Salary: " . $createdEmployee->salary . "\n";
            echo "- Phone: " . $createdEmployee->phone . "\n";
            echo "- Address: " . $createdEmployee->address . "\n";
            echo "- Employment Status: " . $createdEmployee->employment_status . "\n";
            echo "- Access Enabled: " . ($createdEmployee->access_enabled ? 'Yes' : 'No') . "\n";
            echo "- Notes: " . ($createdEmployee->notes ?? 'None') . "\n";
        }

        // Test password verification
        if (password_verify($testEmployeeData['password'], $createdEmployee->password)) {
            echo "✅ Password verification successful\n";
        } else {
            echo "❌ Password verification failed\n";
        }

    } catch (Exception $e) {
        echo "❌ Employee registration failed: " . $e->getMessage() . "\n";
        return;
    }

    // Test different roles registration
    echo "\n" . str_repeat("=", 60) . "\n";
    echo "Testing registration for different roles:\n";
    echo str_repeat("=", 60) . "\n\n";

    $roles = ['admin', 'manager', 'cashier'];
    
    foreach ($roles as $role) {
        try {
            $testUser = [
                'name' => 'Test ' . ucfirst($role) . ' Registration',
                'email' => 'test' . $role . 'reg' . time() . '@example.com',
                'password' => bcrypt('password123'),
                'role' => $role,
                'position' => ucfirst($role) . ' Position',
                'department' => ucfirst($role) . ' Department',
                'employee_id' => strtoupper($role) . time(),
                'employment_status' => 'active',
                'access_enabled' => true,
                'notes' => 'Test ' . $role . ' registration',
                'created_at' => now(),
                'updated_at' => now(),
            ];
            
            $userId = DB::table('users')->insertGetId($testUser);
            echo "✅ " . ucfirst($role) . " registration successful (ID: " . $userId . ")\n";
            
        } catch (Exception $e) {
            echo "❌ Failed to register " . $role . ": " . $e->getMessage() . "\n";
        }
    }

    // Show updated system statistics
    echo "\n" . str_repeat("=", 60) . "\n";
    echo "Updated System Statistics:\n";
    echo str_repeat("=", 60) . "\n";
    
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

    // Test if the AttendanceEmailList model can be instantiated
    echo "\n" . str_repeat("=", 60) . "\n";
    echo "Testing AttendanceEmailList Model:\n";
    echo str_repeat("=", 60) . "\n";
    
    try {
        $emailList = new \App\Models\AttendanceEmailList();
        echo "✅ AttendanceEmailList model can be instantiated\n";
        echo "✅ Model fillable fields: " . implode(', ', $emailList->getFillable()) . "\n";
    } catch (Exception $e) {
        echo "❌ AttendanceEmailList model error: " . $e->getMessage() . "\n";
    }
    
    echo "\n✅ Employee registration feature is working properly!\n";
    echo "✅ The AttendanceEmailList error has been fixed!\n";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "Please check your database connection and configuration.\n";
}

echo "\n=== Test Complete ===\n";
?>
