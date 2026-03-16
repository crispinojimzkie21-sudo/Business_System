<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);

$kernel->bootstrap();

// Create multiple Super Admin users
$superAdmins = [
    [
        'name' => 'Romel Super Admin',
        'email' => 'romel@gmail.com',
        'password' => 'admin123',
        'position' => 'Super Administrator',
        'salary' => 50000
    ],
];

foreach ($superAdmins as $adminData) {
    // Check if user already exists
    $existingUser = \App\Models\User::where('email', $adminData['email'])->first();
    
    if ($existingUser) {
        echo "⚠️  User with email {$adminData['email']} already exists. Skipping...\n";
        continue;
    }
    
    // Create Super Admin user
    $user = new \App\Models\User();
    $user->name = $adminData['name'];
    $user->email = $adminData['email'];
    $user->password = bcrypt($adminData['password']);
    $user->role = 'super_admin';
    $user->position = $adminData['position'];
    $user->salary = $adminData['salary'];
    $user->access_enabled = true;
    $user->save();
    
    echo "✅ Super Admin created: {$adminData['email']}\n";
}

echo "\n🎉 Super Admin accounts creation completed!\n";
echo "\n� Login Credentials:\n";
foreach ($superAdmins as $adminData) {
    echo "📧 {$adminData['email']} | 🔑 {$adminData['password']}\n";
}
