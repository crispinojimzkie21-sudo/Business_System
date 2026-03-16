<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);

$kernel->bootstrap();

// Super Admin emails to delete
$emailsToDelete = [
    'admin@business.com',
    'manager@business.com'
];

foreach ($emailsToDelete as $email) {
    $user = \App\Models\User::where('email', $email)->first();
    
    if ($user) {
        $userName = $user->name;
        $user->delete();
        echo "✅ Deleted Super Admin: {$email} ({$userName})\n";
    } else {
        echo "⚠️  User not found: {$email}\n";
    }
}

echo "\n🎉 Super Admin deletion completed!\n";
