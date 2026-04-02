<table>
    <thead>
        <tr>
            <th>Employee Name</th>
            <th>Email</th>
            <th>Role</th>
            <th>Position</th>
            <th>Jan</th>
            <th>Feb</th>
            <th>Mar</th>
            <th>Apr</th>
            <th>May</th>
            <th>Jun</th>
            <th>Jul</th>
            <th>Aug</th>
            <th>Sep</th>
            <th>Oct</th>
            <th>Nov</th>
            <th>Dec</th>
            <th>Total</th>
            <th>Rate</th>
        </tr>
    </thead>
    <tbody>
        @foreach($attendanceData as $employee)
            <tr>
                <td>{{ $employee['name'] }}</td>
                <td>{{ $employee['email'] }}</td>
                <td>{{ $employee['role'] }}</td>
                <td>{{ $employee['position'] }}</td>
                @foreach($months as $monthNum => $monthName)
                    <td style="text-align: center;">
                        @php
                            $monthData = $employee['monthly_stats'][$monthNum] ?? ['present' => 0, 'absent' => 0, 'late' => 0];
                        @endphp
                        {{ $monthData['present'] }}/{{ $monthData['absent'] }}/{{ $monthData['late'] }}
                    </td>
                @endforeach
                <td style="text-align: center; font-weight: bold;">{{ $employee['monthly_stats']['total_present'] ?? 0 }}</td>
                <td style="text-align: center; font-weight: bold;">{{ $employee['monthly_stats']['attendance_rate'] ?? 0 }}%</td>
            </tr>
        @endforeach
    </tbody>
</table>

<div style="margin-top: 20px; font-size: 12px;">
    <p><strong>MANLIQUID COMMUNICATION</strong> - Attendance Report</p>
    <p>Year: {{ $year }} | Generated: {{ $generatedDate }}</p>
    <p>Total Employees: {{ count($attendanceData) }}</p>
</div>
