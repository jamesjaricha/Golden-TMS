<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-semibold text-2xl text-apple-gray-900 leading-tight">
                    {{ __('Dashboard') }}
                </h2>
                <p class="text-sm text-apple-gray-500 mt-1">Welcome back, {{ Auth::user()->name }}</p>
            </div>
        </div>
    </x-slot>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Welcome Card -->
        <div class="mb-6 animate-fade-in">
            <div class="bg-gradient-to-br from-apple-blue to-blue-600 rounded-apple-lg shadow-apple-lg p-8 text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-2xl font-bold mb-2">Good to see you! 👋</h3>
                        <p class="text-blue-100 text-sm">You're all set up and ready to manage your tickets</p>
                    </div>
                    <div class="hidden md:block">
                        <svg class="w-24 h-24 opacity-20" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M2 11a1 1 0 011-1h2a1 1 0 011 1v5a1 1 0 01-1 1H3a1 1 0 01-1-1v-5zM8 7a1 1 0 011-1h2a1 1 0 011 1v9a1 1 0 01-1 1H9a1 1 0 01-1-1V7zM14 4a1 1 0 011-1h2a1 1 0 011 1v12a1 1 0 01-1 1h-2a1 1 0 01-1-1V4z" />
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- Stats Grid -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
            <!-- Stat Card 1 -->
            <a href="{{ route('complaints.index') }}" class="block stat-card bg-white rounded-apple-lg shadow-apple p-6 hover:shadow-apple-md animate-slide-up transition-all duration-200 cursor-pointer">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-apple-gray-500">Total Tickets</p>
                        <p class="text-3xl font-bold text-apple-gray-900 mt-2 stat-value">{{ $totalTickets }}</p>
                    </div>
                    <div class="w-12 h-12 bg-apple-gray-50 rounded-apple flex items-center justify-center">
                        <svg class="w-6 h-6 text-apple-blue" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                    </div>
                </div>
            </a>

            <!-- Stat Card 2 -->
            <a href="{{ route('complaints.index', ['status' => 'in_progress']) }}" class="block stat-card bg-white rounded-apple-lg shadow-apple p-6 hover:shadow-apple-md animate-slide-up transition-all duration-200 cursor-pointer">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-apple-gray-500">Open Tickets</p>
                        <p class="text-3xl font-bold text-apple-gray-900 mt-2 stat-value">{{ $openTickets }}</p>
                    </div>
                    <div class="w-12 h-12 bg-apple-gray-50 rounded-apple flex items-center justify-center">
                        <svg class="w-6 h-6 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>
            </a>

            <!-- Stat Card 3 -->
            <a href="{{ route('complaints.index', ['status' => 'resolved']) }}" class="block stat-card bg-white rounded-apple-lg shadow-apple p-6 hover:shadow-apple-md animate-slide-up transition-all duration-200 cursor-pointer">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-apple-gray-500">Resolved</p>
                        <p class="text-3xl font-bold text-apple-gray-900 mt-2 stat-value">{{ $resolvedTickets }}</p>
                    </div>
                    <div class="w-12 h-12 bg-apple-gray-50 rounded-apple flex items-center justify-center">
                        <svg class="w-6 h-6 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>
            </a>
        </div>

        <!-- My Pending Tasks -->
        @if($totalPendingTasks > 0)
        <div class="bg-gradient-to-br from-yellow-50 to-orange-50 border-2 border-yellow-200 rounded-apple-lg shadow-apple p-6 animate-fade-in">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-apple-gray-900 flex items-center gap-2">
                    📋 My Pending Tasks
                    @if($overdueTasks > 0)
                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-red-100 text-red-800">
                            {{ $overdueTasks }} overdue
                        </span>
                    @endif
                </h3>
                <span class="text-sm font-medium text-apple-gray-600">{{ $totalPendingTasks }} total</span>
            </div>

            <div class="space-y-3">
                @foreach($myPendingTasks as $task)
                    <a href="{{ route('complaints.show', $task->complaint->ticket_number) }}"
                       class="block p-3 bg-white rounded-lg hover:shadow-md transition-shadow border {{ $task->isOverdue() ? 'border-red-300' : 'border-yellow-200' }}">
                        <div class="flex items-start justify-between">
                            <div class="flex-1">
                                <div class="flex items-center flex-wrap gap-2 mb-2">
                                    @if($task->isOverdue())
                                        <span class="inline-flex items-center px-2 py-1 rounded-md text-xs font-semibold bg-red-100 text-red-800">
                                            ⚠ Overdue
                                        </span>
                                    @endif
                                    @if($task->priority === 'high')
                                        <span class="inline-flex items-center px-2 py-1 rounded-md text-xs font-semibold bg-red-100 text-red-800">
                                            🔥 High Priority
                                        </span>
                                    @elseif($task->priority === 'medium')
                                        <span class="inline-flex items-center px-2 py-1 rounded-md text-xs font-semibold bg-yellow-100 text-yellow-800">
                                            ⏰ Medium Priority
                                        </span>
                                    @endif
                                    <span class="text-xs font-medium text-apple-gray-600">Ticket #{{ $task->complaint->ticket_number }}</span>
                                </div>
                                <p class="text-sm font-medium text-apple-gray-900 mb-1">{{ $task->task_description }}</p>
                                <p class="text-xs text-apple-gray-600">
                                    <span class="font-medium">⏰ Due:</span> {{ $task->reminder_datetime->format('M j, Y g:i A') }} <span class="text-apple-gray-500">({{ $task->reminder_datetime->diffForHumans() }})</span>
                                </p>
                            </div>
                            <svg class="w-5 h-5 text-apple-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                            </svg>
                        </div>
                    </a>
                @endforeach

                @if($totalPendingTasks > 5)
                    <p class="text-center text-xs text-apple-gray-600 pt-2">
                        + {{ $totalPendingTasks - 5 }} more pending tasks
                    </p>
                @endif
            </div>
        </div>
        @endif

        <!-- Recent Activity -->
        <div class="bg-white rounded-apple-lg shadow-apple p-6 animate-fade-in">
            <h3 class="text-lg font-semibold text-apple-gray-900 mb-4">Recent Activity</h3>

            @if($recentActivity->count() > 0)
                <div class="activity-list" id="activity-list">
                    @foreach($recentActivity as $activity)
                        <div class="activity-item flex items-start p-3 rounded-lg">
                            <div class="activity-icon activity-icon-{{ strtolower($activity->action) }}">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                    @if(str_contains(strtolower($activity->action), 'created'))
                                        <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd"/>
                                    @elseif(str_contains(strtolower($activity->action), 'updated'))
                                        <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z"/>
                                    @elseif(str_contains(strtolower($activity->action), 'deleted'))
                                        <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9z" clip-rule="evenodd"/>
                                    @else
                                        <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"/>
                                    @endif
                                </svg>
                            </div>
                            <div class="ml-3 flex-1">
                                <p class="text-sm font-medium text-apple-gray-900">{{ $activity->description }}</p>
                                <p class="text-xs text-apple-gray-500 mt-1">{{ $activity->user->name }} • {{ $activity->created_at->diffForHumans() }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="empty-state">
                    <div class="w-16 h-16 bg-apple-gray-50 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-apple-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                        </svg>
                    </div>
                    <p class="text-apple-gray-500 text-sm">No recent activity</p>
                    <p class="text-apple-gray-400 text-xs mt-1">Your ticket activity will appear here</p>
                </div>
            @endif
        </div>
    </div>

    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
    <script src="{{ asset('js/dashboard.js') }}"></script>
</x-app-layout>
