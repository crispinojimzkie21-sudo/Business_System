<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);

$kernel->bootstrap();

// Create sales clerk user
$user = new \App\Models\User();
$user->name = 'Test Sales Clerk';
$user->email = 'salesclerk@test.com';
$user->password = bcrypt('password');
$user->role = 'sales_clerk';
$user->position = 'Sales Clerk';
$user->salary = 15000;
$user->access_enabled = true;
$user->save();

echo "Sales clerk user created successfully!\n";
echo "Email: salesclerk@test.com\n";
echo "Password: password\n";
