<?php

namespace App\Observers;

use App\Models\User;
use App\Models\Attendance;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class UserObserver
{
    /**
     * Handle the User "created" event.
     *
     * @param  \App\Models\User  $user
     * @return void
     */
    public function created(User $user)
    {
        // Only create attendance records for employee roles
        $employeeRoles = ['employee', 'sales_clerk', 'cashier', 'manager', 'admin'];
        
        if (in_array($user->role, $employeeRoles)) {
            try {
                $this->createInitialAttendanceRecords($user);
                Log::info('Initial attendance records created for new employee', [
                    'user_id' => $user->id,
                    'user_name' => $user->name,
                    'user_role' => $user->role
                ]);
            } catch (\Exception $e) {
                Log::error('Failed to create initial attendance records for new employee', [
                    'user_id' => $user->id,
                    'error' => $e->getMessage()
                ]);
            }
        }
    }

    /**
     * Create initial attendance records for the current month
     *
     * @param User $user
     * @return void
     */
    private function createInitialAttendanceRecords(User $user)
    {
        $currentDate = Carbon::now('Asia/Manila');
        $currentYear = $currentDate->year;
        $currentMonth = $currentDate->month;
        $currentDay = $currentDate->day;
        
        // Create attendance records from the 1st of the current month up to today
        for ($day = 1; $day <= $currentDay; $day++) {
            $attendanceDate = Carbon::create($currentYear, $currentMonth, $day, 0, 0, 0, 'Asia/Manila');
            
            // Only create records for weekdays (Monday to Friday)
            if ($attendanceDate->isWeekday()) {
                Attendance::firstOrCreate([
                    'user_id' => $user->id,
                    'date' => $attendanceDate->format('Y-m-d')
                ], [
                    'check_in' => null,
                    'check_out' => null,
                    'check_in_location' => null,
                    'check_out_location' => null,
                    'status' => 'pending',
                    'notes' => 'Auto-generated for new employee',
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            }
        }
        
        Log::info('Attendance records created for new employee', [
            'user_id' => $user->id,
            'user_name' => $user->name,
            'records_created' => $currentDay,
            'month' => $currentMonth,
            'year' => $currentYear
        ]);
    }

    /**
     * Handle the User "updated" event.
     *
     * @param  \App\Models\User  $user
     * @return void
     */
    public function updated(User $user)
    {
        // Check if role was changed to an employee role
        $employeeRoles = ['employee', 'sales_clerk', 'cashier', 'manager', 'admin'];
        
        if ($user->wasChanged('role') && in_array($user->role, $employeeRoles)) {
            try {
                $this->createInitialAttendanceRecords($user);
                Log::info('Attendance records created for user role change', [
                    'user_id' => $user->id,
                    'user_name' => $user->name,
                    'new_role' => $user->role,
                    'old_role' => $user->getOriginal('role')
                ]);
            } catch (\Exception $e) {
                Log::error('Failed to create attendance records for role change', [
                    'user_id' => $user->id,
                    'error' => $e->getMessage()
                ]);
            }
        }
    }

    /**
     * Handle the User "deleted" event.
     *
     * @param  \App\Models\User  $user
     * @return void
     */
    public function deleted(User $user)
    {
        // Log when an employee is deleted
        $employeeRoles = ['employee', 'sales_clerk', 'cashier', 'manager', 'admin'];
        
        if (in_array($user->role, $employeeRoles)) {
            Log::info('Employee deleted', [
                'user_id' => $user->id,
                'user_name' => $user->name,
                'user_role' => $user->role
            ]);
        }
    }
}
