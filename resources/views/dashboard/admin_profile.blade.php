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
<body class="bg-gradient-to-br from-slate-900 via-green-900 to-slate-900 min-h-screen text-white">
    <div class="max-w-4xl mx-auto p-6">
        <!-- Header -->
        <header class="flex items-center justify-between mb-8">
            <div class="flex items-center gap-4">
                <div class="w-14 h-14 bg-gradient-to-br from-green-400 to-emerald-600 rounded-xl flex items-center justify-center text-2xl font-bold">
                    <i class="fas fa-user-shield"></i>
                </div>
                <div>
                    <h1 class="text-3xl font-bold text-white">Admin Profile</h1>
                    <p class="text-green-300 text-sm">Manage your admin account</p>
                </div>
            </div>
            <div class="flex items-center gap-4">
                <a href="{{ route('dashboard.admin') }}" class="px-4 py-2 text-green-200 hover:bg-green-900/30 rounded-lg">
                    <i class="fas fa-arrow-left mr-2"></i>Back to Admin Dashboard
                </a>
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
        <div class="glass-card p-6 rounded-xl border border-green-500/30 mb-6">
            <h2 class="text-xl font-semibold mb-4 text-green-400">
                <i class="fas fa-id-badge mr-2"></i>Admin Profile Information
            </h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <h3 class="font-semibold text-green-300 mb-3">Personal Details</h3>
                    <div class="space-y-3 text-sm">
                        <div class="flex justify-between">
                            <span class="text-green-200">Name:</span>
                            <span class="font-medium">{{ Auth::user()->name }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-green-200">Email:</span>
                            <span class="font-medium">{{ Auth::user()->email }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-green-200">Position:</span>
                            <span class="font-medium">{{ Auth::user()->position ?? 'Admin' }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-green-200">Department:</span>
                            <span class="font-medium">{{ Auth::user()->department ?? 'Administration' }}</span>
                        </div>
                    </div>
                </div>
                <div>
                    <h3 class="font-semibold text-green-300 mb-3">Admin Account Details</h3>
                    <div class="space-y-3 text-sm">
                        <div class="flex justify-between">
                            <span class="text-green-200">Role:</span>
                            <span class="font-medium text-green-400">Admin</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-green-200">Account Type:</span>
                            <span class="font-medium">Administrator</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-green-200">Member Since:</span>
                            <span class="font-medium">{{ Auth::user()->created_at->format('M d, Y') }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-green-200">Monthly Salary:</span>
                            <span class="font-medium">₱{{ number_format(Auth::user()->salary ?? 0, 2) }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Change Password -->
        <div class="glass-card p-6 rounded-xl border border-green-500/30 mb-6">
            <h2 class="text-xl font-semibold mb-4 text-green-400">
                <i class="fas fa-key mr-2"></i>Change Admin Password
            </h2>
            <p class="text-sm text-green-200 mb-6">Update your admin account password for security.</p>
            
            <form method="POST" action="{{ route('profile.update-password') }}" class="space-y-4">
                @csrf
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-green-300 mb-2">New Password</label>
                        <input name="password" type="password" class="w-full px-4 py-3 border border-gray-700 bg-black/40 rounded-lg text-white placeholder-gray-400 focus:border-green-500 focus:ring-1 focus:ring-green-500" placeholder="Enter new password (min 6 chars)" required minlength="6">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-green-300 mb-2">Confirm Password</label>
                        <input name="password_confirmation" type="password" class="w-full px-4 py-3 border border-gray-700 bg-black/40 rounded-lg text-white placeholder-gray-400 focus:border-green-500 focus:ring-1 focus:ring-green-500" placeholder="Confirm new password" required>
                    </div>
                </div>
                
                <button type="submit" class="px-8 py-3 bg-green-600 hover:bg-green-500 text-white font-semibold rounded-lg transition-colors shadow-lg hover:shadow-green-500/25">
                    <i class="fas fa-save mr-2"></i>Update Admin Password
                </button>
            </form>
        </div>

        <!-- Admin Actions -->
        <div class="glass-card p-6 rounded-xl border border-green-500/30">
            <h2 class="text-xl font-semibold mb-4 text-green-400">
                <i class="fas fa-cogs mr-2"></i>Admin Actions
            </h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <a href="{{ route('admin.list') }}" class="p-6 bg-green-900/50 border border-green-700 rounded-lg text-center hover:bg-green-800/50 transition-all">
                    <i class="fas fa-users text-2xl mb-2 text-green-400 block"></i>
                    <p class="font-semibold text-green-300">Manage Admins</p>
                </a>
                <a href="{{ route('employee.list') }}" class="p-6 bg-green-900/50 border border-green-700 rounded-lg text-center hover:bg-green-800/50 transition-all">
                    <i class="fas fa-user-tie text-2xl mb-2 text-green-400 block"></i>
                    <p class="font-semibold text-green-300">Manage Employees</p>
                </a>
                <a href="{{ route('dashboard.admin') }}" class="p-6 bg-gray-900/50 border border-gray-700 rounded-lg text-center hover:bg-gray-800/50 transition-all">
                    <i class="fas fa-chart-bar text-2xl mb-2 text-green-400 block"></i>
                    <p class="font-semibold text-green-300">Sales Dashboard</p>
                </a>
            </div>
        </div>

        <!-- Logout Section -->
        <div class="mt-8 text-center">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="px-8 py-3 bg-red-600 hover:bg-red-500 text-white font-semibold rounded-lg transition-colors shadow-lg hover:shadow-red-500/25">
                    <i class="fas fa-sign-out-alt mr-2"></i>Logout Admin Account
                </button>
            </form>
        </div>
    </div>
</body>
</html>

