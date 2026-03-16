<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Sign In - Business System</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body class="bg-gradient-to-br from-black via-gray-900 to-red-900 min-h-screen text-white">
    <div class="min-h-screen flex items-center justify-center px-6">
        <div class="w-full max-w-5xl grid grid-cols-1 md:grid-cols-2 gap-8 items-center">
            <div class="px-4 md:px-0 text-center md:text-left">
                <a href="{{ url('/') }}" class="inline-block mb-4 text-3xl font-bold text-red-500">Manliquid Communication</a>
                <h2 class="text-4xl font-extrabold text-white mb-2">Welcome back</h2>
                <p class="text-gray-300">Sign in to access your dashboard and manage your business.</p>
            </div>

            <div class="mx-auto w-full max-w-md bg-white rounded-lg shadow-lg p-6">
                <!-- Success Message -->
                @if(session('success'))
                    <div class="mb-4 text-green-700 bg-green-100 p-3 rounded">{{ session('success') }}</div>
                @endif

                <!-- Error Messages -->
                @if($errors->any())
                    <div class="mb-4 text-red-700 bg-red-100 p-3 rounded">
                        <ul class="list-disc list-inside text-sm">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <!-- Session Message -->
                @if(session('message'))
                    <div class="mb-4 text-blue-700 bg-blue-100 p-3 rounded">{{ session('message') }}</div>
                @endif

                <form method="POST" action="http://127.0.0.1:8003/login" class="space-y-4 text-black">
                    @csrf
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Email Address</label>
                        <input type="email" 
                               name="email" 
                               value="{{ old('email') }}" 
                               required 
                               autofocus 
                               class="mt-1 w-full bg-white border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500"
                               placeholder="Enter your email">
                        @error('email')<div class="text-xs text-red-600 mt-1">{{ $message }}</div>@enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Password</label>
                        <input type="password" 
                               name="password" 
                               required 
                               class="mt-1 w-full bg-white border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500"
                               placeholder="Enter your password">
                        @error('password')<div class="text-xs text-red-600 mt-1">{{ $message }}</div>@enderror
                    </div>

                    <div class="flex items-center justify-between">
                        <label class="flex items-center gap-2 text-sm text-gray-700">
                            <input type="checkbox" name="remember" class="form-checkbox text-red-600 rounded"> 
                            Remember me
                        </label>
                    </div>

                    <div>
                        <button type="submit" 
                                class="w-full bg-red-600 hover:bg-red-700 text-white font-semibold py-2 rounded transition-colors">
                            Sign In
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</body>
</html>
