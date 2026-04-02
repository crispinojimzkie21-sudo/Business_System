<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

try {
    // Test database connection
    \Illuminate\Support\Facades\DB::connection()->getPdo();
    echo "✅ SQLite database connection successful!\n";
    
    // Test query
    $result = \Illuminate\Support\Facades\DB::select('SELECT COUNT(*) as count FROM eload_transactions');
    echo "✅ Query successful! Total eload_transactions: " . $result[0]->count . "\n";
    
    // Show recent records
    $recent = \Illuminate\Support\Facades\DB::table('eload_transactions')
        ->orderBy('created_at', 'desc')
        ->limit(3)
        ->get();
    
    echo "\nRecent records:\n";
    foreach ($recent as $record) {
        echo "- ID: {$record->id}, Number: {$record->eload_number}, Price: {$record->price}, Status: {$record->status}\n";
    }
    
} catch (Exception $e) {
    echo '❌ Error: ' . $e->getMessage() . "\n";
}
