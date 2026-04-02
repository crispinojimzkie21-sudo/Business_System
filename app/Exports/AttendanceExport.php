<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use App\Models\User;

class AttendanceExport implements FromView, WithColumnWidths, WithStyles, WithTitle
{
    protected $year;
    protected $month;

    public function __construct($year, $month)
    {
        $this->year = $year;
        $this->month = $month;
    }

    public function view(): View
    {
        // Get employees and their attendance data
        $employees = User::whereIn('role', ['employee', 'admin'])->orderBy('name')->get();
        $months = [
            1 => 'Jan', 2 => 'Feb', 3 => 'Mar', 4 => 'Apr', 5 => 'May', 6 => 'Jun',
            7 => 'Jul', 8 => 'Aug', 9 => 'Sep', 10 => 'Oct', 11 => 'Nov', 12 => 'Dec'
        ];
        
        $attendanceData = [];
        foreach ($employees as $employee) {
            $yearlyStats = app('App\Http\Controllers\SuperAdminController')->getYearlyAttendanceStats($employee->id, $this->year);
            
            $attendanceData[] = [
                'name' => $employee->name,
                'email' => $employee->email,
                'role' => ucfirst($employee->role),
                'position' => $employee->position ?? 'N/A',
                'monthly_stats' => $yearlyStats
            ];
        }
        
        return view('exports.attendance-excel', [
            'attendanceData' => $attendanceData,
            'year' => $this->year,
            'month' => $this->month,
            'months' => $months,
            'generatedDate' => now()->format('F j, Y g:i A')
        ]);
    }

    public function columnWidths(): array
    {
        return [
            'A' => 25, // Name
            'B' => 30, // Email
            'C' => 15, // Role
            'D' => 20, // Position
            'E' => 8,  // Jan
            'F' => 8,  // Feb
            'G' => 8,  // Mar
            'H' => 8,  // Apr
            'I' => 8,  // May
            'J' => 8,  // Jun
            'K' => 8,  // Jul
            'L' => 8,  // Aug
            'M' => 8,  // Sep
            'N' => 8,  // Oct
            'O' => 8,  // Nov
            'P' => 8,  // Dec
            'Q' => 8,  // Total
            'R' => 8,  // Rate
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            // Style the header row
            1 => [
                'font' => ['bold' => true, 'size' => 12],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '4F46E5']
                ],
                'font' => ['color' => ['rgb' => 'FFFFFF']]
            ],
            // Style alternating rows
            'A2:R1000' => [
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                        'color' => ['rgb' => 'E5E7EB']
                    ]
                ]
            ]
        ];
    }

    public function title(): string
    {
        return "Attendance Report {$this->year}";
    }
}
