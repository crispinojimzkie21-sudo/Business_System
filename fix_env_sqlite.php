<?php
// Script to update .env for SQLite connection
$envFile = __DIR__ . '/.env';
$envContent = file_get_contents($envFile);

// Update DB_CONNECTION to sqlite
$envContent = preg_replace('/^DB_CONNECTION=.*$/m', 'DB_CONNECTION=sqlite', $envContent);

// Remove MySQL-specific lines (optional cleanup)
$lines = explode("\n", $envContent);
$newLines = [];
$skipDbParams = false;

foreach ($lines as $line) {
    // Skip DB_HOST, DB_PORT, DB_DATABASE, DB_USERNAME, DB_PASSWORD when using sqlite
    if (preg_match('/^(DB_HOST|DB_PORT|DB_DATABASE|DB_USERNAME|DB_PASSWORD)=/', $line)) {
        // Only add DB_DATABASE for sqlite
        if (strpos($line, 'DB_DATABASE=') !== false && strpos($line, 'DB_DATABASE=sqlite') === false) {
            $newLines[] = 'DB_DATABASE=' . basename(__DIR__) . '/database/database.sqlite';
        }
        continue;
    }
    $newLines[] = $line;
}

$envContent = implode("\n", $newLines);
file_put_contents($envFile, $envContent);
echo "✅ .env updated to use SQLite!\n";

