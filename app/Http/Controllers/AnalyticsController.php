<?php

namespace App\Http\Controllers;

use App\Models\Complaint;
use App\Models\User;
use App\Models\ActivityLog;
use Illuminate\Support\Facades\DB;

class AnalyticsController extends Controller
{
    public function index()
    {
        // Overall Statistics
        $totalTickets = Complaint::count();
        $pendingTickets = Complaint::where('status', 'pending')->count();
        $inProgressTickets = Complaint::where('status', 'in_progress')->count();
        $resolvedTickets = Complaint::where('status', 'resolved')->count();
        $urgentTickets = Complaint::where('priority', 'urgent')
            ->whereIn('status', ['pending', 'assigned', 'in_progress'])
            ->count();

        $highPriorityTickets = Complaint::where('priority', 'high')
            ->whereIn('status', ['pending', 'assigned', 'in_progress'])
            ->count();

        $ticketsToday = Complaint::whereDate('created_at', today())->count();
        $ticketsThisWeek = Complaint::whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->count();
        $ticketsThisMonth = Complaint::whereMonth('created_at', now()->month)->count();

        // Average Resolution Time
        $avgResolutionTime = Complaint::whereNotNull('resolved_at')
            ->get()
            ->avg(function ($complaint) {
                return $complaint->created_at->diffInHours($complaint->resolved_at);
            });

        // Tickets by Department
        $ticketsByDepartment = Complaint::select('department', DB::raw('count(*) as count'))
            ->groupBy('department')
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

        // Agent Performance
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

        // Recent Activity
        $recentActivity = ActivityLog::with('user')
            ->latest()
            ->limit(10)
            ->get();

        // Monthly Trend (last 6 months)
        $monthlyStats = Complaint::select(
                DB::raw('strftime("%Y-%m", created_at) as month'),
                DB::raw('count(*) as count')
            )
            ->where('created_at', '>=', now()->subMonths(6))
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        return view('analytics.index', compact(
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
            'recentActivity',
            'monthlyStats'
        ));
    }
}
