<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Access Control - {{ config('app.name') }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gradient-to-br from-black via-gray-900 to-blue-900 min-h-screen text-white">
    <div class="max-w-6xl mx-auto p-6">
        <!-- Header -->
        <header class="flex items-center justify-between mb-8">
            <div>
                <h1 class="text-3xl font-bold text-green-400">🔐Admin Access Control</h1>
                <p class="text-blue-200 text-sm mt-1">Manage access for all user accounts in the system</p>
            </div>
            <div class="flex gap-4">
                <a href="{{ route('dashboard.superadmin') }}" class="px-4 py-2 bg-gray-700 hover:bg-gray-600 text-white rounded-md transition-colors">
                    ← Dashboard
                </a>
            </div>
        </header>

        @if (session('success'))
            <div class="mb-6 bg-green-900/50 border border-green-700 rounded-md p-4">
                <p class="text-green-200">{{ session('success') }}</p>
            </div>
        @endif

        @if (session('error'))
            <div class="mb-6 bg-red-900/50 border border-red-700 rounded-md p-4">
                <p class="text-red-200">{{ session('error') }}</p>
            </div>
        @endif

        <!-- Stats -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">
            <div class="bg-black/60 p-6 rounded-lg border border-green-900/30 text-center">
                <h3 class="text-green-400 font-semibold text-lg">Total Users</h3>
                <p class="text-3xl font-bold text-white">{{ $users->total() }}</p>
                <p class="text-xs text-green-200">All user accounts</p>
            </div>
            <div class="bg-black/60 p-6 rounded-lg border border-blue-900/30 text-center">
                <h3 class="text-blue-400 font-semibold text-lg">Active Users</h3>
                <p class="text-3xl font-bold text-white">{{ $enabledCount }}</p>
                <p class="text-xs text-blue-200">Access enabled</p>
            </div>
            <div class="bg-black/60 p-6 rounded-lg border border-red-900/30 text-center">
                <h3 class="text-red-400 font-semibold text-lg">Disabled Users</h3>
                <p class="text-3xl font-bold text-white">{{ $disabledCount }}</p>
                <p class="text-xs text-red-200">Access disabled</p>
            </div>
            <div class="bg-black/60 p-6 rounded-lg border border-purple-900/30 text-center">
                <h3 class="text-purple-400 font-semibold text-lg">User Types</h3>
                <p class="text-3xl font-bold text-white">{{ count($roleStats) }}</p>
                <p class="text-xs text-purple-200">Different roles</p>
            </div>
        </div>

        <!-- Role Statistics -->
        @if(!empty($roleStats))
            <div class="bg-black/60 p-6 rounded-lg border border-gray-700/30 mb-8">
                <h2 class="text-xl font-semibold mb-4 text-gray-300">📊 User Distribution by Role</h2>
                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">
                    @foreach($roleStats as $role => $count)
                        <div class="text-center">
                            <p class="text-2xl font-bold text-white">{{ $count }}</p>
                            <p class="text-sm text-gray-400">{{ ucfirst($role) }}</p>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        <!-- Users Table -->
        <div class="bg-black/60 rounded-lg border border-blue-900/30 overflow-hidden">
            <div class="p-6">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-xl font-bold text-blue-300">User Access Status</h2>
                    <div class="text-sm text-gray-400">
                        {{ $users->links() }}
                    </div>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="border-b border-blue-900/30">
                                <th class="text-left py-3 px-4 text-blue-300 w-12">Role</th>
                                <th class="text-left py-3 px-4 text-blue-300">Name</th>
                                <th class="text-left py-3 px-4 text-blue-300">Email</th>
                                <th class="text-left py-3 px-4 text-blue-300">Position</th>
                                <th class="text-left py-3 px-4 text-blue-300 w-32">Access Status</th>
                                <th class="text-left py-3 px-4 text-blue-300 w-40">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($users as $user)
                                <tr class="border-b border-blue-900/20 hover:bg-blue-900/10">
                                    <td class="py-3 px-4">
                                        <span class="px-2 py-1 text-xs rounded-full 
                                            @if($user->role == 'admin') bg-blue-600 
                                            @elseif($user->role == 'cashier') bg-green-600 
                                            @elseif($user->role == 'manager') bg-purple-600
                                            @elseif($user->role == 'employee') bg-yellow-600
                                            @elseif($user->role == 'sales_clerk') bg-orange-600
                                            @elseif($user->role == 'super_admin') bg-red-600
                                            @else bg-gray-600 @endif">
                                            {{ ucfirst($user->role) }}
                                        </span>
                                    </td>
                                    <td class="py-3 px-4 font-medium">{{ $user->name }}</td>
                                    <td class="py-3 px-4 text-gray-300">{{ $user->email }}</td>
                                    <td class="py-3 px-4">{{ $user->position ?? 'N/A' }}</td>
                                    <td class="py-3 px-4">
                                        <span class="px-3 py-1 text-xs rounded-full font-semibold
                                            @if($user->access_enabled) bg-green-600 text-green-100 
                                            @else bg-red-600 text-red-100 @endif">
                                            {{ $user->access_enabled ? 'Enabled' : 'Disabled' }}
                                        </span>
                                    </td>
                                    <td class="py-3 px-4">
                                        <form method="POST" action="{{ route('superadmin.toggle-access', $user->id) }}" class="inline">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="px-4 py-2 bg-{{ $user->access_enabled ? 'red' : 'green' }}-600 hover:bg-{{ $user->access_enabled ? 'red' : 'green' }}-700 text-white text-sm rounded-md transition-colors font-medium" 
                                                    onclick="return confirm('{{ $user->access_enabled ? 'Disable' : 'Enable' }} access for {{ $user->name }}?')">
                                                {{ $user->access_enabled ? 'Disable Access' : 'Enable Access' }}
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="py-12 text-center text-gray-400">
                                        No users found. 
                                        <a href="{{ route('admin.register.show') }}" class="text-blue-400 hover:underline">Create one</a>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
