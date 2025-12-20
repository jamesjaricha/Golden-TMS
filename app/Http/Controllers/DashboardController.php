<?php

namespace App\Http\Controllers;

use App\Models\Complaint;
use App\Models\ActivityLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // Base query for tickets
        $ticketQuery = Complaint::query();

        // Filter tickets based on role
        if ($user->role === 'support_agent') {
            $ticketQuery->where('assigned_to', $user->id);
        } elseif ($user->role === 'user') {
            $ticketQuery->where('captured_by', $user->id);
        }
        // Admins and managers see all tickets

        // Debug: Log the query
        Log::info('Dashboard Query', [
            'user_id' => $user->id,
            'user_role' => $user->role,
            'user_name' => $user->name,
            'query_sql' => $ticketQuery->toSql(),
            'total_tickets_in_db' => Complaint::count(),
        ]);

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
