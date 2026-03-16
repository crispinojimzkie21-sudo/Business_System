<?php
// Script to update .env to use database session driver
$envFile = __DIR__ . '/.env';

if (!file_exists($envFile)) {
    echo "❌ .env file not found!\n";
    exit(1);
}

$envContent = file_get_contents($envFile);

// Add or update SESSION_DRIVER
if (preg_match('/^SESSION_DRIVER=/m', $envContent)) {
    $envContent = preg_replace('/^SESSION_DRIVER=.*$/m', 'SESSION_DRIVER=database', $envContent);
    echo "✅ Updated SESSION_DRIVER to database\n";
} else {
    $envContent .= "\nSESSION_DRIVER=database\n";
    echo "✅ Added SESSION_DRIVER=database\n";
}

// Also set SESSION_CONNECTION if not set
if (!preg_match('/^SESSION_CONNECTION=/m', $envContent)) {
    $envContent .= "SESSION_CONNECTION=sqlite\n";
    echo "✅ Added SESSION_CONNECTION=sqlite\n";
}

file_put_contents($envFile, $envContent);
echo "\n✅ .env updated successfully!\n";
echo "Run: php artisan config:clear\n";

