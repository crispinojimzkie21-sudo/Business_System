<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();

        if (!$user) {
            return redirect()->route('login');
        }

        if ($user->isSuperAdmin()) {
            return redirect()->route('dashboard.superadmin');
        }

        if ($user->isAdmin()) {
            return redirect()->route('dashboard.admin');
        }

        // Redirect cashier and sales clerk to cashier dashboard
        if ($user->isCashier()) {
            return redirect()->route('dashboard.cashier');
        }

        // Redirect manager to manager dashboard
        if ($user->isManager()) {
            return redirect()->route('dashboard.manager');
        }

        // Redirect employee, user, and any other roles to employee dashboard
        return redirect()->route('dashboard.employee');
    }
}
