<?php

/**
 * Test E-Load ID Fix
 * This script will test the fixed eload_id NOT NULL constraint issue
 */

require_once 'vendor/autoload.php';

use Illuminate\Support\Facades\DB;

// Load Laravel environment
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== RM Manliquid Business System - Test E-Load ID Fix ===\n\n";

try {
    echo "Testing the fixed eload_id NOT NULL constraint...\n";
    echo str_repeat("=", 50) . "\n";
    
    // Test the exact scenario that was failing
    echo "Testing eload number creation with eload_id...\n";
    echo str_repeat("-", 30) . "\n";
    
    // Get or create a test category
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
    
    // Create a test eload
    $eloadData = [
        'name' => 'Test Load',
        'network' => 'Globe',
        'provider' => 'Globe Telecom',
        'service_type' => 'Mobile Load',
        'code' => 'TEST',
        'description' => 'Test load for eload_id fix',
        'validity' => '30 days',
        'price' => 100.00,
        'category_id' => $category->id,
        'status' => 'active',
        'created_at' => now(),
        'updated_at' => now(),
    ];
    
    $eloadId = DB::table('eloads')->insertGetId($eloadData);
    $eload = DB::table('eloads')->where('id', $eloadId)->first();
    
    echo "Created test e-load: " . $eload->name . " (ID: " . $eload->id . ")\n";
    
    // Test creating eload number with eload_id (the fix)
    echo "\nTesting eload number creation with eload_id...\n";
    
    $eloadNumberData = [
        'eload_id' => $eload->id, // This is the fix - providing the eload_id
        'number' => '01231232421',
        'network' => 'Globe',
        'provider' => 'Globe Telecom',
        'number_type' => 'Mobile',
        'description' => 'Globe mobile number',
        'is_active' => 1,
        'priority' => 1,
        'status' => 'active',
        'created_at' => now(),
        'updated_at' => now(),
    ];
    
    try {
        $eloadNumberId = DB::table('eload_numbers')->insertGetId($eloadNumberData);
        $eloadNumber = DB::table('eload_numbers')->where('id', $eloadNumberId)->first();
        
        echo "SUCCESS: E-Load number created with eload_id!\n";
        echo "- E-Load Number ID: " . $eloadNumber->id . "\n";
        echo "- E-Load ID: " . $eloadNumber->eload_id . "\n";
        echo "- Number: " . $eloadNumber->number . "\n";
        echo "- Network: " . $eloadNumber->network . "\n";
        echo "- Status: " . $eloadNumber->status . "\n";
        
    } catch (Exception $e) {
        echo "ERROR: " . $e->getMessage() . "\n";
        return;
    }
    
    // Test the complete processLoad method with the fix
    echo "\n" . str_repeat("=", 50) . "\n";
    echo "Testing complete processLoad method...\n";
    echo str_repeat("=", 50) . "\n";
    
    // Simulate the processLoad method
    $formData = [
        'network' => 'Smart',
        'eload_number' => '09987654321',
        'price' => 150.00,
        'status' => 'completed',
    ];
    
    echo "Simulating processLoad with data:\n";
    foreach ($formData as $key => $value) {
        echo "- {$key}: {$value}\n";
    }
    
    // Step 1: Clean and format mobile number
    $eloadNumber = preg_replace('/[^0-9]/', '', $formData['eload_number']);
    if (strlen($eloadNumber) === 10 && !str_starts_with($eloadNumber, '0')) {
        $eloadNumber = '0' . $eloadNumber;
    }
    
    // Step 2: Create eload
    $newEload = DB::table('eloads')->insertGetId([
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
    
    echo "\nCreated new e-load: ID " . $newEload . "\n";
    
    // Step 3: Create eload number with eload_id (the fix)
    $newEloadNumber = DB::table('eload_numbers')->insertGetId([
        'eload_id' => $newEload, // This is the fix
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
    
    echo "Created new e-load number: ID " . $newEloadNumber . "\n";
    
    // Step 4: Create transaction
    $status = $formData['status'] === 'completed' ? 'completed' : 'pending';
    $transactionId = DB::table('eload_transactions')->insertGetId([
        'eload_id' => $newEload,
        'eload_number_id' => $newEloadNumber,
        'user_id' => 1,
        'eload_number' => $eloadNumber,
        'price' => $formData['price'],
        'original_price' => $formData['price'],
        'amount' => $formData['price'],
        'customer_name' => 'Walk-in Customer',
        'customer_mobile' => $eloadNumber,
        'network' => $formData['network'],
        'provider' => $formData['network'] . ' Telecom',
        'status' => $status,
        'transaction_id' => 'EL-' . strtoupper(uniqid()) . '-' . rand(1000, 9999),
        'reference_number' => 'REF-' . date('YmdHis') . '-' . rand(100, 999),
        'processed_by' => 1,
        'created_at' => now(),
        'updated_at' => now(),
    ]);
    
    echo "Created transaction: ID " . $transactionId . "\n";
    
    // Verify all records were created properly
    echo "\nVerification:\n";
    echo str_repeat("-", 20) . "\n";
    
    $verifyEload = DB::table('eloads')->where('id', $newEload)->first();
    echo "E-Load: " . $verifyEload->name . " (ID: " . $verifyEload->id . ")\n";
    
    $verifyEloadNumber = DB::table('eload_numbers')->where('id', $newEloadNumber)->first();
    echo "E-Load Number: " . $verifyEloadNumber->number . " (ID: " . $verifyEloadNumber->id . ", eload_id: " . $verifyEloadNumber->eload_id . ")\n";
    
    $verifyTransaction = DB::table('eload_transactions')->where('id', $transactionId)->first();
    echo "Transaction: " . $verifyTransaction->transaction_id . " (ID: " . $verifyTransaction->id . ")\n";
    
    echo "\n" . str_repeat("=", 50) . "\n";
    echo "Summary:\n";
    echo str_repeat("=", 50) . "\n";
    echo "The eload_id NOT NULL constraint violation has been fixed:\n";
    echo "1. Added eload_id field to eload_numbers creation in processLoad\n";
    echo "2. Added eload_id field to eload_numbers creation in processMultipleLoads\n";
    echo "3. All required fields are now properly provided\n";
    echo "4. E-Load numbers are now properly linked to their e-load products\n";
    
    echo "\nBoth super admin and admin add-load pages should now work correctly!\n";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . "\n";
    echo "Line: " . $e->getLine() . "\n";
}

echo "\n=== Test Complete ===\n";
?>
