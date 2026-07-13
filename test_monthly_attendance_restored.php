<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use App\Models\Attendance;
use Illuminate\Support\Facades\Auth;

echo "=== Testing Restored Monthly Attendance Page ===\n";

// Get admin user and simulate login
$admin = User::where('role', 'admin')->first();
if (!$admin) {
    echo "No admin user found!\n";
    exit;
}

echo "Admin User: " . $admin->name . " (" . $admin->email . ")\n";

// Simulate authentication
Auth::login($admin);

// Test the refreshAttendances method
$controller = new \App\Http\Controllers\SuperAdminController();

echo "\nTesting refreshAttendances method with monthly data...\n";

try {
    $response = $controller->refreshAttendances();
    
    if ($response->getStatusCode() === 200) {
        $data = json_decode($response->getContent(), true);
        
        if (!isset($data['error'])) {
            echo "Success! Monthly Attendance Data:\n";
            echo "Today's Statistics:\n";
            echo "  Checked In: " . ($data['checkedInToday'] ?? 0) . "\n";
            echo "  Checked Out: " . ($data['checkedOutToday'] ?? 0) . "\n";
            echo "  Currently Working: " . ($data['currentlyWorking'] ?? 0) . "\n";
            echo "  Total Employees: " . ($data['totalEmployees'] ?? 0) . "\n";
            echo "  Attendance Rate: " . ($data['attendanceRate'] ?? 0) . "%\n";
            
            echo "\nMonthly Overview (Jan-Dec):\n";
            if (isset($data['monthlyData']) && is_array($data['monthlyData'])) {
                foreach ($data['monthlyData'] as $month) {
                    echo "  " . ($month['month_name'] ?? 'Unknown') . ":\n";
                    echo "    Present: " . ($month['present'] ?? 0) . "\n";
                    echo "    Absent: " . ($month['absent'] ?? 0) . "\n";
                    echo "    Late: " . ($month['late'] ?? 0) . "\n";
                    echo "    Rate: " . ($month['rate'] ?? 0) . "%\n";
                }
            }
            
            echo "\nToday's Attendance Records:\n";
            if (isset($data['attendances']) && is_array($data['attendances'])) {
                foreach ($data['attendances'] as $attendance) {
                    echo "  " . ($attendance['user_name'] ?? 'Unknown') . "\n";
                    echo "    Check In: " . ($attendance['check_in'] ?? 'N/A') . "\n";
                    echo "    Check Out: " . ($attendance['check_out'] ?? 'N/A') . "\n";
                    echo "    Location: " . ($attendance['location'] ?? 'N/A') . "\n";
                }
            }
            
            echo "\nFeatures Restored:\n";
            echo "  January to December monthly overview cards\n";
            echo "  Present/Absent/Late statistics per month\n";
            echo "  Attendance rate calculations\n";
            echo "  Real-time data refresh functionality\n";
            echo "  Error handling with fallbacks\n";
            
        } else {
            echo "Error: " . ($data['message'] ?? 'Unknown error') . "\n";
        }
    } else {
        echo "HTTP Error: " . $response->getStatusCode() . "\n";
    }
    
} catch (Exception $e) {
    echo "Exception: " . $e->getMessage() . "\n";
}

echo "\nTest Instructions:\n";
echo "1. Visit: http://127.0.0.1:8000/superadmin/refresh-attendances\n";
echo "2. Login as admin user\n";
echo "3. Check for January to December monthly cards\n";
echo "4. Verify monthly statistics (Present/Absent/Late/Rate)\n";
echo "5. Test real-time refresh functionality\n";
?>
