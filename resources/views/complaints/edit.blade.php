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
                        <option value="partial_closed" {{ old('status', $complaint->status) === 'partial_closed' ? 'selected' : '' }}>⏳ Partial Closed (Awaiting Another Dept)</option>
                        <option value="resolved" {{ old('status', $complaint->status) === 'resolved' ? 'selected' : '' }}>Resolved</option>
                        <option value="closed" {{ old('status', $complaint->status) === 'closed' ? 'selected' : '' }}>Closed</option>
                        <option value="escalated" {{ old('status', $complaint->status) === 'escalated' ? 'selected' : '' }}>Escalated</option>
                    </select>
                    @error('status')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Branch -->
                <div>
                    <label for="branch_id" class="block text-sm font-medium text-apple-gray-700 mb-2">
                        Branch <span class="text-red-500">*</span>
                    </label>
                    <select id="branch_id"
                            name="branch_id"
                            required
                            class="block w-full px-4 py-3 bg-apple-gray-50 border-0 rounded-apple focus:ring-2 focus:ring-apple-blue focus:bg-white transition-all duration-200 @error('branch_id') ring-2 ring-red-500 @enderror">
                        @foreach($branches as $branch)
                            <option value="{{ $branch->id }}" {{ old('branch_id', $complaint->branch_id) == $branch->id ? 'selected' : '' }}>
                                {{ $branch->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('branch_id')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Employer -->
                <div>
                    <label for="employer_id" class="block text-sm font-medium text-apple-gray-700 mb-2">
                        Employer <span class="text-red-500">*</span>
                    </label>
                    <select id="employer_id"
                            name="employer_id"
                            required
                            class="block w-full px-4 py-3 bg-apple-gray-50 border-0 rounded-apple focus:ring-2 focus:ring-apple-blue focus:bg-white transition-all duration-200 @error('employer_id') ring-2 ring-red-500 @enderror">
                        @foreach($employers as $employer)
                            <option value="{{ $employer->id }}" {{ old('employer_id', $complaint->employer_id) == $employer->id ? 'selected' : '' }}>
                                {{ $employer->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('employer_id')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Payment Method -->
                <div>
                    <label for="payment_method_id" class="block text-sm font-medium text-apple-gray-700 mb-2">
                        Payment Method <span class="text-red-500">*</span>
                    </label>
                    <select id="payment_method_id"
                            name="payment_method_id"
                            required
                            class="block w-full px-4 py-3 bg-apple-gray-50 border-0 rounded-apple focus:ring-2 focus:ring-apple-blue focus:bg-white transition-all duration-200 @error('payment_method_id') ring-2 ring-red-500 @enderror">
                        @foreach($paymentMethods as $paymentMethod)
                            <option value="{{ $paymentMethod->id }}" {{ old('payment_method_id', $complaint->payment_method_id) == $paymentMethod->id ? 'selected' : '' }}>
                                {{ $paymentMethod->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('payment_method_id')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Partial Closed Section - Shows when status is partial_closed -->
                <div id="partial-closed-section" class="bg-orange-50 border border-orange-200 rounded-apple p-4 space-y-4" style="display: {{ old('status', $complaint->status) === 'partial_closed' ? 'block' : 'none' }};">
                    <div class="flex items-center gap-2 text-orange-800">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <span class="font-semibold">Partial Closure Details</span>
                    </div>
                    <p class="text-sm text-orange-700">Your department has completed its work, but another department needs to take action.</p>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- Completed Department -->
                        <div>
                            <label for="completed_department_display" class="block text-sm font-medium text-orange-900 mb-2">
                                Your Department (Completed)
                            </label>
                            <input type="text"
                                   id="completed_department_display"
                                   value="{{ auth()->user()->department->name ?? 'N/A' }}"
                                   readonly
                                   class="block w-full px-4 py-3 bg-gray-100 border border-orange-200 rounded-apple text-gray-700 cursor-not-allowed">
                            @if(auth()->user()->department_id)
                                <input type="hidden" name="completed_department" value="{{ auth()->user()->department_id }}">
                            @endif
                            <p class="mt-1 text-xs text-orange-600">Automatically set from your assigned department</p>
                        </div>

                        <!-- Pending Department -->
                        <div>
                            <label for="pending_department" class="block text-sm font-medium text-orange-900 mb-2">
                                Awaiting Department <span class="text-red-500">*</span>
                            </label>
                            <select id="pending_department"
                                    name="pending_department"
                                    class="block w-full px-4 py-3 bg-white border border-orange-200 rounded-apple focus:ring-2 focus:ring-orange-500 focus:border-transparent transition-all duration-200">
                                <option value="">-- Select Department --</option>
                                @foreach($departments as $department)
                                    @if(auth()->user()->department_id != $department->id)
                                        <option value="{{ $department->id }}" {{ old('pending_department', $complaint->pending_department) == $department->id ? 'selected' : '' }}>
                                            {{ $department->name }}
                                        </option>
                                    @endif
                                @endforeach
                            </select>
                            @error('pending_department')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Partial Close Notes -->
                    <div>
                        <label for="partial_close_notes" class="block text-sm font-medium text-orange-900 mb-2">
                            Notes for Pending Department
                        </label>
                        <textarea id="partial_close_notes"
                                  name="partial_close_notes"
                                  rows="3"
                                  class="block w-full px-4 py-3 bg-white border border-orange-200 rounded-apple focus:ring-2 focus:ring-orange-500 focus:border-transparent transition-all duration-200"
                                  placeholder="Describe what the other department needs to do (e.g., 'IT needs to update client data in the system')...">{{ old('partial_close_notes', $complaint->partial_close_notes) }}</textarea>
                        @error('partial_close_notes')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
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
                                {{ $agent->name }}
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
                            <p class="text-sm font-medium text-apple-gray-900">{{ $complaint->department->name ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-apple-gray-500">Employer</p>
                            <p class="text-sm font-medium text-apple-gray-900">{{ $complaint->employer?->name ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-apple-gray-500">Payment Method</p>
                            <p class="text-sm font-medium text-apple-gray-900">{{ $complaint->paymentMethod?->name ?? 'N/A' }}</p>
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
