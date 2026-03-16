<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== Session Configuration ===\n";
echo "SESSION_DRIVER: " . config('session.driver') . "\n";
echo "SESSION_CONNECTION: " . config('session.connection') . "\n";
echo "SESSION_TABLE: " . config('session.table') . "\n";
echo "SESSION_ENCRYPT: " . (config('session.encrypt') ? 'true' : 'false') . "\n";
echo "\n=== Database Configuration ===\n";
echo "DB_CONNECTION: " . config('database.default') . "\n";
echo "DB_DATABASE: " . config('database.connections.sqlite.database') . "\n";
echo "\n=== Check Sessions Table ===\n";

try {
    $count = DB::table('sessions')->count();
    echo "Sessions in DB: $count\n";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

