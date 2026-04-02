<!doctype html>

<html lang="en">

<head>

    <meta charset="utf-8">

    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link rel="icon" href="/favicon.svg" type="image/svg+xml">

    <title>My Profile - {{ config('app.name') }}</title>

    <script src="https://cdn.tailwindcss.com"></script>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>

        .glass-card {

            background: rgba(0, 0, 0, 0.4);

            backdrop-filter: blur(10px);

        }

    </style>

</head>

<body class="bg-gradient-to-br from-slate-900 via-blue-900 to-slate-900 min-h-screen text-white">

    <div class="max-w-4xl mx-auto p-6">

        <!-- Header -->

        <header class="flex items-center justify-between mb-8">

            <div class="flex items-center gap-4">

                <div class="w-14 h-14 bg-gradient-to-br from-blue-400 to-indigo-600 rounded-xl flex items-center justify-center text-2xl font-bold">

                    <i class="fas fa-user-circle"></i>

                </div>

                <div>

                    <h1 class="text-3xl font-bold text-white">My Profile</h1>

                    <p class="text-blue-300 text-sm">Manage your account</p>

                </div>

            </div>

            <div class="flex items-center gap-4">

                @if(Auth::user()->isCashier())

                    <a href="{{ route('dashboard.cashier') }}" class="px-4 py-2 text-green-200 hover:bg-green-900/30 rounded-lg">

                        <i class="fas fa-arrow-left mr-2"></i>Back to Dashboard

                    </a>

                @elseif(Auth::user()->isManager())

                    <a href="{{ route('dashboard.manager') }}" class="px-4 py-2 text-purple-200 hover:bg-purple-900/30 rounded-lg">

                        <i class="fas fa-arrow-left mr-2"></i>Back to Dashboard

                    </a>

                @elseif(Auth::user()->isAdmin())

                    <a href="{{ route('dashboard.admin') }}" class="px-4 py-2 text-blue-200 hover:bg-blue-900/30 rounded-lg">

                        <i class="fas fa-arrow-left mr-2"></i>Back to Dashboard

                    </a>

                @elseif(Auth::user()->isSuperAdmin())

                    <a href="{{ route('dashboard.superadmin') }}" class="px-4 py-2 text-red-200 hover:bg-red-900/30 rounded-lg">

                        <i class="fas fa-arrow-left mr-2"></i>Back to Dashboard

                    </a>

                @else

                    <a href="{{ route('dashboard.employee') }}" class="px-4 py-2 text-blue-200 hover:bg-blue-900/30 rounded-lg">

                        <i class="fas fa-arrow-left mr-2"></i>Back to Dashboard

                    </a>

                @endif

            </div>

        </header>



        <!-- Alert Messages -->

        @if (session('success'))

            <div class="mb-6 bg-green-900/50 border border-green-700 rounded-lg p-4">

                <p class="text-green-200">{{ session('success') }}</p>

            </div>

        @endif



        @if (session('error'))

            <div class="mb-6 bg-red-900/50 border border-red-700 rounded-lg p-4">

                <p class="text-red-200">{{ session('error') }}</p>

            </div>

        @endif



        @if ($errors->any())

            <div class="mb-6 bg-red-900/50 border border-red-700 rounded-lg p-4">

                <ul class="text-red-200">

                    @foreach ($errors->all() as $error)

                        <li>{{ $error }}</li>

                    @endforeach

                </ul>

            </div>

        @endif



        <!-- Profile Information -->

        <div class="glass-card p-6 rounded-xl border border-blue-500/30 mb-6">

            <h2 class="text-xl font-semibold mb-4 text-blue-400">

                <i class="fas fa-id-card mr-2"></i>Profile Information

            </h2>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                <div>

                    <h3 class="font-semibold text-blue-300 mb-3">Personal Details</h3>

                    <div class="space-y-3 text-sm">

                        <div class="flex justify-between">

                            <span class="text-blue-200">Name:</span>

                            <span class="font-medium">{{ Auth::user()->name }}</span>

                        </div>

                        <div class="flex justify-between">

                            <span class="text-blue-200">Email:</span>

                            <span class="font-medium">{{ Auth::user()->email }}</span>

                        </div>

                        <div class="flex justify-between">

                            <span class="text-blue-200">Phone:</span>

                            <span class="font-medium">{{ Auth::user()->phone ?? 'Not set' }}</span>

                        </div>

                        <div class="flex justify-between">

                            <span class="text-blue-200">Address:</span>

                            <span class="font-medium">{{ Auth::user()->address ?? 'Not set' }}</span>

                        </div>

                        @if(Auth::user()->employee_id)

                        <div class="flex justify-between">

                            <span class="text-blue-200">Employee ID:</span>

                            <span class="font-medium">{{ Auth::user()->employee_id }}</span>

                        </div>

                        @endif

                    </div>

                </div>

                <div>

                    <h3 class="font-semibold text-blue-300 mb-3">Work Information</h3>

                    <div class="space-y-3 text-sm">

                        <div class="flex justify-between">

                            <span class="text-blue-200">Position:</span>

                            <span class="font-medium">{{ Auth::user()->position ?? 'Not set' }}</span>

                        </div>

                        <div class="flex justify-between">

                            <span class="text-blue-200">Department:</span>

                            <span class="font-medium">{{ Auth::user()->department ?? 'Not set' }}</span>

                        </div>

                        @if(Auth::user()->hire_date)

                        <div class="flex justify-between">

                            <span class="text-blue-200">Hire Date:</span>

                            <span class="font-medium">{{ \Carbon\Carbon::parse(Auth::user()->hire_date)->format('M d, Y') }}</span>

                        </div>

                        @endif

                        <div class="flex justify-between">

                            <span class="text-blue-200">Role:</span>

                            <span class="font-medium 

                                @if(Auth::user()->isSuperAdmin()) text-red-400

                                @elseif(Auth::user()->isAdmin()) text-blue-400

                                @elseif(Auth::user()->isCashier()) text-green-400

                                @elseif(Auth::user()->isManager()) text-purple-400

                                @else text-gray-400

                                @endif">

                                @if(Auth::user()->isSuperAdmin()) Super Admin

                                @elseif(Auth::user()->isAdmin()) Admin

                                @elseif(Auth::user()->isCashier()) Cashier

                                @elseif(Auth::user()->isManager()) Manager

                                @else Employee

                                @endif

                            </span>

                        </div>

                        <div class="flex justify-between">

                            <span class="text-blue-200">Status:</span>

                            <span class="font-medium 

                                @if(Auth::user()->employment_status == 'active') text-green-400

                                @elseif(Auth::user()->employment_status == 'inactive') text-red-400

                                @elseif(Auth::user()->employment_status == 'on_leave') text-yellow-400

                                @else text-gray-400

                                @endif">

                                {{ ucfirst(Auth::user()->employment_status ?? 'active') }}

                            </span>

                        </div>

                    </div>

                </div>

            </div>

        </div>



        <!-- Additional Information -->

        <div class="glass-card p-6 rounded-xl border border-blue-500/30 mb-6">

            <h2 class="text-xl font-semibold mb-4 text-blue-400">

                <i class="fas fa-info-circle mr-2"></i>Additional Information

            </h2>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                <div class="space-y-3 text-sm">

                    <div class="flex justify-between">

                        <span class="text-blue-200">Member Since:</span>

                        <span class="font-medium">{{ Auth::user()->created_at->format('M d, Y') }}</span>

                    </div>

                    @if(Auth::user()->salary)

                    <div class="flex justify-between">

                        <span class="text-blue-200">Monthly Salary:</span>

                        <span class="font-medium text-green-400">₱{{ number_format(Auth::user()->salary, 2) }}</span>

                    </div>

                    @endif

                    @if(Auth::user()->notes)

                    <div>

                        <span class="text-blue-200">Notes:</span>

                        <p class="font-medium mt-1">{{ Auth::user()->notes }}</p>

                    </div>

                    @endif

                </div>

                <div class="space-y-3 text-sm">

                    <div class="flex justify-between">

                        <span class="text-blue-200">Access Status:</span>

                        <span class="font-medium 

                            @if(Auth::user()->isAccessEnabled()) text-green-400

                            @else text-red-400

                            @endif">

                            @if(Auth::user()->isAccessEnabled()) Enabled

                            @else Disabled

                            @endif

                        </span>

                    </div>

                    <div class="flex justify-between">

                        <span class="text-blue-200">Last Updated:</span>

                        <span class="font-medium">{{ Auth::user()->updated_at->format('M d, Y H:i') }}</span>

                    </div>

                </div>

            </div>

        </div>



        <!-- Change Password -->

        <div class="glass-card p-6 rounded-xl border border-blue-500/30">

            <h2 class="text-xl font-semibold mb-4 text-blue-400">

                <i class="fas fa-key mr-2"></i>Change Password

            </h2>

            <p class="text-sm text-blue-200 mb-4">Leave password fields empty if you don't want to change your password.</p>

            

            <form method="POST" action="{{ route('profile.update-password') }}" class="space-y-4">

                @csrf

                

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                    <div>

                        <label class="block text-sm font-medium text-blue-300 mb-2">New Password</label>

                        <input name="password" type="password" class="w-full px-4 py-2 border border-gray-700 bg-black/40 rounded-md text-white placeholder-gray-400" placeholder="Enter new password (optional)" minlength="6">

                    </div>

                    <div>

                        <label class="block text-sm font-medium text-blue-300 mb-2">Confirm Password</label>

                        <input name="password_confirmation" type="password" class="w-full px-4 py-2 border border-gray-700 bg-black/40 rounded-md text-white placeholder-gray-400" placeholder="Confirm new password (optional)">

                    </div>

                </div>

                

                <button type="submit" class="px-6 py-3 bg-blue-600 hover:bg-blue-500 text-white font-semibold rounded-md transition-colors">

                    <i class="fas fa-save mr-2"></i>Update Password

                </button>

            </form>

        </div>



        <!-- Logout Section -->

        <div class="mt-6 text-center">

            <form method="POST" action="{{ route('logout') }}">

                @csrf

                <button type="submit" class="px-6 py-3 bg-red-600 hover:bg-red-500 text-white font-semibold rounded-md transition-colors">

                    <i class="fas fa-sign-out-alt mr-2"></i>Logout

                </button>

            </form>

        </div>

    </div>

</body>

</html>



