<?php







namespace App\Http\Controllers;







use App\Models\User;



use Illuminate\Http\Request;



use Illuminate\Support\Facades\Auth;



use Illuminate\Support\Facades\Hash;



use Illuminate\Support\Facades\Log;







class AuthController extends Controller



{



    public function showLogin()



    {



        return view('auth.login');



    }







    public function showAdminLogin()



    {



        return view('auth.admin-login');



    }







    public function showSuperAdminLogin()



    {



        return view('auth.superadmin-login');



    }







    public function login(Request $request)



    {



        $credentials = $request->validate([



            'email' => ['required', 'email'],



            'password' => ['required'],



        ]);







        Log::info('Login attempt started', ['email' => $credentials['email']]);







        // First, check if user exists in database



        $user = User::where('email', $credentials['email'])->first();



        



        if (!$user) {



            Log::warning('Login failed: User not found', ['email' => $credentials['email']]);



            return back()->withErrors([



                'email' => 'No account found with this email address. Please check your email or contact your administrator.',



            ])->withInput($request->only('email'));



        }







        Log::info('User found', [



            'user_id' => $user->id,



            'email' => $user->email,



            'role' => $user->role,



            'is_admin' => $user->isAdmin(),



            'is_super_admin' => $user->isSuperAdmin(),



            'access_enabled' => $user->isAccessEnabled()



        ]);







        // Check if user is cashier - they should use admin login portal

        if ($user->isCashier()) {

            Log::warning('Cashier user attempted regular login', [

                'email' => $credentials['email'],

                'user_id' => $user->id

            ]);

            return back()->withErrors([

                'email' => 'Cashier users must use the Staff Portal. Please login at: /admin/login',

            ])->withInput($request->only('email'));

        }



        // Check access enabled and verify password



        if (!$user->isAccessEnabled()) {



            Log::warning('Login failed: Access disabled', ['email' => $credentials['email']]);



            return back()->withErrors([



                'email' => 'Your account access has been disabled. Please contact your administrator.',



            ])->withInput($request->only('email'));



        }







        if (!Hash::check($credentials['password'], $user->password)) {



            Log::warning('Login failed: Invalid password', ['email' => $credentials['email']]);



            return back()->withErrors([



                'password' => 'The password you entered is incorrect. Please try again.',



            ])->withInput($request->only('email'));



        }







        // Attempt to authenticate



        if (Auth::attempt($credentials, $request->boolean('remember'))) {



            $request->session()->regenerate();







            $authenticatedUser = Auth::user();







            Log::info('Login successful', [



                'user_id' => $authenticatedUser->id,



                'email' => $authenticatedUser->email,



                'role' => $authenticatedUser->role,



                'is_super_admin' => $authenticatedUser->isSuperAdmin(),



                'is_admin' => $authenticatedUser->isAdmin(),



                'is_cashier' => $authenticatedUser->isCashier(),



                'is_manager' => $authenticatedUser->isManager(),



                'is_employee' => $authenticatedUser->isEmployee(),



                'access_enabled' => $authenticatedUser->isAccessEnabled(),



            ]);







            // Determine redirect based on role



            if ($authenticatedUser->isSuperAdmin()) {



                Log::info('Redirecting to super admin dashboard');



                return redirect()->route('dashboard.superadmin')



                    ->with('success', 'Welcome back, Super Admin!');



            }







            if ($authenticatedUser->isAdmin()) {



                Log::info('Redirecting to admin dashboard');



                return redirect()->route('dashboard.admin')



                    ->with('success', 'Welcome back, Admin!');



            }







            // Manager - redirect to manager dashboard



            if ($authenticatedUser->isManager()) {



                Log::info('Redirecting to manager dashboard');



                return redirect()->route('dashboard.manager')



                    ->with('success', 'Welcome back, Manager!');



            }







            // Employee - redirect to employee dashboard



            if ($authenticatedUser->isEmployee()) {



                Log::info('Redirecting to employee dashboard');



                return redirect()->route('dashboard.employee')



                    ->with('success', 'Welcome back, Employee!');



            }







            // Fallback - redirect to user dashboard



            Log::info('Redirecting to user dashboard');



            return redirect()->route('dashboard.user')



                ->with('success', 'Welcome back!');



        }







        Log::warning('Login failed: Authentication attempt failed', ['email' => $credentials['email']]);







        return back()->withErrors([

            'email' => 'No account found with this email address. Please check your email or contact your administrator.',

        ])->withInput($request->only('email'));

    }



    public function adminLogin(Request $request)

    {

        $credentials = $request->validate([

            'email' => ['required', 'email'],

            'password' => ['required'],

        ]);



        Log::info('Admin login attempt started', ['email' => $credentials['email']]);



        // First, check if user exists in database

        $user = User::where('email', $credentials['email'])->first();

        

        if (!$user) {

            Log::warning('Admin login failed: User not found', ['email' => $credentials['email']]);

            return back()->withErrors([

                'email' => 'No account found with this email address. Please check your email or contact your administrator.',

            ])->withInput($request->only('email'));

        }



        // Check if user access is enabled

        if (!$user->isAccessEnabled()) {

            Log::warning('Admin login failed: Access disabled', ['email' => $credentials['email']]);

            return back()->withErrors([

                'email' => 'Your account access has been disabled. Please contact your administrator.',

            ])->withInput($request->only('email'));

        }



        if (Auth::attempt($credentials, $request->boolean('remember'))) {

            $user = Auth::user();

            

            // Verify user has admin or cashier role

            if (!$user->isAdmin() && !$user->isCashier()) {

                Auth::logout();

                Log::warning('Non-admin/cashier user attempted admin login', [

                    'email' => $credentials['email'], 

                    'role' => $user->role,

                    'user_id' => $user->id

                ]);

                return back()->withErrors([

                    'email' => 'Access denied. Admin or Cashier privileges required.',

                ])->withInput($request->only('email'));

            }



            $request->session()->regenerate();

            

            Log::info('Admin logged in successfully', [

                'user_id' => $user->id, 

                'email' => $user->email,

                'role' => $user->role

            ]);



            // Redirect to appropriate dashboard based on role

            $redirectRoute = $user->isCashier() ? route('dashboard.cashier') : route('dashboard.admin');

            $welcomeMessage = $user->isCashier() ? 'Welcome back, Cashier!' : 'Welcome back, Admin!';

            

            return redirect()->intended($redirectRoute)->with('success', $welcomeMessage);

        }



        Log::warning('Admin login failed - invalid credentials', ['email' => $credentials['email']]);



        return back()->withErrors([

            'email' => 'Invalid admin credentials. Please check your email and password.',

        ])->withInput($request->only('email'));

    }



    public function superAdminLogin(Request $request)

    {

        $credentials = $request->validate([

            'email' => ['required', 'email'],

            'password' => ['required', 'min:8'],

        ]);



        Log::info('Super Admin login attempt started', ['email' => $credentials['email']]);



        // First, check if user exists in database

        $user = User::where('email', $credentials['email'])->first();

        

        if (!$user) {

            Log::warning('Super Admin login failed: User not found', ['email' => $credentials['email']]);

            return back()->withErrors([

                'email' => 'No account found with this email address. Please check your email or contact your administrator.',

            ])->withInput($request->only('email'));

        }



        // Check if user access is enabled

        if (!$user->isAccessEnabled()) {

            Log::warning('Super Admin login failed: Access disabled', ['email' => $credentials['email']]);

            return back()->withErrors([

                'email' => 'Your account access has been disabled. Please contact your administrator.',

            ])->withInput($request->only('email'));

        }



        if (Auth::attempt($credentials, $request->boolean('remember'))) {

            $user = Auth::user();

            

            // Verify user has super admin role

            if (!$user->isSuperAdmin()) {

                Auth::logout();

                Log::warning('Non-super admin user attempted super admin login', [

                    'email' => $credentials['email'], 

                    'role' => $user->role,

                    'user_id' => $user->id

                ]);

                return back()->withErrors([

                    'email' => 'Access denied. Super Admin privileges required.',

                ])->withInput($request->only('email'));

            }



            $request->session()->regenerate();

            

            Log::info('Super Admin logged in successfully', [

                'user_id' => $user->id, 

                'email' => $user->email,

                'role' => $user->role,

                'ip' => $request->ip(),

                'user_agent' => $request->userAgent()

            ]);



            // Clear any old session data

            $request->session()->forget(['url.intended', 'errors', 'success']);

            

            // Set session variables for super admin

            $request->session()->put([

                'super_admin_login' => true,

                'login_time' => now(),

                'user_name' => $user->name

            ]);



            return redirect()->route('dashboard.superadmin')->with('success', 'Welcome back, Super Admin!');

        }



        Log::warning('Super Admin login failed - invalid credentials', ['email' => $credentials['email']]);



        return back()->withErrors([

            'email' => 'Invalid super admin credentials. Please check your email and password.',

        ])->withInput($request->only('email'));

    }



    public function logout(Request $request)

    {

        $user = Auth::user();

        Log::info('User logged out', ['user_id' => $user?->id, 'email' => $user?->email]);



        Auth::logout();



        $request->session()->invalidate();

        $request->session()->regenerateToken();



        return redirect('/')->with('message', 'You have been logged out successfully.');

    }

}







