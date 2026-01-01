<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-apple-gray-800 leading-tight">
                {{ ucwords(str_replace('_', ' ', $reportType)) }} Report
            </h2>
            <a href="{{ route('reports.wizard') }}" class="px-4 py-2 bg-apple-gray-100 text-apple-gray-700 rounded-apple font-medium hover:bg-apple-gray-200 transition-colors text-sm">
                New Report
            </a>
        </div>
    </x-slot>

    <div class="py-6 sm:py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Applied Filters Summary -->
            <div class="bg-white rounded-apple shadow-apple p-6 mb-6">
                <h3 class="font-semibold text-apple-gray-900 mb-4">Applied Filters</h3>
                <div class="flex flex-wrap gap-2">
                    @if(isset($filters['date_from']) && $filters['date_from'])
                        <span class="px-3 py-1 bg-blue-100 text-blue-800 rounded-full text-sm">
                            From: {{ \Carbon\Carbon::parse($filters['date_from'])->format('M d, Y') }}
                        </span>
                    @endif
                    @if(isset($filters['date_to']) && $filters['date_to'])
                        <span class="px-3 py-1 bg-blue-100 text-blue-800 rounded-full text-sm">
                            To: {{ \Carbon\Carbon::parse($filters['date_to'])->format('M d, Y') }}
                        </span>
                    @endif
                    @if(isset($filters['status']) && $filters['status'])
                        <span class="px-3 py-1 bg-purple-100 text-purple-800 rounded-full text-sm">
                            Status: {{ ucwords(str_replace('_', ' ', $filters['status'])) }}
                        </span>
                    @endif
                    @if(isset($filters['priority']) && $filters['priority'])
                        <span class="px-3 py-1 bg-orange-100 text-orange-800 rounded-full text-sm">
                            Priority: {{ ucfirst($filters['priority']) }}
                        </span>
                    @endif
                    @if(isset($filters['department']) && $filters['department'])
                        <span class="px-3 py-1 bg-green-100 text-green-800 rounded-full text-sm">
                            Department: {{ ucwords(str_replace('_', ' ', $filters['department'])) }}
                        </span>
                    @endif
                    @if(!isset($filters['date_from']) && !isset($filters['status']) && !isset($filters['priority']) && !isset($filters['department']))
                        <span class="text-sm text-apple-gray-600">No filters applied</span>
                    @endif
                </div>
            </div>

            <!-- Statistics Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
                <div class="bg-white rounded-apple shadow-apple p-6">
                    <div class="flex items-center">
                        <div class="p-3 bg-blue-100 rounded-apple">
                            <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-apple-gray-600">Total Tickets</p>
                            <p class="text-2xl font-bold text-apple-gray-900">{{ $statistics['total'] }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-apple shadow-apple p-6">
                    <div class="flex items-center">
                        <div class="p-3 bg-green-100 rounded-apple">
                            <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-apple-gray-600">Resolved</p>
                            <p class="text-2xl font-bold text-apple-gray-900">{{ $statistics['by_status']['resolved'] ?? 0 }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-apple shadow-apple p-6">
                    <div class="flex items-center">
                        <div class="p-3 bg-yellow-100 rounded-apple">
                            <svg class="w-8 h-8 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-apple-gray-600">In Progress</p>
                            <p class="text-2xl font-bold text-apple-gray-900">{{ $statistics['by_status']['in_progress'] ?? 0 }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-apple shadow-apple p-6">
                    <div class="flex items-center">
                        <div class="p-3 bg-purple-100 rounded-apple">
                            <svg class="w-8 h-8 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-apple-gray-600">Avg. Resolution</p>
                            <p class="text-2xl font-bold text-apple-gray-900">{{ $statistics['avg_resolution_hours'] }}h</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Charts Section -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
                <!-- Status Breakdown -->
                <div class="bg-white rounded-apple shadow-apple p-6">
                    <h3 class="font-semibold text-apple-gray-900 mb-4">Status Breakdown</h3>
                    <div class="space-y-3">
                        @foreach($statistics['by_status'] as $status => $count)
                            @php
                                $percentage = $statistics['total'] > 0 ? ($count / $statistics['total']) * 100 : 0;
                                $colors = [
                                    'pending' => 'bg-gray-500',
                                    'assigned' => 'bg-blue-500',
                                    'in_progress' => 'bg-yellow-500',
                                    'partial_closed' => 'bg-orange-500',
                                    'resolved' => 'bg-green-500',
                                    'closed' => 'bg-gray-700',
                                    'escalated' => 'bg-red-500'
                                ];
                                $color = $colors[$status] ?? 'bg-apple-gray-500';
                            @endphp
                            <div>
                                <div class="flex justify-between text-sm mb-1">
                                    <span class="text-apple-gray-700">{{ ucwords(str_replace('_', ' ', $status)) }}</span>
                                    <span class="font-medium text-apple-gray-900">{{ $count }} ({{ number_format($percentage, 1) }}%)</span>
                                </div>
                                <div class="w-full bg-apple-gray-200 rounded-full h-2">
                                    <div class="{{ $color }} h-2 rounded-full" style="width: {{ $percentage }}%"></div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Priority Breakdown -->
                <div class="bg-white rounded-apple shadow-apple p-6">
                    <h3 class="font-semibold text-apple-gray-900 mb-4">Priority Breakdown</h3>
                    <div class="space-y-3">
                        @foreach($statistics['by_priority'] as $priority => $count)
                            @php
                                $percentage = $statistics['total'] > 0 ? ($count / $statistics['total']) * 100 : 0;
                                $colors = [
                                    'low' => 'bg-blue-400',
                                    'medium' => 'bg-yellow-400',
                                    'high' => 'bg-orange-500',
                                    'urgent' => 'bg-red-600'
                                ];
                                $color = $colors[$priority] ?? 'bg-apple-gray-500';
                            @endphp
                            <div>
                                <div class="flex justify-between text-sm mb-1">
                                    <span class="text-apple-gray-700">{{ ucfirst($priority) }}</span>
                                    <span class="font-medium text-apple-gray-900">{{ $count }} ({{ number_format($percentage, 1) }}%)</span>
                                </div>
                                <div class="w-full bg-apple-gray-200 rounded-full h-2">
                                    <div class="{{ $color }} h-2 rounded-full" style="width: {{ $percentage }}%"></div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Tickets Table -->
            <div class="bg-white rounded-apple shadow-apple overflow-hidden">
                <div class="p-6 border-b border-apple-gray-200">
                    <h3 class="font-semibold text-apple-gray-900">Ticket Details ({{ $complaints->count() }} results)</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-apple-gray-200">
                        <thead class="bg-apple-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-apple-gray-500 uppercase tracking-wider">Ticket #</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-apple-gray-500 uppercase tracking-wider">Customer</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-apple-gray-500 uppercase tracking-wider">Department</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-apple-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-apple-gray-500 uppercase tracking-wider">Priority</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-apple-gray-500 uppercase tracking-wider">Assigned To</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-apple-gray-500 uppercase tracking-wider">Created</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-apple-gray-200">
                            @forelse($complaints as $complaint)
                                <tr class="hover:bg-apple-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <a href="{{ route('complaints.show', $complaint) }}" class="text-apple-blue hover:text-blue-800 font-medium">
                                            {{ $complaint->ticket_number }}
                                        </a>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-apple-gray-900">
                                        {{ $complaint->full_name }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-apple-gray-700">
                                        {{ $complaint->department->name ?? 'N/A' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 py-1 text-xs font-medium rounded-full
                                            @if($complaint->status === 'resolved') bg-green-100 text-green-800
                                            @elseif($complaint->status === 'in_progress') bg-yellow-100 text-yellow-800
                                            @elseif($complaint->status === 'pending') bg-gray-100 text-gray-800
                                            @elseif($complaint->status === 'escalated') bg-red-100 text-red-800
                                            @else bg-blue-100 text-blue-800
                                            @endif">
                                            {{ ucwords(str_replace('_', ' ', $complaint->status)) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 py-1 text-xs font-medium rounded-full
                                            @if($complaint->priority === 'urgent') bg-red-100 text-red-800
                                            @elseif($complaint->priority === 'high') bg-orange-100 text-orange-800
                                            @elseif($complaint->priority === 'medium') bg-yellow-100 text-yellow-800
                                            @else bg-blue-100 text-blue-800
                                            @endif">
                                            {{ ucfirst($complaint->priority) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-apple-gray-700">
                                        {{ $complaint->assignedTo?->name ?? 'Unassigned' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-apple-gray-600">
                                        {{ $complaint->created_at->format('M d, Y') }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-6 py-12 text-center text-apple-gray-500">
                                        No tickets found matching your criteria
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Export Actions -->
            <div class="mt-6 flex justify-center gap-4">
                <form action="{{ route('reports.generate') }}" method="POST" class="inline">
                    @csrf
                    @foreach($filters as $key => $value)
                        @if($value)
                            <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                        @endif
                    @endforeach
                    <input type="hidden" name="report_type" value="{{ $reportType }}">
                    <input type="hidden" name="format" value="excel">
                    <button type="submit" class="px-6 py-3 bg-green-600 text-white rounded-apple font-medium hover:bg-green-700 transition-colors shadow-apple">
                        <svg class="w-5 h-5 inline-block mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        Export to Excel
                    </button>
                </form>

                <form action="{{ route('reports.generate') }}" method="POST" class="inline">
                    @csrf
                    @foreach($filters as $key => $value)
                        @if($value)
                            <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                        @endif
                    @endforeach
                    <input type="hidden" name="report_type" value="{{ $reportType }}">
                    <input type="hidden" name="format" value="pdf">
                    <button type="submit" class="px-6 py-3 bg-red-600 text-white rounded-apple font-medium hover:bg-red-700 transition-colors shadow-apple">
                        <svg class="w-5 h-5 inline-block mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                        </svg>
                        Export to PDF
                    </button>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
