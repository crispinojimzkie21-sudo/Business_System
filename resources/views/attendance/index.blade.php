<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Today's Attendance - {{ config('app.name') }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gradient-to-br from-black via-gray-900 to-blue-900 min-h-screen text-white">
    <div class="max-w-6xl mx-auto p-6">
        <!-- Header -->
        <header class="flex items-center justify-between mb-8">
            <div>
                <h1 class="text-3xl font-bold text-blue-400">⏰ Today's Attendance</h1>
                <p class="text-blue-200 text-sm mt-1">Check in and check out for today</p>
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

        <!-- Check In/Out Forms -->
        <div class="bg-black/60 p-6 rounded-lg border border-blue-900/30 mb-8">
            <h2 class="text-xl font-semibold mb-4 text-blue-400">⏱️ Attendance Actions</h2>
            <div class="flex gap-4">
                <form method="POST" action="{{ route('attendance.checkin') }}" class="inline">
                    @csrf
                    <input type="hidden" name="latitude" value="">
                    <input type="hidden" name="longitude" value="">
                    <input type="hidden" name="location_name" value="Manual Check-in">
                    <button type="submit" class="px-6 py-3 bg-green-600 hover:bg-green-700 text-white font-semibold rounded-md transition-colors">
                        ⏰ Check In
                    </button>
                </form>
                <form method="POST" action="{{ route('attendance.checkout') }}" class="inline">
                    @csrf
                    <input type="hidden" name="latitude" value="">
                    <input type="hidden" name="longitude" value="">
                    <input type="hidden" name="location_name" value="Manual Check-out">
                    <button type="submit" class="px-6 py-3 bg-red-600 hover:bg-red-700 text-white font-semibold rounded-md transition-colors">
                        ⏱️ Check Out
                    </button>
                </form>
                <a href="{{ route('attendance.records') }}" class="px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-md transition-colors">
                    📅 View All Records
                </a>
            </div>
        </div>

        <!-- Today's Attendance List -->
        <div class="bg-black/60 rounded-lg border border-blue-900/30 overflow-hidden">
            <div class="p-6">
                <h2 class="text-xl font-semibold mb-4 text-blue-400">Today's Check-ins</h2>
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="border-b border-blue-900/30">
                                <th class="text-left py-3 px-4 text-blue-300">Employee</th>
                                <th class="text-left py-3 px-4 text-blue-300">Check In</th>
                                <th class="text-left py-3 px-4 text-blue-300">Check Out</th>
                                <th class="text-left py-3 px-4 text-blue-300">Duration</th>
                                @if(Auth::user()->isSuperAdmin() || Auth::user()->isAdmin())
                                <th class="text-left py-3 px-4 text-blue-300">Actions</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($todayAttendance as $userId => $records)
                                @foreach($records as $record)
                                <tr class="border-b border-blue-900/20 hover:bg-blue-900/10">
                                    <td class="py-3 px-4">{{ $record->user->name ?? 'Unknown' }}</td>
                                    <td class="py-3 px-4">{{ $record->check_in ? \Carbon\Carbon::parse($record->check_in)->format('H:i:s') : 'N/A' }}</td>
                                    <td class="py-3 px-4">
                                        @if($record->check_out)
                                            {{ \Carbon\Carbon::parse($record->check_out)->format('H:i:s') }}
                                        @else
                                            <span class="text-yellow-400">Still checked in</span>
                                        @endif
                                    </td>
                                    <td class="py-3 px-4">
                                        @if($record->check_in && $record->check_out)
                                            {{ \Carbon\Carbon::parse($record->check_in)->diffForHumans(\Carbon\Carbon::parse($record->check_out), true) }}
                                        @else
                                            -
                                        @endif
                                    </td>
                                    @if(Auth::user()->isSuperAdmin() || Auth::user()->isAdmin())
                                    <td class="py-3 px-4">
                                        @if(!$record->check_out)
                                            <form action="{{ route('attendance.admin-checkout', $record->user_id) }}" method="POST" style="display: inline;">
                                                @csrf
                                                <button type="submit" class="px-3 py-1 bg-red-600 hover:bg-red-700 text-white rounded text-sm" onclick="return confirm('Check out this user?')">
                                                    ⏱️ Check Out
                                                </button>
                                            </form>
                                        @else
                                            <span class="text-green-400 text-sm">✓ Completed</span>
                                        @endif
                                    </td>
                                    @endif
                                </tr>
                                @endforeach
                            @empty
                                <tr>
                                    <td colspan="{{ (Auth::user()->isSuperAdmin() || Auth::user()->isAdmin()) ? '5' : '4' }}" class="py-8 text-center text-gray-400">
                                        No attendance records for today.
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

