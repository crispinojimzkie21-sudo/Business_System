<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class CheckUserAccess
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Only check access for authenticated users
        if (Auth::check()) {
            $user = Auth::user();
            
            // Skip access check for super admin (they always have access)
            if ($user->role === 'super_admin') {
                return $next($request);
            }
            
            // Check if user has access status disabled
            if (!$user->isAccessEnabled()) {
                Log::warning('Access denied - user access disabled', [
                    'user_id' => $user->id,
                    'user_email' => $user->email,
                    'user_role' => $user->role,
                    'access_enabled' => $user->access_enabled,
                    'request_path' => $request->path(),
                    'ip_address' => $request->ip(),
                ]);
                
                // Logout the user immediately
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();
                
                // Redirect to login with error message
                return redirect()->route('login')
                    ->with('error', 'Your account access has been disabled. Please contact your system administrator.');
            }
        }
        
        return $next($request);
    }
}
