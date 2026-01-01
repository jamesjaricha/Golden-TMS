<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-apple-gray-800 leading-tight">
            {{ __('Report Wizard') }}
        </h2>
    </x-slot>

    <div class="py-6 sm:py-12">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-apple rounded-apple">
                <div class="p-6 sm:p-8">
                    <!-- Wizard Header -->
                    <div class="mb-8 text-center">
                        <h3 class="text-2xl font-bold text-apple-gray-900 mb-2">Custom Report Builder</h3>
                        <p class="text-apple-gray-600">Configure your report parameters to generate customized insights</p>
                    </div>

                    <form action="{{ route('reports.generate') }}" method="POST" x-data="reportWizard()" class="space-y-8">
                        @csrf

                        <!-- Step Indicator -->
                        <div class="flex items-center justify-between mb-8">
                            <div class="flex items-center flex-1">
                                <div :class="step >= 1 ? 'bg-apple-blue text-white' : 'bg-apple-gray-200 text-apple-gray-600'" class="w-10 h-10 rounded-full flex items-center justify-center font-semibold transition-colors">1</div>
                                <div :class="step >= 2 ? 'bg-apple-blue' : 'bg-apple-gray-200'" class="flex-1 h-1 mx-2 transition-colors"></div>
                            </div>
                            <div class="flex items-center flex-1">
                                <div :class="step >= 2 ? 'bg-apple-blue text-white' : 'bg-apple-gray-200 text-apple-gray-600'" class="w-10 h-10 rounded-full flex items-center justify-center font-semibold transition-colors">2</div>
                                <div :class="step >= 3 ? 'bg-apple-blue' : 'bg-apple-gray-200'" class="flex-1 h-1 mx-2 transition-colors"></div>
                            </div>
                            <div class="flex items-center">
                                <div :class="step >= 3 ? 'bg-apple-blue text-white' : 'bg-apple-gray-200 text-apple-gray-600'" class="w-10 h-10 rounded-full flex items-center justify-center font-semibold transition-colors">3</div>
                            </div>
                        </div>

                        <!-- Step 1: Report Type -->
                        <div x-show="step === 1" x-transition class="space-y-6">
                            <div>
                                <h4 class="text-lg font-semibold text-apple-gray-900 mb-4">Step 1: Select Report Type</h4>
                                <p class="text-sm text-apple-gray-600 mb-6">Choose the type of report you want to generate</p>

                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                    <label class="relative flex flex-col p-4 border-2 rounded-apple cursor-pointer transition-all hover:border-apple-blue" :class="reportType === 'tickets' ? 'border-apple-blue bg-blue-50' : 'border-apple-gray-200'">
                                        <input type="radio" name="report_type" value="tickets" x-model="reportType" class="sr-only">
                                        <div class="flex items-center mb-2">
                                            <svg class="w-6 h-6 text-apple-blue mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                            </svg>
                                            <span class="font-semibold text-apple-gray-900">All Tickets</span>
                                        </div>
                                        <span class="text-sm text-apple-gray-600">Comprehensive ticket listing with filters</span>
                                    </label>

                                    <label class="relative flex flex-col p-4 border-2 rounded-apple cursor-pointer transition-all hover:border-apple-blue" :class="reportType === 'performance' ? 'border-apple-blue bg-blue-50' : 'border-apple-gray-200'">
                                        <input type="radio" name="report_type" value="performance" x-model="reportType" class="sr-only">
                                        <div class="flex items-center mb-2">
                                            <svg class="w-6 h-6 text-apple-blue mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                                            </svg>
                                            <span class="font-semibold text-apple-gray-900">Performance Report</span>
                                        </div>
                                        <span class="text-sm text-apple-gray-600">Resolution times and efficiency metrics</span>
                                    </label>

                                    <label class="relative flex flex-col p-4 border-2 rounded-apple cursor-pointer transition-all hover:border-apple-blue" :class="reportType === 'department' ? 'border-apple-blue bg-blue-50' : 'border-apple-gray-200'">
                                        <input type="radio" name="report_type" value="department" x-model="reportType" class="sr-only">
                                        <div class="flex items-center mb-2">
                                            <svg class="w-6 h-6 text-apple-blue mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                            </svg>
                                            <span class="font-semibold text-apple-gray-900">By Department</span>
                                        </div>
                                        <span class="text-sm text-apple-gray-600">Breakdown by department activity</span>
                                    </label>

                                    <label class="relative flex flex-col p-4 border-2 rounded-apple cursor-pointer transition-all hover:border-apple-blue" :class="reportType === 'agent' ? 'border-apple-blue bg-blue-50' : 'border-apple-gray-200'">
                                        <input type="radio" name="report_type" value="agent" x-model="reportType" class="sr-only">
                                        <div class="flex items-center mb-2">
                                            <svg class="w-6 h-6 text-apple-blue mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                            </svg>
                                            <span class="font-semibold text-apple-gray-900">By Agent</span>
                                        </div>
                                        <span class="text-sm text-apple-gray-600">Agent performance and workload</span>
                                    </label>

                                    <label class="relative flex flex-col p-4 border-2 rounded-apple cursor-pointer transition-all hover:border-apple-blue" :class="reportType === 'branch' ? 'border-apple-blue bg-blue-50' : 'border-apple-gray-200'">
                                        <input type="radio" name="report_type" value="branch" x-model="reportType" class="sr-only">
                                        <div class="flex items-center mb-2">
                                            <svg class="w-6 h-6 text-apple-blue mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            </svg>
                                            <span class="font-semibold text-apple-gray-900">By Branch</span>
                                        </div>
                                        <span class="text-sm text-apple-gray-600">Branch-wise ticket distribution</span>
                                    </label>

                                    <label class="relative flex flex-col p-4 border-2 rounded-apple cursor-pointer transition-all hover:border-apple-blue" :class="reportType === 'status' ? 'border-apple-blue bg-blue-50' : 'border-apple-gray-200'">
                                        <input type="radio" name="report_type" value="status" x-model="reportType" class="sr-only">
                                        <div class="flex items-center mb-2">
                                            <svg class="w-6 h-6 text-apple-blue mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                                            </svg>
                                            <span class="font-semibold text-apple-gray-900">By Status</span>
                                        </div>
                                        <span class="text-sm text-apple-gray-600">Status breakdown and trends</span>
                                    </label>

                                    <label class="relative flex flex-col p-4 border-2 rounded-apple cursor-pointer transition-all hover:border-apple-blue" :class="reportType === 'employer' ? 'border-apple-blue bg-blue-50' : 'border-apple-gray-200'">
                                        <input type="radio" name="report_type" value="employer" x-model="reportType" class="sr-only">
                                        <div class="flex items-center mb-2">
                                            <svg class="w-6 h-6 text-apple-blue mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                            </svg>
                                            <span class="font-semibold text-apple-gray-900">By Employer</span>
                                        </div>
                                        <span class="text-sm text-apple-gray-600">Tickets grouped by employer type</span>
                                    </label>

                                    <label class="relative flex flex-col p-4 border-2 rounded-apple cursor-pointer transition-all hover:border-apple-blue" :class="reportType === 'payment_method' ? 'border-apple-blue bg-blue-50' : 'border-apple-gray-200'">
                                        <input type="radio" name="report_type" value="payment_method" x-model="reportType" class="sr-only">
                                        <div class="flex items-center mb-2">
                                            <svg class="w-6 h-6 text-apple-blue mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                                            </svg>
                                            <span class="font-semibold text-apple-gray-900">By Payment Method</span>
                                        </div>
                                        <span class="text-sm text-apple-gray-600">Tickets by payment method used</span>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <!-- Step 2: Filters -->
                        <div x-show="step === 2" x-transition class="space-y-6">
                            <div>
                                <h4 class="text-lg font-semibold text-apple-gray-900 mb-4">Step 2: Apply Filters</h4>
                                <p class="text-sm text-apple-gray-600 mb-6">Refine your report with specific criteria (all filters are optional)</p>

                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                                    <!-- Date Range -->
                                    <div class="sm:col-span-2">
                                        <label class="block text-sm font-medium text-apple-gray-700 mb-2">Date Range</label>
                                        <div class="grid grid-cols-2 gap-4">
                                            <div>
                                                <label class="block text-xs text-apple-gray-600 mb-1">From</label>
                                                <input type="date" name="date_from" x-model="filters.date_from" class="w-full rounded-apple border-apple-gray-300 focus:border-apple-blue focus:ring focus:ring-apple-blue focus:ring-opacity-20">
                                            </div>
                                            <div>
                                                <label class="block text-xs text-apple-gray-600 mb-1">To</label>
                                                <input type="date" name="date_to" x-model="filters.date_to" class="w-full rounded-apple border-apple-gray-300 focus:border-apple-blue focus:ring focus:ring-apple-blue focus:ring-opacity-20">
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Branch -->
                                    <div>
                                        <label for="branch_id" class="block text-sm font-medium text-apple-gray-700 mb-2">Branch</label>
                                        <select name="branch_id" x-model="filters.branch_id" class="w-full rounded-apple border-apple-gray-300 focus:border-apple-blue focus:ring focus:ring-apple-blue focus:ring-opacity-20">
                                            <option value="">All Branches</option>
                                            @forelse($branches ?? [] as $branch)
                                                <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                                            @empty
                                                <option value="" disabled>No branches available</option>
                                            @endforelse
                                        </select>
                                    </div>

                                    <!-- Agent -->
                                    <div>
                                        <label for="assigned_to" class="block text-sm font-medium text-apple-gray-700 mb-2">Assigned Agent</label>
                                        <select name="assigned_to" x-model="filters.assigned_to" class="w-full rounded-apple border-apple-gray-300 focus:border-apple-blue focus:ring focus:ring-apple-blue focus:ring-opacity-20">
                                            <option value="">All Agents</option>
                                            @forelse($users ?? [] as $user)
                                                <option value="{{ $user->id }}">{{ $user->name }}</option>
                                            @empty
                                                <option value="" disabled>No users available</option>
                                            @endforelse
                                        </select>
                                    </div>

                                    <!-- Status -->
                                    <div>
                                        <label for="status" class="block text-sm font-medium text-apple-gray-700 mb-2">Status</label>
                                        <select name="status" x-model="filters.status" class="w-full rounded-apple border-apple-gray-300 focus:border-apple-blue focus:ring focus:ring-apple-blue focus:ring-opacity-20">
                                            <option value="">All Statuses</option>
                                            <option value="pending">Pending</option>
                                            <option value="assigned">Assigned</option>
                                            <option value="in_progress">In Progress</option>
                                            <option value="partial_closed">Partial Closed</option>
                                            <option value="resolved">Resolved</option>
                                            <option value="closed">Closed</option>
                                            <option value="escalated">Escalated</option>
                                        </select>
                                    </div>

                                    <!-- Priority -->
                                    <div>
                                        <label for="priority" class="block text-sm font-medium text-apple-gray-700 mb-2">Priority</label>
                                        <select name="priority" x-model="filters.priority" class="w-full rounded-apple border-apple-gray-300 focus:border-apple-blue focus:ring focus:ring-apple-blue focus:ring-opacity-20">
                                            <option value="">All Priorities</option>
                                            <option value="low">Low</option>
                                            <option value="medium">Medium</option>
                                            <option value="high">High</option>
                                            <option value="urgent">Urgent</option>
                                        </select>
                                    </div>

                                    <!-- Department -->
                                    <div>
                                        <label for="department" class="block text-sm font-medium text-apple-gray-700 mb-2">Department</label>
                                        <select name="department" x-model="filters.department" class="w-full rounded-apple border-apple-gray-300 focus:border-apple-blue focus:ring focus:ring-apple-blue focus:ring-opacity-20">
                                            <option value="">All Departments</option>
                                            <option value="billing">Billing</option>
                                            <option value="technical">Technical</option>
                                            <option value="customer_service">Customer Service</option>
                                            <option value="general">General</option>
                                        </select>
                                    </div>

                                    <!-- Employer -->
                                    <div>
                                        <label for="employer_id" class="block text-sm font-medium text-apple-gray-700 mb-2">Employer</label>
                                        <select name="employer_id" x-model="filters.employer_id" class="w-full rounded-apple border-apple-gray-300 focus:border-apple-blue focus:ring focus:ring-apple-blue focus:ring-opacity-20">
                                            <option value="">All Employers</option>
                                            @forelse($employers ?? [] as $employer)
                                                <option value="{{ $employer->id }}">{{ $employer->name }}</option>
                                            @empty
                                                <option value="" disabled>No employers available</option>
                                            @endforelse
                                        </select>
                                    </div>

                                    <!-- Payment Method -->
                                    <div>
                                        <label for="payment_method_id" class="block text-sm font-medium text-apple-gray-700 mb-2">Payment Method</label>
                                        <select name="payment_method_id" x-model="filters.payment_method_id" class="w-full rounded-apple border-apple-gray-300 focus:border-apple-blue focus:ring focus:ring-apple-blue focus:ring-opacity-20">
                                            <option value="">All Payment Methods</option>
                                            @forelse($paymentMethods ?? [] as $method)
                                                <option value="{{ $method->id }}">{{ $method->name }}</option>
                                            @empty
                                                <option value="" disabled>No payment methods available</option>
                                            @endforelse
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Step 3: Output Format -->
                        <div x-show="step === 3" x-transition class="space-y-6">
                            <div>
                                <h4 class="text-lg font-semibold text-apple-gray-900 mb-4">Step 3: Choose Output Format</h4>
                                <p class="text-sm text-apple-gray-600 mb-6">Select how you want to view or export your report</p>

                                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                                    <label class="relative flex flex-col p-6 border-2 rounded-apple cursor-pointer transition-all hover:border-apple-blue" :class="format === 'view' ? 'border-apple-blue bg-blue-50' : 'border-apple-gray-200'">
                                        <input type="radio" name="format" value="view" x-model="format" class="sr-only">
                                        <div class="text-center">
                                            <svg class="w-12 h-12 text-apple-blue mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                            </svg>
                                            <span class="font-semibold text-apple-gray-900">View Online</span>
                                            <p class="text-xs text-apple-gray-600 mt-2">Interactive web view</p>
                                        </div>
                                    </label>

                                    <label class="relative flex flex-col p-6 border-2 rounded-apple cursor-pointer transition-all hover:border-apple-blue" :class="format === 'excel' ? 'border-apple-blue bg-blue-50' : 'border-apple-gray-200'">
                                        <input type="radio" name="format" value="excel" x-model="format" class="sr-only">
                                        <div class="text-center">
                                            <svg class="w-12 h-12 text-green-600 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                            </svg>
                                            <span class="font-semibold text-apple-gray-900">Excel Export</span>
                                            <p class="text-xs text-apple-gray-600 mt-2">Download as .xlsx</p>
                                        </div>
                                    </label>

                                    <label class="relative flex flex-col p-6 border-2 rounded-apple cursor-pointer transition-all hover:border-apple-blue" :class="format === 'pdf' ? 'border-apple-blue bg-blue-50' : 'border-apple-gray-200'">
                                        <input type="radio" name="format" value="pdf" x-model="format" class="sr-only">
                                        <div class="text-center">
                                            <svg class="w-12 h-12 text-red-600 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                                            </svg>
                                            <span class="font-semibold text-apple-gray-900">PDF Export</span>
                                            <p class="text-xs text-apple-gray-600 mt-2">Download as .pdf</p>
                                        </div>
                                    </label>
                                </div>
                            </div>

                            <!-- Summary -->
                            <div class="bg-apple-gray-50 rounded-apple p-6 border border-apple-gray-200">
                                <h5 class="font-semibold text-apple-gray-900 mb-3">Report Summary</h5>
                                <dl class="space-y-2 text-sm">
                                    <div class="flex justify-between">
                                        <dt class="text-apple-gray-600">Report Type:</dt>
                                        <dd class="font-medium text-apple-gray-900" x-text="reportType.replace('_', ' ').replace(/\b\w/g, l => l.toUpperCase())"></dd>
                                    </div>
                                    <div class="flex justify-between" x-show="filters.date_from || filters.date_to">
                                        <dt class="text-apple-gray-600">Date Range:</dt>
                                        <dd class="font-medium text-apple-gray-900">
                                            <span x-text="filters.date_from || 'Any'"></span> to <span x-text="filters.date_to || 'Any'"></span>
                                        </dd>
                                    </div>
                                    <div class="flex justify-between">
                                        <dt class="text-apple-gray-600">Output Format:</dt>
                                        <dd class="font-medium text-apple-gray-900" x-text="format.toUpperCase()"></dd>
                                    </div>
                                </dl>
                            </div>
                        </div>

                        <!-- Navigation Buttons -->
                        <div class="flex justify-between pt-6 border-t border-apple-gray-200">
                            <button type="button" @click="previousStep()" x-show="step > 1" class="px-6 py-2 bg-apple-gray-100 text-apple-gray-700 rounded-apple font-medium hover:bg-apple-gray-200 transition-colors">
                                Previous
                            </button>
                            <div x-show="step === 1"></div>

                            <button type="button" @click="nextStep()" x-show="step < 3" class="px-6 py-2 bg-apple-blue text-white rounded-apple font-medium hover:bg-blue-700 transition-colors">
                                Next
                            </button>

                            <button type="submit" x-show="step === 3" class="px-6 py-2 bg-gradient-to-r from-apple-blue to-blue-600 text-white rounded-apple font-medium hover:from-blue-700 hover:to-blue-800 transition-all shadow-apple">
                                Generate Report
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        function reportWizard() {
            return {
                step: 1,
                reportType: 'tickets',
                format: 'view',
                filters: {
                    date_from: '',
                    date_to: '',
                    branch_id: '',
                    assigned_to: '',
                    status: '',
                    priority: '',
                    department: '',
                    employer_id: '',
                    payment_method_id: ''
                },
                nextStep() {
                    if (this.step < 3) {
                        this.step++;
                    }
                },
                previousStep() {
                    if (this.step > 1) {
                        this.step--;
                    }
                }
            }
        }
    </script>
</x-app-layout>
