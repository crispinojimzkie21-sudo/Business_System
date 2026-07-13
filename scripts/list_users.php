<?php

require_once __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$users = \App\Models\User::orderBy('id')->get();

echo "Total users: " . $users->count() . "\n\n";

foreach ($users as $u) {
    printf("%3d | %-30s | %-20s | %-12s | access_enabled=%s\n", $u->id, $u->email, $u->name, $u->role, ($u->access_enabled ? 'true' : 'false'));
}

echo "\n(Printed ID | email | name | role | access_enabled)\n";
