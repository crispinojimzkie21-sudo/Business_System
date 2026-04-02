<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="/favicon.svg" type="image/svg+xml">
    <title>Staff Portal - Business System</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-20px); }
        }
        .float-animation {
            animation: float 3s ease-in-out infinite;
        }
        .glow {
            box-shadow: 0 0 20px rgba(59, 130, 246, 0.5);
        }
        .glass-effect {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
    </style>
</head>
<body class="bg-gradient-to-br from-black via-gray-900 to-blue-900 min-h-screen flex items-center justify-center relative overflow-hidden">
    <!-- Animated Background Elements -->
    <div class="absolute inset-0">
        <div class="absolute top-20 left-20 w-72 h-72 bg-blue-500 rounded-full mix-blend-multiply filter blur-xl opacity-20 float-animation"></div>
        <div class="absolute top-40 right-20 w-72 h-72 bg-purple-500 rounded-full mix-blend-multiply filter blur-xl opacity-20 float-animation" style="animation-delay: 2s;"></div>
        <div class="absolute bottom-20 left-1/2 w-72 h-72 bg-pink-500 rounded-full mix-blend-multiply filter blur-xl opacity-20 float-animation" style="animation-delay: 4s;"></div>
    </div>

    <!-- Main Login Container -->
    <div class="relative z-10 w-full max-w-md mx-4">
        <div class="glass-effect rounded-3xl p-8 glow">
            <!-- Logo and Header -->
            <div class="text-center mb-8">
                <div class="w-20 h-20 bg-gradient-to-br from-blue-500 to-purple-600 rounded-2xl flex items-center justify-center mx-auto mb-4 shadow-2xl transform hover:scale-105 transition-transform">
                    <i class="fas fa-user-shield text-white text-3xl"></i>
                </div>
                <h1 class="text-4xl font-bold text-white mb-2 bg-gradient-to-r from-blue-400 to-purple-400 bg-clip-text text-transparent">
                    Staff Portal
                </h1>
                <p class="text-blue-200 text-sm">Admin & Cashier Access</p>
                <div class="mt-3 flex justify-center space-x-2">
                    <span class="px-3 py-1 bg-green-500/20 text-green-400 text-xs rounded-full border border-green-500/30">
                        <i class="fas fa-shield-alt mr-1"></i>Secure
                    </span>
                    <span class="px-3 py-1 bg-blue-500/20 text-blue-400 text-xs rounded-full border border-blue-500/30">
                        <i class="fas fa-lock mr-1"></i>Private
                    </span>
                </div>
            </div>

            <!-- Error/Success Messages -->
            @if(session('error'))
                <div class="bg-red-500/20 border border-red-500/50 text-red-200 px-4 py-3 rounded-lg mb-6 flex items-center">
                    <i class="fas fa-exclamation-triangle mr-2"></i>
                    {{ session('error') }}
                </div>
            @endif

            @if(session('success'))
                <div class="bg-green-500/20 border border-green-500/50 text-green-200 px-4 py-3 rounded-lg mb-6 flex items-center">
                    <i class="fas fa-check-circle mr-2"></i>
                    {{ session('success') }}
                </div>
            @endif

            <!-- Login Form -->
            <form method="POST" action="{{ route('admin.login.post') }}" id="adminLoginForm" class="space-y-6">
                @csrf
                
                <!-- Email Field -->
                <div class="relative">
                    <label class="block text-blue-200 text-sm font-medium mb-2">
                        <i class="fas fa-envelope mr-1"></i>Email Address
                    </label>
                    <div class="relative">
                        <input type="email" name="email" required
                            class="w-full px-4 py-3 pl-12 bg-white/10 border border-white/20 rounded-xl text-white placeholder-blue-300 focus:outline-none focus:border-blue-400 focus:bg-white/20 transition-all"
                            placeholder="admin@company.com" value="{{ old('email') }}">
                        <i class="fas fa-envelope absolute left-4 top-1/2 transform -translate-y-1/2 text-blue-400"></i>
                    </div>
                </div>

                <!-- Password Field -->
                <div class="relative">
                    <label class="block text-blue-200 text-sm font-medium mb-2">
                        <i class="fas fa-lock mr-1"></i>Password
                    </label>
                    <div class="relative">
                        <input type="password" name="password" required id="password"
                            class="w-full px-4 py-3 pl-12 pr-12 bg-white/10 border border-white/20 rounded-xl text-white placeholder-blue-300 focus:outline-none focus:border-blue-400 focus:bg-white/20 transition-all"
                            placeholder="Enter your password">
                        <i class="fas fa-lock absolute left-4 top-1/2 transform -translate-y-1/2 text-blue-400"></i>
                        <button type="button" id="togglePassword" class="absolute right-4 top-1/2 transform -translate-y-1/2 text-blue-400 hover:text-white transition-colors">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                </div>

                <!-- Remember Me -->
                <div class="flex items-center justify-between">
                    <label class="flex items-center text-blue-200 text-sm">
                        <input type="checkbox" name="remember" class="mr-2 rounded border-blue-400 bg-white/10 text-blue-500 focus:ring-blue-400 focus:ring-offset-0">
                        <span>Remember me</span>
                    </label>
                </div>

                <!-- Submit Button -->
                <button type="submit" class="w-full bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 text-white font-semibold py-3 px-4 rounded-xl transition-all transform hover:scale-105 shadow-lg">
                    <i class="fas fa-sign-in-alt mr-2"></i>
                    Sign In to Staff Portal
                </button>
            </form>

            <!-- Footer Links -->
            <div class="mt-8 text-center space-y-3">
                <div class="flex justify-center space-x-4 text-sm">
                    <a href="{{ route('login') }}" class="text-blue-300 hover:text-white transition-colors flex items-center">
                        <i class="fas fa-arrow-left mr-1"></i>
                        Back to Login
                    </a>
                </div>
                <div class="text-xs text-blue-400/60">
                    <i class="fas fa-shield-alt mr-1"></i>
                    Secure authentication required
                </div>
            </div>
        </div>

        <!-- System Info -->
        <div class="text-center mt-6 text-xs text-blue-400/40">
            <p>Business System Staff Portal v2.0</p>
            <p class="mt-1">© 2026 All rights reserved</p>
        </div>
    </div>

    <script>
        // Password visibility toggle
        document.getElementById('togglePassword').addEventListener('click', function() {
            const passwordInput = document.getElementById('password');
            const icon = this.querySelector('i');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                passwordInput.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        });

        // Form submission animation
        document.getElementById('adminLoginForm').addEventListener('submit', function() {
            const button = this.querySelector('button[type="submit"]');
            button.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Signing in...';
            button.disabled = true;
        });
    </script>
</body>
</html>
