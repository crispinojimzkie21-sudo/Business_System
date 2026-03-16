<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class UserAccessController extends Controller
{
    /**
     * Display the user access control panel
     */
    public function index()
    {
        // Get all admin and cashier users (exclude super admin)
        $users = User::whereIn('role', ['admin', 'employee'])
            ->where('role', '!=', 'super_admin')
            ->orderBy('role')
            ->orderBy('name')
            ->get();

        // Statistics
        $totalUsers = $users->count();
        $enabledUsers = $users->where('access_status', 'enabled')->count();
        $disabledUsers = $users->where('access_status', 'disabled')->count();
        
        $adminUsers = $users->where('role', 'admin');
        $cashierUsers = $users->where('role', 'employee');

        return view('user-access.index', compact(
            'users',
            'totalUsers',
            'enabledUsers',
            'disabledUsers',
            'adminUsers',
            'cashierUsers'
        ));
    }

    /**
     * Enable user access
     */
    public function enableAccess(Request $request, $userId)
    {
        $user = User::findOrFail($userId);
        
        // Validate that this is not a super admin
        if ($user->role === 'super_admin') {
            return back()->with('error', 'Cannot modify Super Admin access.');
        }

        // Validate that the user has admin or employee role
        if (!in_array($user->role, ['admin', 'employee'])) {
            return back()->with('error', 'Access control is only available for Admin and Employee roles.');
        }

        $oldStatus = $user->access_status;
        
        $user->update([
            'access_status' => 'enabled',
            'access_restriction_reason' => null,
            'access_enabled_at' => Carbon::now(),
            'enabled_by' => Auth::id(),
        ]);

        // Log the action
        Log::info('User access enabled', [
            'target_user_id' => $user->id,
            'target_user_name' => $user->name,
            'target_user_role' => $user->role,
            'enabled_by' => Auth::id(),
            'enabled_by_name' => Auth::user()->name,
            'old_status' => $oldStatus,
            'new_status' => 'enabled',
            'timestamp' => Carbon::now(),
        ]);

        return back()->with('success', "Access enabled for {$user->name} ({$user->role}).");
    }

    /**
     * Disable user access
     */
    public function disableAccess(Request $request, $userId)
    {
        $request->validate([
            'reason' => 'required|string|max:255',
        ]);

        $user = User::findOrFail($userId);
        
        // Validate that this is not a super admin
        if ($user->role === 'super_admin') {
            return back()->with('error', 'Cannot modify Super Admin access.');
        }

        // Validate that the user has admin or employee role
        if (!in_array($user->role, ['admin', 'employee'])) {
            return back()->with('error', 'Access control is only available for Admin and Employee roles.');
        }

        // Prevent disabling yourself
        if ($user->id === Auth::id()) {
            return back()->with('error', 'You cannot disable your own access.');
        }

        $oldStatus = $user->access_status;
        
        $user->update([
            'access_status' => 'disabled',
            'access_restriction_reason' => $request->reason,
            'access_disabled_at' => Carbon::now(),
            'disabled_by' => Auth::id(),
        ]);

        // Log the action
        Log::info('User access disabled', [
            'target_user_id' => $user->id,
            'target_user_name' => $user->name,
            'target_user_role' => $user->role,
            'disabled_by' => Auth::id(),
            'disabled_by_name' => Auth::user()->name,
            'reason' => $request->reason,
            'old_status' => $oldStatus,
            'new_status' => 'disabled',
            'timestamp' => Carbon::now(),
        ]);

        return back()->with('success', "Access disabled for {$user->name} ({$user->role}). Reason: {$request->reason}");
    }

    /**
     * Show access history for a user
     */
    public function showAccessHistory($userId)
    {
        $user = User::findOrFail($userId);
        
        // Get access logs from the log file (simplified version)
        $logs = $this->getUserAccessLogs($userId);

        return view('user-access.history', compact('user', 'logs'));
    }

    /**
     * Bulk enable/disable user access
     */
    public function bulkUpdateAccess(Request $request)
    {
        $request->validate([
            'user_ids' => 'required|array',
            'user_ids.*' => 'exists:users,id',
            'action' => 'required|in:enable,disable',
            'reason' => 'required_if:action,disable|string|max:255',
        ]);

        $userIds = $request->user_ids;
        $action = $request->action;
        $reason = $request->reason;
        $updatedCount = 0;

        foreach ($userIds as $userId) {
            $user = User::findOrFail($userId);
            
            // Skip super admin and self
            if ($user->role === 'super_admin' || $user->id === Auth::id()) {
                continue;
            }

            // Only admin and employee roles
            if (!in_array($user->role, ['admin', 'employee'])) {
                continue;
            }

            if ($action === 'enable') {
                $user->update([
                    'access_status' => 'enabled',
                    'access_restriction_reason' => null,
                    'access_enabled_at' => Carbon::now(),
                    'enabled_by' => Auth::id(),
                ]);
                $updatedCount++;
            } else {
                $user->update([
                    'access_status' => 'disabled',
                    'access_restriction_reason' => $reason,
                    'access_disabled_at' => Carbon::now(),
                    'disabled_by' => Auth::id(),
                ]);
                $updatedCount++;
            }
        }

        $actionText = $action === 'enable' ? 'enabled' : 'disabled';
        return back()->with('success', "Successfully {$actionText} access for {$updatedCount} users.");
    }

    /**
     * Get user access logs from log file
     */
    private function getUserAccessLogs($userId)
    {
        // This is a simplified version - in production, you might want to store logs in database
        $logs = [];
        $logFile = storage_path('logs/laravel.log');
        
        if (file_exists($logFile)) {
            $content = file_get_contents($logFile);
            $lines = explode("\n", $content);
            
            foreach ($lines as $line) {
                if (strpos($line, 'User access') !== false && strpos($line, "target_user_id:{$userId}") !== false) {
                    $logs[] = $line;
                }
            }
        }
        
        return array_reverse(array_slice($logs, -50)); // Last 50 entries, newest first
    }

    /**
     * API endpoint to check user access status
     */
    public function checkUserAccess($userId)
    {
        $user = User::findOrFail($userId);
        
        return response()->json([
            'user_id' => $user->id,
            'name' => $user->name,
            'role' => $user->role,
            'access_status' => $user->access_status,
            'can_login' => $user->access_status === 'enabled',
            'restriction_reason' => $user->access_restriction_reason,
            'access_disabled_at' => $user->access_disabled_at,
            'access_enabled_at' => $user->access_enabled_at,
        ]);
    }
}
