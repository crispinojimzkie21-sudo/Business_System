<!DOCTYPE html>
<html>
<head>
    <title>Attendance Report - {{ $year }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            margin: 20px;
            color: #000;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #333;
            padding-bottom: 20px;
        }
        .company-name {
            font-size: 24px;
            font-weight: bold;
            color: #000;
            margin-bottom: 10px;
        }
        .report-title {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 5px;
        }
        .report-info {
            font-size: 11px;
            color: #666;
            margin-bottom: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            padding: 6px 8px;
            text-align: left;
            border: 1px solid #333;
            font-size: 10px;
        }
        th {
            background-color: #f0f0f0;
            font-weight: bold;
            text-align: center;
        }
        .text-center {
            text-align: center;
        }
        .text-right {
            text-align: right;
        }
        .employee-info {
            font-weight: bold;
        }
        .role-badge {
            background-color: #e0e0e0;
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 9px;
            text-align: center;
        }
        .attendance-stats {
            font-size: 9px;
            text-align: center;
        }
        .present { color: #22c55e; }
        .absent { color: #ef4444; }
        .late { color: #f59e0b; }
        .total { font-weight: bold; }
        .rate { font-weight: bold; }
        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 10px;
            color: #666;
            border-top: 1px solid #333;
            padding-top: 10px;
        }
        @media print {
            body { margin: 10px; }
            .header { page-break-after: avoid; }
            table { page-break-inside: auto; }
            tr { page-break-inside: avoid; page-break-after: auto; }
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="company-name">MANLIQUID COMMUNICATION</div>
        <div class="report-title">ANNUAL ATTENDANCE REPORT</div>
        <div class="report-info">
            Year: {{ $year }} | Generated: {{ $generatedDate }}
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th rowspan="2" style="vertical-align: middle;">Employee Name</th>
                <th rowspan="2" style="vertical-align: middle;">Email</th>
                <th rowspan="2" style="vertical-align: middle;">Role</th>
                <th rowspan="2" style="vertical-align: middle;">Position</th>
                <th colspan="12" style="text-align: center;">Monthly Attendance (Present/Absent/Late)</th>
                <th rowspan="2" style="vertical-align: middle;">Total</th>
                <th rowspan="2" style="vertical-align: middle;">Rate</th>
            </tr>
            <tr>
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
            </tr>
        </thead>
        <tbody>
            @foreach($attendanceData as $index => $employee)
                <tr>
                    <td class="employee-info">{{ $employee['name'] }}</td>
                    <td>{{ $employee['email'] }}</td>
                    <td class="text-center">
                        <span class="role-badge">{{ $employee['role'] }}</span>
                    </td>
                    <td>{{ $employee['position'] }}</td>
                    @foreach($months as $monthNum => $monthName)
                        <td class="attendance-stats">
                            @php
                                $monthData = $employee['monthly_stats'][$monthNum] ?? ['present' => 0, 'absent' => 0, 'late' => 0];
                            @endphp
                            <span class="present">{{ $monthData['present'] }}</span>/
                            <span class="absent">{{ $monthData['absent'] }}</span>/
                            <span class="late">{{ $monthData['late'] }}</span>
                        </td>
                    @endforeach
                    <td class="text-center total">{{ $employee['monthly_stats']['total_present'] ?? 0 }}</td>
                    <td class="text-center rate">{{ $employee['monthly_stats']['attendance_rate'] ?? 0 }}%</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        <p><strong>MANLIQUID COMMUNICATION</strong> - Attendance Management System</p>
        <p>This report was automatically generated on {{ $generatedDate }}</p>
        <p>Total Employees: {{ count($attendanceData) }}</p>
    </div>
</body>
</html>
