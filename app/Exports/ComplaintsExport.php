<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use Illuminate\Database\Eloquent\Builder;

class ComplaintsExport implements FromQuery, WithHeadings, WithMapping, WithStyles, ShouldAutoSize, WithColumnFormatting
{
    protected $query;

    public function __construct(Builder $query)
    {
        $this->query = $query;
    }

    public function query()
    {
        return $this->query;
    }

    public function headings(): array
    {
        return [
            'Ticket Number',
            'Policy Number',
            'Client Name',
            'Phone Number',
            'Location',
            'Branch Visited',
            'Department',
            'Complaint',
            'Status',
            'Priority',
            'Captured By',
            'Assigned To',
            'Created Date',
            'Resolved Date',
            'Closed Date',
        ];
    }

    public function map($complaint): array
    {
        return [
            $complaint->ticket_number,
            $complaint->policy_number,
            $complaint->full_name,
            "'" . $complaint->phone_number, // Prefix with single quote to force text format
            $complaint->location,
            $complaint->visited_branch,
            $complaint->department->name ?? 'N/A',
            $complaint->complaint_text,
            ucwords(str_replace('_', ' ', $complaint->status)),
            ucfirst($complaint->priority),
            $complaint->capturedBy?->name ?? 'N/A',
            $complaint->assignedTo?->name ?? 'Unassigned',
            $complaint->created_at->format('Y-m-d H:i'),
            $complaint->resolved_at?->format('Y-m-d H:i') ?? 'N/A',
            $complaint->closed_at?->format('Y-m-d H:i') ?? 'N/A',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => ['bold' => true, 'size' => 12],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'E5E7EB']
                ],
            ],
        ];
    }

    public function columnFormats(): array
    {
        return [
            'D' => NumberFormat::FORMAT_TEXT, // Phone Number column
        ];
    }
}
