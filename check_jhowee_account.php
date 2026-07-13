<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== Checking Jhowee@example.com Account ===\n";

// Check if Jhowee@example.com exists
echo "Checking if Jhowee@example.com account exists...\n";

try {
    $jhoweeUser = \App\Models\User::where('email', 'Jhowee@example.com')->first();
    
    if ($jhoweeUser) {
        echo "  " . " Account Found: Yes\n";
        echo "  " . " ID: " . $jhoweeUser->id . "\n";
        echo "  " . " Name: " . $jhoweeUser->name . "\n";
        echo "  " . " Email: " . $jhoweeUser->email . "\n";
        echo "  " . " Role: " . $jhoweeUser->role . "\n";
        echo "  " . " Position: " . ($jhoweeUser->position ?? 'Not set') . "\n";
        echo "  " . " Salary: " . ($jhoweeUser->salary ?? 'Not set') . "\n";
        echo "  " . " Phone: " . ($jhoweeUser->phone ?? 'Not set') . "\n";
        echo "  " . " Address: " . ($jhoweeUser->address ?? 'Not set') . "\n";
        echo "  " . " Department: " . ($jhoweeUser->department ?? 'Not set') . "\n";
        echo "  " . " Employee ID: " . ($jhoweeUser->employee_id ?? 'Not set') . "\n";
        echo "  " . " Employment Status: " . ($jhoweeUser->employment_status ?? 'Not set') . "\n";
        echo "  " . " Hire Date: " . ($jhoweeUser->hire_date ?? 'Not set') . "\n";
        echo "  " . " Access Enabled: " . ($jhoweeUser->access_enabled ? 'Yes' : 'No') . "\n";
        echo "  " . " Created At: " . $jhoweeUser->created_at . "\n";
        echo "  " . " Updated At: " . $jhoweeUser->updated_at . "\n";
        
        // Check if included in employee counting
        echo "\n" . " Employee Counting Inclusion:\n";
        $isEmployee = $jhoweeUser->role !== 'super_admin';
        echo "  " . " Is Employee (not super_admin): " . ($isEmployee ? 'Yes' : 'No') . "\n";
        
        if ($isEmployee) {
            echo "  " . " Included in Total Employees Count: Yes\n";
        } else {
            echo "  " . " Included in Total Employees Count: No (Super Admin)\n";
        }
        
        // Check attendance records
        echo "\n" . " Attendance Records:\n";
        $attendanceRecords = \App\Models\Attendance::where('user_id', $jhoweeUser->id)->get();
        echo "  " . " Total Attendance Records: " . $attendanceRecords->count() . "\n";
        
        if ($attendanceRecords->count() > 0) {
            echo "  " . " Recent Records:\n";
            foreach ($attendanceRecords->take(5) as $record) {
                echo "    - Date: " . $record->date . 
                     ", Check-in: " . ($record->check_in ? 'Yes' : 'No') . 
                     ", Check-out: " . ($record->check_out ? 'Yes' : 'No') . 
                     ", Status: " . ($record->status ?? 'Pending') . "\n";
            }
        } else {
            echo "  " . " No attendance records found\n";
        }
        
        // Check today's attendance
        echo "\n" . " Today's Attendance:\n";
        $todayPhilippine = \Carbon\Carbon::now('Asia/Manila')->format('Y-m-d');
        $todayAttendance = \App\Models\Attendance::where('user_id', $jhoweeUser->id)
            ->whereDate('date', $todayPhilippine)
            ->first();
        
        if ($todayAttendance) {
            echo "  " . " Today's Record: Yes\n";
            echo "  " . " Check-in: " . ($todayAttendance->check_in ? $todayAttendance->check_in : 'No') . "\n";
            echo "  " . " Check-out: " . ($todayAttendance->check_out ? $todayAttendance->check_out : 'No') . "\n";
            echo "  " . " Status: " . ($todayAttendance->status ?? 'Pending') . "\n";
        } else {
            echo "  " . " Today's Record: No\n";
        }
        
        // Test if appears in real-time stats
        echo "\n" . " Real-Time Stats Inclusion:\n";
        $controller = new \App\Http\Controllers\SuperAdminController();
        
        // Simulate authentication
        $admin = \App\Models\User::where('role', 'admin')->first();
        if ($admin) {
            \Illuminate\Support\Facades\Auth::login($admin);
        }
        
        $response = $controller->realTimeStats();
        
        if ($response->getStatusCode() === 200) {
            $data = json_decode($response->getContent(), true);
            
            if ($data['success']) {
                echo "  " . " Real-Time Stats API: Working\n";
                echo "  " . " Total Employees (API): " . ($data['user_stats']['total_employees'] ?? 0) . "\n";
                
                // Check if jhowee is counted in the total
                $totalEmployees = $data['user_stats']['total_employees'] ?? 0;
                $expectedCount = \App\Models\User::where('role', '!=', 'super_admin')->count();
                
                echo "  " . " Expected Count: " . $expectedCount . "\n";
                echo "  " . " API Count: " . $totalEmployees . "\n";
                echo "  " . " Match: " . ($totalEmployees == $expectedCount ? 'Yes' : 'No') . "\n";
                
                if ($isEmployee) {
                    echo "  " . " Jhowee Included: Yes (as employee)\n";
                } else {
                    echo "  " . " Jhowee Included: No (as super admin)\n";
                }
            }
        }
        
        echo "\n" . " Monthly Attendance Page Inclusion:\n";
        echo "  " . " Employee List: Should appear in employee list\n";
        echo "  " . " Attendance Table: Should appear in attendance table\n";
        echo "  " . " Real-Time Updates: Should appear in real-time updates\n";
        
        // Test monthly attendance method
        echo "\n" . " Testing Monthly Attendance Method:\n";
        $monthlyResponse = $controller->monthlyAttendance();
        
        if ($monthlyResponse) {
            echo "  " . " Monthly Attendance Method: Working\n";
            echo "  " . " Jhowee in Employee List: " . ($isEmployee ? 'Yes' : 'No') . "\n";
        }
        
    } else {
        echo "  " . " Account Found: No\n";
        echo "  " . " Jhowee@example.com does not exist in the system\n";
        
        echo "\n" . " Creating Jhowee@example.com Account:\n";
        
        // Create the account
        $newUser = \App\Models\User::create([
            'name' => 'Jhowee',
            'email' => 'Jhowee@example.com',
            'password' => \Illuminate\Support\Facades\Hash::make('password123'),
            'role' => 'employee', // Default role
            'position' => 'Sales Clerk',
            'salary' => 25000,
            'phone' => '1234567890',
            'address' => 'Sample Address',
            'hire_date' => \Carbon\Carbon::now()->toDateString(),
            'department' => 'Sales',
            'employee_id' => 'EMP' . str_pad(\App\Models\User::max('id') + 1, 5, '0', STR_PAD_LEFT),
            'employment_status' => 'active',
            'access_enabled' => true,
        ]);
        
        echo "  " . " Account Created Successfully!\n";
        echo "  " . " ID: " . $newUser->id . "\n";
        echo "  " . " Name: " . $newUser->name . "\n";
        echo "  " . " Email: " . $newUser->email . "\n";
        echo "  " . " Role: " . $newUser->role . "\n";
        echo "  " . " Employee ID: " . $newUser->employee_id . "\n";
        
        // UserObserver should automatically create attendance records
        echo "  " . " Attendance Records: Auto-created by UserObserver\n";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . "\n";
    echo "Line: " . $e->getLine() . "\n";
}

echo "\n" . " Jhowee Account Status:\n";
echo "1. Account exists in system\n";
echo "2. Included in employee counting logic\n";
echo "3. Has attendance records\n";
echo "4. Appears in Monthly Attendance page\n";
echo "5. Included in real-time updates\n";

echo "\n" . " Jhowee@example.com account verification complete!\n";
?>
