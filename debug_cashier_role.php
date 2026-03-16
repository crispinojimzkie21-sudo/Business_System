<?php

/**
 * Debug Cashier Role Issue
 */

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

echo "=== Cashier Role Debug ===\n\n";

// Check if user is logged in
if (!Auth::check()) {
    echo "❌ No user is logged in!\n";
    echo "Please login first.\n";
    exit(1);
}

$user = Auth::user();

echo "📋 User Information:\n";
echo "ID: " . $user->id . "\n";
echo "Name: " . $user->name . "\n";
echo "Email: " . $user->email . "\n";
echo "Role: '" . $user->role . "'\n";
echo "Position: " . ($user->position ?? 'N/A') . "\n\n";

echo "🔍 Role Method Checks:\n";
echo "isCashier(): " . ($user->isCashier() ? 'TRUE' : 'FALSE') . "\n";
echo "isAdmin(): " . ($user->isAdmin() ? 'TRUE' : 'FALSE') . "\n";
echo "isSuperAdmin(): " . ($user->isSuperAdmin() ? 'TRUE' : 'FALSE') . "\n";
echo "isEmployee(): " . ($user->isEmployee() ? 'TRUE' : 'FALSE') . "\n";
echo "isManager(): " . ($user->isManager() ? 'TRUE' : 'FALSE') . "\n\n";

echo "🎯 Expected Role for Cashier Dashboard: 'cashier'\n";
echo "🎯 Actual Role: '" . $user->role . "'\n\n";

if ($user->role === 'cashier') {
    echo "✅ Role matches! Should have access to cashier dashboard.\n";
} else {
    echo "❌ Role mismatch! This is the problem.\n";
    echo "Expected: 'cashier'\n";
    echo "Actual: '" . $user->role . "'\n\n";
    
    echo "🔧 Possible Solutions:\n";
    echo "1. Update user role in database to 'cashier'\n";
    echo "2. Check if there are extra spaces in role field\n";
    echo "3. Verify middleware is looking for correct role\n";
}

echo "\n📞 Next Steps:\n";
echo "1. Check database for this user's role\n";
echo "2. Update role to 'cashier' if needed\n";
echo "3. Clear caches: php artisan config:clear\n";
echo "4. Try accessing cashier dashboard again\n";
