<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Manage Employees - {{ config('app.name') }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gradient-to-br from-black via-gray-900 to-blue-900 min-h-screen text-white">
    <div class="max-w-6xl mx-auto p-6">
        <!-- Header -->
        <header class="flex items-center justify-between mb-8">
            <div>
                <h1 class="text-3xl font-bold text-blue-400">👥 Manage Employees</h1>
                <p class="text-blue-200 text-sm mt-1">View and manage all employee accounts</p>
            </div>
            <div class="flex gap-4">
                <a href="{{ route('employee.register.show') }}" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-md transition-colors">
                    ➕ Add Employee
                </a>
                <a href="{{ Auth::user()->isSuperAdmin() ? url('/dashboard/super-admin') : url('/dashboard/admin') }}" class="px-4 py-2 text-blue-200 hover:bg-blue-900/30 rounded">← Dashboard</a>
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

        <!-- Employee List -->
        <div class="bg-black/60 rounded-lg border border-blue-900/30 overflow-hidden">
            <div class="p-6">
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="border-b border-blue-900/30">
<th class="text-left py-3 px-4 text-blue-300">Name</th>
                                <th class="text-left py-3 px-4 text-blue-300">Email</th>
                                <th class="text-left py-3 px-4 text-blue-300">Role</th>
                                <th class="text-left py-3 px-4 text-blue-300">Position</th>
                                <th class="text-left py-3 px-4 text-blue-300">Salary</th>
                                <th class="text-left py-3 px-4 text-blue-300 w-28">Access Status</th>
                                <th class="text-left py-3 px-4 text-blue-300">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($admins as $admin)
                                <tr class="border-b border-blue-900/20 hover:bg-blue-900/10">
                                    <td class="py-3 px-4">{{ $admin->name }}</td>
                                    <td class="py-3 px-4">{{ $admin->email }}</td>
                                    <td class="py-3 px-4">
                                        <span class="px-2 py-1 text-xs rounded-full 
                                            @if($admin->role == 'admin') bg-blue-600 
                                            @elseif($admin->role == 'super_admin') bg-red-600 
                                            @else bg-gray-600 
                                            @endif">
                                            {{ ucfirst($admin->role) }}
                                        </span>
                                    </td>
                                    <td class="py-3 px-4">{{ $admin->position ?? 'Not specified' }}</td>
                                    <td class="py-3 px-4">₱{{ number_format($admin->salary ?? 0, 2) }}</td>
                                    <td class="py-3 px-4">
                                        <span class="px-3 py-1 text-xs rounded-full font-semibold
                                            @if($admin->access_enabled) bg-green-600 text-green-100 
                                            @else bg-red-600 text-red-100 @endif">
                                            {{ $admin->access_enabled ? 'Enabled' : 'Disabled' }}
                                        </span>
                                    </td>
                                    <td class="py-3 px-4">
                                        <div class="flex gap-2">
                                            <a href="{{ route('admin.edit', $admin->id) }}" class="px-2 py-1 bg-blue-600 hover:bg-blue-700 text-white text-xs rounded">
                                                Edit
                                            </a>
                                            @if(Auth::user()->isSuperAdmin() && $admin->id !== Auth::id())
                                                <form method="POST" action="{{ route('superadmin.toggle-access', $admin->id) }}" class="inline" onsubmit="return confirm('{{ $admin->access_enabled ? 'Disable' : 'Enable' }} access for {{ $admin->name }}?');">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="submit" class="px-3 py-1 bg-{{ $admin->access_enabled ? 'red' : 'green' }}-600 hover:bg-{{ $admin->access_enabled ? 'red' : 'green' }}-700 text-white text-xs rounded">
                                                        {{ $admin->access_enabled ? 'Disable' : 'Enable' }}
                                                    </button>
                                                </form>
                                            @endif
                                            @if($admin->id !== Auth::id())
                                                <form method="POST" action="{{ route('admin.destroy', $admin->id) }}" class="inline" onsubmit="return confirm('Are you sure you want to delete {{ $admin->name }}?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="px-2 py-1 bg-red-600 hover:bg-red-700 text-white text-xs rounded">
                                                        Delete
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="py-8 text-center text-gray-400">
                                        No admin accounts found. <a href="{{ route('admin.register.show') }}" class="text-blue-400 hover:underline">Create your first admin account</a>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Stats -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-8">
            <div class="bg-black/60 p-4 rounded-lg border border-blue-900/30">
                <h3 class="text-blue-400 font-semibold">Total Admins</h3>
                <p class="text-2xl font-bold">{{ $admins->count() }}</p>
            </div>
            <div class="bg-black/60 p-4 rounded-lg border border-blue-900/30">
                <h3 class="text-blue-400 font-semibold">Admin Assistants</h3>
                <p class="text-2xl font-bold">{{ $admins->where('role', 'admin')->count() }}</p>
            </div>
            <div class="bg-black/60 p-4 rounded-lg border border-blue-900/30">
                <h3 class="text-blue-400 font-semibold">Super Admins</h3>
                <p class="text-2xl font-bold">{{ $admins->where('role', 'super_admin')->count() }}</p>
            </div>
        </div>
    </div>
</body>
</html>
