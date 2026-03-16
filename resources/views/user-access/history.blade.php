<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Access History - {{ config('app.name') }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gradient-to-br from-black via-gray-900 to-purple-900 min-h-screen text-white">
    <div class="max-w-6xl mx-auto p-6">
        <!-- Header -->
        <header class="flex items-center justify-between mb-8">
            <div>
                <h1 class="text-3xl font-bold text-purple-400">📋 Access History</h1>
                <p class="text-purple-200 text-sm mt-1">Access control history for {{ $user->name }}</p>
            </div>
            <div class="flex items-center gap-4">
                <a href="{{ route('user-access.index') }}" class="px-4 py-2 text-purple-200 hover:bg-purple-900/30 rounded">← Back to Access Control</a>
                <a href="{{ url('/dashboard/super-admin') }}" class="px-4 py-2 text-purple-200 hover:bg-purple-900/30 rounded">Dashboard</a>
            </div>
        </header>

        <!-- User Info Card -->
        <div class="bg-black/60 rounded-lg border border-purple-900/30 p-6 mb-8">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <div class="w-16 h-16 bg-purple-600 rounded-full flex items-center justify-center text-2xl font-bold">
                        {{ strtoupper(substr($user->name, 0, 1)) }}
                    </div>
                    <div>
                        <h2 class="text-xl font-bold text-white">{{ $user->name }}</h2>
                        <p class="text-purple-300">{{ $user->email }}</p>
                        <div class="flex items-center gap-4 mt-2">
                            <span class="px-3 py-1 text-sm rounded-full 
                                @if($user->role === 'admin') bg-blue-900/50 text-blue-300
                                @elseif($user->role === 'employee') bg-green-900/50 text-green-300
                                @endif">
                                {{ ucfirst($user->role) }}
                            </span>
                            @if($user->access_status === 'enabled')
                                <span class="flex items-center gap-2 text-green-400">
                                    <span class="w-2 h-2 bg-green-400 rounded-full"></span>
                                    Access Enabled
                                </span>
                            @else
                                <span class="flex items-center gap-2 text-red-400">
                                    <span class="w-2 h-2 bg-red-400 rounded-full"></span>
                                    Access Disabled
                                </span>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="text-right">
                    @if($user->access_restriction_reason)
                        <p class="text-sm text-purple-300 mb-2">Current Reason:</p>
                        <p class="text-red-300">{{ $user->access_restriction_reason }}</p>
                    @endif
                </div>
            </div>
        </div>

        <!-- Access History Logs -->
        <div class="bg-black/60 rounded-lg border border-purple-900/30 overflow-hidden">
            <div class="p-6">
                <h3 class="text-lg font-semibold text-purple-400 mb-4">Access Control Logs</h3>
                
                @if(count($logs) > 0)
                    <div class="space-y-4">
                        @foreach($logs as $log)
                            <div class="bg-purple-900/20 rounded-lg p-4 border border-purple-900/30">
                                <div class="flex items-start justify-between">
                                    <div class="flex-1">
                                        <div class="flex items-center gap-2 mb-2">
                                            @if(strpos($log, 'enabled') !== false)
                                                <span class="w-2 h-2 bg-green-400 rounded-full"></span>
                                                <span class="text-green-400 font-medium">Access Enabled</span>
                                            @else
                                                <span class="w-2 h-2 bg-red-400 rounded-full"></span>
                                                <span class="text-red-400 font-medium">Access Disabled</span>
                                            @endif
                                        </div>
                                        <p class="text-purple-200 text-sm font-mono whitespace-pre-wrap">{{ $log }}</p>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-8">
                        <p class="text-purple-300">No access history logs found for this user.</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Current Status Details -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-8">
            <div class="bg-black/60 rounded-lg border border-purple-900/30 p-6">
                <h3 class="text-lg font-semibold text-purple-400 mb-4">Current Status</h3>
                <div class="space-y-3">
                    <div class="flex justify-between">
                        <span class="text-purple-300">Access Status:</span>
                        <span class="font-medium 
                            @if($user->access_status === 'enabled') text-green-400
                            @else text-red-400 @endif">
                            {{ ucfirst($user->access_status) }}
                        </span>
                    </div>
                    @if($user->access_enabled_at)
                        <div class="flex justify-between">
                            <span class="text-purple-300">Last Enabled:</span>
                            <span class="text-white">{{ $user->access_enabled_at->format('M d, Y H:i:s') }}</span>
                        </div>
                    @endif
                    @if($user->access_disabled_at)
                        <div class="flex justify-between">
                            <span class="text-purple-300">Last Disabled:</span>
                            <span class="text-white">{{ $user->access_disabled_at->format('M d, Y H:i:s') }}</span>
                        </div>
                    @endif
                </div>
            </div>

            <div class="bg-black/60 rounded-lg border border-purple-900/30 p-6">
                <h3 class="text-lg font-semibold text-purple-400 mb-4">Quick Actions</h3>
                <div class="space-y-3">
                    @if($user->access_status === 'enabled')
                        <button onclick="window.location.href='/user-access/disable/{{ $user->id }}'" 
                                class="w-full px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded transition-colors">
                            Disable Access
                        </button>
                    @else
                        <button onclick="window.location.href='/user-access/enable/{{ $user->id }}'" 
                                class="w-full px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded transition-colors">
                            Enable Access
                        </button>
                    @endif
                    <a href="{{ route('user-access.index') }}" 
                       class="block w-full px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white text-center rounded transition-colors">
                        Back to Access Control
                    </a>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Set correct form action URLs
        document.addEventListener('DOMContentLoaded', function() {
            const currentHost = window.location.protocol + '//' + window.location.host;
            
            // Fix all links with user-access routes
            const links = document.querySelectorAll('a[href*="/user-access/"]');
            links.forEach(link => {
                link.href = currentHost + link.getAttribute('href');
            });
        });
    </script>
</body>
</html>
