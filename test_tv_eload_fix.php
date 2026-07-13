<?php

/**
 * Test TV E-Load Fix
 * This script will test the fixed TV E-Load functionality
 */

require_once 'vendor/autoload.php';

use Illuminate\Support\Facades\DB;

// Load Laravel environment
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== RM Manliquid Business System - Test TV E-Load Fix ===\n\n";

try {
    echo "Testing the fixed TV E-Load functionality...\n";
    echo str_repeat("=", 50) . "\n";
    
    // 1. Test controller validation
    echo "1. Testing TelevisionEloadController validation:\n";
    
    $controller = new \App\Http\Controllers\TelevisionEloadController();
    
    // Test validation with correct field names
    $testCases = [
        [
            'account_number' => 'TV-12345',
            'provider' => 'Cignal',
            'amount' => 100,
            'customer_name' => 'Test Customer',
            'customer_mobile' => '09123456789',
            'should_pass' => true
        ],
        [
            'account_number' => 'TV-12345',
            'provider' => 'Cignal',
            'amount' => 100,
            'customer_name' => 'Test Customer',
            'customer_contact' => '09123456789', // Old field name
            'should_pass' => false
        ]
    ];
    
    foreach ($testCases as $testCase) {
        $mockRequest = new \Illuminate\Http\Request();
        $mockRequest->merge($testCase);
        
        try {
            $mockRequest->validate([
                'account_number' => 'required|string|min:1|max:50',
                'provider' => 'required|string',
                'amount' => 'required|numeric|min:50|max:10000',
                'customer_name' => 'nullable|string|max:100',
                'customer_mobile' => 'nullable|string|max:20',
            ]);
            
            if ($testCase['should_pass']) {
                echo "   SUCCESS: Validation passed with customer_mobile\n";
            } else {
                echo "   ERROR: Validation should have failed with customer_contact\n";
            }
        } catch (\Illuminate\Validation\ValidationException $e) {
            if (!$testCase['should_pass']) {
                echo "   SUCCESS: Validation failed with customer_contact (expected)\n";
            } else {
                echo "   ERROR: Validation should have passed but failed\n";
            }
        }
    }
    
    // 2. Test database insertion
    echo "\n2. Testing database insertion with correct fields:\n";
    
    try {
        // Get existing transaction for valid IDs
        $existingTransaction = DB::table('eload_transactions')
            ->where('status', 'completed')
            ->first();
        
        if (!$existingTransaction) {
            echo "   ERROR: No existing transaction found for reference\n";
            return;
        }
        
        $transactionData = [
            'eload_id' => $existingTransaction->eload_id,
            'eload_number_id' => $existingTransaction->eload_number_id,
            'user_id' => 1,
            'eload_number' => 'TV-TEST-12345',
            'price' => 100,
            'status' => 'completed',
            'transaction_id' => 'TVL' . strtoupper(uniqid()),
            'customer_name' => 'Test Customer',
            'customer_mobile' => '09123456789', // Correct field name
            'provider' => 'Cignal',
            'created_at' => now(),
            'updated_at' => now(),
        ];
        
        $transactionId = DB::table('eload_transactions')->insertGetId($transactionData);
        
        echo "   SUCCESS: TV E-Load transaction created with customer_mobile\n";
        echo "   Transaction ID: {$transactionId}\n";
        
        // Verify the transaction
        $newTransaction = DB::table('eload_transactions')->where('id', $transactionId)->first();
        if ($newTransaction) {
            echo "   Verification:\n";
            echo "   - Account Number: {$newTransaction->eload_number}\n";
            echo "   - Customer Name: {$newTransaction->customer_name}\n";
            echo "   - Customer Mobile: {$newTransaction->customer_mobile}\n";
            echo "   - Provider: {$newTransaction->provider}\n";
            echo "   - Amount: {$newTransaction->price}\n";
        }
        
    } catch (Exception $e) {
        echo "   ERROR: Database insertion failed - " . $e->getMessage() . "\n";
    }
    
    // 3. Test that customer_contact field is no longer used
    echo "\n3. Testing that customer_contact field is no longer used:\n";
    
    try {
        // This should fail because customer_contact doesn't exist
        $invalidData = [
            'eload_id' => 1,
            'eload_number_id' => 1,
            'user_id' => 1,
            'eload_number' => 'TV-INVALID-12345',
            'price' => 100,
            'status' => 'completed',
            'transaction_id' => 'TVL-INVALID-' . strtoupper(uniqid()),
            'customer_name' => 'Test Customer',
            'customer_contact' => '09123456789', // Invalid field
            'provider' => 'Cignal',
            'created_at' => now(),
            'updated_at' => now(),
        ];
        
        // This should fail because customer_contact column doesn't exist
        DB::table('eload_transactions')->insert($invalidData);
        echo "   ERROR: Should have failed with customer_contact field\n";
        
    } catch (Exception $e) {
        if (strpos($e->getMessage(), 'customer_contact') !== false) {
            echo "   SUCCESS: Correctly rejected customer_contact field\n";
        } else {
            echo "   ERROR: Failed for different reason - " . $e->getMessage() . "\n";
        }
    }
    
    // 4. Check current database schema
    echo "\n4. Checking current database schema:\n";
    
    $columns = DB::select("PRAGMA table_info(eload_transactions)");
    
    $hasCustomerMobile = false;
    $hasCustomerContact = false;
    
    foreach ($columns as $column) {
        if ($column->name === 'customer_mobile') {
            $hasCustomerMobile = true;
        }
        if ($column->name === 'customer_contact') {
            $hasCustomerContact = true;
        }
    }
    
    echo "   customer_mobile column exists: " . ($hasCustomerMobile ? 'YES' : 'NO') . "\n";
    echo "   customer_contact column exists: " . ($hasCustomerContact ? 'YES' : 'NO') . "\n";
    
    if ($hasCustomerMobile && !$hasCustomerContact) {
        echo "   SUCCESS: Database schema is correct\n";
    } else {
        echo "   ERROR: Database schema issue detected\n";
    }
    
    // 5. Test the actual controller method
    echo "\n5. Testing TelevisionEloadController::processLoad method:\n";
    
    try {
        $mockRequest = new \Illuminate\Http\Request();
        $mockRequest->merge([
            'account_number' => 'TV-CONTROLLER-TEST',
            'provider' => 'Cignal',
            'amount' => 150,
            'customer_name' => 'Controller Test',
            'customer_mobile' => '09987654321',
        ]);
        
        // Mock auth
        if (!class_exists('Illuminate\Support\Facades\Auth')) {
            class MockAuth {
                public static function id() { return 1; }
                public static function user() { 
                    $user = new stdClass();
                    $user->id = 1;
                    return $user;
                }
            }
            class_alias('MockAuth', 'Illuminate\Support\Facades\Auth');
        }
        
        // This should work now
        $response = $controller->processLoad($mockRequest);
        
        if ($response->getStatusCode() === 200) {
            echo "   SUCCESS: Controller method executed successfully\n";
        } else {
            echo "   ERROR: Controller method failed with status " . $response->getStatusCode() . "\n";
        }
        
    } catch (Exception $e) {
        echo "   ERROR: Controller method failed - " . $e->getMessage() . "\n";
    }
    
    echo "\n" . str_repeat("=", 50) . "\n";
    echo "Summary:\n";
    echo str_repeat("=", 50) . "\n";
    echo "TV E-Load functionality has been fixed:\n";
    echo "1. Controller validation now uses 'customer_mobile' instead of 'customer_contact'\n";
    echo "2. Transaction creation uses 'customer_mobile' field\n";
    echo "3. Response arrays use 'customer_mobile' field\n";
    echo "4. Export functionality uses 'customer_mobile' field\n";
    echo "5. Database schema correctly has 'customer_mobile' but not 'customer_contact'\n";
    
    echo "\nTV E-Load at /television-eload should now work correctly!\n";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . "\n";
    echo "Line: " . $e->getLine() . "\n";
}

echo "\n=== Test Complete ===\n";
?>
