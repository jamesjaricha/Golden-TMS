<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-semibold text-2xl text-apple-gray-900 leading-tight">
                    Edit Ticket #{{ $complaint->ticket_number }}
                </h2>
                <p class="text-sm text-apple-gray-500 mt-1">Update ticket status and assignment</p>
            </div>
            <a href="{{ route('complaints.show', $complaint) }}"
               class="inline-flex items-center px-4 py-2 bg-apple-gray-100 text-apple-gray-700 font-medium rounded-apple hover:bg-apple-gray-200 transition-all duration-200">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Back
            </a>
        </div>
    </x-slot>

    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-white rounded-apple-lg shadow-apple p-8">
            <form method="POST" action="{{ route('complaints.update', $complaint) }}" class="space-y-6">
                @csrf
                @method('PATCH')

                <!-- Status -->
                <div>
                    <label for="status" class="block text-sm font-medium text-apple-gray-700 mb-2">
                        Status <span class="text-red-500">*</span>
                    </label>
                    <select id="status"
                            name="status"
                            required
                            class="block w-full px-4 py-3 bg-apple-gray-50 border-0 rounded-apple focus:ring-2 focus:ring-apple-blue focus:bg-white transition-all duration-200 @error('status') ring-2 ring-red-500 @enderror">
                        <option value="pending" {{ old('status', $complaint->status) === 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="assigned" {{ old('status', $complaint->status) === 'assigned' ? 'selected' : '' }}>Assigned</option>
                        <option value="in_progress" {{ old('status', $complaint->status) === 'in_progress' ? 'selected' : '' }}>In Progress</option>
                        <option value="resolved" {{ old('status', $complaint->status) === 'resolved' ? 'selected' : '' }}>Resolved</option>
                        <option value="closed" {{ old('status', $complaint->status) === 'closed' ? 'selected' : '' }}>Closed</option>
                        <option value="escalated" {{ old('status', $complaint->status) === 'escalated' ? 'selected' : '' }}>Escalated</option>
                    </select>
                    @error('status')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Priority -->
                <div>
                    <label for="priority" class="block text-sm font-medium text-apple-gray-700 mb-2">
                        Priority <span class="text-red-500">*</span>
                    </label>
                    <select id="priority"
                            name="priority"
                            required
                            class="block w-full px-4 py-3 bg-apple-gray-50 border-0 rounded-apple focus:ring-2 focus:ring-apple-blue focus:bg-white transition-all duration-200 @error('priority') ring-2 ring-red-500 @enderror">
                        <option value="low" {{ old('priority', $complaint->priority) === 'low' ? 'selected' : '' }}>Low</option>
                        <option value="medium" {{ old('priority', $complaint->priority) === 'medium' ? 'selected' : '' }}>Medium</option>
                        <option value="high" {{ old('priority', $complaint->priority) === 'high' ? 'selected' : '' }}>High</option>
                        <option value="urgent" {{ old('priority', $complaint->priority) === 'urgent' ? 'selected' : '' }}>Urgent</option>
                    </select>
                    @error('priority')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Assigned To -->
                <div>
                    <label for="assigned_to" class="block text-sm font-medium text-apple-gray-700 mb-2">
                        Assign To
                    </label>
                    <select id="assigned_to"
                            name="assigned_to"
                            class="block w-full px-4 py-3 bg-apple-gray-50 border-0 rounded-apple focus:ring-2 focus:ring-apple-blue focus:bg-white transition-all duration-200 @error('assigned_to') ring-2 ring-red-500 @enderror">
                        <option value="">Unassigned</option>
                        @foreach($agents as $agent)
                            <option value="{{ $agent->id }}" {{ old('assigned_to', $complaint->assigned_to) == $agent->id ? 'selected' : '' }}>
                                {{ $agent->name }} ({{ ucwords(str_replace('_', ' ', $agent->role)) }})
                            </option>
                        @endforeach
                    </select>
                    @error('assigned_to')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Resolution Notes -->
                <div>
                    <label for="resolution_notes" class="block text-sm font-medium text-apple-gray-700 mb-2">
                        Resolution Notes
                    </label>
                    <textarea id="resolution_notes"
                              name="resolution_notes"
                              rows="5"
                              class="block w-full px-4 py-3 bg-apple-gray-50 border-0 rounded-apple focus:ring-2 focus:ring-apple-blue focus:bg-white transition-all duration-200 @error('resolution_notes') ring-2 ring-red-500 @enderror"
                              placeholder="Add notes about how this complaint was resolved...">{{ old('resolution_notes', $complaint->resolution_notes) }}</textarea>
                    @error('resolution_notes')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Add Comment -->
                <div class="bg-blue-50 border border-blue-200 rounded-apple p-4">
                    <label for="comment" class="block text-sm font-medium text-blue-900 mb-2">
                        <svg class="w-5 h-5 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/>
                        </svg>
                        Add Comment (Optional)
                    </label>
                    <textarea id="comment"
                              name="comment"
                              rows="3"
                              class="block w-full px-4 py-3 bg-white border border-blue-200 rounded-apple focus:ring-2 focus:ring-apple-blue focus:border-transparent transition-all duration-200 @error('comment') ring-2 ring-red-500 @enderror"
                              placeholder="Add a comment about this update (e.g., reason for reassignment, progress notes)...">{{ old('comment') }}</textarea>
                    <p class="text-xs text-blue-700 mt-1">💡 Use comments to explain changes, escalations, or handoffs to other agents</p>
                    @error('comment')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Client Info (Read Only) -->
                <div class="pt-6 border-t border-apple-gray-100">
                    <h3 class="text-sm font-semibold text-apple-gray-900 mb-4">Client Information (Read Only)</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 bg-apple-gray-50 rounded-apple p-4">
                        <div>
                            <p class="text-xs text-apple-gray-500">Full Name</p>
                            <p class="text-sm font-medium text-apple-gray-900">{{ $complaint->full_name }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-apple-gray-500">Policy Number</p>
                            <p class="text-sm font-medium text-apple-gray-900">{{ $complaint->policy_number }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-apple-gray-500">Phone</p>
                            <p class="text-sm font-medium text-apple-gray-900">{{ $complaint->phone_number }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-apple-gray-500">Location</p>
                            <p class="text-sm font-medium text-apple-gray-900">{{ $complaint->location }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-apple-gray-500">Branch Visited</p>
                            <p class="text-sm font-medium text-apple-gray-900">{{ $complaint->visited_branch }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-apple-gray-500">Department</p>
                            <p class="text-sm font-medium text-apple-gray-900">{{ $complaint->department }}</p>
                        </div>
                    </div>
                </div>

                <!-- Submit Buttons -->
                <div class="flex items-center justify-end space-x-3 pt-4 border-t border-apple-gray-100">
                    <a href="{{ route('complaints.show', $complaint) }}"
                       class="px-6 py-3 bg-apple-gray-100 text-apple-gray-700 font-medium rounded-apple hover:bg-apple-gray-200 transition-all duration-200">
                        Cancel
                    </a>
                    <button type="submit"
                            class="px-6 py-3 bg-apple-blue text-white font-semibold rounded-apple hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-apple-blue transition-all duration-200 shadow-apple hover:shadow-apple-md transform hover:scale-105">
                        Update Ticket
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
