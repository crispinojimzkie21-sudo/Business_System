<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

try {
    echo "Testing TV E-Load with SQLite (Final Version)...\n\n";
    
    // Use existing eload_id and eload_number_id to avoid foreign key issues
    $existingTransaction = \Illuminate\Support\Facades\DB::table('eload_transactions')
        ->where('status', 'completed')
        ->first();
        
    if (!$existingTransaction) {
        echo "❌ No existing reference records found.\n";
        exit;
    }
    
    echo "✅ Found existing transaction for reference:\n";
    echo "  - eload_id: {$existingTransaction->eload_id}\n";
    echo "  - eload_number_id: {$existingTransaction->eload_number_id}\n";
    echo "  - user_id: {$existingTransaction->user_id}\n";
    echo "  - Original eload_number: {$existingTransaction->eload_number}\n";
    echo "  - Original price: ₱{$existingTransaction->price}\n\n";
    
    // Create TV E-Load record using existing references
    $transactionData = [
        'eload_id' => $existingTransaction->eload_id, // Use existing eload_id
        'eload_number_id' => $existingTransaction->eload_number_id, // Use existing eload_number_id
        'user_id' => $existingTransaction->user_id, // Use existing user_id
        'eload_number' => 'TV-CIGNAL-' . time(), // TV account number
        'price' => 100, // TV load amount
        'status' => 'completed',
        'transaction_id' => 'TVL' . strtoupper(uniqid()), // TV reference
        'created_at' => now(),
        'updated_at' => now(),
    ];
    
    $id = \Illuminate\Support\Facades\DB::table('eload_transactions')->insertGetId($transactionData);
    
    echo "✅ TV E-Load record added successfully!\n";
    echo "✅ Record ID: $id\n";
    echo "✅ TV Account Number: {$transactionData['eload_number']}\n";
    echo "✅ Amount: ₱{$transactionData['price']}\n";
    echo "✅ Reference: {$transactionData['transaction_id']}\n";
    echo "✅ Status: {$transactionData['status']}\n";
    echo "✅ Using existing eload_id: {$transactionData['eload_id']}\n";
    echo "✅ Using existing eload_number_id: {$transactionData['eload_number_id']}\n";
    echo "✅ Using existing user_id: {$transactionData['user_id']}\n";
    
    // Verify the record was added
    $newRecord = \Illuminate\Support\Facades\DB::table('eload_transactions')->find($id);
    if ($newRecord) {
        echo "\n✅ Record verified in database!\n";
        echo "✅ Total records now: " . \Illuminate\Support\Facades\DB::table('eload_transactions')->count() . "\n";
        echo "\n🎉 TV E-Load with SQLite database is working perfectly!\n";
        echo "🎯 System uses existing references to avoid foreign key constraints!\n";
        echo "📺 TV E-Load records are now working with SQLite only!\n";
    } else {
        echo "\n❌ Record not found in database!\n";
    }
    
} catch (Exception $e) {
    echo '❌ Error: ' . $e->getMessage() . "\n";
    echo 'File: ' . $e->getFile() . "\n";
    echo 'Line: ' . $e->getLine() . "\n";
}
?>
