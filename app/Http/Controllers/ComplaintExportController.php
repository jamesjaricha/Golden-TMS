<?php

namespace App\Http\Controllers;

use App\Models\Complaint;
use App\Exports\ComplaintsExport;
use App\Exports\MonthlyReportExport;
use App\Services\AuditLogService;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class ComplaintExportController extends Controller
{
    /**
     * Export filtered complaints to Excel
     */
    public function exportExcel(Request $request)
    {
        $query = $this->getFilteredQuery($request);
        $count = (clone $query)->count();

        // Log the export
        AuditLogService::logExport(
            'tickets',
            'Excel',
            $this->getFiltersArray($request),
            $count
        );

        $fileName = 'tickets_' . now()->format('Y-m-d_His') . '.xlsx';

        return Excel::download(
            new ComplaintsExport($query),
            $fileName
        );
    }

    /**
     * Export filtered complaints to PDF
     */
    public function exportPdf(Request $request)
    {
        $complaints = $this->getFilteredQuery($request)->get();

        // Log the export
        AuditLogService::logExport(
            'tickets',
            'PDF',
            $this->getFiltersArray($request),
            $complaints->count()
        );

        $pdf = Pdf::loadView('complaints.exports.pdf-list', [
            'complaints' => $complaints,
            'filters' => $this->getFiltersSummary($request),
            'generatedAt' => now(),
        ]);

        $fileName = 'tickets_' . now()->format('Y-m-d_His') . '.pdf';

        return $pdf->download($fileName);
    }

    /**
     * Print view for single ticket
     */
    public function print(Complaint $complaint)
    {
        // Log viewing for print
        AuditLogService::logView($complaint);

        return view('complaints.exports.print', compact('complaint'));
    }

    /**
     * Export single ticket to PDF
     */
    public function exportTicketPdf(Complaint $complaint)
    {
        // Log the single ticket export
        AuditLogService::logExport(
            'single_ticket',
            'PDF',
            ['ticket_number' => $complaint->ticket_number],
            1
        );

        $pdf = Pdf::loadView('complaints.exports.ticket-pdf', [
            'complaint' => $complaint,
            'generatedAt' => now(),
        ]);

        $fileName = 'ticket_' . $complaint->ticket_number . '.pdf';

        return $pdf->download($fileName);
    }

    /**
     * Generate monthly report
     */
    public function monthlyReport(Request $request)
    {
        $month = $request->input('month', now()->format('Y-m'));
        $date = Carbon::parse($month . '-01');

        $startDate = $date->copy()->startOfMonth();
        $endDate = $date->copy()->endOfMonth();

        $data = [
            'period' => $date->format('F Y'),
            'startDate' => $startDate,
            'endDate' => $endDate,
            'total' => Complaint::whereBetween('created_at', [$startDate, $endDate])->count(),
            'byStatus' => Complaint::whereBetween('created_at', [$startDate, $endDate])
                ->selectRaw('status, count(*) as count')
                ->groupBy('status')
                ->pluck('count', 'status'),
            'byPriority' => Complaint::whereBetween('created_at', [$startDate, $endDate])
                ->selectRaw('priority, count(*) as count')
                ->groupBy('priority')
                ->pluck('count', 'priority'),
            'byDepartment' => Complaint::whereBetween('created_at', [$startDate, $endDate])
                ->selectRaw('department, count(*) as count')
                ->groupBy('department')
                ->pluck('count', 'department'),
            'resolved' => Complaint::whereBetween('created_at', [$startDate, $endDate])
                ->where('status', 'resolved')
                ->count(),
            'pending' => Complaint::whereBetween('created_at', [$startDate, $endDate])
                ->where('status', 'pending')
                ->count(),
            'escalated' => Complaint::whereBetween('created_at', [$startDate, $endDate])
                ->where('status', 'escalated')
                ->count(),
        ];

        $format = $request->input('format', 'excel');

        // Log the monthly report generation
        AuditLogService::logReportGenerated('monthly_report', [
            'month' => $month,
            'format' => $format,
            'total_tickets' => $data['total'],
        ]);

        if ($format === 'pdf') {
            $pdf = Pdf::loadView('complaints.exports.monthly-report', array_merge($data, [
                'generatedAt' => now(),
            ]));

            return $pdf->download('report_' . $date->format('Y-m') . '.pdf');
        }

        // Excel export
        return Excel::download(
            new MonthlyReportExport($startDate, $endDate, $data),
            'report_' . $date->format('Y-m') . '.xlsx'
        );
    }

    /**
     * Get filtered query based on request parameters
     */
    private function getFilteredQuery(Request $request)
    {
        $query = Complaint::with(['capturedBy', 'assignedTo']);

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('ticket_number', 'like', "%{$search}%")
                    ->orWhere('policy_number', 'like', "%{$search}%")
                    ->orWhere('full_name', 'like', "%{$search}%")
                    ->orWhere('phone_number', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->filled('priority')) {
            $query->where('priority', $request->input('priority'));
        }

        if ($request->filled('department')) {
            $query->where('department', $request->input('department'));
        }

        return $query->orderBy('created_at', 'desc');
    }

    /**
     * Get filters summary for PDF
     */
    private function getFiltersSummary(Request $request)
    {
        $filters = [];

        if ($request->filled('search')) {
            $filters[] = 'Search: ' . $request->input('search');
        }

        if ($request->filled('status')) {
            $filters[] = 'Status: ' . ucwords(str_replace('_', ' ', $request->input('status')));
        }

        if ($request->filled('priority')) {
            $filters[] = 'Priority: ' . ucfirst($request->input('priority'));
        }

        if ($request->filled('department')) {
            $filters[] = 'Department: ' . $request->input('department');
        }

        return empty($filters) ? 'All Tickets' : implode(', ', $filters);
    }

    /**
     * Get filters as array for audit logging
     */
    private function getFiltersArray(Request $request): array
    {
        $filters = [];

        if ($request->filled('search')) {
            $filters['search'] = $request->input('search');
        }

        if ($request->filled('status')) {
            $filters['status'] = $request->input('status');
        }

        if ($request->filled('priority')) {
            $filters['priority'] = $request->input('priority');
        }

        if ($request->filled('department')) {
            $filters['department'] = $request->input('department');
        }

        return $filters;
    }
}
