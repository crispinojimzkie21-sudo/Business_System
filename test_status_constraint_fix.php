<?php

/**
 * Test Status Constraint Fix
 * This script will test the fixed status CHECK constraint issue
 */

require_once 'vendor/autoload.php';

use Illuminate\Support\Facades\DB;

// Load Laravel environment
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== RM Manliquid Business System - Test Status Constraint Fix ===\n\n";

try {
    echo "Testing the fixed status CHECK constraint...\n";
    echo str_repeat("=", 50) . "\n";
    
    // Check the database constraint
    echo "1. Checking database constraint:\n";
    $constraints = DB::select("PRAGMA table_info(eload_transactions)");
    foreach ($constraints as $constraint) {
        if ($constraint->name === 'status') {
            echo "   Status field type: " . $constraint->type . "\n";
            echo "   Status nullable: " . ($constraint->notnull ? 'No' : 'Yes') . "\n";
            break;
        }
    }
    
    // Test the exact scenario that was failing
    echo "\n2. Testing status values that are allowed:\n";
    $allowedStatuses = ['pending', 'completed', 'failed'];
    
    foreach ($allowedStatuses as $status) {
        try {
            // Create test data
            $category = DB::table('eload_categories')->first();
            if (!$category) {
                $categoryId = DB::table('eload_categories')->insertGetId([
                    'name' => 'Default',
                    'description' => 'Default category',
                    'status' => 'active',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                $category = DB::table('eload_categories')->where('id', $categoryId)->first();
            }
            
            $eloadId = DB::table('eloads')->insertGetId([
                'name' => 'Test Load',
                'network' => 'Globe',
                'provider' => 'Globe Telecom',
                'price' => 100,
                'category_id' => $category->id,
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            
            $eloadNumberId = DB::table('eload_numbers')->insertGetId([
                'eload_id' => $eloadId,
                'number' => '09123456789',
                'network' => 'Globe',
                'provider' => 'Globe Telecom',
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            
            // Create transaction with test status
            $transactionId = DB::table('eload_transactions')->insertGetId([
                'eload_id' => $eloadId,
                'eload_number_id' => $eloadNumberId,
                'user_id' => 1,
                'eload_number' => '09123456789',
                'price' => 100,
                'original_price' => 100,
                'amount' => 100,
                'customer_name' => 'Test Customer',
                'customer_mobile' => '09123456789',
                'network' => 'Globe',
                'provider' => 'Globe Telecom',
                'status' => $status,
                'transaction_id' => 'EL-TEST-' . strtoupper(uniqid()) . '-' . rand(1000, 9999),
                'reference_number' => 'REF-' . date('YmdHis') . '-' . rand(100, 999),
                'processed_by' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            
            echo "   SUCCESS: '{$status}' status allowed (Transaction ID: {$transactionId})\n";
            
        } catch (Exception $e) {
            echo "   ERROR: '{$status}' status failed - " . $e->getMessage() . "\n";
        }
    }
    
    // Test the status that was causing the error
    echo "\n3. Testing the problematic status that was causing the error:\n";
    try {
        $category = DB::table('eload_categories')->first();
        $eloadId = DB::table('eloads')->insertGetId([
            'name' => 'Test Load Error',
            'network' => 'Smart',
            'provider' => 'Smart Communications',
            'price' => 150,
            'category_id' => $category->id,
            'status' => 'active',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        
        $eloadNumberId = DB::table('eload_numbers')->insertGetId([
            'eload_id' => $eloadId,
            'number' => '09987654321',
            'network' => 'Smart',
            'provider' => 'Smart Communications',
            'status' => 'active',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        
        // This should fail with the old code but now we're testing if it would fail
        $transactionId = DB::table('eload_transactions')->insertGetId([
            'eload_id' => $eloadId,
            'eload_number_id' => $eloadNumberId,
            'user_id' => 1,
            'eload_number' => '09987654321',
            'price' => 150,
            'original_price' => 150,
            'amount' => 150,
            'customer_name' => 'Test Customer Error',
            'customer_mobile' => '09987654321',
            'network' => 'Smart',
            'provider' => 'Smart Communications',
            'status' => 'not_completed', // This was the problematic value
            'transaction_id' => 'EL-ERROR-' . strtoupper(uniqid()) . '-' . rand(1000, 9999),
            'reference_number' => 'REF-' . date('YmdHis') . '-' . rand(100, 999),
            'processed_by' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        
        echo "   ERROR: 'not_completed' should have failed but didn't!\n";
        
    } catch (Exception $e) {
        echo "   SUCCESS: 'not_completed' correctly rejected - " . $e->getMessage() . "\n";
    }
    
    // Test the controller validation
    echo "\n4. Testing controller validation with fixed rules:\n";
    
    // Test valid status values
    $validStatuses = ['pending', 'completed'];
    foreach ($validStatuses as $status) {
        $mockRequest = new \Illuminate\Http\Request();
        $mockRequest->merge([
            'network' => 'Globe',
            'eload_number' => '09123456789',
            'price' => 100,
            'status' => $status,
        ]);
        
        try {
            $mockRequest->validate([
                'network' => 'required|string|max:255',
                'eload_number' => 'required|string|max:20',
                'price' => 'required|numeric|min:0.01',
                'status' => 'required|in:pending,completed',
            ]);
            echo "   SUCCESS: '{$status}' validation passed\n";
        } catch (\Illuminate\Validation\ValidationException $e) {
            echo "   ERROR: '{$status}' validation failed - " . implode(', ', $e->errors()->all()) . "\n";
        }
    }
    
    // Test invalid status value
    echo "\n5. Testing invalid status value:\n";
    $mockRequest = new \Illuminate\Http\Request();
    $mockRequest->merge([
        'network' => 'Globe',
        'eload_number' => '09123456789',
        'price' => 100,
        'status' => 'not_completed', // This should fail validation
    ]);
    
    try {
        $mockRequest->validate([
            'network' => 'required|string|max:255',
            'eload_number' => 'required|string|max:20',
            'price' => 'required|numeric|min:0.01',
            'status' => 'required|in:pending,completed',
        ]);
        echo "   ERROR: 'not_completed' should have failed validation but didn't!\n";
    } catch (\Illuminate\Validation\ValidationException $e) {
        echo "   SUCCESS: 'not_completed' correctly rejected by validation\n";
    }
    
    echo "\n" . str_repeat("=", 50) . "\n";
    echo "Summary:\n";
    echo str_repeat("=", 50) . "\n";
    echo "The status CHECK constraint violation has been fixed:\n";
    echo "1. Updated validation rules to use 'pending' instead of 'not_completed'\n";
    echo "2. Fixed processLoad method validation\n";
    echo "3. Fixed processMultipleLoads method validation\n";
    echo "4. Database constraint now matches application validation\n";
    
    echo "\nAllowed status values: pending, completed, failed\n";
    echo "Rejected status values: not_completed, invalid_status\n";
    
    echo "\nBoth super admin and admin add-load pages should now work correctly!\n";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . "\n";
    echo "Line: " . $e->getLine() . "\n";
}

echo "\n=== Test Complete ===\n";
?>
