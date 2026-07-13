<?php

/**
 * Fix E-Load Database Schema
 * This script will add missing columns to the eloads table
 */

require_once 'vendor/autoload.php';

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

// Load Laravel environment
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== RM Manliquid Business System - Fix E-Load Schema ===\n\n";

try {
    // Check current eloads table structure
    echo "Current eloads table structure:\n";
    echo "--------------------------------\n";
    $columns = DB::select("PRAGMA table_info(eloads)");
    foreach ($columns as $column) {
        echo "- {$column->name}: {$column->type} (Nullable: " . ($column->notnull ? 'No' : 'Yes') . ")\n";
    }
    
    // Check and add missing columns
    $missingColumns = [
        'network' => 'TEXT NULL',
        'provider' => 'TEXT NULL',
        'service_type' => 'TEXT NULL',
        'code' => 'TEXT NULL',
        'description' => 'TEXT NULL',
        'validity' => 'TEXT NULL',
        'discount' => 'REAL DEFAULT 0',
        'commission' => 'REAL DEFAULT 0',
        'min_amount' => 'REAL DEFAULT 0',
        'max_amount' => 'REAL DEFAULT 0',
    ];
    
    echo "\nChecking and adding missing columns:\n";
    foreach ($missingColumns as $column => $definition) {
        if (Schema::hasColumn('eloads', $column)) {
            echo "✓ $column already exists\n";
        } else {
            echo "Adding $column...\n";
            DB::statement("ALTER TABLE eloads ADD COLUMN $column $definition");
            echo "✓ $column added successfully\n";
        }
    }
    
    // Show updated table structure
    echo "\nUpdated eloads table structure:\n";
    echo "--------------------------------\n";
    $updatedColumns = DB::select("PRAGMA table_info(eloads)");
    foreach ($updatedColumns as $column) {
        echo "- {$column->name}: {$column->type} (Nullable: " . ($column->notnull ? 'No' : 'Yes') . ")\n";
    }
    
    // Check if there are any eload records
    $eloadCount = DB::table('eloads')->count();
    echo "\nCurrent e-load records: " . $eloadCount . "\n";
    
    if ($eloadCount > 0) {
        echo "\nRecent e-load records:\n";
        $recentEloads = DB::table('eloads')
            ->select('id', 'category_id', 'name', 'price', 'status', 'created_at')
            ->orderBy('created_at', 'desc')
            ->limit(3)
            ->get();
            
        foreach ($recentEloads as $eload) {
            echo "- ID: {$eload->id}, Category ID: {$eload->category_id}, Name: {$eload->name}\n";
            echo "  Price: " . $eload->price . ", Status: " . $eload->status . "\n";
            echo "  Created: " . $eload->created_at . "\n\n";
        }
    }
    
    // Check eload_categories table
    echo "\nChecking eload_categories table:\n";
    if (Schema::hasTable('eload_categories')) {
        $categoryCount = DB::table('eload_categories')->count();
        echo "✓ eload_categories table exists with " . $categoryCount . " records\n";
        
        if ($categoryCount > 0) {
            $categories = DB::table('eload_categories')->get();
            echo "Available categories:\n";
            foreach ($categories as $category) {
                echo "- ID: {$category->id}, Name: {$category->name}, Status: {$category->status}\n";
            }
        }
    } else {
        echo "✗ eload_categories table does not exist\n";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

echo "\n=== E-Load Schema Fix Complete ===\n";
?>
