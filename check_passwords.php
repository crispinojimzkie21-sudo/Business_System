<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

echo "=== Password Hash Check ===\n\n";

// Get all users with their passwords
$users = User::whereIn('role', ['super_admin', 'admin'])->get();

foreach ($users as $user) {
    echo "Email: {$user->email}\n";
    echo "Role: {$user->role}\n";
    echo "Password Hash: {$user->password}\n";
    
    // Test various common passwords
    $testPasswords = ['password', 'admin', '123456', 'secret', 'admin123', 'superadmin', '12345678'];
    
    foreach ($testPasswords as $pwd) {
        if (Hash::check($pwd, $user->password)) {
            echo "MATCH FOUND: '{$pwd}'\n";
        }
    }
    echo "\n";
}

// Let's also check what happens if we create new password hashes
echo "=== Testing New Password Hashes ===\n";
echo "Hash for 'SuperSecret123!': " . Hash::make('SuperSecret123!') . "\n";
echo "Hash for 'Admin123!': " . Hash::make('Admin123!') . "\n";
echo "Hash for 'password': " . Hash::make('password') . "\n";

