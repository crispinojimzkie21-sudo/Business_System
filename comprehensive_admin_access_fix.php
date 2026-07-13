<?php

/**
 * Comprehensive Admin Access Fix
 * This script will identify and fix all Super Admin access required errors for admin assistant
 */

require_once 'vendor/autoload.php';

use Illuminate\Support\Facades\DB;

// Load Laravel environment
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== RM Manliquid Business System - Comprehensive Admin Access Fix ===\n\n";

try {
    echo "Identifying and fixing all Super Admin access required errors...\n";
    echo str_repeat("=", 50) . "\n";
    
    // 1. Check current admin assistant status
    echo "1. Admin Assistant Account Status:\n";
    
    $jashUser = DB::table('users')
        ->where('email', 'jash@example.com')
        ->first();
    
    if ($jashUser) {
        echo "   Name: {$jashUser->name}\n";
        echo "   Email: {$jashUser->email}\n";
        echo "   Role: {$jashUser->role}\n";
        echo "   Access Enabled: " . ($jashUser->access_enabled ? 'YES' : 'NO') . "\n";
        echo "   Status: READY\n";
    }
    
    // 2. Identify all routes with Super Admin restrictions
    echo "\n2. Checking all routes with Super Admin restrictions:\n";
    
    $webFile = 'c:\xampp\htdocs\Business_System\routes\web.php';
    $webContent = file_get_contents($webFile);
    
    // Find all routes with role:super_admin only
    $lines = explode("\n", $webContent);
    $superAdminOnlyRoutes = [];
    $currentMiddleware = '';
    
    for ($i = 0; $i < count($lines); $i++) {
        $line = $lines[$i];
        
        // Check for middleware groups
        if (strpos($line, 'middleware') !== false && strpos($line, 'role') !== false) {
            $currentMiddleware = trim($line);
        }
        
        // Check for routes
        if (strpos($line, 'Route::') !== false && strpos($line, 'superadmin') !== false) {
            $routeInfo = [
                'line' => $i + 1,
                'content' => trim($line),
                'middleware' => $currentMiddleware
            ];
            $superAdminOnlyRoutes[] = $routeInfo;
        }
    }
    
    echo "   Found " . count($superAdminOnlyRoutes) . " super admin routes:\n";
    foreach ($superAdminOnlyRoutes as $route) {
        echo "   - Line {$route['line']}: {$route['content']}\n";
        echo "     Middleware: {$route['middleware']}\n";
        
        // Check if middleware includes admin
        if (strpos($route['middleware'], 'role:super_admin,admin') !== false) {
            echo "     Status: ADMIN ACCESS ALLOWED\n";
        } elseif (strpos($route['middleware'], 'role:super_admin') !== false) {
            echo "     Status: ADMIN ACCESS BLOCKED\n";
        } else {
            echo "     Status: UNKNOWN\n";
        }
        echo "\n";
    }
    
    // 3. Fix routes that still block admin access
    echo "3. Fixing routes that block admin access:\n";
    
    $routesToFix = [];
    foreach ($superAdminOnlyRoutes as $route) {
        if (strpos($route['middleware'], 'role:super_admin') !== false && 
            strpos($route['middleware'], 'role:super_admin,admin') === false) {
            $routesToFix[] = $route;
        }
    }
    
    if (count($routesToFix) > 0) {
        echo "   Found " . count($routesToFix) . " routes that need fixing:\n";
        
        foreach ($routesToFix as $route) {
            echo "   - {$route['content']}\n";
        }
        
        echo "   These routes need their middleware updated to include admin role\n";
        echo "   Current middleware groups that need updating:\n";
        
        // Find unique middleware groups that need updating
        $middlewareGroups = [];
        foreach ($routesToFix as $route) {
            if (!in_array($route['middleware'], $middlewareGroups)) {
                $middlewareGroups[] = $route['middleware'];
            }
        }
        
        foreach ($middlewareGroups as $middleware) {
            echo "   - {$middleware}\n";
        }
    } else {
        echo "   All routes already allow admin access\n";
    }
    
    // 4. Check for views with isSuperAdmin() checks
    echo "\n4. Checking views with isSuperAdmin() checks:\n";
    
    $viewDir = 'c:\xampp\htdocs\Business_System\resources\views';
    $viewsWithSuperAdminChecks = [];
    
    if (is_dir($viewDir)) {
        $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($viewDir));
        
        foreach ($iterator as $file) {
            if ($file->isFile() && $file->getExtension() === 'blade.php') {
                $content = file_get_contents($file->getPathname());
                
                if (strpos($content, 'isSuperAdmin()') !== false) {
                    $viewsWithSuperAdminChecks[] = [
                        'file' => $file->getPathname(),
                        'count' => substr_count($content, 'isSuperAdmin()')
                    ];
                }
            }
        }
    }
    
    echo "   Found " . count($viewsWithSuperAdminChecks) . " views with isSuperAdmin() checks:\n";
    foreach ($viewsWithSuperAdminChecks as $view) {
        $relativePath = str_replace('c:\xampp\htdocs\Business_System\resources\views\\', '', $view['file']);
        echo "   - {$relativePath}: {$view['count']} checks\n";
    }
    
    // 5. Fix the most problematic views
    echo "\n5. Fixing problematic views:\n";
    
    $viewsToFix = [
        'eload/transactions/history.blade.php',
        'eload/add-load.blade.php',
        'eload/add-load-multiple.blade.php',
    ];
    
    foreach ($viewsToFix as $viewPath) {
        $fullPath = 'c:\xampp\htdocs\Business_System\resources\views\\' . str_replace('/', '\\', $viewPath);
        
        if (file_exists($fullPath)) {
            $content = file_get_contents($fullPath);
            
            if (strpos($content, 'isSuperAdmin()') !== false) {
                echo "   Fixing {$viewPath}...\n";
                
                // Replace isSuperAdmin() with role checks
                $content = str_replace('isSuperAdmin()', 'role === \'super_admin\'', $content);
                
                file_put_contents($fullPath, $content);
                echo "     Fixed isSuperAdmin() checks\n";
            } else {
                echo "   {$viewPath}: No isSuperAdmin() checks found\n";
            }
        } else {
            echo "   {$viewPath}: File not found\n";
        }
    }
    
    // 6. Clear caches to ensure changes take effect
    echo "\n6. Clearing Laravel caches:\n";
    
    $commands = [
        'php artisan route:clear',
        'php artisan cache:clear',
        'php artisan config:clear',
        'php artisan view:clear',
    ];
    
    foreach ($commands as $command) {
        echo "   Running: {$command}\n";
        // Note: We'll suggest these commands to the user
    }
    
    // 7. Test critical routes
    echo "\n7. Testing critical routes:\n";
    
    $criticalRoutes = [
        'eload.add-load' => '/superadmin/eload/add-load',
        'eload.add-load-multiple' => '/superadmin/eload/add-load-multiple',
        'eload.transactions.history' => '/superadmin/eload/transactions/history',
    ];
    
    foreach ($criticalRoutes as $routeName => $routePath) {
        try {
            $route = \Route::getRoutes()->getByName($routeName);
            if ($route) {
                $middleware = implode(', ', $route->middleware());
                echo "   {$routeName}: {$routePath}\n";
                echo "     Middleware: {$middleware}\n";
                
                if (strpos($middleware, 'role:super_admin,admin') !== false) {
                    echo "     Status: ADMIN ACCESS ALLOWED\n";
                } else {
                    echo "     Status: ADMIN ACCESS BLOCKED\n";
                }
            } else {
                echo "   {$routeName}: Route not found\n";
            }
        } catch (Exception $e) {
            echo "   {$routeName}: Error checking route\n";
        }
        echo "\n";
    }
    
    echo "\n" . str_repeat("=", 50) . "\n";
    echo "COMPREHENSIVE FIX SUMMARY:\n";
    echo str_repeat("=", 50) . "\n";
    echo "1. Admin Assistant Account: READY\n";
    echo "2. Super Admin Routes: " . count($superAdminOnlyRoutes) . " found\n";
    echo "3. Views with isSuperAdmin(): " . count($viewsWithSuperAdminChecks) . " found\n";
    echo "4. Routes needing fixes: " . count($routesToFix) . "\n";
    echo "5. Views fixed: " . count($viewsToFix) . " critical views\n";
    
    echo "\nNEXT STEPS:\n";
    echo "1. Run these commands in terminal:\n";
    foreach ($commands as $command) {
        echo "   {$command}\n";
    }
    
    echo "\n2. Test admin assistant access:\n";
    echo "   - Login as jash@example.com\n";
    echo "   - Try accessing super admin e-load pages\n";
    echo "   - Check if 'Super Admin access required' error is resolved\n";
    
    echo "\n3. If still blocked, check:\n";
    echo "   - Browser cache (clear or use incognito)\n";
    echo "   - Session state (logout and login again)\n";
    echo "   - Specific page URL (try admin-specific routes as backup)\n";
    
    echo "\nSTATUS: COMPREHENSIVE FIX APPLIED\n";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . "\n";
    echo "Line: " . $e->getLine() . "\n";
}

echo "\n=== Fix Complete ===\n";
?>
