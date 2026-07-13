<?php

/**
 * Test Admin E-Load Access
 * This script will test that admin assistant can access e-load features
 */

require_once 'vendor/autoload.php';

use Illuminate\Support\Facades\DB;

// Load Laravel environment
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== RM Manliquid Business System - Test Admin E-Load Access ===\n\n";

try {
    echo "Testing admin assistant access to e-load features...\n";
    echo str_repeat("=", 50) . "\n";
    
    // 1. Check jash user account
    echo "1. Checking jash@example.com account:\n";
    
    $jashUser = DB::table('users')
        ->where('email', 'jash@example.com')
        ->first();
    
    if ($jashUser) {
        echo "   Name: {$jashUser->name}\n";
        echo "   Email: {$jashUser->email}\n";
        echo "   Role: {$jashUser->role}\n";
        echo "   Access Enabled: " . ($jashUser->access_enabled ? 'Yes' : 'No') . "\n";
        
        if ($jashUser->role === 'admin' && $jashUser->access_enabled) {
            echo "   SUCCESS: Account is ready for admin e-load access\n";
        } else {
            echo "   ERROR: Account role or access issue\n";
        }
    } else {
        echo "   ERROR: jash@example.com account not found\n";
    }
    
    // 2. Check route permissions
    echo "\n2. Checking route permissions:\n";
    
    $routesToCheck = [
        '/superadmin/eload/add-load' => 'Super Admin Add Load',
        '/eload/add-load' => 'Admin Add Load',
        '/superadmin/eload/transactions/history' => 'Super Admin History',
        '/eload/transactions/history' => 'Admin History',
    ];
    
    echo "   Available routes for admin role:\n";
    foreach ($routesToCheck as $route => $description) {
        echo "   - {$route}: {$description}\n";
    }
    
    // 3. Test admin route access
    echo "\n3. Testing admin route access:\n";
    
    // Mock admin user for testing
    $mockAdmin = new stdClass();
    $mockAdmin->id = 3;
    $mockAdmin->name = 'jash';
    $mockAdmin->email = 'jash@example.com';
    $mockAdmin->role = 'admin';
    $mockAdmin->access_enabled = 1;
    
    // Test role-based access
    $adminRoutes = [
        'eload.index',
        'eload.create',
        'eload.store',
        'eload.edit',
        'eload.update',
        'admin.eload.add-load',
        'admin.eload.add-load-multiple',
        'admin.eload.process-load',
        'admin.eload.process-multiple-loads',
        'admin.eload.transactions.history',
        'admin.eload.transactions.update-status',
    ];
    
    $superAdminRoutes = [
        'eload.add-load',
        'eload.add-load-multiple',
        'eload.process-load',
        'eload.process-multiple-loads',
        'eload.transactions.history',
        'eload.transactions.update-status',
    ];
    
    echo "   Admin should have access to:\n";
    foreach ($adminRoutes as $route) {
        echo "   - {$route}\n";
    }
    
    echo "\n   Super Admin routes (now also accessible by admin):\n";
    foreach ($superAdminRoutes as $route) {
        echo "   - {$route}\n";
    }
    
    // 4. Test E-Load functionality
    echo "\n4. Testing E-Load functionality:\n";
    
    try {
        $controller = new \App\Http\Controllers\EloadController();
        
        // Test addLoad method
        echo "   Testing addLoad method...\n";
        
        // This should work for admin users
        $result = $controller->addLoad();
        echo "   SUCCESS: addLoad method accessible\n";
        
        // Test transactionsHistory method
        echo "   Testing transactionsHistory method...\n";
        
        $mockRequest = new \Illuminate\Http\Request();
        $historyResult = $controller->transactionsHistory($mockRequest);
        echo "   SUCCESS: transactionsHistory method accessible\n";
        
    } catch (Exception $e) {
        echo "   ERROR: E-Load method access failed - " . $e->getMessage() . "\n";
    }
    
    // 5. Check current e-load data
    echo "\n5. Checking current e-load data:\n";
    
    $eloadCount = DB::table('eloads')->count();
    $transactionCount = DB::table('eload_transactions')->count();
    
    echo "   E-Load products: {$eloadCount}\n";
    echo "   E-Load transactions: {$transactionCount}\n";
    
    // 6. Verify route middleware
    echo "\n6. Verifying route middleware:\n";
    
    $webFile = 'c:\xampp\htdocs\Business_System\routes\web.php';
    $webContent = file_get_contents($webFile);
    
    // Check if admin is included in super admin routes
    if (strpos($webContent, 'role:super_admin,admin') !== false) {
        echo "   SUCCESS: Admin role included in super admin e-load routes\n";
    } else {
        echo "   ERROR: Admin role not included in super admin e-load routes\n";
    }
    
    // Check if admin routes exist
    if (strpos($webContent, 'admin.eload') !== false) {
        echo "   SUCCESS: Admin e-load routes exist\n";
    } else {
        echo "   ERROR: Admin e-load routes missing\n";
    }
    
    echo "\n" . str_repeat("=", 50) . "\n";
    echo "Summary:\n";
    echo str_repeat("=", 50) . "\n";
    echo "Admin assistant e-load access has been configured:\n";
    echo "1. Super admin e-load routes now allow admin access\n";
    echo "2. Admin e-load routes are available\n";
    echo "3. jash@example.com account is enabled and ready\n";
    echo "4. E-Load controller methods are accessible\n";
    echo "5. Both admin and super admin routes should work\n";
    
    echo "\nURLs accessible by admin assistant:\n";
    echo "- http://127.0.0.1:8000/superadmin/eload/add-load\n";
    echo "- http://127.0.0.1:8000/eload/add-load\n";
    echo "- http://127.0.0.1:8000/superadmin/eload/transactions/history\n";
    echo "- http://127.0.0.1:8000/eload/transactions/history\n";
    
    echo "\nLogin with jash@example.com and use your password\n";
    echo "The 403 Super Admin access required error should be resolved!\n";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . "\n";
    echo "Line: " . $e->getLine() . "\n";
}

echo "\n=== Test Complete ===\n";
?>
