<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

try {
    // Get eload_numbers records
    $eloadNumbers = \Illuminate\Support\Facades\DB::table('eload_numbers')->limit(3)->get();
    echo "Available eload_numbers records:\n";
    foreach ($eloadNumbers as $eloadNumber) {
        echo "- ID: {$eloadNumber->id}\n";
        echo "  number: " . ($eloadNumber->number ?? 'NULL') . "\n";
        echo "  network: " . ($eloadNumber->network ?? 'NULL') . "\n";
        echo "  status: " . ($eloadNumber->status ?? 'NULL') . "\n";
        echo "\n";
    }
    
} catch (Exception $e) {
    echo 'Error: ' . $e->getMessage() . "\n";
}
?>
