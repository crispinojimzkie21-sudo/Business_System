<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;

echo "=== User Database Check ===\n\n";

$userCount = User::count();
echo "Total users in database: " . $userCount . "\n\n";

if ($userCount > 0) {
    echo "Sample users:\n";
    foreach (User::limit(5)->get() as $user) {
        echo "  - ID: {$user->id}, Email: {$user->email}, Role: {$user->role}\n";
    }
    
    // Check for super_admin
    $superAdmins = User::where('role', 'super_admin')->count();
    echo "\nSuper Admins: " . $superAdmins . "\n";
    
    // Check for admins
    $admins = User::where('role', 'admin')->count();
    echo "Admins: " . $admins . "\n";
    
    // Check for employees
    $employees = User::where('role', 'employee')->count();
    echo "Employees: " . $employees . "\n";
    
    // Check for users
    $users = User::where('role', 'user')->count();
    echo "Users: " . $users . "\n";
} else {
    echo "No users found in database!\n";
    echo "Run: php artisan db:seed --class=SuperAdminSeeder\n";
}

