<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class LectureAttendanceExport implements FromCollection, WithHeadings, WithStyles
{
    protected $attendanceData;

    public function __construct($attendanceData)
    {
        $this->attendanceData = $attendanceData;
    }

    public function collection()
    {
        return $this->attendanceData;
    }

    public function headings(): array
    {
        return [
            'Student ID',
            'Name',
            'Program',
            'Check-in Time',
            'Check-in Method',
            'Comment'
        ];
    }

    public function styles(Worksheet $sheet)
    {
        // Style the first row
        return [
            1 => [
                'font' => ['bold' => true],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'DDDDDD']
                ]
            ]
        ];
    }
}
