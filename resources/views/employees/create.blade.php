<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Add Employee - {{ config('app.name') }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gradient-to-br from-black via-gray-900 to-blue-900 min-h-screen text-white">
    <div class="max-w-4xl mx-auto p-6">
        <!-- Header -->
        <header class="flex items-center justify-between mb-8">
            <div>
                <h1 class="text-3xl font-bold text-blue-400">➕ Add New Employee</h1>
                <p class="text-blue-200 text-sm mt-1">Create a new employee account</p>
            </div>
            <a href="{{ route('employee.list') }}" class="px-4 py-2 text-blue-200 hover:bg-blue-900/30 rounded">← Back to List</a>
        </header>

        @if (session('success'))
            <div class="mb-6 bg-green-900/50 border border-green-700 rounded-md p-4">
                <p class="text-green-200">{{ session('success') }}</p>
            </div>
        @endif

        @if ($errors->any())
            <div class="mb-6 bg-red-900/50 border border-red-700 rounded-md p-4">
                <ul class="text-red-200">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Registration Form -->
        <div class="bg-black/60 p-6 rounded-lg border border-blue-900/30">
            <form method="POST" action="{{ route('employee.register.store') }}" class="space-y-4">
                @csrf
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-blue-300 mb-2">Full Name</label>
                        <input name="name" type="text" value="{{ old('name') }}" class="w-full px-4 py-2 border border-gray-700 bg-black/40 rounded-md text-white placeholder-gray-400" required />
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-blue-300 mb-2">Email</label>
                        <input name="email" type="email" value="{{ old('email') }}" class="w-full px-4 py-2 border border-gray-700 bg-black/40 rounded-md text-white placeholder-gray-400" required />
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-blue-300 mb-2">Password</label>
                        <input name="password" type="password" class="w-full px-4 py-2 border border-gray-700 bg-black/40 rounded-md text-white placeholder-gray-400" required />
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-blue-300 mb-2">Confirm Password</label>
                        <input name="password_confirmation" type="password" class="w-full px-4 py-2 border border-gray-700 bg-black/40 rounded-md text-white placeholder-gray-400" required />
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-blue-300 mb-2">Position</label>
                        <input name="position" type="text" value="{{ old('position') }}" class="w-full px-4 py-2 border border-gray-700 bg-black/40 rounded-md text-white placeholder-gray-400" placeholder="e.g., Sales Associate" />
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-blue-300 mb-2">Department</label>
                        <input name="department" type="text" value="{{ old('department') }}" class="w-full px-4 py-2 border border-gray-700 bg-black/40 rounded-md text-white placeholder-gray-400" placeholder="e.g., Sales" />
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-blue-300 mb-2">Salary</label>
                        <input name="salary" type="number" step="0.01" value="{{ old('salary') }}" class="w-full px-4 py-2 border border-gray-700 bg-black/40 rounded-md text-white placeholder-gray-400" placeholder="15000.00" />
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-blue-300 mb-2">Role</label>
                        <select name="role" class="w-full px-4 py-2 border border-gray-700 bg-black/40 rounded-md text-white" required>
                            <option value="user">Employee (Sales Clerk)</option>
                            <option value="cashier">Cashier</option>
                            <option value="admin">Admin Assistant</option>
                        </select>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-blue-300 mb-2">Phone</label>
                        <input name="phone" type="text" value="{{ old('phone') }}" class="w-full px-4 py-2 border border-gray-700 bg-black/40 rounded-md text-white placeholder-gray-400" placeholder="e.g., 09123456789" />
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-blue-300 mb-2">Employment Status</label>
                        <select name="employment_status" class="w-full px-4 py-2 border border-gray-700 bg-black/40 rounded-md text-white">
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                            <option value="on_leave">On Leave</option>
                        </select>
                    </div>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-blue-300 mb-2">Address</label>
                    <textarea name="address" class="w-full px-4 py-2 border border-gray-700 bg-black/40 rounded-md text-white placeholder-gray-400" rows="2" placeholder="Full address">{{ old('address') }}</textarea>
                </div>
                
                <div class="flex gap-4">
                    <button type="submit" class="px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-md transition-colors">
                        Create Employee
                    </button>
                    <a href="{{ route('employee.list') }}" class="px-6 py-3 bg-gray-700 hover:bg-gray-600 text-white font-semibold rounded-md transition-colors">
                        Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
</body>
</html>

