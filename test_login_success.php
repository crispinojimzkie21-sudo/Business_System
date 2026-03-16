<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

echo "=== Testing Login Functionality ===\n\n";

// Test 1: Check all users and their roles
echo "1. All Users in Database:\n";
$users = User::all();
foreach ($users as $user) {
    echo "   ID: {$user->id}, Email: {$user->email}, Role: {$user->role}\n";
    echo "      - isAdmin(): " . ($user->isAdmin() ? 'true' : 'false') . "\n";
    echo "      - isSuperAdmin(): " . ($user->isSuperAdmin() ? 'true' : 'false') . "\n";
}

echo "\n2. Checking for super_admin users:\n";
$superAdmins = User::where('role', 'super_admin')->get();
echo "   Found: " . $superAdmins->count() . " super_admin(s)\n";
foreach ($superAdmins as $sa) {
    echo "   - {$sa->email}\n";
}

echo "\n3. Checking for admin users:\n";
$admins = User::where('role', 'admin')->get();
echo "   Found: " . $admins->count() . " admin(s)\n";
foreach ($admins as $admin) {
    echo "   - {$admin->email}\n";
}

echo "\n4. Testing password verification:\n";
$testUser = User::where('email', 'superadmin@example.com')->first();
if ($testUser) {
    $passwordCheck = Hash::check('password', $testUser->password);
    echo "   Testing superadmin@example.com with 'password': " . ($passwordCheck ? 'SUCCESS' : 'FAILED') . "\n";
}

$testUser2 = User::where('email', 'admin@example.com')->first();
if ($testUser2) {
    $passwordCheck2 = Hash::check('password', $testUser2->password);
    echo "   Testing admin@example.com with 'password': " . ($passwordCheck2 ? 'SUCCESS' : 'FAILED') . "\n";
}

echo "\n=== Test Complete ===\n";

