<?php

namespace App\Http\Controllers;

use App\Models\Complaint;
use App\Models\ActivityLog;
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

        return view('dashboard', compact(
            'totalTickets',
            'openTickets',
            'resolvedTickets',
            'recentActivity',
            'ticketsByStatus'
        ));
    }
}
