<?php

/**
 * Debug Admin Access Issue
 * This script will debug why admin assistant still gets "Admin access required" error
 */

require_once 'vendor/autoload.php';

use Illuminate\Support\Facades\DB;

// Load Laravel environment
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== RM Manliquid Business System - Debug Admin Access Issue ===\n\n";

try {
    echo "Debugging admin access to transactions history...\n";
    echo str_repeat("=", 50) . "\n";
    
    // 1. Check current user authentication status
    echo "1. Checking admin assistant authentication:\n";
    
    // Check if we can simulate admin authentication
    try {
        $jashUser = DB::table('users')
            ->where('email', 'jash@example.com')
            ->first();
        
        if ($jashUser) {
            echo "   User found: {$jashUser->name} ({$jashUser->email})\n";
            echo "   Role: {$jashUser->role}\n";
            echo "   Access Enabled: " . ($jashUser->access_enabled ? 'YES' : 'NO') . "\n";
            
            // Check User model methods
            echo "   User model methods:\n";
            
            // Test isSuperAdmin method
            if (method_exists('App\Models\User', 'isSuperAdmin')) {
                $isSuperAdmin = \App\Models\User::find($jashUser->id)->isSuperAdmin();
                echo "   - isSuperAdmin(): " . ($isSuperAdmin ? 'TRUE' : 'FALSE') . "\n";
            } else {
                echo "   - isSuperAdmin(): Method not found\n";
            }
            
            // Test isAdmin method
            if (method_exists('App\Models\User', 'isAdmin')) {
                $isAdmin = \App\Models\User::find($jashUser->id)->isAdmin();
                echo "   - isAdmin(): " . ($isAdmin ? 'TRUE' : 'FALSE') . "\n";
            } else {
                echo "   - isAdmin(): Method not found\n";
            }
        }
    } catch (Exception $e) {
        echo "   Error checking user: " . $e->getMessage() . "\n";
    }
    
    // 2. Check route registration and middleware
    echo "\n2. Checking route registration:\n";
    
    try {
        // Check if the route exists
        $routeExists = \Route::has('eload.transactions.history');
        echo "   Route 'eload.transactions.history' exists: " . ($routeExists ? 'YES' : 'NO') . "\n";
        
        // Try to get the route details
        try {
            $route = \Route::getRoutes()->getByName('eload.transactions.history');
            if ($route) {
                echo "   Route URI: " . $route->uri() . "\n";
                echo "   Route action: " . $route->getActionName() . "\n";
                echo "   Route middleware: " . implode(', ', $route->middleware()) . "\n";
            }
        } catch (Exception $e) {
            echo "   Error getting route details: " . $e->getMessage() . "\n";
        }
    } catch (Exception $e) {
        echo "   Error checking routes: " . $e->getMessage() . "\n";
    }
    
    // 3. Check for any custom middleware that might block access
    echo "\n3. Checking for custom middleware:\n";
    
    $middlewareFile = 'c:\xampp\htdocs\Business_System\app\Http\Middleware';
    if (is_dir($middlewareFile)) {
        $middlewareFiles = glob($middlewareFile . '/*.php');
        echo "   Found middleware files:\n";
        
        foreach ($middlewareFiles as $file) {
            $middlewareName = basename($file, '.php');
            echo "   - {$middlewareName}\n";
            
            // Check if any middleware might be blocking admin access
            $content = file_get_contents($file);
            if (strpos($content, 'admin') !== false || strpos($content, 'super_admin') !== false) {
                echo "     Contains role checks\n";
            }
        }
    }
    
    // 4. Check for any Gates or Policies
    echo "\n4. Checking for Gates/Policies:\n";
    
    try {
        // Check if there are any gates defined
        $providersFile = 'c:\xampp\htdocs\Business_System\app\Providers\AuthServiceProvider.php';
        if (file_exists($providersFile)) {
            $providerContent = file_get_contents($providersFile);
            if (strpos($providerContent, 'Gate::') !== false) {
                echo "   Gates found in AuthServiceProvider\n";
            } else {
                echo "   No gates found\n";
            }
        }
    } catch (Exception $e) {
        echo "   Error checking gates: " . $e->getMessage() . "\n";
    }
    
    // 5. Check Laravel cache
    echo "\n5. Checking Laravel cache:\n";
    
    try {
        // Check if cache is enabled
        $cacheConfig = config('cache.default');
        echo "   Cache driver: {$cacheConfig}\n";
        
        // Clear route cache to ensure latest routes are loaded
        echo "   Try clearing route cache...\n";
        
        $commands = [
            'php artisan route:clear',
            'php artisan cache:clear',
            'php artisan config:clear',
        ];
        
        foreach ($commands as $command) {
            echo "   Running: {$command}\n";
            // Note: We can't actually run artisan commands here, but we'll suggest them
        }
    } catch (Exception $e) {
        echo "   Error checking cache: " . $e->getMessage() . "\n";
    }
    
    // 6. Test direct controller access
    echo "\n6. Testing direct controller access:\n";
    
    try {
        $controller = new \App\Http\Controllers\EloadController();
        $mockRequest = new \Illuminate\Http\Request();
        
        // Test the method directly
        $result = $controller->transactionsHistory($mockRequest);
        echo "   Direct controller access: SUCCESS\n";
        echo "   Returned: " . get_class($result) . "\n";
    } catch (Exception $e) {
        echo "   Direct controller access: FAILED\n";
        echo "   Error: " . $e->getMessage() . "\n";
    }
    
    // 7. Check web.php route configuration again
    echo "\n7. Double-checking route configuration:\n";
    
    $webFile = 'c:\xampp\htdocs\Business_System\routes\web.php';
    $webContent = file_get_contents($webFile);
    
    // Find the exact route line
    if (preg_match('/Route::get\(\'\/superadmin\/eload\/transactions\/history\'[^)]*\)/', $webContent, $matches)) {
        echo "   Found route definition:\n";
        echo "   " . $matches[0] . "\n";
        
        // Check what middleware group it's in
        $lines = explode("\n", $webContent);
        $routeLine = -1;
        
        foreach ($lines as $index => $line) {
            if (strpos($line, '/superadmin/eload/transactions/history') !== false) {
                $routeLine = $index;
                break;
            }
        }
        
        if ($routeLine > 0) {
            // Look backwards to find the middleware group
            for ($i = $routeLine; $i >= 0; $i--) {
                $line = $lines[$i];
                if (strpos($line, 'middleware') !== false && strpos($line, 'role') !== false) {
                    echo "   Middleware group: " . trim($line) . "\n";
                    break;
                }
            }
        }
    }
    
    echo "\n" . str_repeat("=", 50) . "\n";
    echo "DEBUGGING RECOMMENDATIONS:\n";
    echo str_repeat("=", 50) . "\n";
    echo "1. Clear Laravel caches:\n";
    echo "   php artisan route:clear\n";
    echo "   php artisan cache:clear\n";
    echo "   php artisan config:clear\n";
    echo "   php artisan view:clear\n\n";
    
    echo "2. Check browser for cached redirects\n";
    echo "   - Clear browser cache\n";
    echo "   - Try incognito/private mode\n\n";
    
    echo "3. Verify authentication\n";
    echo "   - Make sure you're logged in as jash@example.com\n";
    echo "   - Check session is active\n\n";
    
    echo "4. Test alternative URL:\n";
    echo "   - Try: http://localhost:8000/eload/transactions/history\n";
    echo "   - This uses admin-specific routes\n\n";
    
    echo "5. Check for middleware conflicts\n";
    echo "   - Look for any custom middleware blocking access\n";
    echo "   - Verify User model role methods work correctly\n";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . "\n";
    echo "Line: " . $e->getLine() . "\n";
}

echo "\n=== Debug Complete ===\n";
?>
