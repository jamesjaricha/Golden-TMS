<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class MonthlyReportExport implements WithMultipleSheets
{
    protected $startDate;
    protected $endDate;
    protected $data;

    public function __construct($startDate, $endDate, $data)
    {
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->data = $data;
    }

    public function sheets(): array
    {
        return [
            new MonthlyReportSummarySheet($this->data),
            new MonthlyReportDetailsSheet($this->startDate, $this->endDate),
        ];
    }
}
