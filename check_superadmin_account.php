<?php
require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== Superadmin Account Check ===\n";

try {
    $superadmin = DB::table('users')->where('email', 'superadmin@example.com')->first();
    
    if ($superadmin) {
        echo "Account Found:\n";
        echo "Name: {$superadmin->name}\n";
        echo "Email: {$superadmin->email}\n";
        echo "Role: {$superadmin->role}\n";
        echo "Access Enabled: " . ($superadmin->access_enabled ? 'YES' : 'NO') . "\n";
        echo "Password Hash: " . (substr($superadmin->password, 0, 20) . "...") . "\n";
        echo "\nDefault Password: admin123\n";
    } else {
        echo "Account NOT FOUND\n";
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

echo "=== Check Complete ===\n";
?>
