<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="font-semibold text-xl sm:text-2xl text-apple-gray-900 leading-tight">
                    {{ __('Tickets & Complaints') }}
                </h2>
                <p class="text-sm text-apple-gray-500 mt-1">Manage and track all customer complaints</p>
            </div>
            <a href="{{ route('complaints.create') }}"
               class="lg:hidden inline-flex items-center px-4 py-2 bg-apple-blue text-white font-semibold rounded-apple hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-apple-blue transition-all duration-200 shadow-apple hover:shadow-apple-md transform hover:scale-105">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                </svg>
                New Complaint
            </a>
        </div>
    </x-slot>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        @if(session('success'))
            <div class="mb-6 bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-apple animate-fade-in">
                <p class="font-medium">{{ session('success') }}</p>
            </div>
        @endif

        <!-- Filters -->
        <div class="bg-white rounded-apple-lg shadow-apple p-4 sm:p-6 mb-6">
            <form method="GET" action="{{ route('complaints.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4">
                <div class="sm:col-span-2 lg:col-span-1">
                    <input type="text"
                           name="search"
                           value="{{ request('search') }}"
                           placeholder="Search tickets..."
                           class="block w-full px-4 py-2 bg-apple-gray-50 border-0 rounded-apple focus:ring-2 focus:ring-apple-blue text-sm">
                </div>
                <div>
                    <select name="status" class="block w-full px-4 py-2 bg-apple-gray-50 border-0 rounded-apple focus:ring-2 focus:ring-apple-blue text-sm">
                        <option value="">All Statuses</option>
                        <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="assigned" {{ request('status') === 'assigned' ? 'selected' : '' }}>Assigned</option>
                        <option value="in_progress" {{ request('status') === 'in_progress' ? 'selected' : '' }}>In Progress</option>
                        <option value="resolved" {{ request('status') === 'resolved' ? 'selected' : '' }}>Resolved</option>
                        <option value="closed" {{ request('status') === 'closed' ? 'selected' : '' }}>Closed</option>
                        <option value="escalated" {{ request('status') === 'escalated' ? 'selected' : '' }}>Escalated</option>
                    </select>
                </div>
                <div>
                    <select name="priority" class="block w-full px-4 py-2 bg-apple-gray-50 border-0 rounded-apple focus:ring-2 focus:ring-apple-blue text-sm">
                        <option value="">All Priorities</option>
                        <option value="low" {{ request('priority') === 'low' ? 'selected' : '' }}>Low</option>
                        <option value="medium" {{ request('priority') === 'medium' ? 'selected' : '' }}>Medium</option>
                        <option value="high" {{ request('priority') === 'high' ? 'selected' : '' }}>High</option>
                        <option value="urgent" {{ request('priority') === 'urgent' ? 'selected' : '' }}>Urgent</option>
                    </select>
                </div>
                <div class="flex gap-2 sm:col-span-2 lg:col-span-1">
                    <button type="submit" class="flex-1 px-4 py-2 bg-apple-blue text-white font-medium rounded-apple hover:bg-blue-600 transition-all text-sm">
                        Filter
                    </button>
                    <a href="{{ route('complaints.index') }}" class="px-4 py-2 bg-apple-gray-100 text-apple-gray-700 font-medium rounded-apple hover:bg-apple-gray-200 transition-all text-sm">
                        Reset
                    </a>
                </div>
            </form>
        </div>

        <!-- Tickets List - Desktop Table View -->
        <div class="hidden lg:block bg-white rounded-apple-lg shadow-apple overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-apple-gray-200">
                    <thead class="bg-apple-gray-50">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-apple-gray-700 uppercase tracking-wider">
                                Ticket #
                            </th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-apple-gray-700 uppercase tracking-wider">
                                Client
                            </th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-apple-gray-700 uppercase tracking-wider">
                                Policy #
                            </th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-apple-gray-700 uppercase tracking-wider">
                                Department
                            </th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-apple-gray-700 uppercase tracking-wider">
                                Status
                            </th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-apple-gray-700 uppercase tracking-wider">
                                Priority
                            </th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-apple-gray-700 uppercase tracking-wider">
                                Assigned To
                            </th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-apple-gray-700 uppercase tracking-wider">
                                Created
                            </th>
                            <th class="px-6 py-4 text-right text-xs font-semibold text-apple-gray-700 uppercase tracking-wider">
                                Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-apple-gray-100">
                        @forelse($complaints as $complaint)
                            <tr class="hover:bg-apple-gray-50 transition-colors duration-150">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <a href="{{ route('complaints.show', $complaint) }}" class="text-apple-blue hover:text-blue-600 font-medium">
                                        {{ $complaint->ticket_number }}
                                    </a>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm font-medium text-apple-gray-900">{{ $complaint->full_name }}</div>
                                    <div class="text-sm text-apple-gray-500">{{ $complaint->phone_number }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-apple-gray-900">
                                    {{ $complaint->policy_number }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-apple-gray-700">
                                    {{ $complaint->department }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full
                                        @if($complaint->status === 'pending') bg-yellow-100 text-yellow-800
                                        @elseif($complaint->status === 'assigned') bg-blue-100 text-blue-800
                                        @elseif($complaint->status === 'in_progress') bg-indigo-100 text-indigo-800
                                        @elseif($complaint->status === 'resolved') bg-green-100 text-green-800
                                        @elseif($complaint->status === 'closed') bg-gray-100 text-gray-800
                                        @elseif($complaint->status === 'escalated') bg-red-100 text-red-800
                                        @endif">
                                        {{ ucwords(str_replace('_', ' ', $complaint->status)) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full
                                        @if($complaint->priority === 'low') bg-gray-100 text-gray-800
                                        @elseif($complaint->priority === 'medium') bg-blue-100 text-blue-800
                                        @elseif($complaint->priority === 'high') bg-orange-100 text-orange-800
                                        @elseif($complaint->priority === 'urgent') bg-red-100 text-red-800
                                        @endif">
                                        {{ ucfirst($complaint->priority) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-apple-gray-500">
                                    {{ $complaint->assignedTo?->name ?? 'Unassigned' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-apple-gray-500">
                                    {{ $complaint->created_at->format('M d, Y') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <a href="{{ route('complaints.show', $complaint) }}"
                                       class="text-apple-blue hover:text-blue-600 transition-colors">
                                        View
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="px-6 py-12 text-center">
                                    <div class="text-apple-gray-400">
                                        <svg class="mx-auto h-12 w-12 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                        </svg>
                                        <p class="text-sm font-medium">No tickets found</p>
                                        <p class="text-xs mt-1">Try adjusting your search or filter criteria</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($complaints->hasPages())
                <div class="px-6 py-4 border-t border-apple-gray-200">
                    {{ $complaints->links() }}
                </div>
            @endif
        </div>

        <!-- Tickets List - Mobile Card View -->
        <div class="space-y-4 lg:!hidden">
            @forelse($complaints as $complaint)
                <div class="bg-white rounded-apple-lg shadow-apple p-4 hover:shadow-apple-md transition-shadow duration-150">
                    <div class="flex items-start justify-between mb-3">
                        <div class="flex-1">
                            <a href="{{ route('complaints.show', $complaint) }}" class="text-apple-blue hover:text-blue-600 font-semibold text-sm block mb-1">
                                {{ $complaint->ticket_number }}
                            </a>
                            <p class="text-sm font-medium text-apple-gray-900">{{ $complaint->full_name }}</p>
                            <p class="text-xs text-apple-gray-500">{{ $complaint->phone_number }}</p>
                        </div>
                        <div class="flex flex-col items-end gap-2">
                            <span class="px-2.5 py-1 inline-flex text-xs leading-4 font-semibold rounded-full
                                @if($complaint->status === 'pending') bg-yellow-100 text-yellow-800
                                @elseif($complaint->status === 'assigned') bg-blue-100 text-blue-800
                                @elseif($complaint->status === 'in_progress') bg-indigo-100 text-indigo-800
                                @elseif($complaint->status === 'resolved') bg-green-100 text-green-800
                                @elseif($complaint->status === 'closed') bg-gray-100 text-gray-800
                                @elseif($complaint->status === 'escalated') bg-red-100 text-red-800
                                @endif">
                                {{ ucwords(str_replace('_', ' ', $complaint->status)) }}
                            </span>
                            <span class="px-2.5 py-1 inline-flex text-xs leading-4 font-semibold rounded-full
                                @if($complaint->priority === 'low') bg-gray-100 text-gray-800
                                @elseif($complaint->priority === 'medium') bg-blue-100 text-blue-800
                                @elseif($complaint->priority === 'high') bg-orange-100 text-orange-800
                                @elseif($complaint->priority === 'urgent') bg-red-100 text-red-800
                                @endif">
                                {{ ucfirst($complaint->priority) }}
                            </span>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-3 text-xs">
                        <div>
                            <span class="text-apple-gray-500 block">Policy #</span>
                            <span class="text-apple-gray-900 font-medium">{{ $complaint->policy_number }}</span>
                        </div>
                        <div>
                            <span class="text-apple-gray-500 block">Department</span>
                            <span class="text-apple-gray-900 font-medium">{{ $complaint->department }}</span>
                        </div>
                        <div>
                            <span class="text-apple-gray-500 block">Assigned To</span>
                            <span class="text-apple-gray-900 font-medium">{{ $complaint->assignedTo?->name ?? 'Unassigned' }}</span>
                        </div>
                        <div>
                            <span class="text-apple-gray-500 block">Created</span>
                            <span class="text-apple-gray-900 font-medium">{{ $complaint->created_at->format('M d, Y') }}</span>
                        </div>
                    </div>

                    <div class="mt-3 pt-3 border-t border-apple-gray-100">
                        <a href="{{ route('complaints.show', $complaint) }}"
                           class="inline-flex items-center text-sm font-medium text-apple-blue hover:text-blue-600 transition-colors">
                            View Details
                            <svg class="ml-1 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                            </svg>
                        </a>
                    </div>
                </div>
            @empty
                <div class="bg-white rounded-apple-lg shadow-apple p-8 text-center">
                    <div class="text-apple-gray-400">
                        <svg class="mx-auto h-12 w-12 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        <p class="text-sm font-medium">No tickets found</p>
                        <p class="text-xs mt-1">Try adjusting your search or filter criteria</p>
                    </div>
                </div>
            @endforelse

            @if($complaints->hasPages())
                <div class="bg-white rounded-apple-lg shadow-apple px-4 py-3">
                    {{ $complaints->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
