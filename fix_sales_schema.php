<?php

/**
 * Fix Sales Database Schema
 * This script will add missing columns to the sales table
 */

require_once 'vendor/autoload.php';

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

// Load Laravel environment
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== RM Manliquid Business System - Fix Sales Schema ===\n\n";

try {
    // Check current sales table structure
    echo "Current sales table structure:\n";
    echo "--------------------------------\n";
    $columns = DB::select("PRAGMA table_info(sales)");
    foreach ($columns as $column) {
        echo "- {$column->name}: {$column->type} (Nullable: " . ($column->notnull ? 'No' : 'Yes') . ")\n";
    }
    
    // Check and add missing columns
    $missingColumns = [
        'transaction_id' => 'TEXT NULL',
        'receipt_number' => 'TEXT NULL',
        'invoice_number' => 'TEXT NULL',
        'status' => 'TEXT DEFAULT "completed"',
        'payment_status' => 'TEXT DEFAULT "paid"',
        'notes' => 'TEXT NULL',
    ];
    
    echo "\nChecking and adding missing columns:\n";
    foreach ($missingColumns as $column => $definition) {
        if (Schema::hasColumn('sales', $column)) {
            echo "✓ $column already exists\n";
        } else {
            echo "Adding $column...\n";
            DB::statement("ALTER TABLE sales ADD COLUMN $column $definition");
            echo "✓ $column added successfully\n";
        }
    }
    
    // Show updated table structure
    echo "\nUpdated sales table structure:\n";
    echo "--------------------------------\n";
    $updatedColumns = DB::select("PRAGMA table_info(sales)");
    foreach ($updatedColumns as $column) {
        echo "- {$column->name}: {$column->type} (Nullable: " . ($column->notnull ? 'No' : 'Yes') . ")\n";
    }
    
    // Check if there are any sales records
    $salesCount = DB::table('sales')->count();
    echo "\nCurrent sales records: " . $salesCount . "\n";
    
    if ($salesCount > 0) {
        echo "\nRecent sales records:\n";
        $recentSales = DB::table('sales')
            ->select('id', 'user_id', 'total_amount', 'payment_method', 'customer_name', 'created_at')
            ->orderBy('created_at', 'desc')
            ->limit(3)
            ->get();
            
        foreach ($recentSales as $sale) {
            echo "- ID: {$sale->id}, User ID: {$sale->user_id}, Amount: {$sale->total_amount}\n";
            echo "  Customer: " . ($sale->customer_name ?? 'N/A') . "\n";
            echo "  Payment: " . $sale->payment_method . "\n";
            echo "  Created: " . $sale->created_at . "\n\n";
        }
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

echo "\n=== Sales Schema Fix Complete ===\n";
?>
