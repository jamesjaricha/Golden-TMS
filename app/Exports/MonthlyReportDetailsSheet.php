<?php

namespace App\Exports;

use App\Models\Complaint;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class MonthlyReportDetailsSheet implements FromQuery, WithTitle, WithHeadings, WithMapping, ShouldAutoSize
{
    protected $startDate;
    protected $endDate;

    public function __construct($startDate, $endDate)
    {
        $this->startDate = $startDate;
        $this->endDate = $endDate;
    }

    public function query()
    {
        return Complaint::query()
            ->with(['capturedBy', 'assignedTo'])
            ->whereBetween('created_at', [$this->startDate, $this->endDate])
            ->orderBy('created_at', 'desc');
    }

    public function title(): string
    {
        return 'Details';
    }

    public function headings(): array
    {
        return [
            'Ticket Number',
            'Client Name',
            'Department',
            'Status',
            'Priority',
            'Assigned To',
            'Created Date',
            'Resolution Status',
        ];
    }

    public function map($complaint): array
    {
        return [
            $complaint->ticket_number,
            $complaint->full_name,
            $complaint->department,
            ucwords(str_replace('_', ' ', $complaint->status)),
            ucfirst($complaint->priority),
            $complaint->assignedTo?->name ?? 'Unassigned',
            $complaint->created_at->format('Y-m-d H:i'),
            $complaint->resolved_at ? 'Resolved' : 'Open',
        ];
    }
}
