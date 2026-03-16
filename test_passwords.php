<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\Hash;

echo "=== Testing Login with Correct Passwords ===\n\n";

// Test super_admin
$superAdmin = User::where('email', 'superadmin@example.com')->first();
if ($superAdmin) {
    $result = Hash::check('SuperSecret123!', $superAdmin->password);
    echo "superadmin@example.com + 'SuperSecret123!': " . ($result ? "SUCCESS" : "FAILED") . "\n";
}

// Test admin
$admin = User::where('email', 'admin@example.com')->first();
if ($admin) {
    $result = Hash::check('Admin123!', $admin->password);
    echo "admin@example.com + 'Admin123!': " . ($result ? "SUCCESS" : "FAILED") . "\n";
}

// Test user
$user = User::where('email', 'employee1@example.com')->first();
if ($user) {
    $result = Hash::check('Employee123!', $user->password);
    echo "employee1@example.com + 'Employee123!': " . ($result ? "SUCCESS" : "FAILED") . "\n";
}

echo "\n=== Test Complete ===\n";

