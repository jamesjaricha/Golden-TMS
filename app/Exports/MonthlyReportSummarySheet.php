<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class MonthlyReportSummarySheet implements FromCollection, WithTitle, WithHeadings, WithStyles
{
    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function collection()
    {
        $rows = collect();

        // Summary stats
        $rows->push(['Total Tickets', $this->data['total']]);
        $rows->push(['Resolved', $this->data['resolved']]);
        $rows->push(['Pending', $this->data['pending']]);
        $rows->push(['Escalated', $this->data['escalated']]);
        $rows->push(['']); // Empty row

        // By Status
        $rows->push(['Status Breakdown', 'Count']);
        foreach ($this->data['byStatus'] as $status => $count) {
            $rows->push([ucwords(str_replace('_', ' ', $status)), $count]);
        }
        $rows->push(['']); // Empty row

        // By Priority
        $rows->push(['Priority Breakdown', 'Count']);
        foreach ($this->data['byPriority'] as $priority => $count) {
            $rows->push([ucfirst($priority), $count]);
        }
        $rows->push(['']); // Empty row

        // By Department
        $rows->push(['Department Breakdown', 'Count']);
        foreach ($this->data['byDepartment'] as $department => $count) {
            $rows->push([$department, $count]);
        }

        return $rows;
    }

    public function title(): string
    {
        return 'Summary';
    }

    public function headings(): array
    {
        return ['Metric', 'Value'];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true, 'size' => 12]],
        ];
    }
}
