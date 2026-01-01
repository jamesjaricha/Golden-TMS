<?php

namespace App\Http\Controllers;

use App\Models\Complaint;
use App\Models\Branch;
use App\Models\User;
use App\Models\Employer;
use App\Models\PaymentMethod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class ReportController extends Controller
{
    /**
     * Show the report wizard
     */
    public function index()
    {
        // Get all active options for filters
        $branches = Branch::orderBy('name')->get();
        $users = User::orderBy('name')->get();
        $employers = Employer::orderBy('name')->get();
        $paymentMethods = PaymentMethod::orderBy('name')->get();

        return view('reports.wizard', compact('branches', 'users', 'employers', 'paymentMethods'));
    }

    /**
     * Generate custom report based on wizard selections
     */
    public function generate(Request $request)
    {
        $request->validate([
            'report_type' => 'required|in:tickets,performance,department,agent,branch,employer,payment_method,status,priority,timeframe',
            'date_from' => 'nullable|date',
            'date_to' => 'nullable|date|after_or_equal:date_from',
            'branch_id' => 'nullable|exists:branches,id',
            'assigned_to' => 'nullable|exists:users,id',
            'status' => 'nullable|in:pending,assigned,in_progress,partial_closed,resolved,closed,escalated',
            'priority' => 'nullable|in:low,medium,high,urgent',
            'department' => 'nullable|in:billing,technical,customer_service,general',
            'employer_id' => 'nullable|exists:employers,id',
            'payment_method_id' => 'nullable|exists:payment_methods,id',
            'format' => 'required|in:view,excel,pdf',
        ]);

        // Build query based on filters
        $query = Complaint::query()->with(['capturedBy', 'assignedTo', 'branch', 'employer', 'paymentMethod']);

        // Apply date filters
        if ($request->date_from) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->date_to) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // Apply other filters
        if ($request->branch_id) {
            $query->where('branch_id', $request->branch_id);
        }
        if ($request->assigned_to) {
            $query->where('assigned_to', $request->assigned_to);
        }
        if ($request->status) {
            $query->where('status', $request->status);
        }
        if ($request->priority) {
            $query->where('priority', $request->priority);
        }
        if ($request->department) {
            $query->where('department', $request->department);
        }
        if ($request->employer_id) {
            $query->where('employer_id', $request->employer_id);
        }
        if ($request->payment_method_id) {
            $query->where('payment_method_id', $request->payment_method_id);
        }

        // Apply ordering
        $query->latest();

        // Handle different output formats
        if ($request->format === 'excel') {
            // For Excel, pass the query builder directly (before calling get())
            return $this->exportToExcel($query, $request->all());
        }

        // For PDF and view, we need the collection
        $complaints = $query->get();

        // Generate statistics based on report type
        $statistics = $this->generateStatistics($complaints, $request->report_type);

        if ($request->format === 'pdf') {
            return $this->exportToPdf($complaints, $statistics, $request->all());
        }

        // Default: return view
        return view('reports.results', [
            'complaints' => $complaints,
            'statistics' => $statistics,
            'filters' => $request->all(),
            'reportType' => $request->report_type,
        ]);
    }

    /**
     * Generate statistics based on report type
     */
    private function generateStatistics($complaints, $reportType)
    {
        $stats = [
            'total' => $complaints->count(),
            'by_status' => $complaints->groupBy('status')->map->count(),
            'by_priority' => $complaints->groupBy('priority')->map->count(),
            'by_department' => $complaints->groupBy('department')->map->count(),
            'by_branch' => $complaints->groupBy('branch.name')->map->count(),
            'by_agent' => $complaints->groupBy('assignedTo.name')->map->count(),
            'by_employer' => $complaints->groupBy('employer.name')->map->count(),
            'by_payment_method' => $complaints->groupBy('paymentMethod.name')->map->count(),
        ];

        // Calculate average resolution time
        $resolvedTickets = $complaints->whereIn('status', ['resolved', 'closed']);
        if ($resolvedTickets->count() > 0) {
            $totalResolutionTime = 0;
            foreach ($resolvedTickets as $ticket) {
                if ($ticket->updated_at && $ticket->created_at) {
                    $totalResolutionTime += $ticket->created_at->diffInHours($ticket->updated_at);
                }
            }
            $stats['avg_resolution_hours'] = round($totalResolutionTime / $resolvedTickets->count(), 2);
        } else {
            $stats['avg_resolution_hours'] = 0;
        }

        // Add time-based grouping
        $stats['by_month'] = $complaints->groupBy(function ($complaint) {
            return $complaint->created_at->format('Y-m');
        })->map->count();

        $stats['by_day'] = $complaints->groupBy(function ($complaint) {
            return $complaint->created_at->format('Y-m-d');
        })->map->count();

        return $stats;
    }

    /**
     * Export report to Excel
     */
    private function exportToExcel($query, $filters)
    {
        $export = new \App\Exports\ComplaintsExport($query);
        $filename = 'custom_report_' . now()->format('Y-m-d_His') . '.xlsx';

        return \Maatwebsite\Excel\Facades\Excel::download($export, $filename);
    }

    /**
     * Export report to PDF
     */
    private function exportToPdf($complaints, $statistics, $filters)
    {
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('reports.pdf', [
            'complaints' => $complaints,
            'statistics' => $statistics,
            'filters' => $filters,
        ]);

        $filename = 'custom_report_' . now()->format('Y-m-d_His') . '.pdf';

        return $pdf->download($filename);
    }
}
