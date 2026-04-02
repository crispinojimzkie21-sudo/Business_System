<!DOCTYPE html>
<html>
<head>
    <title>Attendance Report - {{ $year }}</title>
    <style>
        @media print {
            body { 
                margin: 10px; 
                font-size: 10px;
                color: black !important;
                background: white !important;
            }
            .no-print { display: none !important; }
            .header { page-break-after: avoid; }
            table { page-break-inside: auto; }
            tr { page-break-inside: avoid; page-break-after: auto; }
        }
        
        body {
            font-family: 'Courier New', monospace;
            font-size: 11px;
            margin: 20px;
            color: black;
            background: white;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid black;
            padding-bottom: 15px;
        }
        .company-name {
            font-size: 20px;
            font-weight: bold;
            color: black;
            margin-bottom: 8px;
        }
        .report-title {
            font-size: 16px;
            font-weight: bold;
            margin-bottom: 5px;
            color: black;
        }
        .report-info {
            font-size: 10px;
            color: black;
            margin-bottom: 15px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            font-size: 9px;
        }
        th, td {
            padding: 4px 6px;
            text-align: left;
            border: 1px solid black;
            color: black;
        }
        th {
            background-color: #f0f0f0;
            font-weight: bold;
            text-align: center;
            color: black;
        }
        .text-center {
            text-align: center;
        }
        .text-right {
            text-align: right;
        }
        .employee-info {
            font-weight: bold;
            font-size: 10px;
        }
        .role-badge {
            background-color: #e0e0e0;
            padding: 1px 4px;
            border-radius: 2px;
            font-size: 8px;
            text-align: center;
        }
        .attendance-stats {
            font-size: 8px;
            text-align: center;
        }
        .present { color: black; font-weight: bold; }
        .absent { color: black; }
        .late { color: black; }
        .total { font-weight: bold; }
        .rate { font-weight: bold; }
        .footer {
            margin-top: 20px;
            text-align: center;
            font-size: 9px;
            color: black;
            border-top: 1px solid black;
            padding-top: 10px;
        }
        .print-actions {
            text-align: center;
            margin-bottom: 20px;
            padding: 10px;
            background: #f5f5f5;
            border: 1px solid #ddd;
        }
        .btn {
            padding: 8px 16px;
            margin: 0 5px;
            background: #333;
            color: white;
            text-decoration: none;
            border: none;
            cursor: pointer;
            font-size: 12px;
        }
        .btn:hover {
            background: #555;
        }
    </style>
</head>
<body>
    <div class="print-actions no-print">
        <button onclick="window.print()" class="btn">🖨️ Print Report</button>
        <button onclick="window.close()" class="btn">❌ Close</button>
    </div>

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
                <th colspan="12" style="text-align: center;">Monthly Attendance (P/A/L)</th>
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
        <p>Report generated on {{ $generatedDate }}</p>
        <p>Total Employees: {{ count($attendanceData) }}</p>
        <p style="margin-top: 10px; font-size: 8px;">P = Present | A = Absent | L = Late</p>
    </div>

    <script>
        // Auto-print when page loads (optional)
        // window.onload = function() { window.print(); };
    </script>
</body>
</html>
