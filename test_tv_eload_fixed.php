<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

try {
    echo "Testing TV E-Load with SQLite database (Updated Logic)...\n\n";
    
    // Simulate the getOrCreateTVLoadEload method
    $provider = 'Cignal';
    $amount = 100;
    
    // Try to find an existing eload with matching price
    $eload = \Illuminate\Support\Facades\DB::table('eloads')
        ->where('price', $amount)
        ->where('status', 'active')
        ->first();
        
    if ($eload) {
        $eloadId = $eload->id;
        echo "✅ Found existing eload: ID $eloadId, Price: ₱$eload->price\n";
    } else {
        // Create a new eload for TV E-Load
        $eloadId = \Illuminate\Support\Facades\DB::table('eloads')->insertGetId([
            'name' => "TV Load - {$provider}",
            'network' => $provider,
            'price' => $amount,
            'category_id' => 17, // Use existing category
            'status' => 'active',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        echo "✅ Created new eload: ID $eloadId for $provider - ₱$amount\n";
    }
    
    // Now create the transaction record
    $transactionData = [
        'eload_id' => $eloadId, // Required field - reference to eloads table
        'user_id' => 1, // Assuming user ID 1 exists
        'eload_number' => 'TV-' . $provider . '-' . time(),
        'price' => $amount,
        'status' => 'completed',
        'transaction_id' => 'TVL' . strtoupper(uniqid()),
        'created_at' => now(),
        'updated_at' => now(),
    ];
    
    // Insert record
    $id = \Illuminate\Support\Facades\DB::table('eload_transactions')->insertGetId($transactionData);
    
    echo "\n✅ TV E-Load record added successfully!\n";
    echo "✅ Record ID: $id\n";
    echo "✅ Account Number: {$transactionData['eload_number']}\n";
    echo "✅ Amount: ₱{$transactionData['price']}\n";
    echo "✅ Reference: {$transactionData['transaction_id']}\n";
    echo "✅ Status: {$transactionData['status']}\n";
    echo "✅ Eload ID: {$transactionData['eload_id']}\n";
    
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
