<?php

require_once __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$emails = [
    'superadmin@example.com',
    'admin@manliquid.com',
];

use App\Models\User;

$users = User::whereIn('email', $emails)->get();

if ($users->isEmpty()) {
    echo "No matching users found for deletion.\n";
    exit(0);
}

foreach ($users as $u) {
    echo "Deleting user: {$u->id} | {$u->email} | {$u->role}\n";
    // Use delete() to remove record
    $u->delete();
}

echo "Deletion completed.\n";
