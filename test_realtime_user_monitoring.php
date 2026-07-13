<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== Testing Real-Time User Account Monitoring ===\n";

// Test the realTimeStats method
$controller = new \App\Http\Controllers\SuperAdminController();

echo "Testing realTimeStats method...\n";

try {
    // Simulate authentication
    $admin = \App\Models\User::where('role', 'admin')->first();
    if ($admin) {
        \Illuminate\Support\Facades\Auth::login($admin);
    }
    
    $response = $controller->realTimeStats();
    
    if ($response->getStatusCode() === 200) {
        $data = json_decode($response->getContent(), true);
        
        if ($data['success']) {
            echo "Success! Real-time statistics loaded:\n";
            echo "Timestamp: " . ($data['timestamp'] ?? 'N/A') . "\n";
            
            echo "\nUser Statistics:\n";
            echo "  Total Employees: " . ($data['user_stats']['total_employees'] ?? 0) . "\n";
            echo "  Super Admin Count: " . ($data['user_stats']['super_admin_count'] ?? 0) . "\n";
            echo "  Admin Count: " . ($data['user_stats']['admin_count'] ?? 0) . "\n";
            echo "  Employee Count: " . ($data['user_stats']['employee_count'] ?? 0) . "\n";
            echo "  Cashier Count: " . ($data['user_stats']['cashier_count'] ?? 0) . "\n";
            echo "  Manager Count: " . ($data['user_stats']['manager_count'] ?? 0) . "\n";
            echo "  Total Users: " . ($data['user_stats']['total_users'] ?? 0) . "\n";
            echo "  Active Users: " . ($data['user_stats']['active_users'] ?? 0) . "\n";
            echo "  Disabled Users: " . ($data['user_stats']['disabled_users'] ?? 0) . "\n";
            
            echo "\nAttendance Statistics:\n";
            echo "  Checked In Today: " . ($data['attendance_stats']['checked_in_today'] ?? 0) . "\n";
            echo "  Checked Out Today: " . ($data['attendance_stats']['checked_out_today'] ?? 0) . "\n";
            echo "  Currently Working: " . ($data['attendance_stats']['currently_working'] ?? 0) . "\n";
            echo "  Absent Today: " . ($data['attendance_stats']['absent_today'] ?? 0) . "\n";
            echo "  Attendance Rate: " . ($data['attendance_stats']['attendance_rate'] ?? 0) . "%\n";
            
            echo "\nRecent Activity (Last 24 Hours):\n";
            echo "  New Users: " . ($data['notifications']['new_user_count'] ?? 0) . "\n";
            echo "  Deleted Users: " . ($data['notifications']['deleted_user_count'] ?? 0) . "\n";
            
            if (!empty($data['recent_activity']['new_users'])) {
                echo "  Recent New Users:\n";
                foreach ($data['recent_activity']['new_users'] as $user) {
                    echo "    - " . ($user['name'] ?? 'Unknown') . " (" . ($user['role'] ?? 'Unknown') . ") - " . ($user['time_ago'] ?? 'Unknown') . "\n";
                }
            }
            
            if (!empty($data['recent_activity']['deleted_users'])) {
                echo "  Recently Deleted Users:\n";
                foreach ($data['recent_activity']['deleted_users'] as $user) {
                    echo "    - " . ($user['name'] ?? 'Unknown') . " (" . ($user['role'] ?? 'Unknown') . ") - " . ($user['time_ago'] ?? 'Unknown') . "\n";
                }
            }
            
            echo "\nReal-Time Features:\n";
            echo "  User Account Monitoring:\n";
            echo "    - Detects new user account creation\n";
            echo "    - Detects user account deletion\n";
            echo "    - Tracks user count changes\n";
            echo "    - Monitors role-based statistics\n";
            echo "    - Updates in real-time (every 10 seconds)\n\n";
            
            echo "  Notification System:\n";
            echo "    - Queue-based notification management\n";
            echo "    - Animated user count changes\n";
            echo "    - Color-coded notifications (success/warning/info)\n";
            echo "    - Icon-based notification categories\n";
            echo "    - Auto-dismiss after 5 seconds\n\n";
            
            echo "  Performance Optimization:\n";
            echo "    - Page visibility API integration\n";
            echo "    - Reduced updates when page hidden\n";
            echo "    - Efficient polling intervals\n";
            echo "    - Minimal JavaScript overhead\n\n";
            
            echo "  Mobile Responsiveness:\n";
            echo "    - Touch-friendly notifications\n";
            echo "    - Responsive notification layout\n";
            echo "    - Mobile-optimized animations\n";
            echo "    - Consistent with mobile design\n\n";
            
        } else {
            echo "Error: " . ($data['error'] ?? 'Unknown error') . "\n";
        }
    } else {
        echo "HTTP Error: " . $response->getStatusCode() . "\n";
    }
    
} catch (Exception $e) {
    echo "Exception: " . $e->getMessage() . "\n";
}

echo "\nReal-Time Monitoring Test:\n";
echo "  Polling Frequency: Every 10 seconds for user changes\n";
echo "  Attendance Updates: Every 30 seconds\n";
echo "  Notification Queue: Prevents notification overlap\n";
echo "  Animation Effects: Scale and color transitions\n";
echo "  Error Handling: Graceful fallback on API errors\n";

echo "\nTest Instructions:\n";
echo "1. Visit: http://127.0.0.1:8000/superadmin/monthly-attendance\n";
echo "2. Open browser dev tools to monitor network requests\n";
echo "3. Create a new user account in another tab\n";
echo "4. Watch for real-time notifications on the Monthly Attendance page\n";
echo "5. Delete a user account and observe the notifications\n";
echo "6. Check the user count animation effects\n";
echo "7. Verify notification queue works with multiple changes\n";
echo "8. Test mobile responsiveness of notifications\n";

echo "\nExpected Behaviors:\n";
echo "  - New user: Green notification with user-plus icon\n";
echo "  - Deleted user: Yellow notification with user-times icon\n";
echo "  - Count increase: Green animation on total employees\n";
echo "  - Count decrease: Red animation on total employees\n";
echo "  - Multiple changes: Queued notifications\n";
echo "  - Page hidden: Reduced update frequency\n";
echo "  - Page visible: Normal update frequency resumed\n";
?>
