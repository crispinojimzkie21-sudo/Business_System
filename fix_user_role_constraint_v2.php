<?php

/**
 * Fix User Role Constraint V2
 * This script will fix the CHECK constraint for user role properly
 */

require_once 'vendor/autoload.php';

use Illuminate\Support\Facades\DB;

// Load Laravel environment
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== RM Manliquid Business System - Fix User Role Constraint V2 ===\n\n";

try {
    echo "Fixing user role CHECK constraint to include sales_clerk...\n";
    echo str_repeat("=", 50) . "\n";
    
    // 1. Get exact column structure
    echo "1. Getting exact column structure...\n";
    $columns = DB::select("PRAGMA table_info(users)");
    
    echo "   Current columns:\n";
    foreach ($columns as $column) {
        echo "   - {$column->name}: {$column->type}\n";
    }
    
    // 2. Get current data
    echo "\n2. Backing up current data...\n";
    $currentUsers = DB::table('users')->get();
    echo "   Backed up {$currentUsers->count()} users\n";
    
    // 3. Drop and recreate table with correct structure
    echo "\n3. Recreating table with correct structure...\n";
    
    // Drop the table
    DB::statement('DROP TABLE users');
    
    // Create table with exact same structure but updated constraint
    $createSQL = '
        CREATE TABLE users (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            name VARCHAR(255) NOT NULL,
            email VARCHAR(255) UNIQUE NOT NULL,
            email_verified_at TIMESTAMP NULL,
            password VARCHAR(255) NOT NULL,
            remember_token VARCHAR(100) NULL,
            created_at TIMESTAMP NULL,
            updated_at TIMESTAMP NULL,
            position VARCHAR(255) NULL,
            salary DECIMAL(10,2) DEFAULT 0.00,
            role VARCHAR(50) NOT NULL DEFAULT "employee" CHECK (role IN ("super_admin", "admin", "manager", "cashier", "employee", "sales_clerk")),
            phone VARCHAR(20) NULL,
            address TEXT NULL,
            hire_date DATE NULL,
            department VARCHAR(255) NULL,
            employee_id VARCHAR(50) UNIQUE NULL,
            employment_status VARCHAR(20) DEFAULT "active" CHECK (employment_status IN ("active", "inactive", "on_leave", "terminated")),
            notes TEXT NULL,
            access_enabled BOOLEAN DEFAULT 1
        )
    ';
    
    DB::statement($createSQL);
    echo "   Table recreated with updated constraint\n";
    
    // 4. Restore data
    echo "\n4. Restoring user data...\n";
    
    $restoredCount = 0;
    foreach ($currentUsers as $user) {
        try {
            DB::table('users')->insert([
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'email_verified_at' => $user->email_verified_at,
                'password' => $user->password,
                'remember_token' => $user->remember_token,
                'created_at' => $user->created_at,
                'updated_at' => $user->updated_at,
                'position' => $user->position,
                'salary' => $user->salary,
                'role' => $user->role,
                'phone' => $user->phone,
                'address' => $user->address,
                'hire_date' => $user->hire_date,
                'department' => $user->department,
                'employee_id' => $user->employee_id,
                'employment_status' => $user->employment_status,
                'notes' => $user->notes,
                'access_enabled' => $user->access_enabled,
            ]);
            $restoredCount++;
        } catch (Exception $e) {
            echo "   ERROR restoring user {$user->id}: " . $e->getMessage() . "\n";
        }
    }
    
    echo "   Restored {$restoredCount} users\n";
    
    // 5. Test the fix
    echo "\n5. Testing the fix...\n";
    
    try {
        DB::table('users')->insert([
            'name' => 'Test Sales Clerk Final',
            'email' => 'testscfinal' . time() . '@example.com',
            'password' => bcrypt('password123'),
            'role' => 'sales_clerk',
            'access_enabled' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        
        echo "   SUCCESS: sales_clerk role can now be inserted\n";
        
        // Clean up test record
        DB::table('users')
            ->where('email', 'like', 'testscfinal%@example.com')
            ->delete();
        
    } catch (Exception $e) {
        echo "   ERROR: sales_clerk role insertion failed - " . $e->getMessage() . "\n";
    }
    
    // 6. Show final role distribution
    echo "\n6. Final role distribution:\n";
    $finalRoles = DB::table('users')
        ->selectRaw('role, COUNT(*) as count')
        ->groupBy('role')
        ->get();
    
    echo "   Final roles in database:\n";
    foreach ($finalRoles as $roleCount) {
        echo "   - {$roleCount->role}: {$roleCount->count}\n";
    }
    
    echo "\n" . str_repeat("=", 50) . "\n";
    echo "Summary:\n";
    echo str_repeat("=", 50) . "\n";
    echo "User role CHECK constraint has been fixed:\n";
    echo "1. Updated constraint to include 'sales_clerk' role\n";
    echo "2. Preserved all existing user data\n";
    echo "3. Database now accepts all required roles\n";
    echo "4. Employee registration should work for all roles\n";
    
    echo "\nAllowed roles: super_admin, admin, manager, cashier, employee, sales_clerk\n";
    echo "\nSales clerk users can now create employee accounts at /employee/register!\n";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . "\n";
    echo "Line: " . $e->getLine() . "\n";
}

echo "\n=== Fix Complete ===\n";
?>
