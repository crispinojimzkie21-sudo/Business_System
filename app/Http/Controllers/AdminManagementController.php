<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\AttendanceEmailList;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class AdminManagementController extends Controller
{
    // Employee Management Dashboard
    public function index()
    {
        // Statistics
        $totalEmployees = User::where('role', '!=', 'super_admin')->count();
        $activeEmployees = User::where('role', '!=', 'super_admin')
            ->where('employment_status', 'active')
            ->count();
        $inactiveEmployees = User::where('role', '!=', 'super_admin')
            ->where('employment_status', 'inactive')
            ->count();
        $onLeaveEmployees = User::where('role', '!=', 'super_admin')
            ->where('employment_status', 'on_leave')
            ->count();
        
        // Salary statistics
        $totalSalary = User::where('role', '!=', 'super_admin')
            ->where('employment_status', 'active')
            ->sum('salary');
        $avgSalary = User::where('role', '!=', 'super_admin')
            ->where('employment_status', 'active')
            ->avg('salary');
        
        // Department breakdown
        $departments = User::where('role', '!=', 'super_admin')
            ->whereNotNull('department')
            ->where('department', '!=', '')
            ->select('department')
            ->distinct()
            ->pluck('department');
        
        $recentHires = User::where('role', '!=', 'super_admin')
            ->whereNotNull('hire_date')
            ->orderBy('hire_date', 'desc')
            ->limit(5)
            ->get();
        
        return view('employees.index', compact(
            'totalEmployees', 'activeEmployees', 'inactiveEmployees', 
            'onLeaveEmployees', 'totalSalary', 'avgSalary', 
            'departments', 'recentHires'
        ));
    }

    // List all employees with search and filter
    public function listEmployees(Request $request)
    {
        $query = User::where('role', '!=', 'super_admin');
        
        // Search
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('employee_id', 'like', "%{$search}%")
                  ->orWhere('position', 'like', "%{$search}%")
                  ->orWhere('department', 'like', "%{$search}%");
            });
        }
        
        // Filter by role
        if ($request->has('role') && $request->role) {
            $query->where('role', $request->role);
        }
        
        // Filter by employment status
        if ($request->has('status') && $request->status) {
            $query->where('employment_status', $request->status);
        }
        
        // Filter by department
        if ($request->has('department') && $request->department) {
            $query->where('department', $request->department);
        }
        
        // Filter by position
        if ($request->has('position') && $request->position) {
            $query->where('position', $request->position);
        }
        
        // Sort
        $sortBy = $request->get('sort', 'name');
        $sortOrder = $request->get('order', 'asc');
        $query->orderBy($sortBy, $sortOrder);
        
        $employees = $query->paginate(15);
        
        // Get unique values for filters
        $departments = User::where('role', '!=', 'super_admin')
            ->whereNotNull('department')
            ->where('department', '!=', '')
            ->distinct()
            ->pluck('department');
        
        $positions = User::where('role', '!=', 'super_admin')
            ->whereNotNull('position')
            ->where('position', '!=', '')
            ->distinct()
            ->pluck('position');
        
        return view('employees.list', compact('employees', 'departments', 'positions'));
    }

    // Show admin registration form
    public function showRegister()
    {
        return view('admin.register');
    }

    // Register new admin account
    public function register(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', 'min:8'],
            'position' => ['nullable', 'string', 'max:255'],
            'salary' => ['nullable', 'numeric', 'min:0'],
        ]);

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'position' => $data['position'] ?? 'Admin Assistant',
            'salary' => $data['salary'] ?? 30000,
            'role' => 'admin',
            'access_enabled' => true, // Enable access by default
        ]);

        return redirect()->route('admin.list')->with('success', 'Admin account created successfully!');
    }

    public function showEmployeeRegister()
    {
        return view('employees.create');
    }

    public function registerEmployee(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', 'min:8'],
            'position' => ['nullable', 'string', 'max:255'],
            'salary' => ['nullable', 'numeric', 'min:0'],
            'role' => ['required', 'in:employee,admin,cashier'],
            'phone' => ['nullable', 'string', 'max:20'],
            'address' => ['nullable', 'string', 'max:500'],
            'hire_date' => ['nullable', 'date'],
            'department' => ['nullable', 'string', 'max:255'],
            'employee_id' => ['nullable', 'string', 'max:50', 'unique:users'],
            'employment_status' => ['nullable', 'in:active,inactive,on_leave,terminated'],
            'notes' => ['nullable', 'string'],
        ]);

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'position' => $data['position'] ?? null,
            'salary' => $data['salary'] ?? null,
            'role' => $data['role'],
            'phone' => $data['phone'] ?? null,
            'address' => $data['address'] ?? null,
            'hire_date' => $data['hire_date'] ?? Carbon::today()->toDateString(),
            'department' => $data['department'] ?? null,
            'employee_id' => $data['employee_id'] ?? 'EMP' . str_pad(User::max('id') + 1, 5, '0', STR_PAD_LEFT),
            'employment_status' => $data['employment_status'] ?? 'active',
            'notes' => $data['notes'] ?? null,
            'access_enabled' => true, // Enable access by default for all roles
        ]);

        // Automatically add to attendance email list (except super admin)
        if ($user->role !== 'super_admin') {
            AttendanceEmailList::create([
                'user_id' => $user->id,
                'email' => $user->email,
                'name' => $user->name,
                'role' => $user->role,
                'position' => $user->position,
                'is_active' => true,
            ]);
        }

        return redirect()->route('employee.list')->with('success', 'Employee account created successfully and added to attendance list!');
    }

    public function listAdmins()
    {
        $admins = User::where('role', 'admin')->paginate(10);
        return view('admin.list', compact('admins'));
    }

    public function editUser($id)
    {
        $user = User::findOrFail($id);
        
        // Prevent super admin from accessing their own edit page
        if ($user->id === Auth::id() && Auth::user()->isSuperAdmin()) {
            return back()->with('error', 'Super admin cannot edit their own profile through employee edit. Use Profile menu instead.');
        }
        
        return view('employees.edit', compact('user'));
    }

    public function updateUser(Request $request, $id)
    {
        $user = User::findOrFail($id);
        
        // Prevent super admin from editing their own profile through employee edit
        if ($user->id === Auth::id() && Auth::user()->isSuperAdmin()) {
            return back()->with('error', 'Super admin cannot edit their own profile through employee edit. Use Profile menu instead.');
        }
        
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'position' => ['nullable', 'string', 'max:255'],
            'salary' => ['nullable', 'numeric', 'min:0'],
            'role' => ['required', 'in:admin,employee,cashier,sales_clerk,manager,super_admin'],
            'phone' => ['nullable', 'string', 'max:20'],
            'address' => ['nullable', 'string', 'max:500'],
            'hire_date' => ['nullable', 'date'],
            'department' => ['nullable', 'string', 'max:255'],
            'employee_id' => ['nullable', 'string', 'max:50', 'unique:users,employee_id,' . $id],
            'employment_status' => ['nullable', 'in:active,inactive,on_leave,terminated'],
            'notes' => ['nullable', 'string'],
            'password' => ['nullable', 'string', 'min:6', 'confirmed'],
        ]);

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        // Update attendance email list if user exists there
        $attendanceEmailEntry = $user->attendanceEmailList;
        if ($attendanceEmailEntry) {
            $attendanceEmailEntry->update([
                'email' => $user->email,
                'name' => $user->name,
                'role' => $user->role,
                'position' => $user->position,
            ]);
        }

        return back()->with('success', 'Employee updated successfully!');
    }

    public function deleteUser($id)
    {
        $user = User::findOrFail($id);
        
        if ($user->id === Auth::id()) {
            return back()->with('error', 'You cannot delete your own account.');
        }

        $user->delete();
        return back()->with('success', 'Employee deleted successfully!');
    }
    
    // Admin-specific edit (for super admin to edit admin accounts)
    public function editAdmin($id)
    {
        $user = User::findOrFail($id);
        
        // Prevent editing super_admin accounts
        if ($user->role === 'super_admin') {
            return back()->with('error', 'Cannot edit super admin accounts.');
        }
        
        return view('admin.edit', compact('user'));
    }
    
    // Admin-specific update
    public function updateAdmin(Request $request, $id)
    {
        $admin = User::findOrFail($id);
        
        // Prevent editing super_admin accounts
        if ($admin->role === 'super_admin') {
            return back()->with('error', 'Cannot update super admin accounts.');
        }
        
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'position' => ['nullable', 'string', 'max:255'],
            'salary' => ['nullable', 'numeric', 'min:0'],
            'phone' => ['nullable', 'string', 'max:20'],
            'address' => ['nullable', 'string', 'max:500'],
            'department' => ['nullable', 'string', 'max:255'],
            'employment_status' => ['nullable', 'in:active,inactive,on_leave,terminated'],
            'role' => ['required', 'in:admin,employee'],
        ]);

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $admin->update($data);

        return redirect()->route('admin.list')->with('success', 'Admin account updated successfully!');
    }
    
    // Admin-specific delete
    public function deleteAdmin($id)
    {
        $admin = User::findOrFail($id);
        
        // Prevent deleting super_admin accounts
        if ($admin->role === 'super_admin') {
            return back()->with('error', 'Cannot delete super admin accounts.');
        }
        
        if ($admin->id === Auth::id()) {
            return back()->with('error', 'You cannot delete your own account.');
        }

        $admin->delete();
        return redirect()->route('admin.list')->with('success', 'Admin account deleted successfully!');
    }
    
    // Update employment status
    public function updateStatus(Request $request, $id)
    {
        $user = User::findOrFail($id);
        
        $data = $request->validate([
            'employment_status' => ['required', 'in:active,inactive,on_leave,terminated'],
        ]);
        
        $user->update($data);
        
        return back()->with('success', 'Employment status updated successfully!');
    }
    
    // Employee profile view
    public function profile($id)
    {
        $employee = User::with(['attendances', 'sales'])->findOrFail($id);
        return view('employees.profile', compact('employee'));
    }

    /**
     * View all user profiles (Super Admin and Admin only)
     */
    public function allProfiles(Request $request)
    {
        $query = User::where('role', '!=', 'super_admin');
        
        // Search functionality
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('employee_id', 'like', "%{$search}%")
                  ->orWhere('position', 'like', "%{$search}%")
                  ->orWhere('department', 'like', "%{$search}%");
            });
        }
        
        // Filter by role
        if ($request->has('role') && $request->role) {
            $query->where('role', $request->role);
        }
        
        // Filter by employment status
        if ($request->has('status') && $request->status) {
            $query->where('employment_status', $request->status);
        }
        
        // Filter by access status
        if ($request->has('access') && $request->access) {
            if ($request->access === 'enabled') {
                $query->where('access_enabled', true);
            } elseif ($request->access === 'disabled') {
                $query->where('access_enabled', false);
            }
        }
        
        // Sort
        $sortBy = $request->get('sort', 'name');
        $sortOrder = $request->get('order', 'asc');
        $query->orderBy($sortBy, $sortOrder);
        
        $profiles = $query->paginate(15);
        
        return view('profiles.all', compact('profiles'));
    }

    /**
     * Toggle user access enabled status (Super Admin only)
     */
    public function toggleAccess($id)
    {
        $user = User::findOrFail($id);
        
        if (!in_array($user->role, ['admin', 'cashier', 'employee'])) {
            return back()->with('error', 'Can only toggle access for Admins, Cashiers, and Employees.');
        }

        $user->access_enabled = !$user->access_enabled;
        $user->save();

        $status = $user->access_enabled ? 'enabled' : 'disabled';
        return back()->with('success', "User {$user->name} access has been {$status}.");
    }
}

