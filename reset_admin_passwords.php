<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

echo "=== Resetting Admin Passwords ===\n\n";

// Reset super_admin password
$superAdmin = User::where('role', 'super_admin')->first();
if ($superAdmin) {
    $superAdmin->password = Hash::make('SuperSecret123!');
    $superAdmin->save();
    echo "✓ Reset super_admin password to: SuperSecret123!\n";
}

// Reset admin password
$admin = User::where('role', 'admin')->first();
if ($admin) {
    $admin->password = Hash::make('Admin123!');
    $admin->save();
    echo "✓ Reset admin password to: Admin123!\n";
}

// Verify the passwords now work
echo "\n=== Verifying Login ===\n";

$sa = User::where('email', 'superadmin@example.com')->first();
if ($sa) {
    $works = Hash::check('SuperSecret123!', $sa->password);
    echo "superadmin@example.com + 'SuperSecret123!': " . ($works ? "✓ SUCCESS" : "✗ FAILED") . "\n";
}

$adm = User::where('email', 'admin@example.com')->first();
if ($adm) {
    $works = Hash::check('Admin123!', $adm->password);
    echo "admin@example.com + 'Admin123!': " . ($works ? "✓ SUCCESS" : "✗ FAILED") . "\n";
}

echo "\n=== Complete ===\n";

