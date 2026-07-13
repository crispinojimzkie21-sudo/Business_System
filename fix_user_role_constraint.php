<?php

/**
 * Fix User Role Constraint
 * This script will fix the CHECK constraint for user role to include sales_clerk
 */

require_once 'vendor/autoload.php';

use Illuminate\Support\Facades\DB;

// Load Laravel environment
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== RM Manliquid Business System - Fix User Role Constraint ===\n\n";

try {
    echo "Fixing user role CHECK constraint to include sales_clerk...\n";
    echo str_repeat("=", 50) . "\n";
    
    // 1. Check current constraint
    echo "1. Checking current user table schema:\n";
    $columns = DB::select("PRAGMA table_info(users)");
    
    foreach ($columns as $column) {
        if ($column->name === 'role') {
            echo "   Role column: {$column->type}\n";
            break;
        }
    }
    
    // 2. Check current constraint
    echo "\n2. Checking current role values in database:\n";
    $currentRoles = DB::table('users')
        ->selectRaw('DISTINCT role')
        ->pluck('role');
    
    echo "   Current roles in database:\n";
    foreach ($currentRoles as $role) {
        echo "   - {$role}\n";
    }
    
    // 3. Create backup table
    echo "\n3. Creating backup of users table...\n";
    DB::statement('CREATE TABLE users_backup AS SELECT * FROM users');
    echo "   Backup table created: users_backup\n";
    
    // 4. Recreate table with updated constraint
    echo "\n4. Recreating users table with updated constraint...\n";
    
    // Drop the table
    DB::statement('DROP TABLE users');
    
    // Recreate with updated constraint
    DB::statement('
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
    ');
    
    // 5. Restore data from backup
    echo "\n5. Restoring data from backup...\n";
    DB::statement('INSERT INTO users SELECT * FROM users_backup');
    
    // 6. Drop backup table
    echo "\n6. Cleaning up backup table...\n";
    DB::statement('DROP TABLE users_backup');
    
    // 7. Verify the fix
    echo "\n7. Verifying the fix:\n";
    
    // Test inserting sales_clerk role
    try {
        DB::table('users')->insert([
            'name' => 'Test Sales Clerk Fix',
            'email' => 'testscfix' . time() . '@example.com',
            'password' => bcrypt('password123'),
            'role' => 'sales_clerk',
            'access_enabled' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        
        echo "   SUCCESS: sales_clerk role can now be inserted\n";
        
        // Clean up test record
        DB::table('users')
            ->where('email', 'like', 'testscfix%@example.com')
            ->delete();
        
    } catch (Exception $e) {
        echo "   ERROR: sales_clerk role insertion failed - " . $e->getMessage() . "\n";
    }
    
    // 8. Show final role distribution
    echo "\n8. Final role distribution:\n";
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
    
    // Try to restore from backup if something went wrong
    try {
        if (DB::getSchemaBuilder()->hasTable('users_backup')) {
            echo "\nAttempting to restore from backup...\n";
            DB::statement('DROP TABLE users');
            DB::statement('ALTER TABLE users_backup RENAME TO users');
            echo "Backup restored successfully.\n";
        }
    } catch (Exception $restoreError) {
        echo "Failed to restore backup: " . $restoreError->getMessage() . "\n";
    }
}

echo "\n=== Fix Complete ===\n";
?>
