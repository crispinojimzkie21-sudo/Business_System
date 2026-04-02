<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" href="/favicon.svg" type="image/svg+xml">
    <title>Sign In - Business System</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        @keyframes pulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.05); }
        }
        
        @keyframes shimmer {
            0% { background-position: -200% 0; }
            100% { background-position: 200% 0; }
        }
        
        .fade-in-up {
            animation: fadeInUp 0.8s ease-out;
        }
        
        .pulse-animation {
            animation: pulse 2s infinite;
        }
        
        .shimmer-effect {
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.1), transparent);
            background-size: 200% 100%;
            animation: shimmer 3s infinite;
        }
        
        .glass-morphism {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
        }
        
        .modern-input {
            background: rgba(255, 255, 255, 0.9);
            border: 2px solid transparent;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
        }
        
        .modern-input:focus {
            background: rgba(255, 255, 255, 1);
            border-color: #ef4444;
            box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.1);
            transform: translateY(-2px);
        }
        
        .modern-button {
            background: linear-gradient(135deg, #ef4444, #dc2626);
            position: relative;
            overflow: hidden;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        
        .modern-button::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
            transition: left 0.5s;
        }
        
        .modern-button:hover::before {
            left: 100%;
        }
        
        .modern-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(239, 68, 68, 0.3);
        }
        
        .feature-card {
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(5px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            transition: all 0.3s ease;
        }
        
        .feature-card:hover {
            background: rgba(255, 255, 255, 0.1);
            transform: translateX(10px);
        }
        
        .floating-element {
            animation: float 6s ease-in-out infinite;
        }
        
        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-10px); }
        }
    </style>
</head>
<body class="bg-gradient-to-br from-black via-gray-900 to-red-900 min-h-screen text-white relative overflow-hidden">
    
    <!-- Background Elements -->
    <div class="absolute inset-0">
        <div class="absolute top-20 left-20 w-64 h-64 bg-red-500/10 rounded-full blur-3xl floating-element"></div>
        <div class="absolute bottom-20 right-20 w-96 h-96 bg-red-600/10 rounded-full blur-3xl floating-element" style="animation-delay: 3s;"></div>
        <div class="absolute top-1/2 left-1/2 w-80 h-80 bg-red-400/5 rounded-full blur-3xl floating-element" style="animation-delay: 1.5s;"></div>
    </div>

    <div class="relative z-10 min-h-screen flex items-center justify-center px-6">
        <div class="w-full max-w-6xl">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
                
                <!-- Left Side - Branding -->
                <div class="space-y-8 fade-in-up">
                    <!-- Logo Section -->
                    <div class="flex items-center space-x-4">
                        <div class="w-16 h-16 bg-red-500/20 backdrop-blur-lg rounded-2xl flex items-center justify-center shimmer-effect">
                            <i class="fas fa-store text-2xl text-red-400"></i>
                        </div>
                        <div>
                            <h1 class="text-4xl font-bold text-white">Manliquid</h1>
                            <p class="text-xl text-red-300">Communication</p>
                        </div>
                    </div>
                    
                    <!-- Welcome Message -->
                    <div class="space-y-4">
                        <h2 class="text-5xl font-extrabold text-white leading-tight">
                            Welcome <span class="text-transparent bg-clip-text bg-gradient-to-r from-red-400 to-red-600">Back</span>
                        </h2>
                        <p class="text-xl text-gray-300 leading-relaxed">
                            Sign in to access your dashboard and manage your business with our powerful management system.
                        </p>
                    </div>
                    
                    <!-- Features -->
                    <div class="space-y-4">
                        <div class="feature-card p-4 rounded-xl flex items-center space-x-4">
                            <div class="w-12 h-12 bg-red-500/20 backdrop-blur-lg rounded-xl flex items-center justify-center">
                                <i class="fas fa-shield-alt text-red-400 text-lg"></i>
                            </div>
                            <div>
                                <h3 class="text-white font-semibold">Secure Authentication</h3>
                                <p class="text-gray-400 text-sm">Advanced security protocols</p>
                            </div>
                        </div>
                        
                        <div class="feature-card p-4 rounded-xl flex items-center space-x-4">
                            <div class="w-12 h-12 bg-red-500/20 backdrop-blur-lg rounded-xl flex items-center justify-center">
                                <i class="fas fa-chart-line text-red-400 text-lg"></i>
                            </div>
                            <div>
                                <h3 class="text-white font-semibold">Advanced Analytics</h3>
                                <p class="text-gray-400 text-sm">Real-time data insights</p>
                            </div>
                        </div>
                        
                        <div class="feature-card p-4 rounded-xl flex items-center space-x-4">
                            <div class="w-12 h-12 bg-red-500/20 backdrop-blur-lg rounded-xl flex items-center justify-center">
                                <i class="fas fa-users text-red-400 text-lg"></i>
                            </div>
                            <div>
                                <h3 class="text-white font-semibold">Team Management</h3>
                                <p class="text-gray-400 text-sm">Efficient collaboration tools</p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Right Side - Login Form -->
                <div class="fade-in-up" style="animation-delay: 0.2s;">
                    <div class="glass-morphism rounded-3xl p-8 shadow-2xl">
                        <!-- Success Message -->
                        @if(session('success'))
                            <div class="mb-6 bg-green-500/20 backdrop-blur border border-green-500/30 text-green-300 px-4 py-3 rounded-xl flex items-center">
                                <i class="fas fa-check-circle mr-3 text-green-400"></i>
                                {{ session('success') }}
                            </div>
                        @endif

                        <!-- Error Messages -->
                        @if($errors->any())
                            <div class="mb-6 bg-red-500/20 backdrop-blur border border-red-500/30 text-red-300 px-4 py-3 rounded-xl">
                                <div class="flex items-start">
                                    <i class="fas fa-exclamation-triangle mr-3 text-red-400 mt-1"></i>
                                    <div>
                                        <p class="font-semibold">Login Failed</p>
                                        <ul class="list-disc list-inside text-sm mt-1">
                                            @foreach($errors->all() as $error)
                                                <li>{{ $error }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        @endif

                        <!-- Session Message -->
                        @if(session('message'))
                            <div class="mb-6 bg-blue-500/20 backdrop-blur border border-blue-500/30 text-blue-300 px-4 py-3 rounded-xl flex items-center">
                                <i class="fas fa-info-circle mr-3 text-blue-400"></i>
                                {{ session('message') }}
                            </div>
                        @endif

                        <form method="POST" action="{{ route('login') }}" class="space-y-6">
                            @csrf
                            
                            <!-- Email Field -->
                            <div>
                                <label class="block text-sm font-semibold text-gray-200 mb-2">
                                    <i class="fas fa-envelope mr-2 text-red-400"></i>Email Address
                                </label>
                                <div class="relative">
                                    <input type="email" 
                                           name="email" 
                                           value="{{ old('email') }}" 
                                           required 
                                           autofocus 
                                           class="w-full px-4 py-3 pl-12 modern-input rounded-xl text-gray-900 placeholder-gray-500 focus:outline-none"
                                           placeholder="Enter your email">
                                    <i class="fas fa-envelope absolute left-4 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                                </div>
                                @error('email')<div class="mt-2 text-xs text-red-400 flex items-center"><i class="fas fa-exclamation-triangle mr-1"></i>{{ $message }}</div>@enderror
                            </div>

                            <!-- Password Field -->
                            <div>
                                <label class="block text-sm font-semibold text-gray-200 mb-2">
                                    <i class="fas fa-lock mr-2 text-red-400"></i>Password
                                </label>
                                <div class="relative">
                                    <input type="password" 
                                           name="password" 
                                           required 
                                           class="w-full px-4 py-3 pl-12 modern-input rounded-xl text-gray-900 placeholder-gray-500 focus:outline-none"
                                           placeholder="Enter your password">
                                    <i class="fas fa-lock absolute left-4 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                                </div>
                                @error('password')<div class="mt-2 text-xs text-red-400 flex items-center"><i class="fas fa-exclamation-triangle mr-1"></i>{{ $message }}</div>@enderror
                            </div>

                            <!-- Remember Me & Forgot Password -->
                            <div class="flex items-center justify-between">
                                <label class="flex items-center space-x-2 text-sm text-gray-300 cursor-pointer hover:text-red-400 transition-colors">
                                    <input type="checkbox" name="remember" class="w-4 h-4 text-red-500 border-gray-300 rounded focus:ring-red-500">
                                    <span>Remember me</span>
                                </label>
                                <a href="#" class="text-sm text-red-400 hover:text-red-300 font-medium transition-colors">
                                    Forgot password?
                                </a>
                            </div>

                            <!-- Submit Button -->
                            <button type="submit" class="w-full modern-button text-white font-bold py-4 px-4 rounded-xl shadow-lg flex items-center justify-center space-x-2">
                                <i class="fas fa-sign-in-alt"></i>
                                <span>Sign In</span>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            
            <!-- Footer -->
            <div class="text-center mt-12 fade-in-up" style="animation-delay: 0.4s;">
                <p class="text-gray-400 text-sm">
                    2026 Manliquid Communication. All rights reserved.
                </p>
            </div>
        </div>
    </div>
</body>
</html>
