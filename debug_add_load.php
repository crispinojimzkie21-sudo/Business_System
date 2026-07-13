<?php

/**
 * Debug Add Load Functionality
 * This script will help identify the specific error in the add-load functionality
 */

require_once 'vendor/autoload.php';

use Illuminate\Support\Facades\DB;

// Load Laravel environment
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== RM Manliquid Business System - Debug Add Load ===\n\n";

try {
    echo "Checking add-load functionality components...\n";
    echo str_repeat("=", 50) . "\n";
    
    // Check if models exist and can be instantiated
    echo "1. Checking models:\n";
    try {
        $category = new \App\Models\Category();
        echo "   - Category model: OK\n";
    } catch (Exception $e) {
        echo "   - Category model: ERROR - " . $e->getMessage() . "\n";
    }
    
    try {
        $eload = new \App\Models\Eload();
        echo "   - Eload model: OK\n";
    } catch (Exception $e) {
        echo "   - Eload model: ERROR - " . $e->getMessage() . "\n";
    }
    
    try {
        $eloadNumber = new \App\Models\EloadNumber();
        echo "   - EloadNumber model: OK\n";
    } catch (Exception $e) {
        echo "   - EloadNumber model: ERROR - " . $e->getMessage() . "\n";
    }
    
    try {
        $eloadTransaction = new \App\Models\EloadTransaction();
        echo "   - EloadTransaction model: OK\n";
    } catch (Exception $e) {
        echo "   - EloadTransaction model: ERROR - " . $e->getMessage() . "\n";
    }
    
    // Check if database tables exist
    echo "\n2. Checking database tables:\n";
    $tables = ['eload_categories', 'eloads', 'eload_numbers', 'eload_transactions', 'users'];
    foreach ($tables as $table) {
        try {
            $count = DB::table($table)->count();
            echo "   - {$table}: OK ({$count} records)\n";
        } catch (Exception $e) {
            echo "   - {$table}: ERROR - " . $e->getMessage() . "\n";
        }
    }
    
    // Check if controller can be instantiated
    echo "\n3. Checking controller:\n";
    try {
        $controller = new \App\Http\Controllers\EloadController();
        echo "   - EloadController: OK\n";
    } catch (Exception $e) {
        echo "   - EloadController: ERROR - " . $e->getMessage() . "\n";
    }
    
    // Test the addLoad method
    echo "\n4. Testing addLoad method:\n";
    try {
        $controller = new \App\Http\Controllers\EloadController();
        
        // Mock a request
        $mockRequest = new \Illuminate\Http\Request();
        
        // This should not cause an error
        $result = $controller->addLoad();
        echo "   - addLoad method: OK\n";
        
    } catch (Exception $e) {
        echo "   - addLoad method: ERROR - " . $e->getMessage() . "\n";
        echo "   - File: " . $e->getFile() . "\n";
        echo "   - Line: " . $e->getLine() . "\n";
    }
    
    // Test the processLoad method with mock data
    echo "\n5. Testing processLoad method with mock data:\n";
    try {
        $controller = new \App\Http\Controllers\EloadController();
        
        // Create a mock request with test data
        $mockRequest = \Illuminate\Http\Request::create('/eload/process-load', 'POST', [
            'network' => 'Globe',
            'eload_number' => '09123456789',
            'price' => 100.00,
            'status' => 'completed',
        ]);
        
        // Mock the Auth facade
        if (!class_exists('Auth')) {
            class Auth {
                public static function id() {
                    return 1;
                }
            }
        }
        
        echo "   - Mock request created\n";
        
        // This might cause an error - let's see what happens
        $result = $controller->processLoad($mockRequest);
        echo "   - processLoad method: OK\n";
        
    } catch (Exception $e) {
        echo "   - processLoad method: ERROR - " . $e->getMessage() . "\n";
        echo "   - File: " . $e->getFile() . "\n";
        echo "   - Line: " . $e->getLine() . "\n";
        
        // Show the full error trace
        echo "   - Stack trace:\n";
        $trace = $e->getTrace();
        foreach ($trace as $index => $traceItem) {
            if ($index < 5) { // Show first 5 items
                echo "     " . ($index + 1) . ". " . ($traceItem['file'] ?? 'Unknown') . ":" . ($traceItem['line'] ?? 'Unknown') . "\n";
            }
        }
    }
    
    // Check if there are any syntax errors in the controller file
    echo "\n6. Checking controller syntax:\n";
    $controllerFile = 'c:\xampp\htdocs\Business_System\app\Http\Controllers\EloadController.php';
    if (file_exists($controllerFile)) {
        $output = [];
        $returnCode = 0;
        exec("php -l \"$controllerFile\" 2>&1", $output, $returnCode);
        
        if ($returnCode === 0) {
            echo "   - Controller syntax: OK\n";
        } else {
            echo "   - Controller syntax: ERROR\n";
            foreach ($output as $line) {
                echo "     " . $line . "\n";
            }
        }
    } else {
        echo "   - Controller file not found\n";
    }
    
    echo "\n" . str_repeat("=", 50) . "\n";
    echo "Debug complete. If you see any ERROR messages above,\n";
    echo "those are the issues that need to be fixed.\n";
    
} catch (Exception $e) {
    echo "Critical error: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . "\n";
    echo "Line: " . $e->getLine() . "\n";
}

echo "\n=== Debug Complete ===\n";
?>
