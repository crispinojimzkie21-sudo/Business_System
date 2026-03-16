<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class SessionTimeout
{
    /**
     * Handle an incoming request.
     * Automatically logs out users after a period of inactivity.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // If user is not authenticated, just continue
        if (!Auth::check()) {
            return $next($request);
        }

        $user = Auth::user();
        
        // Get the last activity time from session
        $lastActivity = $request->session()->get('last_activity');
        
        // Get session lifetime from config (default 120 minutes)
        $sessionLifetime = config('session.lifetime', 120);
        
        // If no last_activity, set it now
        if (!$lastActivity) {
            $request->session()->put('last_activity', time());
            return $next($request);
        }
        
        // Calculate time since last activity
        $inactiveSeconds = time() - $lastActivity;
        $lifetimeSeconds = $sessionLifetime * 60;
        
        // Check if session has expired due to inactivity
        if ($inactiveSeconds >= $lifetimeSeconds) {
            Log::info('Session expired due to inactivity', [
                'user_id' => $user->id,
                'email' => $user->email,
                'role' => $user->role,
                'inactive_minutes' => round($inactiveSeconds / 60, 2),
                'session_lifetime_minutes' => $sessionLifetime
            ]);
            
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            
            return redirect()->route('login')->with('message', 'Your session has expired due to inactivity. Please log in again.');
        }
        
        // Update last activity time
        $request->session()->put('last_activity', time());
        
        return $next($request);
    }
}

