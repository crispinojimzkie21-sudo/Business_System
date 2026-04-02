<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

try {
    $cols = Illuminate\Support\Facades\Schema::getColumnListing('eload_transactions');
    echo 'Columns: ' . json_encode($cols) . "\n";
    
    // Check what columns exist
    echo "\nAvailable columns:\n";
    foreach ($cols as $col) {
        echo "- $col\n";
    }
    
} catch (Exception $e) {
    echo 'Error: ' . $e->getMessage() . "\n";
}
