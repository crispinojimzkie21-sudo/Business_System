<?php
// Bootstrap Laravel
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== Database Session Test ===\n";
echo "Session driver: " . config('session.driver') . "\n";
echo "Session connection: " . config('session.connection') . "\n";
echo "Session table: " . config('session.table') . "\n\n";

$sessionCount = DB::table('sessions')->count();
echo "Sessions in database: " . $sessionCount . "\n\n";

echo "Testing session write...\n";

// Start a test session
$testSessionId = 'test_' . time();
$testUserId = 1;
$testPayload = encrypt(['_token' => 'test_token_' . time(), 'login_web_' . 'test' => $testUserId]);

try {
    DB::table('sessions')->insert([
        'id' => $testSessionId,
        'user_id' => $testUserId,
        'ip_address' => '127.0.0.1',
        'user_agent' => 'Test Agent',
        'payload' => base64_encode($testPayload),
        'last_activity' => time()
    ]);
    echo "✅ Test session written to database!\n";
    
    // Verify it was written
    $count = DB::table('sessions')->count();
    echo "Sessions now in DB: " . $count . "\n";
    
    // Clean up test session
    DB::table('sessions')->where('id', $testSessionId)->delete();
    echo "✅ Test session cleaned up!\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}

echo "\n=== Test Complete ===\n";

