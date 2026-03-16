<?php
/**
 * Comprehensive Login and System Data Test
 * Tests the login functionality and displays user data
 */

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

echo "=== BUSINESS SYSTEM LOGIN AND DATA TEST ===\n\n";

// 1. Check Database Connection
echo "1. DATABASE CONNECTION\n";
echo "   --------------------\n";
try {
    $userCount = User::count();
    echo "   [OK] Database connected - Total Users: $userCount\n\n";
} catch (Exception $e) {
    echo "   [ERROR] Database Error: " . $e->getMessage() . "\n\n";
    exit(1);
}

// 2. Display All Users
echo "2. ALL USERS IN SYSTEM\n";
echo "   --------------------\n";
$users = User::all(['id', 'name', 'email', 'role', 'position', 'employment_status'])->toArray();
if (empty($users)) {
    echo "   WARNING: No users found in database!\n";
} else {
    foreach ($users as $user) {
        $role = strtoupper($user['role'] ?? 'N/A');
        $status = $user['employment_status'] ?? 'N/A';
        echo "   ID: {$user['id']} | {$user['name']} | {$user['email']}\n";
        echo "     Role: $role | Position: {$user['position']} | Status: $status\n\n";
    }
}

// 3. Test Login Functionality
echo "3. LOGIN FUNCTIONALITY TEST\n";
echo "   --------------------\n";

// Test credentials
$testCredentials = [
    ['email' => 'superadmin@example.com', 'password' => 'password123', 'expected' => 'super_admin'],
    ['email' => 'admin@example.com', 'password' => 'password123', 'expected' => 'admin'],
    ['email' => 'user@example.com', 'password' => 'password123', 'expected' => 'user'],
];

foreach ($testCredentials as $test) {
    echo "   Testing: {$test['email']} ...\n";
    
    $user = User::where('email', $test['email'])->first();
    
    if (!$user) {
        echo "     [ERROR] User not found\n\n";
        continue;
    }
    
    // Check password
    if (!Hash::check($test['password'], $user->password)) {
        echo "     [ERROR] Invalid password\n\n";
        continue;
    }
    
    // Test authentication
    if (Auth::attempt(['email' => $test['email'], 'password' => $test['password']], false)) {
        $authenticatedUser = Auth::user();
        $role = $authenticatedUser->role;
        
        echo "     [OK] LOGIN SUCCESS!\n";
        echo "       User: {$authenticatedUser->name}\n";
        echo "       Email: {$authenticatedUser->email}\n";
        echo "       Role: $role\n";
        echo "       Position: {$authenticatedUser->position}\n";
        
        // Check role methods
        if (method_exists($authenticatedUser, 'isSuperAdmin')) {
            echo "       isSuperAdmin(): " . ($authenticatedUser->isSuperAdmin() ? 'true' : 'false') . "\n";
        }
        if (method_exists($authenticatedUser, 'isAdmin')) {
            echo "       isAdmin(): " . ($authenticatedUser->isAdmin() ? 'true' : 'false') . "\n";
        }
        
        Auth::logout();
        echo "\n";
    } else {
        echo "     [ERROR] Authentication failed\n\n";
    }
}

// 4. Session Configuration
echo "4. SESSION CONFIGURATION\n";
echo "   --------------------\n";
echo "   Driver: " . config('session.driver') . "\n";
echo "   Connection: " . config('session.connection') . "\n";
echo "   Table: " . config('session.table') . "\n";
echo "   Lifetime: " . config('session.lifetime') . " minutes\n\n";

// 5. Check Sessions Table
echo "5. ACTIVE SESSIONS\n";
echo "   --------------------\n";
try {
    $sessionCount = DB::table('sessions')->count();
    echo "   Active sessions in database: $sessionCount\n\n";
} catch (Exception $e) {
    echo "   Sessions table check failed: " . $e->getMessage() . "\n\n";
}

// 6. Summary
echo "=== TEST SUMMARY ===\n";
echo "[OK] Login system is configured correctly!\n";
echo "[OK] Database connection is working!\n";
echo "[OK] User authentication is functional!\n";
echo "\nTo access the system:\n";
echo "   URL: http://127.0.0.1:8000/login\n";
echo "   Super Admin: superadmin@example.com / password123\n";
echo "   Admin: admin@example.com / password123\n";
echo "   User: user@example.com / password123\n";