<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Http\Request;

class AuditLogController extends Controller
{
    /**
     * Display a listing of audit logs.
     */
    public function index(Request $request)
    {
        $query = AuditLog::with('user')->latest();

        // Check if any filters are applied
        $hasFilters = $request->filled(['category', 'action', 'user_id', 'status', 'date_from', 'date_to', 'search']);

        // If no filters applied, default to today's logs only
        if (!$hasFilters) {
            $query->whereDate('created_at', today());
        } else {
            // Filter by category
            if ($request->filled('category')) {
                $query->where('action_category', $request->category);
            }

            // Filter by action
            if ($request->filled('action')) {
                $query->where('action', $request->action);
            }

            // Filter by user
            if ($request->filled('user_id')) {
                $query->where('user_id', $request->user_id);
            }

            // Filter by status
            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }

            // Filter by date range
            if ($request->filled('date_from')) {
                $query->whereDate('created_at', '>=', $request->date_from);
            }
            if ($request->filled('date_to')) {
                $query->whereDate('created_at', '<=', $request->date_to);
            }

            // Search in description
            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('description', 'like', "%{$search}%")
                      ->orWhere('auditable_identifier', 'like', "%{$search}%")
                      ->orWhere('user_name', 'like', "%{$search}%")
                      ->orWhere('ip_address', 'like', "%{$search}%");
                });
            }
        }

        $logs = $query->paginate(25)->withQueryString();

        // Get filter options
        $categories = AuditLog::distinct()->pluck('action_category')->filter()->sort();
        $actions = AuditLog::distinct()->pluck('action')->filter()->sort();
        $users = User::orderBy('name')->get(['id', 'name']);

        return view('audit-logs.index', compact('logs', 'categories', 'actions', 'users'));
    }

    /**
     * Display the specified audit log.
     */
    public function show(AuditLog $auditLog)
    {
        return view('audit-logs.show', compact('auditLog'));
    }

    /**
     * Get audit logs for a specific model (API endpoint).
     */
    public function forModel(Request $request)
    {
        $request->validate([
            'type' => 'required|string',
            'id' => 'required|integer',
        ]);

        $logs = AuditLog::where('auditable_type', $request->type)
            ->where('auditable_id', $request->id)
            ->with('user')
            ->latest()
            ->get();

        return response()->json($logs);
    }

    /**
     * Export audit logs to CSV.
     */
    public function export(Request $request)
    {
        $query = AuditLog::with('user')->latest();

        // Apply same filters as index
        if ($request->filled('category')) {
            $query->where('action_category', $request->category);
        }
        if ($request->filled('action')) {
            $query->where('action', $request->action);
        }
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $logs = $query->get();

        // Log this export
        \App\Services\AuditLogService::logExport(
            'audit_logs',
            'CSV',
            $request->only(['category', 'action', 'user_id', 'date_from', 'date_to']),
            $logs->count()
        );

        $filename = 'audit_logs_' . now()->format('Y-m-d_His') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($logs) {
            $file = fopen('php://output', 'w');

            // Header row
            fputcsv($file, [
                'Date/Time',
                'User',
                'Role',
                'Action',
                'Category',
                'Description',
                'Entity',
                'IP Address',
                'Device',
                'Status',
            ]);

            foreach ($logs as $log) {
                fputcsv($file, [
                    $log->created_at->format('Y-m-d H:i:s'),
                    $log->user_name ?? 'System',
                    $log->user_role ?? '-',
                    $log->action,
                    $log->action_category,
                    $log->description,
                    $log->auditable_identifier ?? '-',
                    $log->ip_address ?? '-',
                    $log->device_type . '/' . $log->browser,
                    $log->status,
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
