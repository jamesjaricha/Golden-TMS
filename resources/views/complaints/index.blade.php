<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="font-semibold text-xl sm:text-2xl text-apple-gray-900 leading-tight">
                    Tickets & Complaints
                </h2>
                <p class="text-sm text-apple-gray-500 mt-1">Manage and track all customer complaints</p>
            </div>
            <div class="flex gap-2">
                <!-- Export Dropdown -->
                <div x-data="{ open: false }" class="relative">
                    <button @click="open = !open" @click.away="open = false"
                           class="inline-flex items-center px-4 py-2 bg-apple-gray-100 text-apple-gray-700 font-semibold rounded-apple hover:bg-apple-gray-200 focus:outline-none focus:ring-2 focus:ring-apple-gray-400 transition-all duration-200">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10"/>
                        </svg>
                        Export
                        <svg class="w-4 h-4 ml-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"/>
                        </svg>
                    </button>
                    <div x-show="open"
                         x-transition:enter="transition ease-out duration-100"
                         x-transition:enter-start="transform opacity-0 scale-95"
                         x-transition:enter-end="transform opacity-100 scale-100"
                         x-transition:leave="transition ease-in duration-75"
                         x-transition:leave-start="transform opacity-100 scale-100"
                         x-transition:leave-end="transform opacity-0 scale-95"
                         class="absolute right-0 mt-2 w-56 rounded-apple-lg shadow-apple-lg bg-white ring-1 ring-black ring-opacity-5 z-50"
                         style="display: none;">
                        <div class="py-1">
                            <a href="{{ route('complaints.export.excel', request()->all()) }}"
                               class="flex items-center px-4 py-2 text-sm text-apple-gray-700 hover:bg-apple-gray-50 transition-colors">
                                <svg class="w-5 h-5 mr-3 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zM6.293 6.707a1 1 0 010-1.414l3-3a1 1 0 011.414 0l3 3a1 1 0 01-1.414 1.414L11 5.414V13a1 1 0 11-2 0V5.414L7.707 6.707a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
                                </svg>
                                Export to Excel
                            </a>
                            <a href="{{ route('complaints.export.pdf', request()->all()) }}"
                               class="flex items-center px-4 py-2 text-sm text-apple-gray-700 hover:bg-apple-gray-50 transition-colors">
                                <svg class="w-5 h-5 mr-3 text-red-600" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zM6.293 6.707a1 1 0 010-1.414l3-3a1 1 0 011.414 0l3 3a1 1 0 01-1.414 1.414L11 5.414V13a1 1 0 11-2 0V5.414L7.707 6.707a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
                                </svg>
                                Export to PDF
                            </a>
                            <div class="border-t border-apple-gray-100 my-1"></div>
                            <div class="px-4 py-2">
                                <p class="text-xs text-apple-gray-500 mb-2">Monthly Reports</p>
                                <form action="{{ route('reports.monthly') }}" method="GET" class="space-y-2">
                                    <input type="month" name="month" value="{{ now()->format('Y-m') }}"
                                           class="block w-full text-xs px-2 py-1 border border-apple-gray-300 rounded-apple focus:ring-apple-blue">
                                    <div class="flex gap-1">
                                        <button type="submit" name="format" value="excel"
                                                class="flex-1 text-xs px-2 py-1 bg-green-50 text-green-700 rounded hover:bg-green-100">
                                            Excel
                                        </button>
                                        <button type="submit" name="format" value="pdf"
                                                class="flex-1 text-xs px-2 py-1 bg-red-50 text-red-700 rounded hover:bg-red-100">
                                            PDF
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- New Complaint Button -->
                <a href="{{ route('complaints.create') }}"
                   class="inline-flex items-center px-4 py-2 bg-apple-blue text-white font-semibold rounded-apple hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-apple-blue transition-all duration-200 shadow-apple hover:shadow-apple-md transform hover:scale-105">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                    </svg>
                    New Complaint
                </a>
            </div>
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
            <form method="GET" action="{{ route('complaints.index') }}">
                <div class="space-y-4">
                    <!-- Search -->
                    <div class="space-y-1.5">
                        <label class="block text-xs font-medium uppercase tracking-wide text-apple-gray-600">Search</label>
                        <input type="text"
                               name="search"
                               value="{{ request('search') }}"
                               placeholder="Search tickets..."
                               class="block w-full px-4 py-2.5 bg-apple-gray-50 border border-apple-gray-200 rounded-apple focus:ring-2 focus:ring-apple-blue focus:border-apple-blue text-sm transition-all">
                    </div>

                    <!-- Status & Priority Row -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                        <div class="space-y-1.5">
                            <label class="block text-xs font-medium uppercase tracking-wide text-apple-gray-600">Status</label>
                            <select name="status" class="block w-full px-4 py-2.5 bg-apple-gray-50 border border-apple-gray-200 rounded-apple focus:ring-2 focus:ring-apple-blue focus:border-apple-blue text-sm transition-all">
                                <option value="">All Statuses</option>
                                <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="assigned" {{ request('status') === 'assigned' ? 'selected' : '' }}>Assigned</option>
                                <option value="in_progress" {{ request('status') === 'in_progress' ? 'selected' : '' }}>In Progress</option>
                                <option value="partial_closed" {{ request('status') === 'partial_closed' ? 'selected' : '' }}>⏳ Partial Closed</option>
                                <option value="resolved" {{ request('status') === 'resolved' ? 'selected' : '' }}>Resolved</option>
                                <option value="closed" {{ request('status') === 'closed' ? 'selected' : '' }}>Closed</option>
                                <option value="escalated" {{ request('status') === 'escalated' ? 'selected' : '' }}>Escalated</option>
                            </select>
                        </div>
                        <div class="space-y-1.5">
                            <label class="block text-xs font-medium uppercase tracking-wide text-apple-gray-600">Priority</label>
                            <select name="priority" class="block w-full px-4 py-2.5 bg-apple-gray-50 border border-apple-gray-200 rounded-apple focus:ring-2 focus:ring-apple-blue focus:border-apple-blue text-sm transition-all">
                                <option value="">All Priorities</option>
                                <option value="low" {{ request('priority') === 'low' ? 'selected' : '' }}>Low</option>
                                <option value="medium" {{ request('priority') === 'medium' ? 'selected' : '' }}>Medium</option>
                                <option value="high" {{ request('priority') === 'high' ? 'selected' : '' }}>High</option>
                                <option value="urgent" {{ request('priority') === 'urgent' ? 'selected' : '' }}>Urgent</option>
                            </select>
                        </div>
                        <div class="space-y-1.5">
                            <label class="block text-xs font-medium uppercase tracking-wide text-apple-gray-600">Branch</label>
                            <select name="branch_id" class="block w-full px-4 py-2.5 bg-apple-gray-50 border border-apple-gray-200 rounded-apple focus:ring-2 focus:ring-apple-blue focus:border-apple-blue text-sm transition-all">
                                <option value="">All Branches</option>
                                @foreach($branches as $branch)
                                    <option value="{{ $branch->id }}" {{ (string)request('branch_id') === (string)$branch->id ? 'selected' : '' }}>{{ $branch->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <!-- Date Range Row -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="space-y-1.5">
                            <label class="block text-xs font-medium uppercase tracking-wide text-apple-gray-600">From</label>
                            <input type="date"
                                   name="start_date"
                                   value="{{ request('start_date') }}"
                                   class="block w-full px-4 py-2.5 bg-apple-gray-50 border border-apple-gray-200 rounded-apple focus:ring-2 focus:ring-apple-blue focus:border-apple-blue text-sm transition-all">
                        </div>
                        <div class="space-y-1.5">
                            <label class="block text-xs font-medium uppercase tracking-wide text-apple-gray-600">To</label>
                            <input type="date"
                                   name="end_date"
                                   value="{{ request('end_date') }}"
                                   class="block w-full px-4 py-2.5 bg-apple-gray-50 border border-apple-gray-200 rounded-apple focus:ring-2 focus:ring-apple-blue focus:border-apple-blue text-sm transition-all">
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="grid grid-cols-2 gap-3 pt-2">
                        <button type="submit" class="w-full px-4 py-2.5 bg-apple-blue text-white font-semibold rounded-apple hover:bg-blue-600 focus:ring-2 focus:ring-apple-blue focus:ring-offset-2 transition-all text-sm shadow-apple-sm">
                            <svg class="w-4 h-4 inline-block mr-2 -mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
                            </svg>
                            Filter
                        </button>
                        <a href="{{ route('complaints.index') }}" class="w-full px-4 py-2.5 bg-apple-gray-100 text-apple-gray-700 font-semibold rounded-apple hover:bg-apple-gray-200 focus:ring-2 focus:ring-apple-gray-400 focus:ring-offset-2 transition-all text-sm text-center">
                            <svg class="w-4 h-4 inline-block mr-2 -mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                            Reset
                        </a>
                    </div>
                </div>
            </form>
        </div>

        <!-- Stats Summary -->
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-4 mb-6">
            <a href="{{ route('complaints.index', ['status' => 'pending']) }}"
               class="bg-white rounded-apple-lg shadow-apple p-4 border-l-4 border-yellow-400 hover:shadow-apple-md hover:scale-[1.02] transition-all duration-200 cursor-pointer group {{ request('status') === 'pending' ? 'ring-2 ring-yellow-400 ring-offset-2' : '' }}">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-medium text-apple-gray-500 uppercase tracking-wide group-hover:text-yellow-600 transition-colors">Pending</p>
                        <p class="text-2xl font-bold text-apple-gray-900 mt-1">{{ $statusCounts['pending'] }}</p>
                    </div>
                    <div class="p-2 bg-yellow-50 rounded-full group-hover:bg-yellow-100 transition-colors">
                        <svg class="w-6 h-6 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                </div>
            </a>
            <a href="{{ route('complaints.index', ['status' => 'in_progress']) }}"
               class="bg-white rounded-apple-lg shadow-apple p-4 border-l-4 border-indigo-400 hover:shadow-apple-md hover:scale-[1.02] transition-all duration-200 cursor-pointer group {{ request('status') === 'in_progress' ? 'ring-2 ring-indigo-400 ring-offset-2' : '' }}">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-medium text-apple-gray-500 uppercase tracking-wide group-hover:text-indigo-600 transition-colors">In Progress</p>
                        <p class="text-2xl font-bold text-apple-gray-900 mt-1">{{ $statusCounts['in_progress'] }}</p>
                    </div>
                    <div class="p-2 bg-indigo-50 rounded-full group-hover:bg-indigo-100 transition-colors">
                        <svg class="w-6 h-6 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                        </svg>
                    </div>
                </div>
            </a>
            <a href="{{ route('complaints.index', ['status' => 'partial_closed']) }}"
               class="bg-white rounded-apple-lg shadow-apple p-4 border-l-4 border-orange-400 hover:shadow-apple-md hover:scale-[1.02] transition-all duration-200 cursor-pointer group {{ request('status') === 'partial_closed' ? 'ring-2 ring-orange-400 ring-offset-2' : '' }}">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-medium text-apple-gray-500 uppercase tracking-wide group-hover:text-orange-600 transition-colors">Partial</p>
                        <p class="text-2xl font-bold text-apple-gray-900 mt-1">{{ $statusCounts['partial_closed'] ?? 0 }}</p>
                    </div>
                    <div class="p-2 bg-orange-50 rounded-full group-hover:bg-orange-100 transition-colors">
                        <svg class="w-6 h-6 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/>
                        </svg>
                    </div>
                </div>
            </a>
            <a href="{{ route('complaints.index', ['status' => 'resolved']) }}"
               class="bg-white rounded-apple-lg shadow-apple p-4 border-l-4 border-green-400 hover:shadow-apple-md hover:scale-[1.02] transition-all duration-200 cursor-pointer group {{ request('status') === 'resolved' ? 'ring-2 ring-green-400 ring-offset-2' : '' }}">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-medium text-apple-gray-500 uppercase tracking-wide group-hover:text-green-600 transition-colors">Resolved</p>
                        <p class="text-2xl font-bold text-apple-gray-900 mt-1">{{ $statusCounts['resolved'] }}</p>
                    </div>
                    <div class="p-2 bg-green-50 rounded-full group-hover:bg-green-100 transition-colors">
                        <svg class="w-6 h-6 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                </div>
            </a>
            <a href="{{ route('complaints.index', ['status' => 'escalated']) }}"
               class="bg-white rounded-apple-lg shadow-apple p-4 border-l-4 border-red-400 hover:shadow-apple-md hover:scale-[1.02] transition-all duration-200 cursor-pointer group {{ request('status') === 'escalated' ? 'ring-2 ring-red-400 ring-offset-2' : '' }}">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-medium text-apple-gray-500 uppercase tracking-wide group-hover:text-red-600 transition-colors">Escalated</p>
                        <p class="text-2xl font-bold text-apple-gray-900 mt-1">{{ $statusCounts['escalated'] }}</p>
                    </div>
                    <div class="p-2 bg-red-50 rounded-full group-hover:bg-red-100 transition-colors">
                        <svg class="w-6 h-6 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                    </div>
                </div>
            </a>
        </div>

        <!-- Tickets List - Desktop Table View -->
        <div class="hidden lg:block bg-white rounded-apple-lg shadow-apple overflow-hidden">
            <table class="w-full table-fixed divide-y divide-apple-gray-200">
                <thead class="bg-gradient-to-r from-apple-gray-50 to-apple-gray-100">
                    <tr>
                        <th class="w-[10%] px-4 py-4 text-left text-xs font-semibold text-apple-gray-700 uppercase tracking-wider">
                            Ticket #
                        </th>
                        <th class="w-[18%] px-4 py-4 text-left text-xs font-semibold text-apple-gray-700 uppercase tracking-wider">
                            Client
                        </th>
                        <th class="w-[10%] px-4 py-4 text-left text-xs font-semibold text-apple-gray-700 uppercase tracking-wider">
                            Policy #
                        </th>
                        <th class="w-[12%] px-4 py-4 text-left text-xs font-semibold text-apple-gray-700 uppercase tracking-wider">
                            Department
                        </th>
                        <th class="w-[10%] px-4 py-4 text-left text-xs font-semibold text-apple-gray-700 uppercase tracking-wider">
                            Status
                        </th>
                        <th class="w-[8%] px-4 py-4 text-left text-xs font-semibold text-apple-gray-700 uppercase tracking-wider">
                            Priority
                        </th>
                        <th class="w-[14%] px-4 py-4 text-left text-xs font-semibold text-apple-gray-700 uppercase tracking-wider">
                            Assigned To
                        </th>
                        <th class="w-[10%] px-4 py-4 text-left text-xs font-semibold text-apple-gray-700 uppercase tracking-wider">
                            Created
                        </th>
                        <th class="w-[8%] px-4 py-4 text-center text-xs font-semibold text-apple-gray-700 uppercase tracking-wider">
                            Actions
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-apple-gray-100">
                    @forelse($complaints as $complaint)
                        <tr class="hover:bg-blue-50/50 transition-colors duration-150 group">
                            <td class="px-4 py-4">
                                <a href="{{ route('complaints.show', $complaint) }}" class="text-apple-blue hover:text-blue-600 font-semibold text-sm group-hover:underline">
                                    {{ $complaint->ticket_number }}
                                </a>
                            </td>
                            <td class="px-4 py-4">
                                <div class="min-w-0">
                                    <p class="text-sm font-medium text-apple-gray-900 break-words leading-tight" title="{{ $complaint->full_name }}">
                                        {{ $complaint->full_name }}
                                    </p>
                                    <p class="text-xs text-apple-gray-500 mt-0.5">{{ $complaint->phone_number }}</p>
                                </div>
                            </td>
                            <td class="px-4 py-4">
                                <p class="text-sm text-apple-gray-900 break-words leading-tight" title="{{ $complaint->policy_number }}">
                                    {{ $complaint->policy_number }}
                                </p>
                            </td>
                            <td class="px-4 py-4">
                                <p class="text-sm text-apple-gray-700 break-words leading-tight">
                                    {{ $complaint->department->name ?? 'N/A' }}
                                </p>
                            </td>
                            <td class="px-4 py-4">
                                <span class="inline-flex items-center px-2.5 py-1 text-xs font-semibold rounded-full whitespace-nowrap
                                    @if($complaint->status === 'pending') bg-yellow-100 text-yellow-800 ring-1 ring-yellow-200
                                    @elseif($complaint->status === 'assigned') bg-blue-100 text-blue-800 ring-1 ring-blue-200
                                    @elseif($complaint->status === 'in_progress') bg-indigo-100 text-indigo-800 ring-1 ring-indigo-200
                                    @elseif($complaint->status === 'partial_closed') bg-orange-100 text-orange-800 ring-1 ring-orange-200
                                    @elseif($complaint->status === 'resolved') bg-green-100 text-green-800 ring-1 ring-green-200
                                    @elseif($complaint->status === 'closed') bg-gray-100 text-gray-800 ring-1 ring-gray-200
                                    @elseif($complaint->status === 'escalated') bg-red-100 text-red-800 ring-1 ring-red-200
                                    @endif">
                                    @if($complaint->status === 'pending')
                                        <span class="w-1.5 h-1.5 mr-1.5 bg-yellow-500 rounded-full animate-pulse"></span>
                                    @elseif($complaint->status === 'in_progress')
                                        <span class="w-1.5 h-1.5 mr-1.5 bg-indigo-500 rounded-full animate-pulse"></span>
                                    @elseif($complaint->status === 'partial_closed')
                                        <span class="w-1.5 h-1.5 mr-1.5 bg-orange-500 rounded-full animate-pulse"></span>
                                    @elseif($complaint->status === 'escalated')
                                        <span class="w-1.5 h-1.5 mr-1.5 bg-red-500 rounded-full animate-pulse"></span>
                                    @endif
                                    {{ ucwords(str_replace('_', ' ', $complaint->status)) }}
                                </span>
                                @if($complaint->status === 'partial_closed' && $complaint->pending_department)
                                    <p class="text-xs text-orange-600 mt-1">⏳ {{ $complaint->pending_department }}</p>
                                @endif
                            </td>
                            <td class="px-4 py-4">
                                <span class="inline-flex items-center px-2 py-0.5 text-xs font-medium rounded
                                    @if($complaint->priority === 'low') bg-gray-100 text-gray-700
                                    @elseif($complaint->priority === 'medium') bg-blue-100 text-blue-700
                                    @elseif($complaint->priority === 'high') bg-orange-100 text-orange-700
                                    @elseif($complaint->priority === 'urgent') bg-red-100 text-red-700 animate-pulse
                                    @endif">
                                    @if($complaint->priority === 'urgent')
                                        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                        </svg>
                                    @endif
                                    {{ ucfirst($complaint->priority) }}
                                </span>
                            </td>
                            <td class="px-4 py-4">
                                <div class="flex items-center min-w-0">
                                    @if($complaint->assignedTo)
                                        <div class="flex-shrink-0 w-7 h-7 bg-gradient-to-br from-apple-blue to-indigo-600 rounded-full flex items-center justify-center mr-2">
                                            <span class="text-xs font-medium text-white">{{ strtoupper(substr($complaint->assignedTo->name, 0, 1)) }}</span>
                                        </div>
                                        <p class="text-sm text-apple-gray-700 break-words leading-tight">{{ $complaint->assignedTo->name }}</p>
                                    @else
                                        <div class="flex-shrink-0 w-7 h-7 bg-apple-gray-200 rounded-full flex items-center justify-center mr-2">
                                            <svg class="w-4 h-4 text-apple-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                            </svg>
                                        </div>
                                        <span class="text-sm text-apple-gray-400 italic">Unassigned</span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-4 py-4">
                                <div class="text-sm text-apple-gray-500">
                                    <p class="font-medium">{{ $complaint->created_at->format('M d') }}</p>
                                    <p class="text-xs text-apple-gray-400">{{ $complaint->created_at->format('Y') }}</p>
                                </div>
                            </td>
                            <td class="px-4 py-4 text-center">
                                <a href="{{ route('complaints.show', $complaint) }}"
                                   class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-apple-gray-100 text-apple-gray-600 hover:bg-apple-blue hover:text-white transition-all duration-200 group-hover:scale-110"
                                   title="View Details">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="px-6 py-16 text-center">
                                <div class="flex flex-col items-center">
                                    <div class="w-20 h-20 bg-apple-gray-100 rounded-full flex items-center justify-center mb-4">
                                        <svg class="w-10 h-10 text-apple-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                        </svg>
                                    </div>
                                    <p class="text-base font-medium text-apple-gray-700">No tickets found</p>
                                    <p class="text-sm text-apple-gray-500 mt-1">Try adjusting your search or filter criteria</p>
                                    <a href="{{ route('complaints.create') }}" class="mt-4 inline-flex items-center px-4 py-2 bg-apple-blue text-white text-sm font-medium rounded-apple hover:bg-blue-600 transition-colors">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                                        </svg>
                                        Create New Ticket
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            @if($complaints->hasPages())
                <div class="px-6 py-4 bg-apple-gray-50 border-t border-apple-gray-200">
                    {{ $complaints->links() }}
                </div>
            @endif
        </div>

        <!-- Tickets List - Mobile Card View -->
        <div class="space-y-4 lg:!hidden">
            @forelse($complaints as $complaint)
                <div class="bg-white rounded-apple-lg shadow-apple overflow-hidden hover:shadow-apple-md transition-shadow duration-150">
                    <!-- Card Header with Status Indicator -->
                    <div class="px-4 py-3 border-b border-apple-gray-100 bg-gradient-to-r from-apple-gray-50 to-white">
                        <div class="flex items-center justify-between">
                            <a href="{{ route('complaints.show', $complaint) }}" class="text-apple-blue hover:text-blue-600 font-bold text-sm">
                                {{ $complaint->ticket_number }}
                            </a>
                            <div class="flex items-center gap-2">
                                <span class="inline-flex items-center px-2 py-0.5 text-xs font-medium rounded
                                    @if($complaint->priority === 'low') bg-gray-100 text-gray-700
                                    @elseif($complaint->priority === 'medium') bg-blue-100 text-blue-700
                                    @elseif($complaint->priority === 'high') bg-orange-100 text-orange-700
                                    @elseif($complaint->priority === 'urgent') bg-red-100 text-red-700
                                    @endif">
                                    @if($complaint->priority === 'urgent')
                                        <span class="w-1.5 h-1.5 mr-1 bg-red-500 rounded-full animate-pulse"></span>
                                    @endif
                                    {{ ucfirst($complaint->priority) }}
                                </span>
                                <span class="inline-flex items-center px-2.5 py-1 text-xs font-semibold rounded-full
                                    @if($complaint->status === 'pending') bg-yellow-100 text-yellow-800 ring-1 ring-yellow-200
                                    @elseif($complaint->status === 'assigned') bg-blue-100 text-blue-800 ring-1 ring-blue-200
                                    @elseif($complaint->status === 'in_progress') bg-indigo-100 text-indigo-800 ring-1 ring-indigo-200
                                    @elseif($complaint->status === 'partial_closed') bg-orange-100 text-orange-800 ring-1 ring-orange-200
                                    @elseif($complaint->status === 'resolved') bg-green-100 text-green-800 ring-1 ring-green-200
                                    @elseif($complaint->status === 'closed') bg-gray-100 text-gray-800 ring-1 ring-gray-200
                                    @elseif($complaint->status === 'escalated') bg-red-100 text-red-800 ring-1 ring-red-200
                                    @endif">
                                    @if(in_array($complaint->status, ['pending', 'in_progress', 'partial_closed', 'escalated']))
                                        <span class="w-1.5 h-1.5 mr-1.5 rounded-full animate-pulse
                                            @if($complaint->status === 'pending') bg-yellow-500
                                            @elseif($complaint->status === 'in_progress') bg-indigo-500
                                            @elseif($complaint->status === 'partial_closed') bg-orange-500
                                            @else bg-red-500
                                            @endif"></span>
                                    @endif
                                    {{ ucwords(str_replace('_', ' ', $complaint->status)) }}
                                </span>
                                @if($complaint->status === 'partial_closed' && $complaint->pending_department)
                                    <span class="text-xs text-orange-600">⏳ {{ $complaint->pending_department }}</span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Card Body -->
                    <div class="p-4">
                        <div class="flex items-start gap-3 mb-3">
                            <div class="flex-shrink-0 w-10 h-10 bg-gradient-to-br from-apple-blue to-indigo-600 rounded-full flex items-center justify-center">
                                <span class="text-sm font-semibold text-white">{{ strtoupper(substr($complaint->full_name, 0, 1)) }}</span>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-semibold text-apple-gray-900 break-words">{{ $complaint->full_name }}</p>
                                <p class="text-xs text-apple-gray-500">{{ $complaint->phone_number }}</p>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-3 text-xs">
                            <div class="bg-apple-gray-50 rounded-lg p-2">
                                <span class="text-apple-gray-500 block mb-0.5">Policy #</span>
                                <span class="text-apple-gray-900 font-medium break-words">{{ $complaint->policy_number }}</span>
                            </div>
                            <div class="bg-apple-gray-50 rounded-lg p-2">
                                <span class="text-apple-gray-500 block mb-0.5">Department</span>
                                <span class="text-apple-gray-900 font-medium break-words">{{ $complaint->department->name ?? 'N/A' }}</span>
                            </div>
                            <div class="bg-apple-gray-50 rounded-lg p-2">
                                <span class="text-apple-gray-500 block mb-0.5">Assigned To</span>
                                <span class="text-apple-gray-900 font-medium break-words">
                                    @if($complaint->assignedTo)
                                        {{ $complaint->assignedTo->name }}
                                    @else
                                        <span class="text-apple-gray-400 italic">Unassigned</span>
                                    @endif
                                </span>
                            </div>
                            <div class="bg-apple-gray-50 rounded-lg p-2">
                                <span class="text-apple-gray-500 block mb-0.5">Created</span>
                                <span class="text-apple-gray-900 font-medium">{{ $complaint->created_at->format('M d, Y') }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Card Footer -->
                    <div class="px-4 py-3 bg-apple-gray-50 border-t border-apple-gray-100">
                        <a href="{{ route('complaints.show', $complaint) }}"
                           class="flex items-center justify-center w-full py-2 text-sm font-medium text-apple-blue hover:text-white hover:bg-apple-blue rounded-apple transition-all duration-200">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                            View Details
                        </a>
                    </div>
                </div>
            @empty
                <div class="bg-white rounded-apple-lg shadow-apple p-8 text-center">
                    <div class="flex flex-col items-center">
                        <div class="w-16 h-16 bg-apple-gray-100 rounded-full flex items-center justify-center mb-4">
                            <svg class="w-8 h-8 text-apple-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                        </div>
                        <p class="text-sm font-medium text-apple-gray-700">No tickets found</p>
                        <p class="text-xs text-apple-gray-500 mt-1">Try adjusting your search or filter criteria</p>
                        <a href="{{ route('complaints.create') }}" class="mt-4 inline-flex items-center px-4 py-2 bg-apple-blue text-white text-sm font-medium rounded-apple hover:bg-blue-600 transition-colors">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                            </svg>
                            Create New Ticket
                        </a>
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
