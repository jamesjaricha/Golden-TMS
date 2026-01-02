<?php

namespace App\Http\Controllers;

use App\Models\Complaint;
use App\Models\ActivityLog;
use App\Models\TicketReminder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // Base query for tickets
        $ticketQuery = Complaint::query();

        // Filter tickets based on role
        if ($user->role === 'user') {
            // Regular users see only their created tickets
            $ticketQuery->where('captured_by', $user->id);
        }
        // Support agents, managers, and admins see all tickets
        // This supports the collaborative ticket system where agents can take over

        // Calculate stats
        $totalTickets = $ticketQuery->count();
        $openTickets = (clone $ticketQuery)->whereIn('status', ['pending', 'assigned', 'in_progress'])->count();
        $resolvedTickets = (clone $ticketQuery)->where('status', 'resolved')->count();

        // Recent activity - last 10 activities
        // Non-admins only see their own activities
        $activityQuery = ActivityLog::with('user');

        if (!in_array($user->role, ['super_admin', 'manager'])) {
            $activityQuery->where('user_id', $user->id);
        }

        $recentActivity = $activityQuery->latest()
            ->limit(10)
            ->get();

        // Tickets by status
        $ticketsByStatus = (clone $ticketQuery)
            ->select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        // Pending task reminders for current user
        $myPendingTasks = TicketReminder::where('user_id', $user->id)
            ->where('status', 'pending')
            ->orderBy('reminder_datetime', 'asc')
            ->limit(5)
            ->with(['complaint', 'creator'])
            ->get();

        $totalPendingTasks = TicketReminder::where('user_id', $user->id)
            ->where('status', 'pending')
            ->count();

        $overdueTasks = TicketReminder::where('user_id', $user->id)
            ->where('status', 'pending')
            ->where('reminder_datetime', '<', now())
            ->count();

        return view('dashboard', compact(
            'totalTickets',
            'openTickets',
            'resolvedTickets',
            'recentActivity',
            'ticketsByStatus',
            'myPendingTasks',
            'totalPendingTasks',
            'overdueTasks'
        ));
    }

    /**
     * Get recent activity for AJAX refresh
     */
    public function getActivity()
    {
        $user = Auth::user();

        // Non-admins only see their own activities
        $activityQuery = ActivityLog::with('user');

        if (!in_array($user->role, ['super_admin', 'manager'])) {
            $activityQuery->where('user_id', $user->id);
        }

        $recentActivity = $activityQuery->latest()
            ->limit(10)
            ->get();

        return response()->json([
            'activities' => $recentActivity->map(function($activity) {
                return [
                    'id' => $activity->id,
                    'description' => $activity->description,
                    'user_name' => $activity->user->name ?? 'System',
                    'created_at' => $activity->created_at->diffForHumans(),
                    'type' => $activity->event_type,
                ];
            })
        ]);
    }
}
