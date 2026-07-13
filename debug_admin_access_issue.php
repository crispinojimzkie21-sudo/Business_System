<?php

/**
 * Debug Admin Access Issue
 * This script will debug the 403 Admin access required error
 */

require_once 'vendor/autoload.php';

use Illuminate\Support\Facades\DB;

// Load Laravel environment
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== RM Manliquid Business System - Debug Admin Access Issue ===\n\n";

try {
    echo "Debugging 403 Admin access required error...\n";
    echo str_repeat("=", 50) . "\n";
    
    // 1. Check jash user account details
    echo "1. Checking jash@example.com account:\n";
    
    $jashUser = DB::table('users')
        ->where('email', 'jash@example.com')
        ->first();
    
    if ($jashUser) {
        echo "   ID: {$jashUser->id}\n";
        echo "   Name: {$jashUser->name}\n";
        echo "   Email: {$jashUser->email}\n";
        echo "   Role: {$jashUser->role}\n";
        echo "   Access Enabled: " . ($jashUser->access_enabled ? 'YES' : 'NO') . "\n";
        echo "   Created At: {$jashUser->created_at}\n";
        echo "   Updated At: {$jashUser->updated_at}\n";
        
        // Check if access is enabled
        if ($jashUser->access_enabled) {
            echo "   Status: ACCESS ENABLED\n";
        } else {
            echo "   Status: ACCESS DISABLED - This could cause 403 error\n";
        }
    } else {
        echo "   Status: USER NOT FOUND\n";
    }
    
    // 2. Test CheckUserAccess middleware logic
    echo "\n2. Testing CheckUserAccess middleware logic:\n";
    
    if ($jashUser) {
        // Simulate the middleware logic
        $userRole = $jashUser->role;
        $accessEnabled = $jashUser->access_enabled;
        
        echo "   User Role: {$userRole}\n";
        echo "   Access Enabled: " . ($accessEnabled ? 'YES' : 'NO') . "\n";
        
        if ($userRole === 'super_admin') {
            echo "   Middleware Result: ALLOWED (super admin bypass)\n";
        } elseif (!$accessEnabled) {
            echo "   Middleware Result: BLOCKED (access disabled)\n";
        } else {
            echo "   Middleware Result: ALLOWED (access enabled)\n";
        }
    }
    
    // 3. Check admin route registration
    echo "\n3. Checking admin e-load route registration:\n";
    
    try {
        $route = \Route::getRoutes()->getByName('admin.eload.transactions.history');
        if ($route) {
            echo "   Route found: admin.eload.transactions.history\n";
            echo "   Route URI: " . $route->uri() . "\n";
            echo "   Route action: " . $route->getActionName() . "\n";
            echo "   Route middleware: " . implode(', ', $route->middleware()) . "\n";
            
            // Check if check.access middleware is present
            $middleware = $route->middleware();
            if (in_array('check.access', $middleware)) {
                echo "   Status: check.access middleware FOUND - This could block access\n";
            } else {
                echo "   Status: check.access middleware NOT FOUND\n";
            }
        } else {
            echo "   Status: Route NOT FOUND\n";
        }
    } catch (Exception $e) {
        echo "   Error checking route: " . $e->getMessage() . "\n";
    }
    
    // 4. Test direct controller access
    echo "\n4. Testing direct controller access:\n";
    
    try {
        $controller = new \App\Http\Controllers\EloadController();
        $mockRequest = new \Illuminate\Http\Request();
        
        // Test transactionsHistory method
        $result = $controller->transactionsHistory($mockRequest);
        echo "   Controller Method: transactionsHistory - SUCCESS\n";
        echo "   Returned: " . get_class($result) . "\n";
        
    } catch (Exception $e) {
        echo "   Controller Method: transactionsHistory - ERROR\n";
        echo "   Error: " . $e->getMessage() . "\n";
    }
    
    // 5. Check if there are any other permission checks
    echo "\n5. Checking for additional permission checks:\n";
    
    // Check if there are any custom gates or policies
    try {
        $providersFile = 'c:\xampp\htdocs\Business_System\app\Providers\AuthServiceProvider.php';
        if (file_exists($providersFile)) {
            $providerContent = file_get_contents($providersFile);
            if (strpos($providerContent, 'Gate::') !== false) {
                echo "   Gates found in AuthServiceProvider\n";
            } else {
                echo "   No gates found in AuthServiceProvider\n";
            }
        }
    } catch (Exception $e) {
        echo "   Error checking gates: " . $e->getMessage() . "\n";
    }
    
    // 6. Check if jash user can access admin dashboard
    echo "\n6. Testing admin dashboard access:\n";
    
    try {
        $adminRoute = \Route::getRoutes()->getByName('dashboard.admin');
        if ($adminRoute) {
            echo "   Admin dashboard route: EXISTS\n";
            echo "   Admin dashboard middleware: " . implode(', ', $adminRoute->middleware()) . "\n";
        } else {
            echo "   Admin dashboard route: NOT FOUND\n";
        }
    } catch (Exception $e) {
        echo "   Error checking admin dashboard: " . $e->getMessage() . "\n";
    }
    
    // 7. Provide solution
    echo "\n7. Recommended Solutions:\n";
    
    if ($jashUser && !$jashUser->access_enabled) {
        echo "   SOLUTION 1: Enable access for jash@example.com\n";
        echo "   - Run: UPDATE users SET access_enabled = 1 WHERE email = 'jash@example.com'\n";
    }
    
    echo "   SOLUTION 2: Remove check.access middleware from admin e-load routes\n";
    echo "   - Edit routes/web.php\n";
    echo "   - Change middleware from 'role:admin,check.access' to 'role:admin'\n";
    
    echo "   SOLUTION 3: Clear Laravel caches\n";
    echo "   - Run: php artisan route:clear\n";
    echo "   - Run: php artisan cache:clear\n";
    
    echo "\n" . str_repeat("=", 50) . "\n";
    echo "DEBUG COMPLETE\n";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . "\n";
    echo "Line: " . $e->getLine() . "\n";
}

echo "\n=== Debug Complete ===\n";
?>
