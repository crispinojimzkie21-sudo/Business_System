<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $employee->name }} - Profile</title>
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
    <div class="max-w-6xl mx-auto p-6">
        <!-- Header -->
        <header class="flex items-center justify-between mb-8">
            <div class="flex items-center gap-4">
                <div class="w-16 h-16 bg-gradient-to-br 
                    @if($employee->role == 'admin') from-blue-400 to-blue-600
                    @elseif($employee->role == 'cashier') from-green-400 to-green-600
                    @else from-purple-400 to-purple-600
                    @endif rounded-full flex items-center justify-center text-2xl font-bold text-white">
                    {{ substr($employee->name, 0, 1) }}
                </div>
                <div>
                    <h1 class="text-3xl font-bold text-red-400">{{ $employee->name }}</h1>
                    <p class="text-red-200 text-sm">
                        @if($employee->role == 'admin') Admin
                        @elseif($employee->role == 'cashier') Cashier
                        @else Employee
                        @endif Profile
                    </p>
                </div>
            </div>
            <div class="flex items-center gap-4">
                <a href="{{ route('profiles.all') }}" class="px-4 py-2 text-red-200 hover:bg-red-900/30 rounded-lg transition-colors">
                    <i class="fas fa-arrow-left mr-2"></i>Back to All Profiles
                </a>
                <a href="{{ route('employee.edit', $employee->id) }}" class="px-4 py-2 text-yellow-200 hover:bg-yellow-900/30 rounded-lg transition-colors">
                    <i class="fas fa-edit mr-2"></i>Edit Profile
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

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Main Profile Information -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Personal Information -->
                <div class="glass-card p-6 rounded-xl border border-red-500/30">
                    <h2 class="text-xl font-semibold mb-4 text-red-400">
                        <i class="fas fa-user mr-2"></i>Personal Information
                    </h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-4">
                            <div>
                                <label class="text-sm text-red-300">Full Name</label>
                                <p class="font-medium">{{ $employee->name }}</p>
                            </div>
                            <div>
                                <label class="text-sm text-red-300">Email Address</label>
                                <p class="font-medium">{{ $employee->email }}</p>
                            </div>
                            <div>
                                <label class="text-sm text-red-300">Phone Number</label>
                                <p class="font-medium">{{ $employee->phone ?? 'Not set' }}</p>
                            </div>
                            <div>
                                <label class="text-sm text-red-300">Address</label>
                                <p class="font-medium">{{ $employee->address ?? 'Not set' }}</p>
                            </div>
                        </div>
                        <div class="space-y-4">
                            @if($employee->employee_id)
                            <div>
                                <label class="text-sm text-red-300">Employee ID</label>
                                <p class="font-medium">{{ $employee->employee_id }}</p>
                            </div>
                            @endif
                            @if($employee->hire_date)
                            <div>
                                <label class="text-sm text-red-300">Hire Date</label>
                                <p class="font-medium">{{ \Carbon\Carbon::parse($employee->hire_date)->format('M d, Y') }}</p>
                            </div>
                            @endif
                            <div>
                                <label class="text-sm text-red-300">Member Since</label>
                                <p class="font-medium">{{ $employee->created_at->format('M d, Y') }}</p>
                            </div>
                            <div>
                                <label class="text-sm text-red-300">Last Updated</label>
                                <p class="font-medium">{{ $employee->updated_at->format('M d, Y H:i') }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Work Information -->
                <div class="glass-card p-6 rounded-xl border border-red-500/30">
                    <h2 class="text-xl font-semibold mb-4 text-red-400">
                        <i class="fas fa-briefcase mr-2"></i>Work Information
                    </h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-4">
                            <div>
                                <label class="text-sm text-red-300">Position</label>
                                <p class="font-medium">{{ $employee->position ?? 'Not set' }}</p>
                            </div>
                            <div>
                                <label class="text-sm text-red-300">Department</label>
                                <p class="font-medium">{{ $employee->department ?? 'Not set' }}</p>
                            </div>
                        </div>
                        <div class="space-y-4">
                            <div>
                                <label class="text-sm text-red-300">Role</label>
                                <p class="font-medium">
                                    <span class="px-2 py-1 text-xs rounded-full font-semibold
                                        @if($employee->role == 'admin') bg-blue-600
                                        @elseif($employee->role == 'cashier') bg-green-600
                                        @else bg-purple-600
                                        @endif">
                                        @if($employee->role == 'admin') Admin
                                        @elseif($employee->role == 'cashier') Cashier
                                        @else Employee
                                        @endif
                                    </span>
                                </p>
                            </div>
                            <div>
                                <label class="text-sm text-red-300">Employment Status</label>
                                <p class="font-medium">
                                    <span class="px-2 py-1 text-xs rounded-full font-semibold
                                        @if($employee->employment_status == 'active') bg-green-600
                                        @elseif($employee->employment_status == 'inactive') bg-red-600
                                        @elseif($employee->employment_status == 'on_leave') bg-yellow-600
                                        @else bg-gray-600
                                        @endif">
                                        {{ ucfirst($employee->employment_status ?? 'active') }}
                                    </span>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Attendance Summary (if available) -->
                @if(isset($employee->attendances) && $employee->attendances->count() > 0)
                <div class="glass-card p-6 rounded-xl border border-red-500/30">
                    <h2 class="text-xl font-semibold mb-4 text-red-400">
                        <i class="fas fa-clock mr-2"></i>Recent Attendance
                    </h2>
                    <div class="space-y-3">
                        @foreach($employee->attendances->take(5) as $attendance)
                        <div class="flex justify-between items-center p-3 bg-black/40 rounded-lg">
                            <div>
                                <p class="font-medium">{{ \Carbon\Carbon::parse($attendance->date)->format('M d, Y') }}</p>
                                <p class="text-sm text-gray-400">
                                    @if($attendance->check_in) Check-in: {{ \Carbon\Carbon::parse($attendance->check_in)->format('h:i A') }} @endif
                                    @if($attendance->check_out) | Check-out: {{ \Carbon\Carbon::parse($attendance->check_out)->format('h:i A') }} @endif
                                </p>
                            </div>
                            <div>
                                <span class="px-2 py-1 text-xs rounded-full 
                                    @if($attendance->check_in && $attendance->check_out) bg-green-600
                                    @elseif($attendance->check_in) bg-yellow-600
                                    @else bg-gray-600
                                    @endif">
                                    @if($attendance->check_in && $attendance->check_out) Complete
                                    @elseif($attendance->check_in) In Progress
                                    @else No Activity
                                    @endif
                                </span>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif

                <!-- Sales Summary (if available) -->
                @if(isset($employee->sales) && $employee->sales->count() > 0)
                <div class="glass-card p-6 rounded-xl border border-red-500/30">
                    <h2 class="text-xl font-semibold mb-4 text-red-400">
                        <i class="fas fa-shopping-cart mr-2"></i>Recent Sales
                    </h2>
                    <div class="space-y-3">
                        @foreach($employee->sales->take(5) as $sale)
                        <div class="flex justify-between items-center p-3 bg-black/40 rounded-lg">
                            <div>
                                <p class="font-medium">{{ $sale->transaction_id }}</p>
                                <p class="text-sm text-gray-400">{{ \Carbon\Carbon::parse($sale->created_at)->format('M d, Y h:i A') }}</p>
                            </div>
                            <div class="text-right">
                                <p class="font-medium text-green-400">₱{{ number_format($sale->total_amount, 2) }}</p>
                                <p class="text-sm text-gray-400">{{ $sale->payment_method }}</p>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif
            </div>

            <!-- Sidebar -->
            <div class="space-y-6">
                <!-- Status & Access -->
                <div class="glass-card p-6 rounded-xl border border-red-500/30">
                    <h2 class="text-xl font-semibold mb-4 text-red-400">
                        <i class="fas fa-shield-alt mr-2"></i>Status & Access
                    </h2>
                    <div class="space-y-4">
                        <div>
                            <label class="text-sm text-red-300">Access Status</label>
                            <p class="font-medium">
                                <span class="px-2 py-1 text-xs rounded-full font-semibold
                                    @if($employee->access_enabled) bg-green-600
                                    @else bg-red-600
                                    @endif">
                                    {{ $employee->access_enabled ? 'Enabled' : 'Disabled' }}
                                </span>
                            </p>
                        </div>
                        @if($employee->salary)
                        <div>
                            <label class="text-sm text-red-300">Monthly Salary</label>
                            <p class="font-medium text-green-400">₱{{ number_format($employee->salary, 2) }}</p>
                        </div>
                        @endif
                        @if($employee->notes)
                        <div>
                            <label class="text-sm text-red-300">Notes</label>
                            <p class="font-medium text-sm">{{ $employee->notes }}</p>
                        </div>
                        @endif
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="glass-card p-6 rounded-xl border border-red-500/30">
                    <h2 class="text-xl font-semibold mb-4 text-red-400">
                        <i class="fas fa-bolt mr-2"></i>Quick Actions
                    </h2>
                    <div class="space-y-3">
                        @if(Auth::user()->isSuperAdmin())
                        <form method="POST" action="{{ route('superadmin.toggle-access', $employee->id) }}" class="inline">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="w-full px-4 py-2 bg-{{ $employee->access_enabled ? 'red' : 'green' }}-600 hover:bg-{{ $employee->access_enabled ? 'red' : 'green' }}-500 text-white font-semibold rounded-md transition-colors"
                                    onclick="return confirm('{{ $employee->access_enabled ? 'Disable' : 'Enable' }} access for {{ $employee->name }}?')">
                                <i class="fas fa-{{ $employee->access_enabled ? 'ban' : 'check' }} mr-2"></i>
                                {{ $employee->access_enabled ? 'Disable Access' : 'Enable Access' }}
                            </button>
                        </form>
                        @endif
                        <a href="{{ route('employee.edit', $employee->id) }}" 
                           class="block w-full px-4 py-2 bg-yellow-600 hover:bg-yellow-500 text-white font-semibold rounded-md transition-colors text-center">
                            <i class="fas fa-edit mr-2"></i>Edit Profile
                        </a>
                        <a href="{{ route('profiles.all') }}" 
                           class="block w-full px-4 py-2 bg-gray-600 hover:bg-gray-500 text-white font-semibold rounded-md transition-colors text-center">
                            <i class="fas fa-users mr-2"></i>View All Profiles
                        </a>
                    </div>
                </div>

                <!-- Statistics -->
                <div class="glass-card p-6 rounded-xl border border-red-500/30">
                    <h2 class="text-xl font-semibold mb-4 text-red-400">
                        <i class="fas fa-chart-bar mr-2"></i>Statistics
                    </h2>
                    <div class="space-y-3">
                        @if(isset($employee->attendances))
                        <div class="flex justify-between">
                            <span class="text-sm text-red-300">Total Attendances</span>
                            <span class="font-medium">{{ $employee->attendances->count() }}</span>
                        </div>
                        @endif
                        @if(isset($employee->sales))
                        <div class="flex justify-between">
                            <span class="text-sm text-red-300">Total Sales</span>
                            <span class="font-medium">{{ $employee->sales->count() }}</span>
                        </div>
                        @if($employee->sales->count() > 0)
                        <div class="flex justify-between">
                            <span class="text-sm text-red-300">Sales Total</span>
                            <span class="font-medium text-green-400">₱{{ number_format($employee->sales->sum('total_amount'), 2) }}</span>
                        </div>
                        @endif
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
