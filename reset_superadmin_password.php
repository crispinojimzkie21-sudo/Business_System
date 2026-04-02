<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\Hash;

$user = User::find(8);
if (!$user) {
    echo "❌ Super Admin (ID 8) not found!\n";
    exit(1);
}

if ($user->email !== 'superadmin@example.com') {
    echo "❌ Wrong user: {$user->email}\n";
    exit(1);
}

// Update password to SuperSecret123!
$user->password = Hash::make('SuperSecret123!');
$user->save();

echo "✅ Super Admin password reset to 'SuperSecret123!' for {$user->email}\n";
echo "Role: {$user->role}\n";
echo "access_enabled: " . ($user->access_enabled ? 'true' : 'false') . "\n";
echo "\nNow login at http://127.0.0.1:8000/superadmin/login\n";
?>
