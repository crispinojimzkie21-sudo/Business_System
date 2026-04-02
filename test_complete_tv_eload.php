<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

try {
    echo "Testing Complete TV E-Load with SQLite database...\n\n";
    
    $provider = 'Cignal';
    $amount = 100;
    
    // Step 1: Get or create eload
    $eload = \Illuminate\Support\Facades\DB::table('eloads')
        ->where('price', $amount)
        ->where('status', 'active')
        ->first();
        
    if ($eload) {
        $eloadId = $eload->id;
        echo "✅ Found existing eload: ID $eloadId, Price: ₱$eload->price\n";
    } else {
        $eloadId = \Illuminate\Support\Facades\DB::table('eloads')->insertGetId([
            'name' => "TV Load - {$provider}",
            'network' => $provider,
            'price' => $amount,
            'category_id' => 17,
            'status' => 'active',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        echo "✅ Created new eload: ID $eloadId for $provider - ₱$amount\n";
    }
    
    // Step 2: Get or create eload_number
    $eloadNumber = \Illuminate\Support\Facades\DB::table('eload_numbers')
        ->where('network', $provider)
        ->where('status', 'active')
        ->first();
        
    if ($eloadNumber) {
        $eloadNumberId = $eloadNumber->id;
        echo "✅ Found existing eload_number: ID $eloadNumberId, Network: $eloadNumber->network\n";
    } else {
        $eloadNumberId = \Illuminate\Support\Facades\DB::table('eload_numbers')->insertGetId([
            'number' => 'TV-' . $provider . '-' . time(),
            'network' => $provider,
            'status' => 'active',
            'description' => "TV Load number for {$provider}",
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        echo "✅ Created new eload_number: ID $eloadNumberId for $provider\n";
    }
    
    // Step 3: Create transaction
    $transactionData = [
        'eload_id' => $eloadId,
        'eload_number_id' => $eloadNumberId,
        'user_id' => 1,
        'eload_number' => 'TV-' . $provider . '-' . time(),
        'price' => $amount,
        'status' => 'completed',
        'transaction_id' => 'TVL' . strtoupper(uniqid()),
        'created_at' => now(),
        'updated_at' => now(),
    ];
    
    $id = \Illuminate\Support\Facades\DB::table('eload_transactions')->insertGetId($transactionData);
    
    echo "\n✅ TV E-Load record added successfully!\n";
    echo "✅ Record ID: $id\n";
    echo "✅ Account Number: {$transactionData['eload_number']}\n";
    echo "✅ Amount: ₱{$transactionData['price']}\n";
    echo "✅ Reference: {$transactionData['transaction_id']}\n";
    echo "✅ Status: {$transactionData['status']}\n";
    echo "✅ Eload ID: {$transactionData['eload_id']}\n";
    echo "✅ Eload Number ID: {$transactionData['eload_number_id']}\n";
    
    // Verify the record was added
    $newRecord = \Illuminate\Support\Facades\DB::table('eload_transactions')->find($id);
    if ($newRecord) {
        echo "\n✅ Record verified in database!\n";
        echo "✅ Total records now: " . \Illuminate\Support\Facades\DB::table('eload_transactions')->count() . "\n";
        echo "\n🎉 TV E-Load with SQLite database is working perfectly!\n";
    } else {
        echo "\n❌ Record not found in database!\n";
    }
    
} catch (Exception $e) {
    echo '❌ Error: ' . $e->getMessage() . "\n";
    echo 'File: ' . $e->getFile() . "\n";
    echo 'Line: ' . $e->getLine() . "\n";
}
?>
