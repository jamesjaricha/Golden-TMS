<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-semibold text-2xl text-apple-gray-900 leading-tight">
                    Ticket #{{ $complaint->ticket_number }}
                </h2>
                <p class="text-sm text-apple-gray-500 mt-1">{{ $complaint->full_name }} • {{ $complaint->created_at->format('M d, Y h:i A') }}</p>
            </div>
            <div class="flex space-x-2">
                <!-- Export Dropdown -->
                <div x-data="{ open: false }" class="relative">
                    <button @click="open = !open" @click.away="open = false"
                           class="inline-flex items-center px-4 py-2 bg-apple-gray-100 text-apple-gray-700 font-medium rounded-apple hover:bg-apple-gray-200 transition-all duration-200">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10"/>
                        </svg>
                        Export
                    </button>
                    <div x-show="open"
                         x-transition:enter="transition ease-out duration-100"
                         x-transition:enter-start="transform opacity-0 scale-95"
                         x-transition:enter-end="transform opacity-100 scale-100"
                         x-transition:leave="transition ease-in duration-75"
                         x-transition:leave-start="transform opacity-100 scale-100"
                         x-transition:leave-end="transform opacity-0 scale-95"
                         class="absolute right-0 mt-2 w-48 rounded-apple-lg shadow-apple-lg bg-white ring-1 ring-black ring-opacity-5 z-50"
                         style="display: none;">
                        <div class="py-1">
                            <a href="{{ route('complaints.print', $complaint) }}" target="_blank"
                               class="flex items-center px-4 py-2 text-sm text-apple-gray-700 hover:bg-apple-gray-50 transition-colors">
                                <svg class="w-5 h-5 mr-3 text-apple-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                                </svg>
                                Print Ticket
                            </a>
                            <a href="{{ route('complaints.export-ticket-pdf', $complaint) }}"
                               class="flex items-center px-4 py-2 text-sm text-apple-gray-700 hover:bg-apple-gray-50 transition-colors">
                                <svg class="w-5 h-5 mr-3 text-red-600" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zM6.293 6.707a1 1 0 010-1.414l3-3a1 1 0 011.414 0l3 3a1 1 0 01-1.414 1.414L11 5.414V13a1 1 0 11-2 0V5.414L7.707 6.707a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
                                </svg>
                                Download PDF
                            </a>
                        </div>
                    </div>
                </div>

                <a href="{{ route('complaints.index') }}"
                   class="inline-flex items-center px-4 py-2 bg-apple-gray-100 text-apple-gray-700 font-medium rounded-apple hover:bg-apple-gray-200 transition-all duration-200">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Back
                </a>
                @if(Auth::user()->isAdmin() || (Auth::user()->role === 'support_agent' && $complaint->assigned_to === Auth::id()))
                    <a href="{{ route('complaints.edit', $complaint) }}"
                       class="inline-flex items-center px-4 py-2 bg-apple-blue text-white font-semibold rounded-apple hover:bg-blue-600 transition-all duration-200">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                        Attend to Ticket
                    </a>
                @endif
            </div>
        </div>
    </x-slot>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Main Content -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Complaint Details -->
                <div class="bg-white rounded-apple-lg shadow-apple p-6">
                    <h3 class="text-lg font-semibold text-apple-gray-900 mb-4">Complaint Details</h3>
                    <div class="prose max-w-none">
                        <p class="text-apple-gray-700 whitespace-pre-wrap">{{ $complaint->complaint_text }}</p>
                    </div>
                </div>

                <!-- Resolution Notes -->
                @if($complaint->resolution_notes)
                    <div class="bg-green-50 border border-green-200 rounded-apple-lg p-6">
                        <h3 class="text-lg font-semibold text-green-900 mb-4 flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            Resolution Notes
                        </h3>
                        <p class="text-green-800 whitespace-pre-wrap">{{ $complaint->resolution_notes }}</p>
                    </div>
                @endif

                <!-- Client Information -->
                <div class="bg-white rounded-apple-lg shadow-apple p-6">
                    <h3 class="text-lg font-semibold text-apple-gray-900 mb-4">Client Information</h3>
                    <dl class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <dt class="text-sm font-medium text-apple-gray-500">Full Name</dt>
                            <dd class="mt-1 text-sm text-apple-gray-900">{{ $complaint->full_name }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-apple-gray-500">Policy Number</dt>
                            <dd class="mt-1 text-sm text-apple-gray-900">{{ $complaint->policy_number }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-apple-gray-500">Phone Number</dt>
                            <dd class="mt-1 text-sm text-apple-gray-900">{{ $complaint->phone_number }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-apple-gray-500">Location</dt>
                            <dd class="mt-1 text-sm text-apple-gray-900">{{ $complaint->location }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-apple-gray-500">Branch Visited</dt>
                            <dd class="mt-1 text-sm text-apple-gray-900">{{ $complaint->visited_branch }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-apple-gray-500">Department</dt>
                            <dd class="mt-1 text-sm text-apple-gray-900">{{ $complaint->department }}</dd>
                        </div>
                    </dl>
                </div>

                <!-- Timeline -->
                <div class="bg-white rounded-apple-lg shadow-apple p-6">
                    <h3 class="text-lg font-semibold text-apple-gray-900 mb-4">Activity Timeline</h3>

                    @if($activities->count() > 0)
                        <div class="space-y-3">
                            @foreach($activities as $activity)
                                <div class="flex items-start">
                                    @php
                                        $color = 'bg-apple-blue';
                                        if(str_contains(strtolower($activity->action), 'resolved')) $color = 'bg-green-500';
                                        if(str_contains(strtolower($activity->action), 'closed')) $color = 'bg-gray-500';
                                        if(str_contains(strtolower($activity->action), 'assigned')) $color = 'bg-yellow-500';
                                        if(str_contains(strtolower($activity->action), 'updated')) $color = 'bg-blue-500';
                                        if(str_contains(strtolower($activity->action), 'comment')) $color = 'bg-purple-500';
                                    @endphp
                                    <div class="w-2 h-2 {{ $color }} rounded-full mt-2 mr-3 flex-shrink-0"></div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-medium text-apple-gray-900">{{ $activity->description }}</p>
                                        <p class="text-xs text-apple-gray-500">{{ $activity->user->name }} • {{ $activity->created_at->format('M d, Y h:i A') }}</p>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <!-- Pagination -->
                        <div class="mt-6 pt-4 border-t border-apple-gray-200">
                            {{ $activities->links() }}
                        </div>
                    @else
                        <div class="flex items-start">
                            <div class="w-2 h-2 bg-apple-blue rounded-full mt-2 mr-3"></div>
                            <div class="flex-1">
                                <p class="text-sm font-medium text-apple-gray-900">Created</p>
                                <p class="text-xs text-apple-gray-500">{{ $complaint->created_at->format('M d, Y h:i A') }}</p>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Sidebar -->
            <div class="space-y-6">
                <!-- Status & Priority -->
                <div class="bg-white rounded-apple-lg shadow-apple p-6">
                    <h3 class="text-lg font-semibold text-apple-gray-900 mb-4">Status & Priority</h3>
                    <div class="space-y-4">
                        <div>
                            <dt class="text-sm font-medium text-apple-gray-500 mb-2">Status</dt>
                            <span class="px-3 py-1 inline-flex text-sm font-semibold rounded-full
                                @if($complaint->status === 'pending') bg-yellow-100 text-yellow-800
                                @elseif($complaint->status === 'assigned') bg-blue-100 text-blue-800
                                @elseif($complaint->status === 'in_progress') bg-indigo-100 text-indigo-800
                                @elseif($complaint->status === 'resolved') bg-green-100 text-green-800
                                @elseif($complaint->status === 'closed') bg-gray-100 text-gray-800
                                @elseif($complaint->status === 'escalated') bg-red-100 text-red-800
                                @endif">
                                {{ ucwords(str_replace('_', ' ', $complaint->status)) }}
                            </span>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-apple-gray-500 mb-2">Priority</dt>
                            <span class="px-3 py-1 inline-flex text-sm font-semibold rounded-full
                                @if($complaint->priority === 'low') bg-gray-100 text-gray-800
                                @elseif($complaint->priority === 'medium') bg-blue-100 text-blue-800
                                @elseif($complaint->priority === 'high') bg-orange-100 text-orange-800
                                @elseif($complaint->priority === 'urgent') bg-red-100 text-red-800
                                @endif">
                                {{ ucfirst($complaint->priority) }}
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Assignment -->
                <div class="bg-white rounded-apple-lg shadow-apple p-6">
                    <h3 class="text-lg font-semibold text-apple-gray-900 mb-4">Assignment</h3>
                    <div class="space-y-4">
                        <div>
                            <dt class="text-sm font-medium text-apple-gray-500 mb-2">Captured By</dt>
                            <dd class="text-sm text-apple-gray-900">{{ $complaint->capturedBy->name }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-apple-gray-500 mb-2">Assigned To</dt>
                            <dd class="text-sm text-apple-gray-900">
                                {{ $complaint->assignedTo?->name ?? 'Not Assigned' }}
                            </dd>
                        </div>
                    </div>
                </div>

                <!-- Comments & Updates -->
                <div class="bg-white rounded-apple-lg shadow-apple p-6">
                    <h3 class="text-lg font-semibold text-apple-gray-900 mb-4">Comments & Updates</h3>

                    @if($complaint->comments->count() > 0)
                        <div class="space-y-3 max-h-96 overflow-y-auto">
                            @foreach($complaint->comments as $comment)
                                <div class="flex items-start space-x-3 p-3 bg-apple-gray-50 rounded-apple">
                                    <div class="w-8 h-8 bg-gradient-to-br from-apple-blue to-blue-600 rounded-full flex items-center justify-center text-white text-xs font-semibold flex-shrink-0">
                                        {{ strtoupper(substr($comment->user->name, 0, 2)) }}
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-center justify-between mb-1">
                                            <p class="text-sm font-medium text-apple-gray-900">{{ $comment->user->name }}</p>
                                            <p class="text-xs text-apple-gray-500">{{ $comment->created_at->diffForHumans() }}</p>
                                        </div>
                                        <p class="text-sm text-apple-gray-700 whitespace-pre-wrap">{{ $comment->comment }}</p>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-8">
                            <svg class="w-12 h-12 text-apple-gray-300 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                            </svg>
                            <p class="text-sm text-apple-gray-500">No comments yet</p>
                            <p class="text-xs text-apple-gray-400 mt-1">Add comments when updating the ticket</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

