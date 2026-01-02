<?php

namespace App\Http\Controllers;

use App\Models\Complaint;
use App\Models\User;
use App\Models\ActivityLog;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class AnalyticsController extends Controller
{
    public function index()
    {
        // PERFORMANCE: Cache analytics data for 5 minutes to reduce database load
        $cacheKey = 'analytics_dashboard_data';
        $cacheTTL = 300; // 5 minutes

        $analyticsData = Cache::remember($cacheKey, $cacheTTL, function () {
            // Overall Statistics - Single optimised query for counts
            $statusCounts = Complaint::query()
                ->selectRaw('
                    COUNT(*) as total,
                    SUM(CASE WHEN status = "pending" THEN 1 ELSE 0 END) as pending,
                    SUM(CASE WHEN status = "in_progress" THEN 1 ELSE 0 END) as in_progress,
                    SUM(CASE WHEN status = "resolved" THEN 1 ELSE 0 END) as resolved,
                    SUM(CASE WHEN priority = "urgent" AND status IN ("pending", "assigned", "in_progress") THEN 1 ELSE 0 END) as urgent,
                    SUM(CASE WHEN priority = "high" AND status IN ("pending", "assigned", "in_progress") THEN 1 ELSE 0 END) as high_priority
                ')
                ->first();

            $totalTickets = $statusCounts->total ?? 0;
            $pendingTickets = $statusCounts->pending ?? 0;
            $inProgressTickets = $statusCounts->in_progress ?? 0;
            $resolvedTickets = $statusCounts->resolved ?? 0;
            $urgentTickets = $statusCounts->urgent ?? 0;
            $highPriorityTickets = $statusCounts->high_priority ?? 0;

            // Time-based counts
            $ticketsToday = Complaint::whereDate('created_at', today())->count();
            $ticketsThisWeek = Complaint::whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->count();
            $ticketsThisMonth = Complaint::whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->count();

            // PERFORMANCE: Calculate average resolution time in database instead of PHP
            // Using database-level calculation is much more efficient
            $avgResolutionTime = Complaint::whereNotNull('resolved_at')
                ->selectRaw('AVG((JULIANDAY(resolved_at) - JULIANDAY(created_at)) * 24) as avg_hours')
                ->value('avg_hours') ?? 0;

            // Tickets by Department - efficient JOIN query
            $ticketsByDepartment = Complaint::join('departments', 'complaints.department_id', '=', 'departments.id')
                ->select('departments.name as department', DB::raw('count(*) as count'))
                ->whereNull('complaints.deleted_at')
                ->groupBy('departments.name')
                ->pluck('count', 'department')
                ->toArray();

            // Tickets by Status
            $ticketsByStatus = Complaint::select('status', DB::raw('count(*) as count'))
                ->groupBy('status')
                ->pluck('count', 'status')
                ->toArray();

            // Tickets by Priority
            $ticketsByPriority = Complaint::select('priority', DB::raw('count(*) as count'))
                ->groupBy('priority')
                ->pluck('count', 'priority')
                ->toArray();

            // Agent Performance - efficient with withCount
            $agentPerformance = User::whereIn('role', ['support_agent', 'manager'])
                ->withCount([
                    'assignedComplaints',
                    'assignedComplaints as resolved_count' => function ($query) {
                        $query->where('status', 'resolved');
                    }
                ])
                ->get()
                ->map(function ($agent) {
                    $totalAssigned = $agent->assigned_complaints_count;
                    $resolved = $agent->resolved_count;

                    return [
                        'id' => $agent->id,
                        'name' => $agent->name,
                        'role' => $agent->role,
                        'total' => $totalAssigned,
                        'resolved' => $resolved,
                        'pending' => $totalAssigned - $resolved,
                        'resolution_rate' => $totalAssigned > 0 ? round(($resolved / $totalAssigned) * 100, 1) : 0
                    ];
                });

            // Monthly Trend (last 6 months) - SQLite compatible
            $monthlyStats = Complaint::select(
                    DB::raw('strftime("%Y-%m", created_at) as month'),
                    DB::raw('count(*) as count')
                )
                ->where('created_at', '>=', now()->subMonths(6))
                ->groupBy('month')
                ->orderBy('month')
                ->get();

            return compact(
                'totalTickets',
                'pendingTickets',
                'inProgressTickets',
                'resolvedTickets',
                'urgentTickets',
                'highPriorityTickets',
                'ticketsToday',
                'ticketsThisWeek',
                'ticketsThisMonth',
                'avgResolutionTime',
                'ticketsByDepartment',
                'ticketsByStatus',
                'ticketsByPriority',
                'agentPerformance',
                'monthlyStats'
            );
        });

        // Recent Activity - not cached as it changes frequently
        $recentActivity = ActivityLog::with('user')
            ->latest()
            ->limit(10)
            ->get();

        return view('analytics.index', array_merge($analyticsData, compact('recentActivity')));
    }
}
