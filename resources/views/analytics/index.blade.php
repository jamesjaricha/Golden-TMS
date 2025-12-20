<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-semibold text-2xl text-apple-gray-900 leading-tight">
                    {{ __('Analytics Dashboard') }}
                </h2>
                <p class="text-sm text-apple-gray-500 mt-1">Performance metrics and insights</p>
            </div>
            <div class="text-sm text-apple-gray-600">
                {{ now()->format('l, F d, Y') }}
            </div>
        </div>
    </x-slot>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Overview Stats -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
            <!-- Total Tickets -->
            <div class="analytics-card bg-white rounded-apple-lg shadow-apple p-6 animate-fade-in">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="metric-label">Total Tickets</p>
                        <p class="metric-value metric-animate" data-metric="total">{{ $totalTickets }}</p>
                        <p class="text-xs text-apple-gray-500 mt-1">All time</p>
                    </div>
                    <div class="metric-icon bg-blue-50 text-apple-blue">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Pending -->
            <div class="analytics-card bg-white rounded-apple-lg shadow-apple p-6 animate-fade-in">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="metric-label">Pending</p>
                        <p class="metric-value metric-animate text-yellow-600" data-metric="pending">{{ $pendingTickets }}</p>
                        <p class="text-xs text-apple-gray-500 mt-1">Awaiting action</p>
                    </div>
                    <div class="metric-icon bg-yellow-50 text-yellow-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- In Progress -->
            <div class="analytics-card bg-white rounded-apple-lg shadow-apple p-6 animate-fade-in">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="metric-label">In Progress</p>
                        <p class="metric-value metric-animate text-indigo-600" data-metric="inprogress">{{ $inProgressTickets }}</p>
                        <p class="text-xs text-apple-gray-500 mt-1">Being handled</p>
                    </div>
                    <div class="metric-icon bg-indigo-50 text-indigo-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Resolved -->
            <div class="analytics-card bg-white rounded-apple-lg shadow-apple p-6 animate-fade-in">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="metric-label">Resolved</p>
                        <p class="metric-value metric-animate text-green-600" data-metric="resolved">{{ $resolvedTickets }}</p>
                        <p class="text-xs text-apple-gray-500 mt-1">Completed</p>
                    </div>
                    <div class="metric-icon bg-green-50 text-green-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- Additional Metrics -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
            <!-- Avg Resolution Time -->
            <div class="bg-gradient-to-br from-apple-blue to-blue-600 rounded-apple-lg shadow-apple-lg p-6 text-white">
                <h3 class="text-sm font-medium text-blue-100 mb-2">Avg. Resolution Time</h3>
                <p class="text-3xl font-bold">{{ round($avgResolutionTime) }}h</p>
                <p class="text-sm text-blue-100 mt-1">Average hours to resolve</p>
            </div>

            <!-- High Priority Alerts -->
            <div class="bg-gradient-to-br from-orange-500 to-red-500 rounded-apple-lg shadow-apple-lg p-6 text-white">
                <h3 class="text-sm font-medium text-orange-100 mb-2">Priority Alerts</h3>
                <p class="text-3xl font-bold">{{ $urgentTickets + $highPriorityTickets }}</p>
                <p class="text-sm text-orange-100 mt-1">{{ $urgentTickets }} Urgent, {{ $highPriorityTickets }} High</p>
            </div>

            <!-- This Month -->
            <div class="bg-gradient-to-br from-purple-500 to-indigo-600 rounded-apple-lg shadow-apple-lg p-6 text-white">
                <h3 class="text-sm font-medium text-purple-100 mb-2">This Month</h3>
                <p class="text-3xl font-bold">{{ $ticketsThisMonth }}</p>
                <p class="text-sm text-purple-100 mt-1">{{ $ticketsThisWeek }} this week, {{ $ticketsToday }} today</p>
            </div>
        </div>

        <!-- Agent Performance & Recent Activity -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mt-6">
            <!-- Agent Performance -->
            <div class="bg-white rounded-apple-lg shadow-apple p-6">
                <h3 class="text-lg font-semibold text-apple-gray-900 mb-4">Agent Performance</h3>
                <div class="space-y-4">
                    @forelse($agentPerformance as $agent)
                        <div class="dept-stat border-b border-apple-gray-100 pb-4 last:border-0 last:pb-0">
                            <div class="flex items-center justify-between mb-3">
                                <div class="flex items-center flex-1 min-w-0">
                                    <div class="metric-icon bg-gradient-to-br from-apple-blue to-blue-600 text-white text-sm font-semibold mr-3 flex-shrink-0">
                                        {{ strtoupper(substr($agent['name'], 0, 2)) }}
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-medium text-apple-gray-900 truncate">{{ $agent['name'] }}</p>
                                        <p class="text-xs text-apple-gray-500 mt-1">{{ $agent['total'] }} total tickets</p>
                                    </div>
                                </div>
                                <span class="text-lg font-bold text-apple-blue ml-3 flex-shrink-0">{{ $agent['resolution_rate'] }}%</span>
                            </div>
                            <div class="progress-bar mb-3">
                                <div class="progress-fill" data-width="{{ $agent['resolution_rate'] }}" style="width: 0%"></div>
                            </div>
                            <div class="grid grid-cols-2 gap-2 text-xs">
                                <div class="bg-green-50 rounded px-2 py-1.5 text-center">
                                    <span class="text-green-600 font-semibold">✓ {{ $agent['resolved'] }}</span>
                                    <span class="text-green-700 ml-1">Resolved</span>
                                </div>
                                <div class="bg-yellow-50 rounded px-2 py-1.5 text-center">
                                    <span class="text-yellow-600 font-semibold">{{ $agent['pending'] }}</span>
                                    <span class="text-yellow-700 ml-1">Pending</span>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="empty-state">
                            <p class="text-sm text-apple-gray-500">No agent data available</p>
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Recent Activity -->
            <div class="bg-white rounded-apple-lg shadow-apple p-6">
                <h3 class="text-lg font-semibold text-apple-gray-900 mb-4">Recent Activity</h3>
                <div class="activity-list max-h-96 overflow-y-auto" id="activity-list">
                    @forelse($recentActivity as $activity)
                        <div class="activity-item flex items-start p-3 rounded-lg">
                            <div class="metric-icon bg-apple-gray-100 text-apple-gray-700 text-xs font-semibold">
                                {{ strtoupper(substr($activity->user->name ?? 'U', 0, 1)) }}
                            </div>
                            <div class="ml-3 flex-1">
                                <p class="text-sm font-medium text-apple-gray-900">{{ $activity->description }}</p>
                                <p class="text-xs text-apple-gray-500 mt-1">
                                    {{ $activity->user->name ?? 'System' }} • {{ $activity->created_at->diffForHumans() }}
                                </p>
                            </div>
                        </div>
                    @empty
                        <div class="empty-state">
                            <p class="text-sm text-apple-gray-500">No recent activity</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <link rel="stylesheet" href="{{ asset('css/analytics.css') }}">
    <script src="{{ asset('js/analytics.js') }}"></script>
</x-app-layout>
