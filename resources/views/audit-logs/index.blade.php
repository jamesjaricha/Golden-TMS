<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="font-semibold text-xl sm:text-2xl text-apple-gray-900 leading-tight">
                    Audit Logs
                </h2>
                <p class="text-sm text-apple-gray-500 mt-1">
                    @if(request()->hasAny(['category', 'action', 'user_id', 'status', 'date_from', 'date_to', 'search']))
                        Showing filtered results
                    @else
                        Showing today's activity - Use filters to view more
                    @endif
                </p>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('audit-logs.export', request()->all()) }}"
                   class="inline-flex items-center px-4 py-2 bg-apple-gray-100 text-apple-gray-700 font-semibold rounded-apple hover:bg-apple-gray-200 focus:outline-none focus:ring-2 focus:ring-apple-gray-400 transition-all duration-200">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10"/>
                    </svg>
                    Export CSV
                </a>
            </div>
        </div>
    </x-slot>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Filters -->
        <div class="bg-white rounded-apple-lg shadow-apple p-4 sm:p-6 mb-6">
            <form method="GET" action="{{ route('audit-logs.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-6 gap-3 sm:gap-4">
                <div>
                    <input type="text"
                           name="search"
                           value="{{ request('search') }}"
                           placeholder="Search..."
                           class="block w-full px-4 py-2 bg-apple-gray-50 border-0 rounded-apple focus:ring-2 focus:ring-apple-blue text-sm">
                </div>
                <div>
                    <select name="category" class="block w-full px-4 py-2 bg-apple-gray-50 border-0 rounded-apple focus:ring-2 focus:ring-apple-blue text-sm">
                        <option value="">All Categories</option>
                        @foreach($categories as $category)
                            <option value="{{ $category }}" {{ request('category') === $category ? 'selected' : '' }}>
                                {{ ucfirst($category) }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <select name="action" class="block w-full px-4 py-2 bg-apple-gray-50 border-0 rounded-apple focus:ring-2 focus:ring-apple-blue text-sm">
                        <option value="">All Actions</option>
                        @foreach($actions as $action)
                            <option value="{{ $action }}" {{ request('action') === $action ? 'selected' : '' }}>
                                {{ ucwords(str_replace('_', ' ', $action)) }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <select name="user_id" class="block w-full px-4 py-2 bg-apple-gray-50 border-0 rounded-apple focus:ring-2 focus:ring-apple-blue text-sm">
                        <option value="">All Users</option>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>
                                {{ $user->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <input type="date"
                           name="date_from"
                           value="{{ request('date_from') }}"
                           placeholder="From Date"
                           class="block w-full px-4 py-2 bg-apple-gray-50 border-0 rounded-apple focus:ring-2 focus:ring-apple-blue text-sm">
                </div>
                <div class="flex gap-2">
                    <input type="date"
                           name="date_to"
                           value="{{ request('date_to') }}"
                           placeholder="To Date"
                           class="block w-full px-4 py-2 bg-apple-gray-50 border-0 rounded-apple focus:ring-2 focus:ring-apple-blue text-sm">
                    <button type="submit" class="px-4 py-2 bg-apple-blue text-white font-medium rounded-apple hover:bg-blue-600 transition-all text-sm whitespace-nowrap">
                        Apply Filters
                    </button>
                    @if(request()->hasAny(['category', 'action', 'user_id', 'status', 'date_from', 'date_to', 'search']))
                        <a href="{{ route('audit-logs.index') }}" class="px-4 py-2 bg-apple-gray-100 text-apple-gray-700 font-medium rounded-apple hover:bg-apple-gray-200 transition-all text-sm whitespace-nowrap">
                            Clear
                        </a>
                    @endif
                </div>
            </form>
        </div>

        <!-- Info Banner -->
        @if(!request()->hasAny(['category', 'action', 'user_id', 'status', 'date_from', 'date_to', 'search']))
            <div class="bg-blue-50 border border-blue-200 rounded-apple-lg p-4 mb-6">
                <div class="flex items-start">
                    <svg class="w-5 h-5 text-blue-500 mr-3 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                    </svg>
                    <div>
                        <p class="text-sm font-medium text-blue-900">Showing today's activity only</p>
                        <p class="text-xs text-blue-700 mt-1">Use the filters above to view historical logs or search for specific activities.</p>
                    </div>
                </div>
            </div>
        @endif

        <!-- Audit Log List -->
        <div class="bg-white rounded-apple-lg shadow-apple overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-apple-gray-200">
                    <thead class="bg-apple-gray-50">
                        <tr>
                            <th class="px-4 py-4 text-left text-xs font-semibold text-apple-gray-700 uppercase tracking-wider">
                                Date/Time
                            </th>
                            <th class="px-4 py-4 text-left text-xs font-semibold text-apple-gray-700 uppercase tracking-wider">
                                User
                            </th>
                            <th class="px-4 py-4 text-left text-xs font-semibold text-apple-gray-700 uppercase tracking-wider">
                                Action
                            </th>
                            <th class="px-4 py-4 text-left text-xs font-semibold text-apple-gray-700 uppercase tracking-wider">
                                Description
                            </th>
                            <th class="px-4 py-4 text-left text-xs font-semibold text-apple-gray-700 uppercase tracking-wider">
                                IP / Device
                            </th>
                            <th class="px-4 py-4 text-left text-xs font-semibold text-apple-gray-700 uppercase tracking-wider">
                                Status
                            </th>
                            <th class="px-4 py-4 text-right text-xs font-semibold text-apple-gray-700 uppercase tracking-wider">
                                Details
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-apple-gray-100">
                        @forelse($logs as $log)
                            <tr class="hover:bg-apple-gray-50 transition-colors duration-150">
                                <td class="px-4 py-4 whitespace-nowrap text-sm text-apple-gray-500">
                                    <div>{{ $log->created_at->format('M d, Y') }}</div>
                                    <div class="text-xs">{{ $log->created_at->format('H:i:s') }}</div>
                                </td>
                                <td class="px-4 py-4">
                                    <div class="text-sm font-medium text-apple-gray-900">{{ $log->user_name ?? 'System' }}</div>
                                    <div class="text-xs text-apple-gray-500">{{ $log->user_role ?? '-' }}</div>
                                </td>
                                <td class="px-4 py-4 whitespace-nowrap">
                                    <span class="px-2.5 py-1 inline-flex text-xs leading-5 font-semibold rounded-full
                                        @if($log->action_category === 'auth') bg-purple-100 text-purple-800
                                        @elseif($log->action_category === 'ticket') bg-blue-100 text-blue-800
                                        @elseif($log->action_category === 'user') bg-green-100 text-green-800
                                        @elseif($log->action_category === 'report') bg-yellow-100 text-yellow-800
                                        @else bg-gray-100 text-gray-800
                                        @endif">
                                        {{ ucwords(str_replace('_', ' ', $log->action)) }}
                                    </span>
                                </td>
                                <td class="px-4 py-4 text-sm text-apple-gray-700" style="max-width: 300px;">
                                    <div class="truncate" title="{{ $log->description }}">{{ $log->description }}</div>
                                    @if($log->auditable_identifier)
                                        <div class="text-xs text-apple-gray-500">{{ $log->auditable_identifier }}</div>
                                    @endif
                                </td>
                                <td class="px-4 py-4 text-sm text-apple-gray-500">
                                    <div>{{ $log->ip_address ?? '-' }}</div>
                                    <div class="text-xs">{{ $log->browser ?? '' }} / {{ $log->platform ?? '' }}</div>
                                </td>
                                <td class="px-4 py-4 whitespace-nowrap">
                                    <span class="px-2.5 py-1 inline-flex text-xs leading-5 font-semibold rounded-full
                                        @if($log->status === 'success') bg-green-100 text-green-800
                                        @elseif($log->status === 'failed') bg-red-100 text-red-800
                                        @else bg-yellow-100 text-yellow-800
                                        @endif">
                                        {{ ucfirst($log->status) }}
                                    </span>
                                </td>
                                <td class="px-4 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <a href="{{ route('audit-logs.show', $log) }}"
                                       class="text-apple-blue hover:text-blue-600 transition-colors">
                                        View
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-12 text-center">
                                    <div class="text-apple-gray-400">
                                        <svg class="mx-auto h-12 w-12 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                        </svg>
                                        <p class="text-sm font-medium">No audit logs found</p>
                                        <p class="text-xs mt-1">Try adjusting your filters</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($logs->hasPages())
                <div class="px-6 py-4 border-t border-apple-gray-200">
                    {{ $logs->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
