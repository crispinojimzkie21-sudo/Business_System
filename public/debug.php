<?php

// Simple debug script to test Laravel
echo "🔍 Laravel Debug Test\n\n";

// Test 1: Check if Laravel files exist
if (file_exists(__DIR__ . '/../vendor/autoload.php')) {
    echo "✅ vendor/autoload.php exists\n";
} else {
    echo "❌ vendor/autoload.php NOT found - Run 'composer install'\n";
    exit;
}

// Test 2: Check if bootstrap/app.php exists
if (file_exists(__DIR__ . '/../bootstrap/app.php')) {
    echo "✅ bootstrap/app.php exists\n";
} else {
    echo "❌ bootstrap/app.php NOT found\n";
    exit;
}

// Test 3: Try to bootstrap Laravel
try {
    require_once __DIR__ . '/../vendor/autoload.php';
    $app = require_once __DIR__ . '/../bootstrap/app.php';
    echo "✅ Laravel bootstrap successful\n";
} catch (Exception $e) {
    echo "❌ Laravel bootstrap failed: " . $e->getMessage() . "\n";
    exit;
}

// Test 4: Check if routes are registered
try {
    $kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
    $kernel->bootstrap();
    echo "✅ Laravel kernel bootstrapped\n";
    
    $routes = \Illuminate\Support\Facades\Route::getRoutes();
    echo "✅ Routes loaded: " . $routes->count() . " routes\n";
    
} catch (Exception $e) {
    echo "❌ Route loading failed: " . $e->getMessage() . "\n";
}

echo "\n🔧 If all tests pass, the issue is likely:\n";
echo "1. Web server configuration\n";
echo "2. .htaccess file\n";
echo "3. URL rewriting\n";
echo "4. Port blocking\n";

echo "\n📍 Current server info:\n";
echo "PHP Version: " . PHP_VERSION . "\n";
echo "Document Root: " . __DIR__ . "\n";
echo "Request URI: " . ($_SERVER['REQUEST_URI'] ?? 'Not available') . "\n";
