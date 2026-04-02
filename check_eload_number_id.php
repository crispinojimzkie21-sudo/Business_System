<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

try {
    // Check existing records to see what eload_number_id contains
    $existing = \Illuminate\Support\Facades\DB::table('eload_transactions')
        ->select('eload_number_id', 'eload_id', 'eload_number', 'price', 'status')
        ->limit(3)
        ->get();
    
    echo "Existing records and their eload_number_id values:\n";
    foreach ($existing as $record) {
        echo "- eload_number_id: {$record->eload_number_id}, eload_id: {$record->eload_id}, eload_number: {$record->eload_number}, price: {$record->price}\n";
    }
    
    // Check eload_numbers table
    $eloadNumberColumns = \Illuminate\Support\Facades\Schema::getColumnListing('eload_numbers');
    echo "\neload_numbers table columns:\n";
    foreach ($eloadNumberColumns as $column) {
        echo "- $column\n";
    }
    
    // Get eload_numbers records
    $eloadNumbers = \Illuminate\Support\Facades\DB::table('eload_numbers')->limit(3)->get();
    echo "\nAvailable eload_numbers records:\n";
    foreach ($eloadNumbers as $eloadNumber) {
        echo "- ID: {$eloadNumber->id}, Number: {$eloadNumber->eload_number}\n";
    }
    
} catch (Exception $e) {
    echo 'Error: ' . $e->getMessage() . "\n";
}
