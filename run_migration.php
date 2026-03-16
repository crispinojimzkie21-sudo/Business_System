<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

try {
    DB::statement('ALTER TABLE attendances ADD COLUMN deleted_at TEXT');
    echo "Migration successful! Added deleted_at column to attendances table.\n";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
