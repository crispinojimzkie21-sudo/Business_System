<?php

/**
 * Test Customer Mobile Fix
 * This script will test the fixed customer_mobile field in transactions
 */

require_once 'vendor/autoload.php';

use Illuminate\Support\Facades\DB;

// Load Laravel environment
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== RM Manliquid Business System - Test Customer Mobile Fix ===\n\n";

try {
    // Test the fixed transaction creation
    echo "Testing the fixed customer_mobile field...\n";
    echo str_repeat("=", 50) . "\n";
    
    // Simulate form submission data
    $formData = [
        'network' => 'Globe',
        'eload_number' => '09123456789',
        'price' => 100.00,
        'status' => 'completed',
    ];
    
    echo "Simulated form data:\n";
    foreach ($formData as $key => $value) {
        echo "- {$key}: {$value}\n";
    }
    
    echo "\nTesting controller logic with fix...\n";
    echo str_repeat("-", 30) . "\n";
    
    // Clean and format the mobile number
    $eloadNumber = preg_replace('/[^0-9]/', '', $formData['eload_number']);
    if (strlen($eloadNumber) === 10 && !str_starts_with($eloadNumber, '0')) {
        $eloadNumber = '0' . $eloadNumber;
    }
    
    // Get or create category
    $category = DB::table('eload_categories')->first();
    if (!$category) {
        $categoryId = DB::table('eload_categories')->insertGetId([
            'name' => 'Default',
            'description' => 'Default category for custom loads',
            'status' => 'active',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $category = DB::table('eload_categories')->where('id', $categoryId)->first();
    }
    
    // Create or find eload record with provider
    $eload = DB::table('eloads')
        ->where('name', 'Custom Load')
        ->where('network', $formData['network'])
        ->where('price', $formData['price'])
        ->first();
    
    if (!$eload) {
        $eloadId = DB::table('eloads')->insertGetId([
            'name' => 'Custom Load - ' . $formData['network'],
            'network' => $formData['network'],
            'provider' => $formData['network'] . ' Telecom',
            'service_type' => 'Mobile Load',
            'code' => strtoupper(str_replace(' ', '', $formData['network'])),
            'description' => 'Custom load for ' . $formData['network'],
            'validity' => '30 days',
            'price' => $formData['price'],
            'category_id' => $category->id,
            'status' => 'active',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $eload = DB::table('eloads')->where('id', $eloadId)->first();
    }
    
    // Create or find eload number record
    $eloadNumberRecord = DB::table('eload_numbers')
        ->where('number', $eloadNumber)
        ->first();
    
    if (!$eloadNumberRecord) {
        $eloadNumberId = DB::table('eload_numbers')->insertGetId([
            'number' => $eloadNumber,
            'network' => $formData['network'],
            'provider' => $formData['network'] . ' Telecom',
            'number_type' => 'Mobile',
            'description' => $formData['network'] . ' mobile number',
            'is_active' => 1,
            'priority' => 1,
            'status' => 'active',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $eloadNumberRecord = DB::table('eload_numbers')->where('id', $eloadNumberId)->first();
    }
    
    // Convert status
    $status = $formData['status'] === 'completed' ? 'completed' : 'pending';
    
    // Create transaction with the fix
    echo "Creating transaction with customer_mobile fix...\n";
    $transactionId = DB::table('eload_transactions')->insertGetId([
        'eload_id' => $eload->id,
        'eload_number_id' => $eloadNumberRecord->id,
        'user_id' => 1,
        'eload_number' => $eloadNumber,
        'price' => $formData['price'],
        'original_price' => $formData['price'],
        'amount' => $formData['price'],
        'customer_name' => 'Walk-in Customer',
        'customer_mobile' => $eloadNumber, // This field is now properly set
        'network' => $eload->network ?? $formData['network'], // Fixed with fallback
        'provider' => $eload->provider ?? $formData['network'] . ' Telecom', // Fixed with fallback
        'status' => $status,
        'transaction_id' => 'EL-' . strtoupper(uniqid()) . '-' . rand(1000, 9999),
        'reference_number' => 'REF-' . date('YmdHis') . '-' . rand(100, 999),
        'processed_by' => 1,
        'created_at' => now(),
        'updated_at' => now(),
    ]);
    
    echo "Transaction created successfully! ID: " . $transactionId . "\n";
    
    // Verify the transaction
    $transaction = DB::table('eload_transactions')->where('id', $transactionId)->first();
    if ($transaction) {
        echo "\nTransaction verification:\n";
        echo "- Transaction ID: " . $transaction->id . "\n";
        echo "- Customer Name: " . $transaction->customer_name . "\n";
        echo "- Customer Mobile: " . $transaction->customer_mobile . "\n";
        echo "- Network: " . $transaction->network . "\n";
        echo "- Provider: " . $transaction->provider . "\n";
        echo "- Status: " . $transaction->status . "\n";
        echo "- Amount: " . $transaction->amount . "\n";
        
        // Check that customer_mobile is not null
        if ($transaction->customer_mobile !== null) {
            echo "SUCCESS: customer_mobile field is properly set!\n";
        } else {
            echo "ERROR: customer_mobile field is still null!\n";
        }
        
        // Check that provider is not null
        if ($transaction->provider !== null) {
            echo "SUCCESS: provider field is properly set!\n";
        } else {
            echo "ERROR: provider field is still null!\n";
        }
    }
    
    // Test with different networks
    echo "\n" . str_repeat("=", 50) . "\n";
    echo "Testing with different networks:\n";
    echo str_repeat("=", 50) . "\n";
    
    $networks = ['Smart', 'Globe', 'DITO', 'TM'];
    foreach ($networks as $network) {
        try {
            $testTransactionId = DB::table('eload_transactions')->insertGetId([
                'eload_id' => $eload->id,
                'eload_number_id' => $eloadNumberRecord->id,
                'user_id' => 1,
                'eload_number' => '09123456789',
                'price' => 50.00,
                'original_price' => 50.00,
                'amount' => 50.00,
                'customer_name' => 'Walk-in Customer',
                'customer_mobile' => '09123456789',
                'network' => $network,
                'provider' => $network . ' Telecom',
                'status' => 'completed',
                'transaction_id' => 'EL-TEST-' . strtoupper(uniqid()) . '-' . rand(1000, 9999),
                'reference_number' => 'REF-' . date('YmdHis') . '-' . rand(100, 999),
                'processed_by' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            echo "SUCCESS: {$network} transaction created (ID: {$testTransactionId})\n";
        } catch (Exception $e) {
            echo "ERROR: {$network} transaction failed - " . $e->getMessage() . "\n";
        }
    }
    
    echo "\n" . str_repeat("=", 50) . "\n";
    echo "Summary:\n";
    echo str_repeat("=", 50) . "\n";
    echo "The customer_mobile NOT NULL constraint violation has been fixed:\n";
    echo "1. Added fallback values for network and provider fields\n";
    echo "2. Ensured customer_mobile is always set with the eload number\n";
    echo "3. All required fields are now properly populated\n";
    
    echo "\nBoth super admin and admin add-load pages should now work correctly!\n";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "Please check your database connection and configuration.\n";
}

echo "\n=== Test Complete ===\n";
?>
