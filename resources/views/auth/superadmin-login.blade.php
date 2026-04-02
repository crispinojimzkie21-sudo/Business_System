<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="/favicon.svg" type="image/svg+xml">
    <title> Admin Login - Business System</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap');
        
        body {
            font-family: 'Inter', sans-serif;
        }
        
        .animated-gradient {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 25%, #f093fb 50%, #f5576c 75%, #4facfe 100%);
            background-size: 400% 400%;
            animation: gradientShift 15s ease infinite;
        }
        
        @keyframes gradientShift {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }
        
        .glass-morphism {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.37);
        }
        
        .floating-shapes {
            position: absolute;
            width: 100%;
            height: 100%;
            overflow: hidden;
            z-index: 0;
        }
        
        .shape {
            position: absolute;
            opacity: 0.1;
            animation: float 20s infinite ease-in-out;
        }
        
        .shape-1 {
            width: 300px;
            height: 300px;
            background: linear-gradient(45deg, #f093fb, #f5576c);
            border-radius: 50%;
            top: -150px;
            right: -150px;
            animation-delay: 0s;
        }
        
        .shape-2 {
            width: 200px;
            height: 200px;
            background: linear-gradient(45deg, #4facfe, #667eea);
            border-radius: 30% 70% 70% 30% / 30% 30% 70% 70%;
            bottom: -100px;
            left: -100px;
            animation-delay: 5s;
        }
        
        .shape-3 {
            width: 150px;
            height: 150px;
            background: linear-gradient(45deg, #764ba2, #ca3636);
            border-radius: 50%;
            top: 50%;
            left: -75px;
            animation-delay: 10s;
        }
        
        @keyframes float {
            0%, 100% { transform: translateY(0px) rotate(0deg); }
            25% { transform: translateY(-20px) rotate(90deg); }
            50% { transform: translateY(10px) rotate(180deg); }
            75% { transform: translateY(-10px) rotate(270deg); }
        }
        
        .input-focus {
            transition: all 0.3s ease;
        }
        
        .input-focus:focus {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(79, 70, 229, 0.15);
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }
        
        .btn-primary::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
            transition: left 0.5s;
        }
        
        .btn-primary:hover::before {
            left: 100%;
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 15px 35px rgba(102, 126, 234, 0.3);
        }
        
        .security-badge {
            background: linear-gradient(135deg, #244b79 0%, #7b4c52 100%);
            animation: pulse 2s infinite;
        }
        
        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.8; }
        }
    </style>
</head>
<body class="animated-gradient min-h-screen flex items-center justify-center relative">
    <!-- Floating Background Shapes -->
    <div class="floating-shapes">
        <div class="shape shape-1"></div>
        <div class="shape shape-2"></div>
        <div class="shape shape-3"></div>
    </div>
    
    <!-- Main Login Container -->
    <div class="glass-morphism rounded-3xl p-10 w-full max-w-md relative z-10">
        <!-- Header Section -->
        <div class="text-center mb-8">
            <div class="w-20 h-20 bg-gradient-to-r from-indigo-500 via-purple-500 to-pink-500 rounded-2xl flex items-center justify-center mx-auto mb-6 shadow-2xl transform hover:scale-110 transition-transform duration-300">
                <i class="fas fa-crown text-white text-3xl"></i>
            </div>
            <h1 class="text-4xl font-bold text-gray-800 mb-2 tracking-tight">Admin</h1>
            <p class="text-gray-600 font-medium">Ultimate System Access Portal</p>
        </div>

        <!-- Security Warning -->
        <div class="security-badge text-white px-4 py-3 rounded-xl mb-6 text-sm font-medium shadow-lg">
            <i class="fas fa-shield-alt mr-2"></i>
            Authorized Personnel Only
        </div>

        <!-- Flash Messages -->
        @if(session('error'))
            <div class="bg-red-50 border-l-4 border-red-500 text-red-700 px-4 py-3 rounded-lg mb-4 text-sm font-medium">
                <i class="fas fa-exclamation-circle mr-2"></i>
                {{ session('error') }}
            </div>
        @endif

        @if(session('success'))
            <div class="bg-green-50 border-l-4 border-green-500 text-green-700 px-4 py-3 rounded-lg mb-4 text-sm font-medium">
                <i class="fas fa-check-circle mr-2"></i>
                {{ session('success') }}
            </div>
        @endif

        <!-- Login Form -->
        <form method="POST" action="{{ route('superadmin.login.post') }}" id="superAdminLoginForm" class="space-y-6">
            @csrf
            
            <!-- Email Field -->
            <div>
                <label class="block text-gray-700 text-sm font-semibold mb-3">
                    <i class="fas fa-envelope mr-2 text-indigo-500"></i> Admin Email
                </label>
                <input type="email" name="email" required id="superAdminEmail"
                    class="w-full px-4 py-4 bg-white border border-gray-200 rounded-xl text-gray-800 placeholder-gray-400 focus:outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 input-focus"
                    placeholder="superadmin@company.com" value="{{ old('email') }}" autocomplete="email">
                @error('email')
                    <p class="mt-2 text-red-500 text-xs font-medium">{{ $message }}</p>
                @enderror
            </div>

            <!-- Password Field -->
            <div>
                <label class="block text-gray-700 text-sm font-semibold mb-3">
                    <i class="fas fa-key mr-2 text-indigo-500"></i>Master Password
                </label>
                <div class="relative">
                    <input type="password" name="password" required id="superAdminPassword"
                        class="w-full px-4 py-4 bg-white border border-gray-200 rounded-xl text-gray-800 placeholder-gray-400 focus:outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 input-focus"
                        placeholder="Enter master password" autocomplete="current-password">
                    <button type="button" onclick="togglePassword('superAdminPassword', this)" 
                        class="absolute right-4 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-indigo-500 transition-colors">
                        <i class="fas fa-eye"></i>
                    </button>
                </div>
                @error('password')
                    <p class="mt-2 text-red-500 text-xs font-medium">{{ $message }}</p>
                @enderror
            </div>

            <!-- Remember Me -->
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <input id="remember" name="remember" type="checkbox" 
                        class="h-5 w-5 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                    <label for="remember" class="ml-3 block text-sm text-gray-700 font-medium">Remember session</label>
                </div>
            </div>

            <!-- Submit Button -->
            <button type="submit" class="btn-primary w-full text-white font-semibold py-4 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500/50">
                <i class="fas fa-crown mr-2"></i>
                Access Super Admin Panel
            </button>
        </form>

        <!-- Navigation Links -->
        <div class="mt-8 text-center space-y-3">
            <a href="{{ route('admin.login') }}" class="text-gray-600 hover:text-indigo-600 text-sm font-medium block transition-colors">
                <i class="fas fa-user-shield mr-2"></i>Assistant Portal
            </a>
            <a href="{{ route('login') }}" class="text-gray-600 hover:text-indigo-600 text-sm font-medium block transition-colors">
                <i class="fas fa-arrow-left mr-2"></i>Regular Login
            </a>
        </div>

        <!-- Footer -->
        <div class="mt-6 pt-6 border-t border-gray-200">
            <p class="text-xs text-gray-500 text-center font-medium">
                <i class="fas fa-lock mr-1"></i>
                Maximum Security Portal • System Administrators Only
            </p>
        </div>
    </div>

    <script>
        function togglePassword(inputId, button) {
            const input = document.getElementById(inputId);
            const icon = button.querySelector('i');
            
            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                input.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        }
        
        // Enhanced form validation and submission
        document.getElementById('superAdminLoginForm').addEventListener('submit', function(e) {
            const email = document.getElementById('superAdminEmail').value;
            const password = document.getElementById('superAdminPassword').value;
            
            // Basic client-side validation
            if (!email || !password) {
                e.preventDefault();
                alert('Please fill in all required fields.');
                return false;
            }
            
            if (password.length < 8) {
                e.preventDefault();
                alert('Password must be at least 8 characters long.');
                return false;
            }
            
            // Email format validation
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(email)) {
                e.preventDefault();
                alert('Please enter a valid email address.');
                return false;
            }
            
            // Show loading state
            const submitBtn = e.target.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Logging in...';
            submitBtn.disabled = true;
            
            // Re-enable button after 5 seconds in case of network issues
            setTimeout(() => {
                submitBtn.innerHTML = originalText;
                submitBtn.disabled = false;
            }, 5000);
        });
        
        // Clear any existing session data on page load
        window.addEventListener('load', function() {
            // Clear any old form data
            document.getElementById('superAdminEmail').value = '';
            document.getElementById('superAdminPassword').value = '';
            document.getElementById('remember').checked = false;
            
            // Focus on email field
            document.getElementById('superAdminEmail').focus();
            
            // Auto-clear success/error messages after 5 seconds
            @if(session('success') || session('error'))
                setTimeout(() => {
                    const successMsg = document.querySelector('.bg-green-50');
                    const errorMsg = document.querySelector('.bg-red-50');
                    if (successMsg) successMsg.style.display = 'none';
                    if (errorMsg) errorMsg.style.display = 'none';
                }, 5000);
            @endif
        });
        
        // Handle keyboard shortcuts
        document.addEventListener('keydown', function(e) {
            // Enter key to submit form
            if (e.key === 'Enter' && !e.shiftKey) {
                document.getElementById('superAdminLoginForm').dispatchEvent(new Event('submit'));
            }
        });
        
        // Auto-refresh token every 25 minutes to prevent 419 errors
        setInterval(() => {
            fetch('{{ route('superadmin.login') }}')
                .then(() => {
                    console.log('CSRF token refreshed');
                })
                .catch(() => {
                    console.log('Could not refresh CSRF token');
                });
        }, 25 * 60 * 1000); // 25 minutes
    </script>
</body>
</html>
