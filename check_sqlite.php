<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

try {
    // Check if eload_transactions table exists
    $tables = \Illuminate\Support\Facades\DB::select("SELECT name FROM sqlite_master WHERE type='table'");
    echo "Tables in SQLite database:\n";
    foreach ($tables as $table) {
        echo "- " . $table->name . "\n";
    }
    
    // Check if eload_transactions table exists
    $eloadExists = false;
    foreach ($tables as $table) {
        if ($table->name === 'eload_transactions') {
            $eloadExists = true;
            break;
        }
    }
    
    if ($eloadExists) {
        echo "\n✅ eload_transactions table exists!\n";
        
        // Get table structure
        $columns = \Illuminate\Support\Facades\Schema::getColumnListing('eload_transactions');
        echo "\nColumns in eload_transactions:\n";
        foreach ($columns as $column) {
            echo "- $column\n";
        }
        
        // Count records
        $count = \Illuminate\Support\Facades\DB::table('eload_transactions')->count();
        echo "\nTotal records: $count\n";
        
    } else {
        echo "\n❌ eload_transactions table does NOT exist!\n";
    }
    
} catch (Exception $e) {
    echo 'Error: ' . $e->getMessage() . "\n";
}
