<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Deleted Attendance Records - {{ config('app.name') }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gradient-to-br from-black via-gray-900 to-red-900 min-h-screen text-white">
    <div class="max-w-6xl mx-auto p-6">
        <!-- Header -->
        <header class="flex items-center justify-between mb-8">
            <div>
                <h1 class="text-3xl font-bold text-red-400">🗑️ Deleted Attendance Records</h1>
                <p class="text-red-200 text-sm mt-1">View and restore deleted attendance records</p>
            </div>
            <div class="flex gap-4">
                <a href="{{ route('attendance.records') }}" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded">← Back to Records</a>
            </div>
        </header>

        @if(session('success'))
            <div class="mb-6 bg-green-900/50 border border-green-700 rounded-md p-4">
                <p class="text-green-200">{{ session('success') }}</p>
            </div>
        @endif



        <!-- Deleted Attendance Records List -->
        <div class="bg-black/60 rounded-lg border border-red-900/30 overflow-hidden">
            <div class="p-6">
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="border-b border-red-900/30">
                                <th class="text-left py-3 px-4 text-red-300">ID</th>
                                <th class="text-left py-3 px-4 text-red-300">Employee</th>
                                <th class="text-left py-3 px-4 text-red-300">Date</th>
                                <th class="text-left py-3 px-4 text-red-300">Check In</th>
                                <th class="text-left py-3 px-4 text-red-300">Check Out</th>
                                <th class="text-left py-3 px-4 text-red-300">Deleted At</th>
                                <th class="text-left py-3 px-4 text-red-300">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($trashedAttendances as $attendance)
                            <tr class="border-b border-red-900/20 hover:bg-red-900/10">
                                <td class="py-3 px-4">{{ $attendance->id }}</td>
                                <td class="py-3 px-4">{{ $attendance->user->name ?? 'N/A' }}</td>
                                <td class="py-3 px-4">{{ $attendance->date }}</td>
                                <td class="py-3 px-4">{{ $attendance->check_in ? \Carbon\Carbon::parse($attendance->check_in)->format('H:i:s') : 'N/A' }}</td>
                                <td class="py-3 px-4">{{ $attendance->check_out ? \Carbon\Carbon::parse($attendance->check_out)->format('H:i:s') : 'N/A' }}</td>
                                <td class="py-3 px-4">{{ $attendance->deleted_at->format('Y-m-d H:i:s') }}</td>
                                <td class="py-3 px-4">
                                    <form action="{{ route('attendance.restore', $attendance->id) }}" method="POST" style="display: inline;">
                                        @csrf
                                        <button type="submit" class="px-2 py-1 bg-green-600 hover:bg-green-700 text-white rounded text-sm">Restore</button>
                                    </form>
                                    <form action="{{ route('attendance.force-delete', $attendance->id) }}" method="POST" style="display: inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="px-2 py-1 bg-red-700 hover:bg-red-800 text-white rounded text-sm" onclick="return confirm('Are you sure you want to permanently delete this record?')">Delete</button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="py-8 text-center text-gray-400">
                                    No deleted attendance records found.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
        </div>

        <!-- Pagination -->
        <div class="mt-4">
            {{ $trashedAttendances->links() }}
        </div>
</body>
</html>
