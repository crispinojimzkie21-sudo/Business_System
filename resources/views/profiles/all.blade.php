<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>All User Profiles - {{ config('app.name') }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .glass-card {
            background: rgba(0, 0, 0, 0.4);
            backdrop-filter: blur(10px);
        }
    </style>
</head>
<body class="bg-gradient-to-br from-black via-gray-900 to-red-900 min-h-screen text-white">
    <div class="max-w-7xl mx-auto p-6">
        <!-- Header -->
        <header class="flex items-center justify-between mb-8">
            <div class="flex items-center gap-4">
                <div class="w-14 h-14 bg-gradient-to-br from-red-400 to-red-600 rounded-xl flex items-center justify-center text-2xl font-bold">
                    <i class="fas fa-users"></i>
                </div>
                <div>
                    <h1 class="text-3xl font-bold text-red-400">All User Profiles</h1>
                    <p class="text-red-200 text-sm">View and manage all user profiles</p>
                </div>
            </div>
            <div class="flex items-center gap-4">
                <a href="{{ route('dashboard.superadmin') }}" class="px-4 py-2 text-red-200 hover:bg-red-900/30 rounded-lg transition-colors">
                    <i class="fas fa-arrow-left mr-2"></i>Back to Dashboard
                </a>
                <a href="{{ route('superadmin.access-control') }}" class="px-4 py-2 text-red-200 hover:bg-red-900/30 rounded-lg transition-colors">
                    <i class="fas fa-shield-alt mr-2"></i>Access Control
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

        <!-- Search and Filters -->
        <div class="glass-card p-6 rounded-xl border border-red-500/30 mb-6">
            <h2 class="text-xl font-semibold mb-4 text-red-400">
                <i class="fas fa-filter mr-2"></i>Search & Filters
            </h2>
            <form method="GET" action="{{ route('profiles.all') }}" class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-red-300 mb-2">Search</label>
                        <input type="text" name="search" value="{{ request('search') }}" 
                               class="w-full px-4 py-2 border border-gray-700 bg-black/40 rounded-md text-white placeholder-gray-400" 
                               placeholder="Name, email, ID...">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-red-300 mb-2">Role</label>
                        <select name="role" class="w-full px-4 py-2 border border-gray-700 bg-black/40 rounded-md text-white">
                            <option value="">All Roles</option>
                            <option value="admin" {{ request('role') == 'admin' ? 'selected' : '' }}>Admin</option>
                            <option value="cashier" {{ request('role') == 'cashier' ? 'selected' : '' }}>Cashier</option>
                            <option value="user" {{ request('role') == 'user' ? 'selected' : '' }}>User</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-red-300 mb-2">Employment Status</label>
                        <select name="status" class="w-full px-4 py-2 border border-gray-700 bg-black/40 rounded-md text-white">
                            <option value="">All Status</option>
                            <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                            <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                            <option value="on_leave" {{ request('status') == 'on_leave' ? 'selected' : '' }}>On Leave</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-red-300 mb-2">Access Status</label>
                        <select name="access" class="w-full px-4 py-2 border border-gray-700 bg-black/40 rounded-md text-white">
                            <option value="">All Access</option>
                            <option value="enabled" {{ request('access') == 'enabled' ? 'selected' : '' }}>Enabled</option>
                            <option value="disabled" {{ request('access') == 'disabled' ? 'selected' : '' }}>Disabled</option>
                        </select>
                    </div>
                </div>
                <div class="flex gap-4">
                    <button type="submit" class="px-6 py-2 bg-red-600 hover:bg-red-500 text-white font-semibold rounded-md transition-colors">
                        <i class="fas fa-search mr-2"></i>Search
                    </button>
                    <a href="{{ route('profiles.all') }}" class="px-6 py-2 bg-gray-600 hover:bg-gray-500 text-white font-semibold rounded-md transition-colors">
                        <i class="fas fa-times mr-2"></i>Clear
                    </a>
                </div>
            </form>
        </div>

        <!-- Profiles List -->
        <div class="glass-card rounded-xl border border-red-500/30 overflow-hidden">
            <div class="p-6">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-xl font-semibold text-red-400">
                        <i class="fas fa-users mr-2"></i>User Profiles ({{ $profiles->total() }} total)
                    </h2>
                    <div class="text-sm text-red-200">
                        Showing {{ $profiles->firstItem() }} to {{ $profiles->lastItem() }} of {{ $profiles->total() }} users
                    </div>
                </div>
                
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="border-b border-red-900/30">
                                <th class="text-left py-3 px-4 text-red-300">
                                    <a href="?sort=name&order={{ request('order') == 'asc' ? 'desc' : 'asc' }}" class="hover:text-red-200">
                                        Name {{ request('sort') == 'name' ? (request('order') == 'asc' ? '↑' : '↓') : '' }}
                                    </a>
                                </th>
                                <th class="text-left py-3 px-4 text-red-300">Email</th>
                                <th class="text-left py-3 px-4 text-red-300">Role</th>
                                <th class="text-left py-3 px-4 text-red-300">Position</th>
                                <th class="text-left py-3 px-4 text-red-300">Department</th>
                                <th class="text-left py-3 px-4 text-red-300">Status</th>
                                <th class="text-left py-3 px-4 text-red-300">Access</th>
                                <th class="text-left py-3 px-4 text-red-300">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($profiles as $profile)
                                <tr class="border-b border-red-900/20 hover:bg-red-900/10">
                                    <td class="py-3 px-4">
                                        <div class="flex items-center gap-3">
                                            <div class="w-10 h-10 bg-gradient-to-br 
                                                @if($profile->role == 'admin') from-blue-400 to-blue-600
                                                @elseif($profile->role == 'cashier') from-green-400 to-green-600
                                                @else from-purple-400 to-purple-600
                                                @endif rounded-full flex items-center justify-center text-white font-semibold">
                                                {{ substr($profile->name, 0, 1) }}
                                            </div>
                                            <div>
                                                <div class="font-medium">{{ $profile->name }}</div>
                                                @if($profile->employee_id)
                                                <div class="text-xs text-gray-400">ID: {{ $profile->employee_id }}</div>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td class="py-3 px-4 text-gray-300">{{ $profile->email }}</td>
                                    <td class="py-3 px-4">
                                        <span class="px-2 py-1 text-xs rounded-full font-semibold
                                            @if($profile->role == 'admin') bg-blue-600
                                            @elseif($profile->role == 'cashier') bg-green-600
                                            @else bg-purple-600
                                            @endif">
                                            {{ ucfirst($profile->role) }}
                                        </span>
                                    </td>
                                    <td class="py-3 px-4">{{ $profile->position ?? 'N/A' }}</td>
                                    <td class="py-3 px-4">{{ $profile->department ?? 'N/A' }}</td>
                                    <td class="py-3 px-4">
                                        <span class="px-2 py-1 text-xs rounded-full font-semibold
                                            @if($profile->employment_status == 'active') bg-green-600
                                            @elseif($profile->employment_status == 'inactive') bg-red-600
                                            @elseif($profile->employment_status == 'on_leave') bg-yellow-600
                                            @else bg-gray-600
                                            @endif">
                                            {{ ucfirst($profile->employment_status ?? 'active') }}
                                        </span>
                                    </td>
                                    <td class="py-3 px-4">
                                        <span class="px-2 py-1 text-xs rounded-full font-semibold
                                            @if($profile->access_enabled) bg-green-600
                                            @else bg-red-600
                                            @endif">
                                            {{ $profile->access_enabled ? 'Enabled' : 'Disabled' }}
                                        </span>
                                    </td>
                                    <td class="py-3 px-4">
                                        <div class="flex gap-2">
                                            <a href="{{ route('profile.view', $profile->id) }}" 
                                               class="px-3 py-1 bg-blue-600 hover:bg-blue-500 text-white text-xs rounded-md transition-colors">
                                                <i class="fas fa-eye mr-1"></i>View
                                            </a>
                                            <a href="{{ route('employee.edit', $profile->id) }}" 
                                               class="px-3 py-1 bg-yellow-600 hover:bg-yellow-500 text-white text-xs rounded-md transition-colors">
                                                <i class="fas fa-edit mr-1"></i>Edit
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="py-12 text-center text-gray-400">
                                        <i class="fas fa-users-slash text-4xl mb-4"></i>
                                        <p class="text-lg">No user profiles found</p>
                                        <p class="text-sm">Try adjusting your search criteria</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                
                <!-- Pagination -->
                @if($profiles->hasPages())
                    <div class="mt-6">
                        {{ $profiles->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</body>
</html>
