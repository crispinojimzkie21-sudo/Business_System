<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

try {
    // Check if table exists
    $tables = DB::select("SELECT name FROM sqlite_master WHERE type='table' AND name='contact_submissions'");
    
    if (empty($tables)) {
        // Create the table
        DB::statement('
            CREATE TABLE contact_submissions (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                company VARCHAR(255) NOT NULL,
                email VARCHAR(255) NOT NULL,
                message TEXT,
                status VARCHAR(255) DEFAULT "new",
                created_at DATETIME,
                updated_at DATETIME
            )
        ');
        echo "SUCCESS: contact_submissions table created successfully!\n";
    } else {
        echo "Table already exists.\n";
    }
    
    // List all tables
    echo "\nExisting tables:\n";
    $allTables = DB::select("SELECT name FROM sqlite_master WHERE type='table' ORDER BY name");
    foreach ($allTables as $table) {
        echo "- " . $table->name . "\n";
    }
    
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}

