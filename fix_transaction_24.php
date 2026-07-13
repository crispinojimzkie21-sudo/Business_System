<?php

/**
 * Fix Transaction ID 24
 * This script will fix the specific transaction with ID 24 that has invalid status
 */

require_once 'vendor/autoload.php';

use Illuminate\Support\Facades\DB;

// Load Laravel environment
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== RM Manliquid Business System - Fix Transaction ID 24 ===\n\n";

try {
    echo "Checking transaction ID 24...\n";
    echo str_repeat("=", 50) . "\n";
    
    // Check the specific transaction
    $transaction = DB::table('eload_transactions')->where('id', 24)->first();
    
    if ($transaction) {
        echo "Transaction ID 24 found:\n";
        echo "- ID: {$transaction->id}\n";
        echo "- Status: '{$transaction->status}'\n";
        echo "- Transaction ID: {$transaction->transaction_id}\n";
        echo "- Created: {$transaction->created_at}\n";
        echo "- Updated: {$transaction->updated_at}\n";
        
        if ($transaction->status === 'not_completed') {
            echo "\nFixing invalid status...\n";
            
            // Update the status to 'pending'
            DB::table('eload_transactions')
                ->where('id', 24)
                ->update([
                    'status' => 'pending',
                    'updated_at' => now(),
                ]);
            
            echo "Transaction ID 24 status updated: 'not_completed' -> 'pending'\n";
        } else {
            echo "\nTransaction ID 24 already has valid status: '{$transaction->status}'\n";
        }
    } else {
        echo "Transaction ID 24 not found.\n";
    }
    
    // Check for any other transactions with invalid status
    echo "\nChecking for any other invalid status records...\n";
    $invalidTransactions = DB::table('eload_transactions')
        ->whereNotIn('status', ['pending', 'completed', 'failed'])
        ->get();
    
    if ($invalidTransactions->count() > 0) {
        echo "Found {$invalidTransactions->count()} other transactions with invalid status:\n";
        
        foreach ($invalidTransactions as $invalidTransaction) {
            echo "- ID: {$invalidTransaction->id}, Status: '{$invalidTransaction->status}'\n";
            
            // Fix each one
            $newStatus = $invalidTransaction->status === 'not_completed' ? 'pending' : 'pending';
            
            DB::table('eload_transactions')
                ->where('id', $invalidTransaction->id)
                ->update([
                    'status' => $newStatus,
                    'updated_at' => now(),
                ]);
            
            echo "  Fixed: '{$invalidTransaction->status}' -> '{$newStatus}'\n";
        }
    } else {
        echo "No other invalid status records found.\n";
    }
    
    // Final verification
    echo "\nFinal verification:\n";
    $remainingInvalid = DB::table('eload_transactions')
        ->whereNotIn('status', ['pending', 'completed', 'failed'])
        ->count();
    
    echo "Remaining invalid status records: {$remainingInvalid}\n";
    
    if ($remainingInvalid === 0) {
        echo "SUCCESS: All transaction status values are now valid!\n";
    } else {
        echo "ERROR: Some invalid status records still exist.\n";
    }
    
    // Show transaction ID 24 after fix
    echo "\nTransaction ID 24 after fix:\n";
    $fixedTransaction = DB::table('eload_transactions')->where('id', 24)->first();
    if ($fixedTransaction) {
        echo "- ID: {$fixedTransaction->id}\n";
        echo "- Status: '{$fixedTransaction->status}'\n";
        echo "- Updated: {$fixedTransaction->updated_at}\n";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . "\n";
    echo "Line: " . $e->getLine() . "\n";
}

echo "\n=== Fix Complete ===\n";
?>
