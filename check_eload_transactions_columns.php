<?php

/**
 * Check E-Load Transactions Columns
 * This script will check the current schema of eload_transactions table
 */

require_once 'vendor/autoload.php';

use Illuminate\Support\Facades\DB;

// Load Laravel environment
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== RM Manliquid Business System - Check E-Load Transactions Columns ===\n\n";

try {
    echo "Checking eload_transactions table schema:\n";
    echo str_repeat("=", 50) . "\n";
    
    $columns = DB::select("PRAGMA table_info(eload_transactions)");
    
    echo "Current columns in eload_transactions table:\n";
    foreach ($columns as $column) {
        echo "- {$column->name}: {$column->type} (Nullable: " . ($column->notnull ? 'No' : 'Yes') . ")\n";
    }
    
    // Check for customer_contact specifically
    $hasCustomerContact = false;
    foreach ($columns as $column) {
        if ($column->name === 'customer_contact') {
            $hasCustomerContact = true;
            break;
        }
    }
    
    echo "\nCheck for customer_contact column:\n";
    if ($hasCustomerContact) {
        echo "SUCCESS: customer_contact column exists\n";
    } else {
        echo "ERROR: customer_contact column does not exist\n";
    }
    
    // Check for customer_mobile
    $hasCustomerMobile = false;
    foreach ($columns as $column) {
        if ($column->name === 'customer_mobile') {
            $hasCustomerMobile = true;
            break;
        }
    }
    
    echo "\nCheck for customer_mobile column:\n";
    if ($hasCustomerMobile) {
        echo "SUCCESS: customer_mobile column exists\n";
    } else {
        echo "ERROR: customer_mobile column does not exist\n";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . "\n";
    echo "Line: " . $e->getLine() . "\n";
}

echo "\n=== Check Complete ===\n";
?>
