<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

try {
    // Check eloads table structure
    $eloadColumns = \Illuminate\Support\Facades\Schema::getColumnListing('eloads');
    echo "eloads table columns:\n";
    foreach ($eloadColumns as $column) {
        echo "- $column\n";
    }
    
    // Get eload records
    $eloads = \Illuminate\Support\Facades\DB::table('eloads')->limit(3)->get();
    echo "\nAvailable eload records:\n";
    foreach ($eloads as $eload) {
        echo "- ID: {$eload->id}\n";
        foreach ($eloadColumns as $column) {
            if ($column !== 'id') {
                echo "  $column: " . ($eload->$column ?? 'NULL') . "\n";
            }
        }
        echo "\n";
    }
    
} catch (Exception $e) {
    echo 'Error: ' . $e->getMessage() . "\n";
}
