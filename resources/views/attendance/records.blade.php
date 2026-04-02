<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Attendance Records - {{ config('app.name') }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gradient-to-br from-black via-gray-900 to-blue-900 min-h-screen text-white">
    <div class="max-w-6xl mx-auto p-6">
        <!-- Header -->
        <header class="flex items-center justify-between mb-8">
            <div>
                <h1 class="text-3xl font-bold text-blue-400">📅 Attendance Records</h1>
                <p class="text-blue-200 text-sm mt-1">
                    @if(Auth::user()->isSuperAdmin() || Auth::user()->isAdmin())
                        View all employee attendance history
                    @else
                        Your personal attendance history
                    @endif
                </p>
            </div>
            <div class="flex gap-4">
                @if(Auth::user()->isSuperAdmin())
                    <a href="{{ url('/dashboard/super-admin') }}" class="px-4 py-2 text-blue-200 hover:bg-blue-900/30 rounded">← Dashboard</a>
                @elseif(Auth::user()->isAdmin())
                    <a href="{{ url('/dashboard/admin') }}" class="px-4 py-2 text-blue-200 hover:bg-blue-900/30 rounded">← Dashboard</a>
                @elseif(Auth::user()->isManager())
                    <a href="{{ url('/dashboard/manager') }}" class="px-4 py-2 text-purple-200 hover:bg-purple-900/30 rounded">← Dashboard</a>
                @elseif(Auth::user()->isCashier())
                    <a href="{{ url('/dashboard/cashier') }}" class="px-4 py-2 text-green-200 hover:bg-green-900/30 rounded">← Dashboard</a>
                @else
                    <a href="{{ url('/dashboard/employee') }}" class="px-4 py-2 text-blue-200 hover:bg-blue-900/30 rounded">← Dashboard</a>
                @endif
                @if(Auth::user()->isSuperAdmin())
                    <a href="{{ route('attendance.index') }}" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded">Today's Attendance</a>
                    <a href="{{ route('attendance.trashed') }}" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded">🗑️ Trash</a>
                @endif
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

        @if (session('info'))
            <div class="mb-6 bg-blue-900/50 border border-blue-700 rounded-md p-4">
                <p class="text-blue-200">{{ session('info') }}</p>
            </div>
        @endif

        @if(Auth::user()->isSuperAdmin() || Auth::user()->isAdmin())
            <!-- Attendance Records List -->
            <div class="bg-black/60 rounded-lg border border-blue-900/30 overflow-hidden">
                <div class="p-6">
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead>
                                <tr class="border-b border-blue-900/30">
                                    <th class="text-left py-3 px-4 text-blue-300">Employee</th>
                                    <th class="text-left py-3 px-4 text-blue-300">Date</th>
                                    <th class="text-left py-3 px-4 text-blue-300">Check In</th>
                                    <th class="text-left py-3 px-4 text-blue-300">Check Out</th>
                                    <th class="text-left py-3 px-4 text-blue-300">Duration</th>
                                    <th class="text-left py-3 px-4 text-blue-300">Actions</th>
                                </tr>
                            </thead>
                                <tbody>
                                    @forelse ($attendances as $attendance)
                                        <tr class="border-b border-blue-900/20 hover:bg-blue-900/10">
                                            <td class="py-3 px-4">{{ $attendance->user->name ?? 'Unknown' }}</td>
                                            <td class="py-3 px-4">{{ $attendance->date }}</td>
                                            <td class="py-3 px-4">{{ $attendance->check_in ? \Carbon\Carbon::parse($attendance->check_in)->format('H:i:s') : 'N/A' }}</td>
                                            <td class="py-3 px-4">{{ $attendance->check_out ? \Carbon\Carbon::parse($attendance->check_out)->format('H:i:s') : 'Still checked in' }}</td>
                                            <td class="py-3 px-4">
                                                @if($attendance->check_in && $attendance->check_out)
                                                    {{ \Carbon\Carbon::parse($attendance->check_in)->diffForHumans(\Carbon\Carbon::parse($attendance->check_out), true) }}
                                                @else
                                                    -
                                                @endif
                                            </td>
                                            <td class="py-3 px-4">
                                                <form action="{{ route('attendance.destroy', $attendance->id) }}" method="POST" style="display: inline;">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="px-2 py-1 bg-red-600 hover:bg-red-700 text-white rounded text-sm" onclick="return confirm('Are you sure you want to delete this record?')">
                                                        Delete
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="7" class="py-8 text-center text-gray-400">
                                                No attendance records found.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
        @else
            <!-- Attendance Records List (for regular users - no checkboxes) -->
            <div class="bg-black/60 rounded-lg border border-blue-900/30 overflow-hidden">
                <div class="p-6">
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead>
                                <tr class="border-b border-blue-900/30">
                                    <th class="text-left py-3 px-4 text-blue-300">Date</th>
                                    <th class="text-left py-3 px-4 text-blue-300">Check In</th>
                                    <th class="text-left py-3 px-4 text-blue-300">Check Out</th>
                                    <th class="text-left py-3 px-4 text-blue-300">Duration</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($attendances as $attendance)
                                    <tr class="border-b border-blue-900/20 hover:bg-blue-900/10">
                                        <td class="py-3 px-4">{{ $attendance->date }}</td>
                                        <td class="py-3 px-4">{{ $attendance->check_in ? \Carbon\Carbon::parse($attendance->check_in)->format('H:i:s') : 'N/A' }}</td>
                                        <td class="py-3 px-4">{{ $attendance->check_out ? \Carbon\Carbon::parse($attendance->check_out)->format('H:i:s') : 'Still checked in' }}</td>
                                        <td class="py-3 px-4">
                                            @if($attendance->check_in && $attendance->check_out)
                                                {{ \Carbon\Carbon::parse($attendance->check_in)->diffForHumans(\Carbon\Carbon::parse($attendance->check_out), true) }}
                                            @else
                                                -
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="py-8 text-center text-gray-400">
                                            No attendance records found.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @endif

        <!-- Pagination -->
        <div class="mt-4">
            {{ $attendances->links() }}
        </div>
    </div>

</body>
</html>
