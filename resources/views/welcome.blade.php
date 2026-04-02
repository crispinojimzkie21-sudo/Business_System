<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <link rel="icon" href="/favicon.svg" type="image/svg+xml">
    <title>RM Manliquid</title>

    <!-- Tailwind CDN for quick styling -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        brand: {
                            DEFAULT: '#0b0b0b',
                            accent: '#dc2626'
                        }
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-gradient-to-br from-black via-gray-900 to-red-900 min-h-screen text-white">
    <header class="py-6">
            <div class="max-w-6xl mx-auto px-6 flex items-center justify-between">

            <!-- Logo Section -->
            <div class="flex items-center space-x-4">
                <div class="w-16 h-16 bg-red-500/20 backdrop-blur-lg rounded-2xl flex items-center justify-center">
                    <span class="text-2xl font-bold text-red-400">RM</span>
                </div>
                <div>
                    <h1 class="text-3xl font-bold text-white">RM Manliquid</h1>
                    <p class="text-lg text-red-300">Business System</p>
                </div>
            </div>

            <nav class="flex items-center gap-3">
                @if (Route::has('login'))
                    @auth
                        <a href="{{ url('/dashboard') }}" class="px-4 py-2 bg-black/70 text-red-300 rounded shadow">Dashboard</a>
                        <form method="POST" id="logoutForm" class="inline ml-2">
                            @csrf
                            <button type="submit" class="px-4 py-2 text-red-200 rounded border border-red-700 hover:bg-red-700/10">Logout</button>
                        </form>
                    @else
                        <a href="{{ route('login') }}" class="px-4 py-2 text-red-200 rounded border border-red-700 hover:bg-red-700/10">Sign in</a>
                    @endauth
                @endif
            </nav>
        </div>
    </header>

    <main class="max-w-6xl mx-auto px-6">
        <!-- Session Messages -->
        @if(session('message'))
            <div class="mb-4 bg-blue-900/50 border border-blue-700 rounded-md p-4">
                <p class="text-blue-200">{{ session('message') }}</p>
            </div>
        @endif

        <section class="grid lg:grid-cols-2 gap-10 items-center py-12">
            <div>
                <h2 class="text-4xl font-extrabold text-white mb-4">Manage your business with confidence</h2>
                <p class="text-red-200 mb-6">Centralize employees and reports — fast, secure and easy to use. Built for growing businesses.</p>

                <div class="flex gap-3">
                        @if (Route::has('login'))
                            @auth
                                <a href="{{ url('/dashboard') }}" class="inline-block px-6 py-3 bg-black/70 text-red-300 rounded-md shadow">Go to Dashboard</a>
                            @else
                                <a href="{{ route('login') }}" class="inline-block px-6 py-3 bg-red-600 text-white rounded-md shadow">Sign in</a>
                            @endauth
                        @endif
                </div>

                <div class="mt-10 grid sm:grid-cols-3 gap-6">
                    <div class="bg-black/60 rounded-lg p-4 shadow-sm">
                        <h4 class="font-semibold text-white">Accounts & Reports</h4>
                        <p class="text-sm text-red-200 mt-1">Financial summaries, exportable reports and dashboards.</p>
                    </div>
                    <div class="bg-black/60 rounded-lg p-4 shadow-sm">
                        <h4 class="font-semibold text-white">Inventory & Sales</h4>
                        <p class="text-sm text-red-200 mt-1">Track stock, sales and purchase orders in one place.</p>
                    </div>
                    <div class="bg-black/60 rounded-lg p-4 shadow-sm">
                        <h4 class="font-semibold text-white">Users & Roles</h4>
                        <p class="text-sm text-red-200 mt-1">Role-based access control for your team.</p>
                    </div>
                </div>
            </div>

            <div class="order-first lg:order-last">
                <div class="bg-black/70 rounded-2xl p-8 shadow-xl">
                    <div class="text-center">
                        <h3 class="text-xl font-semibold mb-2">Start your free trial</h3>
                        <p class="text-sm text-red-200 mb-4">No credit card required. Set up in minutes.</p>
                    </div>

                    <form method="POST" id="contactForm" class="space-y-3">
                        @csrf
                        @if(session('success'))
                            <div class="mb-4 bg-green-900/50 border border-green-700 rounded-md p-4">
                                <p class="text-green-200">{{ session('success') }}</p>
                            </div>
                        @endif
                        <input name="company" placeholder="Company name" class="w-full px-4 py-3 border border-gray-700 bg-black/40 rounded-md text-white placeholder-gray-400" required />
                        <input name="email" placeholder="Work email" class="w-full px-4 py-3 border border-gray-700 bg-black/40 rounded-md text-white placeholder-gray-400" required />
                        <button type="submit" class="w-full px-4 py-3 bg-red-600 hover:bg-red-700 text-white font-semibold rounded-md transition-colors">Submit</button>
                    </form>
                </div>
            </div>
        </section>
    </main>

    <footer class="mt-12 py-8 text-center text-sm text-slate-500">&copy; {{ date('Y') }} RM Manliquid Business System</footer>
    
    <script>
        // Set the correct form action URLs
        document.addEventListener('DOMContentLoaded', function() {
            const currentHost = window.location.protocol + '//' + window.location.host;
            
            // Fix contact form
            const contactForm = document.getElementById('contactForm');
            if (contactForm) {
                contactForm.action = currentHost + '/contact';
                console.log('Contact form action set to:', contactForm.action);
            }
            
            // Fix logout form
            const logoutForm = document.getElementById('logoutForm');
            if (logoutForm) {
                logoutForm.action = currentHost + '/logout';
                console.log('Logout form action set to:', logoutForm.action);
            }
        });
    </script>
</body>
</html>
