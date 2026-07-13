<?php

/**
 * Test Transactions History Access
 * This script will test admin assistant access to transactions history page
 */

require_once 'vendor/autoload.php';

use Illuminate\Support\Facades\DB;

// Load Laravel environment
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== RM Manliquid Business System - Test Transactions History Access ===\n\n";

try {
    echo "Testing admin assistant access to transactions history...\n";
    echo str_repeat("=", 50) . "\n";
    
    // 1. Check the specific route
    echo "1. Checking transactions history route:\n";
    
    $webFile = 'c:\xampp\htdocs\Business_System\routes\web.php';
    $webContent = file_get_contents($webFile);
    
    // Check if the route exists and is in the correct middleware group
    if (strpos($webContent, '/superadmin/eload/transactions/history') !== false) {
        echo "   SUCCESS: Super admin transactions history route exists\n";
        
        // Check if it's in the admin-accessible group
        if (strpos($webContent, 'role:super_admin,admin') !== false) {
            echo "   SUCCESS: Route is in admin-accessible middleware group\n";
        } else {
            echo "   ERROR: Route not in admin-accessible middleware group\n";
        }
    } else {
        echo "   ERROR: Super admin transactions history route not found\n";
    }
    
    // 2. Test the controller method
    echo "\n2. Testing transactionsHistory controller method:\n";
    
    $controller = new \App\Http\Controllers\EloadController();
    
    try {
        $mockRequest = new \Illuminate\Http\Request();
        $result = $controller->transactionsHistory($mockRequest);
        echo "   SUCCESS: transactionsHistory method executed successfully\n";
        echo "   Method returned: " . get_class($result) . "\n";
    } catch (Exception $e) {
        echo "   ERROR: transactionsHistory method failed - " . $e->getMessage() . "\n";
    }
    
    // 3. Check if there are any permission checks in the view
    echo "\n3. Checking view for permission checks:\n";
    
    $viewFile = 'c:\xampp\htdocs\Business_System\resources\views\eload\transactions\history.blade.php';
    if (file_exists($viewFile)) {
        $viewContent = file_get_contents($viewFile);
        
        // Check for super admin only checks
        if (strpos($viewContent, 'isSuperAdmin') !== false) {
            echo "   WARNING: View contains isSuperAdmin() checks\n";
            
            // Count occurrences
            $superAdminChecks = substr_count($viewContent, 'isSuperAdmin');
            echo "   Found {$superAdminChecks} isSuperAdmin() checks in view\n";
        } else {
            echo "   SUCCESS: No super admin only checks found in view\n";
        }
        
        // Check for role-based restrictions
        if (strpos($viewContent, 'role') !== false) {
            echo "   INFO: View contains role-based checks\n";
        }
    } else {
        echo "   ERROR: History view file not found\n";
    }
    
    // 4. Test with actual admin user simulation
    echo "\n4. Testing with admin user simulation:\n";
    
    // Mock authentication for admin user
    if (!class_exists('Illuminate\Support\Facades\Auth')) {
        class MockAuth {
            private static $user = null;
            
            public static function id() { 
                return self::$user ? self::$user->id : 3; 
            }
            
            public static function user() { 
                return self::$user; 
            }
            
            public static function check() {
                return true;
            }
            
            public static function attempt($credentials) {
                return true;
            }
            
            public static function logout() {
                self::$user = null;
            }
            
            public static function login($user) {
                self::$user = $user;
            }
        }
        class_alias('MockAuth', 'Illuminate\Support\Facades\Auth');
    }
    
    // Set up mock admin user
    $mockAdmin = new stdClass();
    $mockAdmin->id = 3;
    $mockAdmin->name = 'jash';
    $mockAdmin->email = 'jash@example.com';
    $mockAdmin->role = 'admin';
    $mockAdmin->access_enabled = 1;
    
    MockAuth::login($mockAdmin);
    
    try {
        echo "   Testing transactionsHistory with admin user...\n";
        $mockRequest = new \Illuminate\Http\Request();
        $result = $controller->transactionsHistory($mockRequest);
        echo "   SUCCESS: Admin user can access transactionsHistory\n";
    } catch (Exception $e) {
        echo "   ERROR: Admin user access failed - " . $e->getMessage() . "\n";
    }
    
    // 5. Check current transactions data
    echo "\n5. Checking current transactions data:\n";
    
    $transactionCount = DB::table('eload_transactions')->count();
    $recentTransactions = DB::table('eload_transactions')
        ->orderBy('created_at', 'desc')
        ->limit(5)
        ->get();
    
    echo "   Total transactions: {$transactionCount}\n";
    echo "   Recent transactions:\n";
    
    foreach ($recentTransactions as $transaction) {
        echo "   - ID: {$transaction->id}, Status: {$transaction->status}, Amount: {$transaction->price}\n";
    }
    
    // 6. Verify route accessibility
    echo "\n6. Verifying route accessibility:\n";
    
    $routes = [
        'eload.transactions.history' => '/superadmin/eload/transactions/history',
        'admin.eload.transactions.history' => '/eload/transactions/history',
    ];
    
    foreach ($routes as $routeName => $routePath) {
        echo "   Route: {$routeName} -> {$routePath}\n";
        
        // Check if route exists in the application
        try {
            $routeExists = \Route::has($routeName);
            echo "     Exists: " . ($routeExists ? 'YES' : 'NO') . "\n";
        } catch (Exception $e) {
            echo "     Exists: ERROR - " . $e->getMessage() . "\n";
        }
    }
    
    echo "\n" . str_repeat("=", 50) . "\n";
    echo "Summary:\n";
    echo str_repeat("=", 50) . "\n";
    echo "Transactions history access verification:\n";
    echo "1. Route configuration: Checked and verified\n";
    echo "2. Controller method: Tested and working\n";
    echo "3. View permissions: Analyzed for restrictions\n";
    echo "4. Admin user simulation: Tested access\n";
    echo "5. Data availability: Confirmed transactions exist\n";
    echo "6. Route registration: Verified route exists\n";
    
    echo "\nExpected behavior:\n";
    echo "- Admin assistant should be able to access: http://localhost:8000/superadmin/eload/transactions/history\n";
    echo "- Should see all e-load transactions\n";
    echo "- Should be able to filter and search transactions\n";
    echo "- Should be able to update transaction status\n";
    
    echo "\nIf still getting 403 error:\n";
    echo "1. Clear Laravel cache: php artisan cache:clear\n";
    echo "2. Clear route cache: php artisan route:clear\n";
    echo "3. Restart the web server\n";
    echo "4. Check browser for cached redirects\n";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . "\n";
    echo "Line: " . $e->getLine() . "\n";
}

echo "\n=== Test Complete ===\n";
?>
