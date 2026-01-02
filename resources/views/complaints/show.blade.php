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

                <!-- Partial Close Notes -->
                @if($complaint->status === 'partial_closed' || $complaint->partial_close_notes)
                    <div class="bg-orange-50 border border-orange-200 rounded-apple-lg p-6">
                        <h3 class="text-lg font-semibold text-orange-900 mb-4 flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            Partial Closure - Awaiting {{ $complaint->pending_department ?? 'Another Department' }}
                        </h3>
                        <div class="space-y-3">
                            @if($complaint->completed_department)
                                <div class="flex items-center text-orange-800">
                                    <svg class="w-4 h-4 mr-2 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                    </svg>
                                    <span class="text-sm"><strong>{{ $complaint->completed_department }}</strong> has completed their work</span>
                                </div>
                            @endif
                            @if($complaint->pending_department)
                                <div class="flex items-center text-orange-800">
                                    <svg class="w-4 h-4 mr-2 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    <span class="text-sm"><strong>{{ $complaint->pending_department }}</strong> is still working on this ticket</span>
                                </div>
                            @endif
                            @if($complaint->partial_close_notes)
                                <div class="mt-3 pt-3 border-t border-orange-200">
                                    <p class="text-sm font-medium text-orange-900 mb-1">Notes:</p>
                                    <p class="text-orange-800 whitespace-pre-wrap">{{ $complaint->partial_close_notes }}</p>
                                </div>
                            @endif
                            @if($complaint->partial_closed_at)
                                <p class="text-xs text-orange-600 mt-2">
                                    Partial closed on {{ $complaint->partial_closed_at->format('M d, Y h:i A') }}
                                </p>
                            @endif
                        </div>
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
                            <dd class="mt-1 text-sm text-apple-gray-900">{{ $complaint->department->name ?? 'N/A' }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-apple-gray-500">Employer</dt>
                            <dd class="mt-1 text-sm text-apple-gray-900">{{ $complaint->employer?->name ?? 'N/A' }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-apple-gray-500">Payment Method</dt>
                            <dd class="mt-1 text-sm text-apple-gray-900">{{ $complaint->paymentMethod?->name ?? 'N/A' }}</dd>
                        </div>
                    </dl>
                </div>

                <!-- Timeline & Task Reminders -->
                <div class="bg-white rounded-apple-lg shadow-apple p-6" x-data="{ showReminderModal: false, editingReminder: null, activeTab: 'timeline' }">
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center gap-4">
                            <button @click="activeTab = 'timeline'"
                                    :class="activeTab === 'timeline' ? 'text-apple-blue border-b-2 border-apple-blue' : 'text-apple-gray-500'"
                                    class="text-lg font-semibold pb-1 transition-colors">
                                Activity Timeline
                            </button>
                            <button @click="activeTab = 'tasks'"
                                    :class="activeTab === 'tasks' ? 'text-apple-blue border-b-2 border-apple-blue' : 'text-apple-gray-500'"
                                    class="text-lg font-semibold pb-1 transition-colors flex items-center gap-2">
                                📋 Task Reminders
                                @if($complaint->reminders && $complaint->reminders->where('status', 'pending')->count() > 0)
                                    <span class="inline-flex items-center justify-center px-2 py-0.5 text-xs font-bold rounded-full bg-red-600 text-white">
                                        {{ $complaint->reminders->where('status', 'pending')->count() }}
                                    </span>
                                @endif
                            </button>
                        </div>
                        @if(auth()->user()->role !== 'user')
                            <button @click="showReminderModal = true; editingReminder = null; activeTab = 'tasks'"
                                    class="inline-flex items-center px-3 py-1.5 bg-red-600 text-white text-sm font-medium rounded-lg hover:bg-red-700 transition-colors">
                                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                </svg>
                                Add Task
                            </button>
                        @endif
                    </div>

                    <!-- Timeline Tab -->
                    <div x-show="activeTab === 'timeline'" x-transition>
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

                    <!-- Task Reminders Tab -->
                    <div x-show="activeTab === 'tasks'" x-transition>
                        @if($complaint->reminders && $complaint->reminders->count() > 0)
                            <div class="space-y-3">
                                @foreach($complaint->reminders as $reminder)
                                    <div class="border-l-4 {{ $reminder->status === 'completed' ? 'border-green-500 bg-green-50' : ($reminder->isOverdue() ? 'border-red-500 bg-red-50' : 'border-blue-500 bg-blue-50') }} rounded-r-lg p-4 hover:shadow-md transition-shadow">
                                        <div class="flex items-start justify-between gap-4">
                                            <div class="flex-1 min-w-0">
                                                <div class="flex items-center flex-wrap gap-2 mb-2">
                                                    @if($reminder->status === 'completed')
                                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold bg-green-600 text-white">
                                                            ✓ Completed
                                                        </span>
                                                    @elseif($reminder->status === 'cancelled')
                                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold bg-gray-600 text-white">
                                                            Cancelled
                                                        </span>
                                                    @elseif($reminder->isOverdue())
                                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold bg-red-600 text-white">
                                                            ⚠ Overdue
                                                        </span>
                                                    @else
                                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold bg-blue-600 text-white">
                                                            Pending
                                                        </span>
                                                    @endif

                                                    @if($reminder->priority === 'high')
                                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold bg-red-600 text-white">
                                                            🔥 High
                                                        </span>
                                                    @elseif($reminder->priority === 'medium')
                                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold bg-yellow-600 text-white">
                                                            ⏰ Medium
                                                        </span>
                                                    @endif
                                                </div>

                                                <p class="text-sm font-semibold text-apple-gray-900 mb-2 {{ $reminder->status === 'completed' ? 'line-through' : '' }}">
                                                    {{ $reminder->task_description }}
                                                </p>

                                                @if($reminder->notes)
                                                    <div class="bg-white bg-opacity-70 border border-blue-200 p-2 mb-2 rounded text-xs">
                                                        <span class="font-semibold text-blue-900">Notes:</span> <span class="text-blue-800">{{ $reminder->notes }}</span>
                                                    </div>
                                                @endif

                                                <div class="flex flex-wrap gap-x-4 gap-y-1 text-xs text-apple-gray-700">
                                                    <span><strong>Assigned:</strong> {{ $reminder->user->name }}</span>
                                                    <span><strong>Due:</strong> {{ $reminder->reminder_datetime->format('M j, g:i A') }}</span>
                                                    <span><strong>By:</strong> {{ $reminder->creator->name }}</span>
                                                </div>

                                                @if($reminder->status === 'completed')
                                                    <p class="text-xs text-green-700 font-medium mt-1">
                                                        ✓ Completed by {{ $reminder->completedByUser->name }} on {{ $reminder->completed_at->format('M j, g:i A') }}
                                                    </p>
                                                @endif
                                            </div>

                                            @if($reminder->status === 'pending' && (auth()->id() === $reminder->user_id || auth()->user()->role !== 'user'))
                                                <div class="flex flex-col gap-1">
                                                    <form action="{{ route('reminders.complete', [$complaint, $reminder]) }}" method="POST">
                                                        @csrf
                                                        <button type="submit"
                                                                class="inline-flex items-center px-2 py-1 bg-green-600 text-white text-xs font-medium rounded hover:bg-green-700"
                                                                title="Complete">
                                                            ✓
                                                        </button>
                                                    </form>

                                                    <div x-data="{ showSnooze{{ $reminder->id }}: false }" class="relative">
                                                        <button @click="showSnooze{{ $reminder->id }} = !showSnooze{{ $reminder->id }}"
                                                                class="inline-flex items-center px-2 py-1 bg-yellow-600 text-white text-xs font-medium rounded hover:bg-yellow-700"
                                                                title="Snooze">
                                                            💤
                                                        </button>
                                                        <div x-show="showSnooze{{ $reminder->id }}"
                                                             @click.away="showSnooze{{ $reminder->id }} = false"
                                                             class="absolute right-0 top-full mt-1 w-24 bg-white rounded shadow-lg border border-gray-200 z-10">
                                                            <form action="{{ route('reminders.snooze', [$complaint, $reminder]) }}" method="POST">
                                                                @csrf
                                                                <input type="hidden" name="hours" value="1">
                                                                <button type="submit" class="block w-full text-left px-2 py-1 text-xs hover:bg-gray-50">1 hour</button>
                                                            </form>
                                                            <form action="{{ route('reminders.snooze', [$complaint, $reminder]) }}" method="POST">
                                                                @csrf
                                                                <input type="hidden" name="hours" value="4">
                                                                <button type="submit" class="block w-full text-left px-2 py-1 text-xs hover:bg-gray-50">4 hours</button>
                                                            </form>
                                                            <form action="{{ route('reminders.snooze', [$complaint, $reminder]) }}" method="POST">
                                                                @csrf
                                                                <input type="hidden" name="hours" value="24">
                                                                <button type="submit" class="block w-full text-left px-2 py-1 text-xs hover:bg-gray-50">1 day</button>
                                                            </form>
                                                        </div>
                                                    </div>

                                                    <form action="{{ route('reminders.destroy', [$complaint, $reminder]) }}" method="POST" onsubmit="return confirm('Delete this task?')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit"
                                                                class="inline-flex items-center px-2 py-1 bg-red-600 text-white text-xs font-medium rounded hover:bg-red-700"
                                                                title="Delete">
                                                            ✕
                                                        </button>
                                                    </form>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-8">
                                <svg class="w-12 h-12 text-apple-gray-400 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <p class="text-sm text-apple-gray-600 font-medium">No task reminders yet</p>
                                <p class="text-xs text-apple-gray-500 mt-1">Add reminders for follow-up tasks</p>
                            </div>
                        @endif
                    </div>

                    <!-- Add/Edit Task Reminder Modal -->
                    <div x-show="showReminderModal"
                         x-transition:enter="transition ease-out duration-300"
                         x-transition:enter-start="opacity-0"
                         x-transition:enter-end="opacity-100"
                         x-transition:leave="transition ease-in duration-200"
                         x-transition:leave-start="opacity-100"
                         x-transition:leave-end="opacity-0"
                         class="fixed inset-0 z-50 overflow-y-auto"
                         style="display: none;">
                        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
                            <!-- Backdrop -->
                            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="showReminderModal = false"></div>

                            <!-- Modal Panel -->
                            <div class="relative bg-white rounded-apple-lg shadow-xl transform transition-all sm:max-w-lg sm:w-full mx-auto"
                                 @click.away="showReminderModal = false">
                                <form action="{{ route('reminders.store', $complaint) }}" method="POST">
                                    @csrf
                                    <div class="px-6 pt-5 pb-4">
                                        <div class="flex items-center justify-between mb-4">
                                            <h3 class="text-lg font-semibold text-apple-gray-900">
                                                Add Task Reminder
                                            </h3>
                                            <button type="button" @click="showReminderModal = false" class="text-apple-gray-400 hover:text-apple-gray-600">
                                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                                </svg>
                                            </button>
                                        </div>

                                        <div class="space-y-4">
                                            <!-- Assign To -->
                                            <div>
                                                <label for="reminder_user_id" class="block text-sm font-medium text-apple-gray-700 mb-1">Assign To</label>
                                                <select name="user_id" id="reminder_user_id" required
                                                        class="w-full px-3 py-2 border border-apple-gray-300 rounded-apple focus:ring-2 focus:ring-apple-blue focus:border-apple-blue">
                                                    <option value="">Select Agent</option>
                                                    @foreach(\App\Models\User::whereIn('role', ['support_agent', 'manager', 'super_admin'])->orderBy('name')->get() as $user)
                                                        <option value="{{ $user->id }}" {{ $complaint->assigned_to == $user->id ? 'selected' : '' }}>
                                                            {{ $user->name }} ({{ ucwords(str_replace('_', ' ', $user->role)) }})
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>

                                            <!-- Task Description -->
                                            <div>
                                                <label for="task_description" class="block text-sm font-medium text-apple-gray-700 mb-1">Task Description</label>
                                                <input type="text" name="task_description" id="task_description" required
                                                       placeholder="e.g., Follow up with customer"
                                                       class="w-full px-3 py-2 border border-apple-gray-300 rounded-apple focus:ring-2 focus:ring-apple-blue focus:border-apple-blue">
                                            </div>

                                            <!-- Reminder Date & Time -->
                                            <div>
                                                <label for="reminder_datetime" class="block text-sm font-medium text-apple-gray-700 mb-1">Reminder Date & Time</label>
                                                <input type="datetime-local" name="reminder_datetime" id="reminder_datetime" required
                                                       min="{{ now()->format('Y-m-d\TH:i') }}"
                                                       class="w-full px-3 py-2 border border-apple-gray-300 rounded-apple focus:ring-2 focus:ring-apple-blue focus:border-apple-blue">
                                            </div>

                                            <!-- Priority -->
                                            <div>
                                                <label for="reminder_priority" class="block text-sm font-medium text-apple-gray-700 mb-1">Priority</label>
                                                <select name="priority" id="reminder_priority"
                                                        class="w-full px-3 py-2 border border-apple-gray-300 rounded-apple focus:ring-2 focus:ring-apple-blue focus:border-apple-blue">
                                                    <option value="low">Low</option>
                                                    <option value="medium" selected>Medium</option>
                                                    <option value="high">High</option>
                                                    <option value="urgent">Urgent</option>
                                                </select>
                                            </div>

                                            <!-- Notes -->
                                            <div>
                                                <label for="reminder_notes" class="block text-sm font-medium text-apple-gray-700 mb-1">Notes (Optional)</label>
                                                <textarea name="notes" id="reminder_notes" rows="2"
                                                          placeholder="Additional context or instructions..."
                                                          class="w-full px-3 py-2 border border-apple-gray-300 rounded-apple focus:ring-2 focus:ring-apple-blue focus:border-apple-blue"></textarea>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="bg-apple-gray-50 px-6 py-3 flex justify-end gap-3 rounded-b-apple-lg">
                                        <button type="button" @click="showReminderModal = false"
                                                class="px-4 py-2 text-sm font-medium text-apple-gray-700 bg-white border border-apple-gray-300 rounded-apple hover:bg-apple-gray-50 transition-colors">
                                            Cancel
                                        </button>
                                        <button type="submit"
                                                class="px-4 py-2 text-sm font-medium text-white bg-red-600 rounded-apple hover:bg-red-700 transition-colors">
                                            Create Reminder
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
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
                                @elseif($complaint->status === 'partial_closed') bg-orange-100 text-orange-800
                                @elseif($complaint->status === 'resolved') bg-green-100 text-green-800
                                @elseif($complaint->status === 'closed') bg-gray-100 text-gray-800
                                @elseif($complaint->status === 'escalated') bg-red-100 text-red-800
                                @endif">
                                {{ ucwords(str_replace('_', ' ', $complaint->status)) }}
                            </span>
                        </div>

                        @if($complaint->status === 'partial_closed')
                            <!-- Partial Closed Info Box -->
                            <div class="bg-orange-50 border border-orange-200 rounded-apple p-3 mt-2">
                                <div class="flex items-center gap-2 text-orange-800 mb-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    <span class="text-xs font-semibold">Partial Closure</span>
                                </div>
                                @if($complaint->completed_department)
                                    <p class="text-xs text-orange-700">
                                        <span class="font-medium">✓ Completed:</span> {{ $complaint->completed_department }}
                                    </p>
                                @endif
                                @if($complaint->pending_department)
                                    <p class="text-xs text-orange-700 mt-1">
                                        <span class="font-medium">⏳ Awaiting:</span> {{ $complaint->pending_department }}
                                    </p>
                                @endif
                                @if($complaint->partial_closed_at)
                                    <p class="text-xs text-orange-600 mt-1">
                                        Since {{ $complaint->partial_closed_at->format('M d, Y h:i A') }}
                                    </p>
                                @endif
                            </div>
                        @endif

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

                        @if(Auth::user()->role === 'support_agent' && $complaint->assigned_to !== Auth::id() && !in_array($complaint->status, ['closed', 'resolved']))
                            <!-- Take Over Button for Support Agents -->
                            <div class="pt-3 border-t border-apple-gray-200">
                                <form action="{{ route('complaints.assign', $complaint) }}" method="POST"
                                      onsubmit="return confirm('Are you sure you want to take over this ticket? The current assignee will be notified.');">
                                    @csrf
                                    <input type="hidden" name="assigned_to" value="{{ Auth::id() }}">
                                    <button type="submit"
                                            class="w-full inline-flex items-center justify-center px-4 py-2 bg-indigo-600 text-white font-medium rounded-apple hover:bg-indigo-700 transition-all duration-200">
                                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
                                        </svg>
                                        Take Over Ticket
                                    </button>
                                </form>
                                <p class="text-xs text-apple-gray-500 mt-2 text-center">
                                    Take ownership to assist this client
                                </p>
                            </div>
                        @endif

                        @if(in_array(Auth::user()->role, ['super_admin', 'manager']))
                            <!-- Reassign Section for Admins/Managers -->
                            <div class="pt-3 border-t border-apple-gray-200" x-data="{ showAssign: false }">
                                <button @click="showAssign = !showAssign"
                                        type="button"
                                        class="w-full inline-flex items-center justify-center px-4 py-2 bg-apple-gray-100 text-apple-gray-700 font-medium rounded-apple hover:bg-apple-gray-200 transition-all duration-200">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                                    </svg>
                                    <span x-text="showAssign ? 'Cancel' : 'Reassign Ticket'"></span>
                                </button>

                                <div x-show="showAssign"
                                     x-transition:enter="transition ease-out duration-200"
                                     x-transition:enter-start="opacity-0 -translate-y-2"
                                     x-transition:enter-end="opacity-100 translate-y-0"
                                     class="mt-3"
                                     style="display: none;">
                                    <form action="{{ route('complaints.assign', $complaint) }}" method="POST">
                                        @csrf
                                        <label for="assigned_to" class="block text-xs font-medium text-apple-gray-700 mb-1">
                                            Assign To
                                        </label>
                                        <select name="assigned_to" id="assigned_to" required
                                                class="block w-full px-3 py-2 text-sm bg-white border border-apple-gray-300 rounded-apple focus:ring-2 focus:ring-apple-blue focus:border-transparent">
                                            <option value="">Select Agent...</option>
                                            @foreach(\App\Models\User::whereIn('role', ['support_agent', 'manager', 'super_admin'])->orderBy('name')->get() as $agent)
                                                <option value="{{ $agent->id }}" {{ $complaint->assigned_to == $agent->id ? 'selected' : '' }}>
                                                    {{ $agent->name }} ({{ ucwords(str_replace('_', ' ', $agent->role)) }})
                                                </option>
                                            @endforeach
                                        </select>
                                        <button type="submit"
                                                class="w-full mt-2 px-4 py-2 bg-apple-blue text-white text-sm font-medium rounded-apple hover:bg-blue-600 transition-all duration-200">
                                            Assign Ticket
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @endif
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

