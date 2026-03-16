<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use App\Models\Product;
use App\Models\Sale;
use App\Models\Attendance;
use Illuminate\Support\Facades\DB;

echo "=== Complete Database Control Test ===\n\n";

// 1. Test Users Table
echo "1. USERS TABLE\n";
echo "   Users count: " . User::count() . "\n";
$users = User::all();
foreach ($users as $user) {
    echo "   - {$user->name} ({$user->email}) - Role: {$user->role}\n";
}
echo "\n";

// 2. Test Products Table
echo "2. PRODUCTS TABLE\n";
$productsCount = Product::count();
echo "   Products count: " . $productsCount . "\n";
if ($productsCount > 0) {
    foreach (Product::limit(3)->get() as $product) {
        echo "   - {$product->name} - Stock: {$product->stock} - Price: \${$product->price}\n";
    }
}
echo "\n";

// 3. Test Sales Table
echo "3. SALES TABLE\n";
$salesCount = Sale::count();
echo "   Sales count: " . $salesCount . "\n";
if ($salesCount > 0) {
    foreach (Sale::limit(3)->get() as $sale) {
        echo "   - Sale #{$sale->id} - Total: \${$sale->total_amount}\n";
    }
}
echo "\n";

// 4. Test Attendances Table
echo "4. ATTENDANCES TABLE\n";
$attendanceCount = Attendance::count();
echo "   Attendances count: " . $attendanceCount . "\n";
if ($attendanceCount > 0) {
    foreach (Attendance::limit(3)->get() as $att) {
        echo "   - User ID: {$att->user_id} - Check-in: {$att->check_in_time}\n";
    }
}
echo "\n";

// 5. Test Sessions Table
echo "5. SESSIONS TABLE\n";
$sessionCount = DB::table('sessions')->count();
echo "   Sessions count: " . $sessionCount . "\n";
echo "\n";

echo "=== Database Control Summary ===\n";
echo "✅ Users: " . User::count() . " records\n";
echo "✅ Products: " . $productsCount . " records\n";
echo "✅ Sales: " . $salesCount . " records\n";
echo "✅ Attendances: " . $attendanceCount . " records\n";
echo "✅ Sessions: " . $sessionCount . " records (for CSRF)\n";
echo "\nAll database controls are functional!\n";

