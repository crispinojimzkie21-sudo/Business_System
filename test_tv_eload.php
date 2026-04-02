<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

try {
    echo "Testing TV E-Load with SQLite database...\n\n";
    
    // Simulate adding a TV E-Load record
    $transactionData = [
        'user_id' => 1, // Assuming user ID 1 exists
        'eload_number' => 'TV-TEST-' . time(),
        'price' => 100,
        'status' => 'completed',
        'transaction_id' => 'TVL' . strtoupper(uniqid()),
        'created_at' => now(),
        'updated_at' => now(),
    ];
    
    // Insert record
    $id = \Illuminate\Support\Facades\DB::table('eload_transactions')->insertGetId($transactionData);
    
    echo "✅ TV E-Load record added successfully!\n";
    echo "✅ Record ID: $id\n";
    echo "✅ Account Number: {$transactionData['eload_number']}\n";
    echo "✅ Amount: ₱{$transactionData['price']}\n";
    echo "✅ Reference: {$transactionData['transaction_id']}\n";
    echo "✅ Status: {$transactionData['status']}\n";
    
    // Verify the record was added
    $newRecord = \Illuminate\Support\Facades\DB::table('eload_transactions')->find($id);
    if ($newRecord) {
        echo "\n✅ Record verified in database!\n";
        echo "✅ Total records now: " . \Illuminate\Support\Facades\DB::table('eload_transactions')->count() . "\n";
    } else {
        echo "\n❌ Record not found in database!\n";
    }
    
} catch (Exception $e) {
    echo '❌ Error: ' . $e->getMessage() . "\n";
    echo 'File: ' . $e->getFile() . "\n";
    echo 'Line: ' . $e->getLine() . "\n";
}
