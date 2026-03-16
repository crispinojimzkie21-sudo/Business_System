<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\Hash;

echo "=== Seeding Test Users ===\n\n";

// Clear existing test users
User::whereIn('email', [
    'superadmin@example.com',
    'admin@example.com',
    'employee@example.com',
    'user@example.com'
])->delete();

echo "Cleared existing test users\n\n";

// Create Super Admin
$superAdmin = User::create([
    'name' => 'Super Admin',
    'email' => 'superadmin@example.com',
    'password' => Hash::make('password123'),
    'role' => 'super_admin',
]);
echo "✅ Created Super Admin: superadmin@example.com / password123\n";

// Create Admin
$admin = User::create([
    'name' => 'Admin User',
    'email' => 'admin@example.com',
    'password' => Hash::make('password123'),
    'role' => 'admin',
]);
echo "✅ Created Admin: admin@example.com / password123\n";

// Create Employee
$employee = User::create([
    'name' => 'Employee One',
    'email' => 'employee@example.com',
    'password' => Hash::make('password123'),
    'role' => 'employee',
]);
echo "✅ Created Employee: employee@example.com / password123\n";

// Create Regular User
$user = User::create([
    'name' => 'Regular User',
    'email' => 'user@example.com',
    'password' => Hash::make('password123'),
    'role' => 'user',
]);
echo "✅ Created User: user@example.com / password123\n";

echo "\n=== All Test Users Created ===\n";
echo "Total users: " . User::count() . "\n";
echo "\nLogin credentials:\n";
echo "  Super Admin: superadmin@example.com / password123\n";
echo "  Admin: admin@example.com / password123\n";
echo "  Employee: employee@example.com / password123\n";
echo "  User: user@example.com / password123\n";

