<?php
// Script to update .env for MySQL connection
$envFile = __DIR__ . '/.env';
$envContent = file_get_contents($envFile);

// Update DB_CONNECTION to mysql
$envContent = preg_replace('/^DB_CONNECTION=.*$/m', 'DB_CONNECTION=mysql', $envContent);

// Update DB_DATABASE to business_system
$envContent = preg_replace('/^DB_DATABASE=.*$/m', 'DB_DATABASE=business_system', $envContent);

// Make sure other DB settings are correct
if (!preg_match('/^DB_HOST=/m', $envContent)) {
    $envContent .= "\nDB_HOST=127.0.0.1\n";
} else {
    $envContent = preg_replace('/^DB_HOST=.*$/m', 'DB_HOST=127.0.0.1', $envContent);
}

if (!preg_match('/^DB_PORT=/m', $envContent)) {
    $envContent .= "\nDB_PORT=3306\n";
} else {
    $envContent = preg_replace('/^DB_PORT=.*$/m', 'DB_PORT=3306', $envContent);
}

if (!preg_match('/^DB_USERNAME=/m', $envContent)) {
    $envContent .= "\nDB_USERNAME=root\n";
} else {
    $envContent = preg_replace('/^DB_USERNAME=.*$/m', 'DB_USERNAME=root', $envContent);
}

if (!preg_match('/^DB_PASSWORD=/m', $envContent)) {
    $envContent .= "\nDB_PASSWORD=\n";
} else {
    $envContent = preg_replace('/^DB_PASSWORD=.*$/m', 'DB_PASSWORD=', $envContent);
}

file_put_contents($envFile, $envContent);
echo "✅ .env updated successfully!\n";
echo "DB_CONNECTION=mysql\n";
echo "DB_HOST=127.0.0.1\n";
echo "DB_PORT=3306\n";
echo "DB_DATABASE=business_system\n";
echo "DB_USERNAME=root\n";
echo "DB_PASSWORD=(empty)\n";

