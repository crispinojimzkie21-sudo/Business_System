<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

try {
    // Check existing records to see what eload_id contains
    $existing = \Illuminate\Support\Facades\DB::table('eload_transactions')
        ->select('eload_id', 'eload_number', 'price', 'status')
        ->limit(3)
        ->get();
    
    echo "Existing records and their eload_id values:\n";
    foreach ($existing as $record) {
        echo "- eload_id: {$record->eload_id}, eload_number: {$record->eload_number}, price: {$record->price}, status: {$record->status}\n";
    }
    
    // Check if there are any eload records to get eload_id from
    $eloads = \Illuminate\Support\Facades\DB::table('eloads')->limit(3)->get();
    echo "\nAvailable eload records:\n";
    foreach ($eloads as $eload) {
        echo "- ID: {$eload->id}, Number: {$eload->eload_number}, Price: {$eload->price}\n";
    }
    
} catch (Exception $e) {
    echo 'Error: ' . $e->getMessage() . "\n";
}
