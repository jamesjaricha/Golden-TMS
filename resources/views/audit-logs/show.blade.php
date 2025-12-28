<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="font-semibold text-xl sm:text-2xl text-apple-gray-900 leading-tight">
                    Audit Log Details
                </h2>
                <p class="text-sm text-apple-gray-500 mt-1">
                    {{ $auditLog->created_at->format('F d, Y \a\t H:i:s') }}
                </p>
            </div>
            <a href="{{ route('audit-logs.index') }}"
               class="inline-flex items-center px-4 py-2 bg-apple-gray-100 text-apple-gray-700 font-semibold rounded-apple hover:bg-apple-gray-200 focus:outline-none focus:ring-2 focus:ring-apple-gray-400 transition-all duration-200">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Back to Logs
            </a>
        </div>
    </x-slot>

    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Main Info Card -->
        <div class="bg-white rounded-apple-lg shadow-apple p-6 mb-6">
            <div class="flex items-center justify-between mb-6">
                <span class="px-3 py-1.5 inline-flex text-sm leading-5 font-semibold rounded-full
                    @if($auditLog->status === 'success') bg-green-100 text-green-800
                    @elseif($auditLog->status === 'failed') bg-red-100 text-red-800
                    @else bg-yellow-100 text-yellow-800
                    @endif">
                    {{ ucfirst($auditLog->status) }}
                </span>
                <span class="px-3 py-1.5 inline-flex text-sm leading-5 font-semibold rounded-full
                    @if($auditLog->action_category === 'auth') bg-purple-100 text-purple-800
                    @elseif($auditLog->action_category === 'ticket') bg-blue-100 text-blue-800
                    @elseif($auditLog->action_category === 'user') bg-green-100 text-green-800
                    @elseif($auditLog->action_category === 'report') bg-yellow-100 text-yellow-800
                    @else bg-gray-100 text-gray-800
                    @endif">
                    {{ ucfirst($auditLog->action_category) }} / {{ ucwords(str_replace('_', ' ', $auditLog->action)) }}
                </span>
            </div>

            <h3 class="text-lg font-medium text-apple-gray-900 mb-4">{{ $auditLog->description }}</h3>

            @if($auditLog->failure_reason)
                <div class="bg-red-50 border border-red-200 rounded-apple p-4 mb-4">
                    <div class="flex">
                        <svg class="h-5 w-5 text-red-400 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                        </svg>
                        <div>
                            <h4 class="text-sm font-medium text-red-800">Failure Reason</h4>
                            <p class="text-sm text-red-700 mt-1">{{ $auditLog->failure_reason }}</p>
                        </div>
                    </div>
                </div>
            @endif

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- User Info -->
                <div>
                    <h4 class="text-sm font-semibold text-apple-gray-700 uppercase tracking-wider mb-3">User Information</h4>
                    <dl class="space-y-2">
                        <div class="flex justify-between">
                            <dt class="text-sm text-apple-gray-500">Name</dt>
                            <dd class="text-sm font-medium text-apple-gray-900">{{ $auditLog->user_name ?? 'System' }}</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-sm text-apple-gray-500">Email</dt>
                            <dd class="text-sm font-medium text-apple-gray-900">{{ $auditLog->user_email ?? '-' }}</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-sm text-apple-gray-500">Role</dt>
                            <dd class="text-sm font-medium text-apple-gray-900">{{ ucwords(str_replace('_', ' ', $auditLog->user_role ?? '-')) }}</dd>
                        </div>
                    </dl>
                </div>

                <!-- Request Info -->
                <div>
                    <h4 class="text-sm font-semibold text-apple-gray-700 uppercase tracking-wider mb-3">Request Information</h4>
                    <dl class="space-y-2">
                        <div class="flex justify-between">
                            <dt class="text-sm text-apple-gray-500">IP Address</dt>
                            <dd class="text-sm font-medium text-apple-gray-900">{{ $auditLog->ip_address ?? '-' }}</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-sm text-apple-gray-500">Device</dt>
                            <dd class="text-sm font-medium text-apple-gray-900">{{ ucfirst($auditLog->device_type ?? '-') }}</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-sm text-apple-gray-500">Browser</dt>
                            <dd class="text-sm font-medium text-apple-gray-900">{{ $auditLog->browser ?? '-' }}</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-sm text-apple-gray-500">Platform</dt>
                            <dd class="text-sm font-medium text-apple-gray-900">{{ $auditLog->platform ?? '-' }}</dd>
                        </div>
                    </dl>
                </div>
            </div>
        </div>

        <!-- Entity Info -->
        @if($auditLog->auditable_type)
            <div class="bg-white rounded-apple-lg shadow-apple p-6 mb-6">
                <h4 class="text-sm font-semibold text-apple-gray-700 uppercase tracking-wider mb-3">Affected Entity</h4>
                <dl class="space-y-2">
                    <div class="flex justify-between">
                        <dt class="text-sm text-apple-gray-500">Type</dt>
                        <dd class="text-sm font-medium text-apple-gray-900">{{ class_basename($auditLog->auditable_type) }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-sm text-apple-gray-500">ID</dt>
                        <dd class="text-sm font-medium text-apple-gray-900">{{ $auditLog->auditable_id }}</dd>
                    </div>
                    @if($auditLog->auditable_identifier)
                        <div class="flex justify-between">
                            <dt class="text-sm text-apple-gray-500">Identifier</dt>
                            <dd class="text-sm font-medium text-apple-gray-900">{{ $auditLog->auditable_identifier }}</dd>
                        </div>
                    @endif
                </dl>
            </div>
        @endif

        <!-- Changes -->
        @if($auditLog->changed_fields && count($auditLog->changed_fields) > 0)
            <div class="bg-white rounded-apple-lg shadow-apple p-6 mb-6">
                <h4 class="text-sm font-semibold text-apple-gray-700 uppercase tracking-wider mb-4">Changes Made</h4>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-apple-gray-200">
                        <thead class="bg-apple-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-apple-gray-700 uppercase">Field</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-apple-gray-700 uppercase">Old Value</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-apple-gray-700 uppercase">New Value</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-apple-gray-100">
                            @foreach($auditLog->changed_fields as $field)
                                <tr>
                                    <td class="px-4 py-3 text-sm font-medium text-apple-gray-900">
                                        {{ ucwords(str_replace('_', ' ', $field)) }}
                                    </td>
                                    <td class="px-4 py-3 text-sm text-red-600">
                                        <span class="bg-red-50 px-2 py-1 rounded">
                                            {{ $auditLog->old_values[$field] ?? '(empty)' }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 text-sm text-green-600">
                                        <span class="bg-green-50 px-2 py-1 rounded">
                                            {{ $auditLog->new_values[$field] ?? '(empty)' }}
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif

        <!-- Metadata -->
        @if($auditLog->metadata)
            <div class="bg-white rounded-apple-lg shadow-apple p-6 mb-6">
                <h4 class="text-sm font-semibold text-apple-gray-700 uppercase tracking-wider mb-3">Additional Data</h4>
                <pre class="bg-apple-gray-50 rounded-apple p-4 text-sm text-apple-gray-700 overflow-x-auto">{{ json_encode($auditLog->metadata, JSON_PRETTY_PRINT) }}</pre>
            </div>
        @endif

        <!-- Raw User Agent -->
        @if($auditLog->user_agent)
            <div class="bg-white rounded-apple-lg shadow-apple p-6">
                <h4 class="text-sm font-semibold text-apple-gray-700 uppercase tracking-wider mb-3">Raw User Agent</h4>
                <p class="text-xs text-apple-gray-500 font-mono break-all">{{ $auditLog->user_agent }}</p>
            </div>
        @endif
    </div>
</x-app-layout>
