<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;

echo "=== Super Admin Details (ID 8) ===\n";

$user = User::find(8);
if (!$user) {
    echo "User ID 8 not found!\n";
    exit;
}

echo "Email: " . $user->email . "\n";
echo "Name: " . $user->name . "\n";
echo "Role: '" . $user->role . "'\n";
echo "access_enabled: " . ($user->access_enabled ? 'true' : 'false') . "\n";
echo "Password hash (first 20 chars): " . substr($user->password, 0, 20) . "...\n";
echo "isSuperAdmin(): " . ($user->isSuperAdmin() ? 'true' : 'false') . "\n";
echo "isAccessEnabled(): " . ($user->isAccessEnabled() ? 'true' : 'false') . "\n";

$testHash = password_hash('SuperSecret123!', PASSWORD_DEFAULT);
echo "\nTest if matches SuperSecret123! hash? (manual check needed)\n";
echo "DB hash: " . $user->password . "\n";
?>
