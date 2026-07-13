<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "Checking eload_transactions table schema:\n";
$columns = DB::select("PRAGMA table_info(eload_transactions)");
foreach ($columns as $column) {
    echo "- {$column->name}: {$column->type} (Nullable: " . ($column->notnull ? 'No' : 'Yes') . ")\n";
}
?>
