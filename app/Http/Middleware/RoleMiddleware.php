<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next, string $role = null)
    {
        // Check if user is authenticated
        if (!Auth::check()) {
            Log::warning('RoleMiddleware: User not authenticated', [
                'ip' => $request->ip(),
                'url' => $request->url()
            ]);
            return redirect()->route('login')->with('message', 'Please login to access this page.');
        }

        $user = Auth::user();

        Log::info('RoleMiddleware Check', [
            'user_id' => $user->id,
            'user_email' => $user->email,
            'user_role' => $user->role,
            'required_role' => $role,
            'is_admin' => $user->isAdmin(),
            'is_super_admin' => $user->isSuperAdmin(),
            'role_check' => $user->role === $role
        ]);

        // If no specific role required, allow access
        if (!$role) {
            return $next($request);
        }

        // Check for super_admin role
        if ($role === 'super_admin') {
            if (!$user->isSuperAdmin()) {
                Log::error('RoleMiddleware: Super Admin access denied', [
                    'user_id' => $user->id,
                    'user_role' => $user->role,
                    'required_role' => $role
                ]);
                abort(403, 'Super Admin access required. You do not have permission to view this page.');
            }
            return $next($request);
        }

        // Check for admin role (allow both admin and super_admin)
        if ($role === 'admin') {
            if (!$user->isAdmin() && !$user->isSuperAdmin()) {
                Log::error('RoleMiddleware: Admin access denied', [
                    'user_id' => $user->id,
                    'user_role' => $user->role,
                    'required_role' => $role
                ]);
                abort(403, 'Admin access required. You do not have permission to view this page.');
            }
            return $next($request);
        }

        // Check for cashier role
        if ($role === 'cashier') {
            if (!$user->isCashier()) {
                Log::error('RoleMiddleware: Cashier access denied', [
                    'user_id' => $user->id,
                    'user_role' => $user->role,
                    'required_role' => $role
                ]);
                abort(403, 'Cashier access required. You do not have permission to view this page.');
            }
            return $next($request);
        }

        // Check for manager role
        if ($role === 'manager') {
            if (!$user->isManager()) {
                Log::error('RoleMiddleware: Manager access denied', [
                    'user_id' => $user->id,
                    'user_role' => $user->role,
                    'required_role' => $role
                ]);
                abort(403, 'Manager access required. You do not have permission to view this page.');
            }
            return $next($request);
        }

        // Check for employee role
        if ($role === 'employee') {
            if (!$user->isEmployee()) {
                Log::error('RoleMiddleware: Employee access denied', [
                    'user_id' => $user->id,
                    'user_role' => $user->role,
                    'required_role' => $role
                ]);
                abort(403, 'Employee access required. You do not have permission to view this page.');
            }
            return $next($request);
        }

        // Handle multiple roles (support both '|' and ',' separators, e.g., role:employee|manager or role:super_admin,admin)
        if (strpos($role, '|') !== false || strpos($role, ',') !== false) {
            $allowedRoles = preg_split('/[\|,]/', $role);
            $allowedRoles = array_map('trim', $allowedRoles);
            $hasAccess = false;
            
            foreach ($allowedRoles as $allowedRole) {
                switch ($allowedRole) {
                    case 'employee':
                        if ($user->isEmployee()) $hasAccess = true;
                        break;
                    case 'manager':
                        if ($user->isManager()) $hasAccess = true;
                        break;
                    case 'cashier':
                        if ($user->isCashier()) $hasAccess = true;
                        break;
                    case 'sales_clerk':
                        if ($user->isSalesClerk()) $hasAccess = true;
                        break;
                    case 'admin':
                        if ($user->isAdmin() || $user->isSuperAdmin()) $hasAccess = true;
                        break;
                    case 'super_admin':
                        if ($user->isSuperAdmin()) $hasAccess = true;
                        break;
                }
            }
            
            if (!$hasAccess) {
                Log::error('RoleMiddleware: Multiple role access denied', [
                    'user_id' => $user->id,
                    'user_role' => $user->role,
                    'required_roles' => $allowedRoles
                ]);
                abort(403, 'Access denied. You do not have permission to view this page.');
            }
            
            return $next($request);
        }

        // Additional security: Check access_enabled (post-login protection)
        if (!$user->isAccessEnabled() && !$user->isSuperAdmin()) {
            Log::warning('RoleMiddleware: Access disabled post-login', [
                'user_id' => $user->id,
                'user_role' => $user->role,
                'ip' => $request->ip()
            ]);
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            return redirect()->route('login')->with('error', 'Your account access has been disabled by Super Admin. Please contact administrator.');
        }

        return $next($request);
    }
}
