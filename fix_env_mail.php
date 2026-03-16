<?php
/**
 * Fix .env file - specifically MAIL_FROM_NAME value
 */

$envFile = __DIR__ . '/.env';

if (!file_exists($envFile)) {
    echo "❌ .env file not found!\n";
    exit(1);
}

$envContent = file_get_contents($envFile);

// Fix MAIL_FROM_NAME - it should be quoted if it contains spaces
// Replace any unquoted "Manliquid Store" with quoted version
$envContent = preg_replace('/^MAIL_FROM_NAME=.*$/m', 'MAIL_FROM_NAME="Manliquid Store"', $envContent);

file_put_contents($envFile, $envContent);

echo "✅ Fixed MAIL_FROM_NAME in .env file\n";
echo "New value: MAIL_FROM_NAME=\"Business System\"\n";

