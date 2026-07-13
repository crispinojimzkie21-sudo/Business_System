<?php

/**
 * Fix Existing Status Records
 * This script will fix existing transaction records with invalid status values
 */

require_once 'vendor/autoload.php';

use Illuminate\Support\Facades\DB;

// Load Laravel environment
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== RM Manliquid Business System - Fix Existing Status Records ===\n\n";

try {
    echo "Finding and fixing existing transaction records with invalid status...\n";
    echo str_repeat("=", 50) . "\n";
    
    // Find all transactions with invalid status values
    $invalidTransactions = DB::table('eload_transactions')
        ->whereNotIn('status', ['pending', 'completed', 'failed'])
        ->get();
    
    echo "Found " . $invalidTransactions->count() . " transactions with invalid status:\n";
    
    foreach ($invalidTransactions as $transaction) {
        echo "- ID: {$transaction->id}, Status: '{$transaction->status}'\n";
    }
    
    if ($invalidTransactions->count() > 0) {
        echo "\nFixing invalid status values...\n";
        
        foreach ($invalidTransactions as $transaction) {
            // Convert 'not_completed' to 'pending'
            $newStatus = $transaction->status === 'not_completed' ? 'pending' : 'pending';
            
            DB::table('eload_transactions')
                ->where('id', $transaction->id)
                ->update([
                    'status' => $newStatus,
                    'updated_at' => now(),
                ]);
            
            echo "- Transaction ID {$transaction->id}: '{$transaction->status}' -> '{$newStatus}'\n";
        }
        
        echo "\nAll invalid status records have been fixed!\n";
    } else {
        echo "\nNo invalid status records found.\n";
    }
    
    // Verify the fix
    echo "\nVerifying the fix...\n";
    $remainingInvalid = DB::table('eload_transactions')
        ->whereNotIn('status', ['pending', 'completed', 'failed'])
        ->count();
    
    echo "Remaining invalid status records: {$remainingInvalid}\n";
    
    if ($remainingInvalid === 0) {
        echo "SUCCESS: All transaction status values are now valid!\n";
    } else {
        echo "ERROR: Some invalid status records still exist.\n";
    }
    
    // Show current status distribution
    echo "\nCurrent status distribution:\n";
    $statusCounts = DB::table('eload_transactions')
        ->selectRaw('status, COUNT(*) as count')
        ->groupBy('status')
        ->get();
    
    foreach ($statusCounts as $statusCount) {
        echo "- {$statusCount->status}: {$statusCount->count}\n";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . "\n";
    echo "Line: " . $e->getLine() . "\n";
}

echo "\n=== Fix Complete ===\n";
?>
