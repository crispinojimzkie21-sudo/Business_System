<?php
/**
 * Simple test script to verify the system is working
 */

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;

echo "=== Business System Test ===\n\n";

// Test 1: Check PHP version
echo "1. PHP Version: " . PHP_VERSION . "\n";

// Test 2: Check if Laravel files exist
echo "2. Checking Laravel files:\n";
$files = [
    'vendor/autoload.php',
    'artisan',
    'config/app.php',
    'routes/web.php',
];
foreach ($files as $file) {
    $exists = file_exists($file) ? '✅' : '❌';
    echo "   $exists $file\n";
}

// Test 3: Check database connection
echo "\n3. Checking database connection:\n";
try {
    require __DIR__ . '/vendor/autoload.php';
    $app = require_once __DIR__ . '/bootstrap/app.php';
    $app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();
    
    // Test DB connection
    $db = DB::connection();
    $db->getPdo();
    echo "   ✅ Database connected: " . $db->getDatabaseName() . "\n";
} catch (Exception $e) {
    echo "   ❌ Database error: " . $e->getMessage() . "\n";
}

// Test 4: Check routes
echo "\n4. Registered Sales Routes:\n";
$routes = Route::getRoutes();
$salesRoutes = [];
foreach ($routes as $route) {
    if (strpos($route->uri(), 'sales') !== false) {
        $methods = implode(', ', $route->methods());
        $salesRoutes[] = "   $methods: {$route->uri()}";
    }
}
foreach (array_unique($salesRoutes) as $r) {
    echo "$r\n";
}

echo "\n=== Test Complete ===\n";
