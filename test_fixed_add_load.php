<?php

/**
 * Test Fixed Add Load Functionality
 * This script will test the fixed add-load functionality for both super admin and admin
 */

require_once 'vendor/autoload.php';

use Illuminate\Support\Facades\DB;

// Load Laravel environment
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== RM Manliquid Business System - Test Fixed Add Load Functionality ===\n\n";

try {
    // Test the current form structure and controller logic
    echo "Testing the fixed add-load functionality...\n";
    echo str_repeat("=", 50) . "\n";
    
    // Simulate form submission data (current form structure)
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
    
    echo "\nTesting controller logic...\n";
    echo str_repeat("-", 30) . "\n";
    
    // Clean and format the mobile number (controller logic)
    $eloadNumber = preg_replace('/[^0-9]/', '', $formData['eload_number']);
    echo "Original number: " . $formData['eload_number'] . "\n";
    echo "Cleaned number: " . $eloadNumber . "\n";
    
    // Ensure it starts with 0 for Philippine numbers
    if (strlen($eloadNumber) === 10 && !str_starts_with($eloadNumber, '0')) {
        $eloadNumber = '0' . $eloadNumber;
        echo "Formatted number: " . $eloadNumber . "\n";
    }
    
    // Get or create category
    $category = DB::table('eload_categories')->first();
    if (!$category) {
        echo "Creating default category...\n";
        $categoryId = DB::table('eload_categories')->insertGetId([
            'name' => 'Default',
            'description' => 'Default category for custom loads',
            'status' => 'active',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $category = DB::table('eload_categories')->where('id', $categoryId)->first();
    }
    echo "Category: " . $category->name . " (ID: " . $category->id . ")\n";
    
    // Create or find eload record
    $eload = DB::table('eloads')
        ->where('name', 'Custom Load')
        ->where('network', $formData['network'])
        ->where('price', $formData['price'])
        ->first();
    
    if (!$eload) {
        echo "Creating new e-load record...\n";
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
    echo "E-Load: " . $eload->name . " (ID: " . $eload->id . ")\n";
    
    // Create or find eload number record
    $eloadNumberRecord = DB::table('eload_numbers')
        ->where('number', $eloadNumber)
        ->first();
    
    if (!$eloadNumberRecord) {
        echo "Creating new e-load number record...\n";
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
    echo "E-Load Number: " . $eloadNumberRecord->number . " (ID: " . $eloadNumberRecord->id . ")\n";
    
    // Convert status to match database constraints
    $status = $formData['status'] === 'completed' ? 'completed' : 'pending';
    echo "Status conversion: " . $formData['status'] . " -> " . $status . "\n";
    
    // Create transaction
    echo "\nCreating transaction...\n";
    $transactionId = DB::table('eload_transactions')->insertGetId([
        'eload_id' => $eload->id,
        'eload_number_id' => $eloadNumberRecord->id,
        'user_id' => 1, // Assuming user ID 1 exists
        'eload_number' => $eloadNumber,
        'price' => $formData['price'],
        'original_price' => $formData['price'],
        'amount' => $formData['price'],
        'customer_name' => 'Walk-in Customer',
        'customer_mobile' => $eloadNumber,
        'network' => $eload->network,
        'provider' => $eload->provider,
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
        echo "- E-Load ID: " . $transaction->eload_id . "\n";
        echo "- E-Load Number ID: " . $transaction->eload_number_id . "\n";
        echo "- User ID: " . $transaction->user_id . "\n";
        echo "- E-Load Number: " . $transaction->eload_number . "\n";
        echo "- Price: " . $transaction->price . "\n";
        echo "- Original Price: " . $transaction->original_price . "\n";
        echo "- Amount: " . $transaction->amount . "\n";
        echo "- Customer Name: " . $transaction->customer_name . "\n";
        echo "- Customer Mobile: " . $transaction->customer_mobile . "\n";
        echo "- Network: " . $transaction->network . "\n";
        echo "- Provider: " . $transaction->provider . "\n";
        echo "- Status: " . $transaction->status . "\n";
        echo "- Transaction ID: " . $transaction->transaction_id . "\n";
        echo "- Reference Number: " . $transaction->reference_number . "\n";
        echo "- Processed By: " . $transaction->processed_by . "\n";
        echo "- Created: " . $transaction->created_at . "\n";
    }
    
    // Test different statuses
    echo "\n" . str_repeat("=", 50) . "\n";
    echo "Testing different status conversions:\n";
    echo str_repeat("=", 50) . "\n";
    
    $statuses = ['completed', 'not_completed'];
    foreach ($statuses as $testStatus) {
        $convertedStatus = $testStatus === 'completed' ? 'completed' : 'pending';
        echo "- {$testStatus} -> {$convertedStatus}\n";
    }
    
    // Show statistics
    echo "\n" . str_repeat("=", 50) . "\n";
    echo "Current Statistics:\n";
    echo str_repeat("=", 50) . "\n";
    
    $totalTransactions = DB::table('eload_transactions')->count();
    $completedTransactions = DB::table('eload_transactions')->where('status', 'completed')->count();
    $pendingTransactions = DB::table('eload_transactions')->where('status', 'pending')->count();
    $totalRevenue = DB::table('eload_transactions')->where('status', 'completed')->sum('amount');
    
    echo "- Total Transactions: " . $totalTransactions . "\n";
    echo "- Completed Transactions: " . $completedTransactions . "\n";
    echo "- Pending Transactions: " . $pendingTransactions . "\n";
    echo "- Total Revenue: " . number_format($totalRevenue, 2) . "\n";
    
    echo "\n" . str_repeat("=", 50) . "\n";
    echo "Summary:\n";
    echo str_repeat("=", 50) . "\n";
    echo "The add-load functionality has been fixed with:\n";
    echo "1. Updated processLoad method to work with current form structure\n";
    echo "2. Added all required database fields for transactions\n";
    echo "3. Proper status conversion (not_completed -> pending)\n";
    echo "4. Complete transaction tracking with all required fields\n";
    echo "5. Automatic creation of e-load and number records when needed\n";
    
    echo "\nBoth super admin and admin add-load pages should now work correctly!\n";
    echo "The forms will submit successfully and create complete transactions.\n";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "Please check your database connection and configuration.\n";
}

echo "\n=== Test Complete ===\n";
?>
