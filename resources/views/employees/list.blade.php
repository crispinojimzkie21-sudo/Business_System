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

        <!-- Search and Filters -->
        <div class="bg-black/60 p-4 rounded-lg border border-blue-900/30 mb-6">
            <form method="GET" action="{{ route('employee.list') }}" class="flex gap-4 flex-wrap">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search employees..." class="px-4 py-2 border border-gray-700 bg-black/40 rounded-md text-white placeholder-gray-400 flex-1" />
                <select name="role" class="px-4 py-2 border border-gray-700 bg-black/40 rounded-md text-white">
                    <option value="">All Roles</option>
                    <option value="user" {{ request('role') == 'user' ? 'selected' : '' }}>Employee</option>
                    <option value="admin" {{ request('role') == 'admin' ? 'selected' : '' }}>Admin</option>
                </select>
                <select name="status" class="px-4 py-2 border border-gray-700 bg-black/40 rounded-md text-white">
                    <option value="">All Status</option>
                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                    <option value="on_leave" {{ request('status') == 'on_leave' ? 'selected' : '' }}>On Leave</option>
                </select>
                <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-md transition-colors">Search</button>
            </form>
        </div>

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
                                <th class="text-left py-3 px-4 text-blue-300">Department</th>
                                <th class="text-left py-3 px-4 text-blue-300">Status</th>
                                <th class="text-left py-3 px-4 text-blue-300">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($employees as $employee)
                                <tr class="border-b border-blue-900/20 hover:bg-blue-900/10">
                                    <td class="py-3 px-4">{{ $employee->name }}</td>
                                    <td class="py-3 px-4">{{ $employee->email }}</td>
                                    <td class="py-3 px-4">
                                        <span class="px-2 py-1 text-xs rounded-full 
                                            @if($employee->role == 'admin') bg-blue-600 
                                            @elseif($employee->role == 'super_admin') bg-red-600 
                                            @else bg-gray-600 
                                            @endif">
                                            {{ ucfirst($employee->role) }}
                                        </span>
                                    </td>
                                    <td class="py-3 px-4">{{ $employee->position ?? 'Not specified' }}</td>
                                    <td class="py-3 px-4">{{ $employee->department ?? 'Not specified' }}</td>
                                    <td class="py-3 px-4">
                                        <span class="px-2 py-1 text-xs rounded-full 
                                            @if($employee->employment_status == 'active') bg-green-600
                                            @elseif($employee->employment_status == 'inactive') bg-red-600
                                            @else bg-yellow-600
                                            @endif">
                                            {{ ucfirst($employee->employment_status ?? 'active') }}
                                        </span>
                                    </td>
                                    <td class="py-3 px-4">
                                        <div class="flex gap-2">
                                            <a href="{{ route('employee.edit', $employee->id) }}" class="px-2 py-1 bg-blue-600 hover:bg-blue-700 text-white text-xs rounded">
                                                Edit
                                            </a>
                                            @if($employee->id !== Auth::id())
                                                <form method="POST" action="{{ route('employee.destroy', $employee->id) }}" class="inline" onsubmit="return confirm('Are you sure you want to delete {{ $employee->name }}?');">
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
                                    <td colspan="7" class="py-8 text-center text-gray-400">
                                        No employees found. <a href="{{ route('employee.register.show') }}" class="text-blue-400 hover:underline">Create your first employee account</a>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Pagination -->
        <div class="mt-4">
            {{ $employees->links() }}
        </div>
    </div>
</body>
</html>

