<!-- Real-time Attendance Notifications -->
<div class="space-y-2 max-h-96 overflow-y-auto">
    @foreach($recentAttendances as $attendance)
        <div class="flex items-center justify-between p-3 bg-gray-800/50 rounded-lg border border-gray-700/50 mb-2">
            <div class="flex items-center gap-3">
                <div class="w-3 h-3 rounded-full @if($attendance->check_in && !$attendance->check_out) bg-green-500 animate-pulse @elseif($attendance->check_out) bg-blue-500 @else bg-gray-500 @endif"></div>
                <div>
                    <p class="text-sm font-medium text-white">{{ $attendance->user->name }}</p>
                    <p class="text-xs text-gray-400">{{ ucfirst($attendance->user->role) }} • {{ $attendance->user->position ?? 'No position' }}</p>
                </div>
            </div>
            <div class="text-right">
                @if($attendance->check_in && !$attendance->check_out)
                    <p class="text-xs text-green-400 font-medium">🟢 Currently Working</p>
                    <p class="text-xs text-gray-400">In: {{ \Carbon\Carbon::parse($attendance->check_in)->format('h:i A') }}</p>
                    <p class="text-xs text-yellow-400">⏱️ {{ \Carbon\Carbon::parse($attendance->check_in)->diffForHumans(\Carbon\Carbon::now(), true) }}</p>
                @elseif($attendance->check_out)
                    <p class="text-xs text-blue-400 font-medium">🔵 Completed</p>
                    <p class="text-xs text-gray-400">{{ \Carbon\Carbon::parse($attendance->check_in)->format('h:i A') }} - {{ \Carbon\Carbon::parse($attendance->check_out)->format('h:i A') }}</p>
                    <?php 
                        $hours = \Carbon\Carbon::parse($attendance->check_in)->diffInMinutes($attendance->check_out);
                        $hoursFormatted = floor($hours / 60) . 'h ' . ($hours % 60) . 'm';
                    ?>
                    <p class="text-xs text-green-400">⏱️ {{ $hoursFormatted }}</p>
                @else
                    <p class="text-xs text-gray-400">No activity</p>
                @endif
            </div>
        </div>
    @endforeach
</div>
