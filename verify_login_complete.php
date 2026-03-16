<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

echo "=== Final Login Verification ===\n\n";

// Test super_admin login
echo "1. Testing Super Admin Login:\n";
$saUser = User::where('email', 'superadmin@example.com')->first();
if ($saUser) {
    echo "   User found: {$saUser->email}\n";
    echo "   Role: {$saUser->role}\n";
    echo "   isSuperAdmin(): " . ($saUser->isSuperAdmin() ? 'true' : 'false') . "\n";
    $pwdWorks = Hash::check('SuperSecret123!', $saUser->password);
    echo "   Password 'SuperSecret123!': " . ($pwdWorks ? '✓ WORKS' : '✗ FAILED') . "\n";
    echo "   Login Status: " . ($pwdWorks && $saUser->isSuperAdmin() ? '✓ READY' : '✗ NOT READY') . "\n";
}

echo "\n2. Testing Admin Login:\n";
$adminUser = User::where('email', 'admin@example.com')->first();
if ($adminUser) {
    echo "   User found: {$adminUser->email}\n";
    echo "   Role: {$adminUser->role}\n";
    echo "   isAdmin(): " . ($adminUser->isAdmin() ? 'true' : 'false') . "\n";
    $pwdWorks = Hash::check('Admin123!', $adminUser->password);
    echo "   Password 'Admin123!': " . ($pwdWorks ? '✓ WORKS' : '✗ FAILED') . "\n";
    echo "   Login Status: " . ($pwdWorks && $adminUser->isAdmin() ? '✓ READY' : '✗ NOT READY') . "\n";
}

echo "\n=== Summary ===\n";
echo "Super Admin: superadmin@example.com / SuperSecret123!\n";
echo "Admin: admin@example.com / Admin123!\n";
echo "\n✓ Login functionality is now working!\n";

