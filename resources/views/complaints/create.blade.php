<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-semibold text-2xl text-apple-gray-900 leading-tight">
                    {{ __('Submit New Complaint') }}
                </h2>
                <p class="text-sm text-apple-gray-500 mt-1">Fill in the details below to submit a client complaint</p>
            </div>
        </div>
    </x-slot>

    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-white rounded-apple-lg shadow-apple p-8 animate-slide-up">
            <form method="POST" action="{{ route('complaints.store') }}" class="space-y-6">
                @csrf

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Policy Number -->
                    <div>
                        <label for="policy_number" class="block text-sm font-medium text-apple-gray-700 mb-2">
                            Policy Number <span class="text-red-500">*</span>
                        </label>
                        <input id="policy_number"
                               type="text"
                               name="policy_number"
                               value="{{ old('policy_number') }}"
                               required
                               autofocus
                               class="block w-full px-4 py-3 bg-apple-gray-50 border-0 rounded-apple focus:ring-2 focus:ring-apple-blue focus:bg-white transition-all duration-200 @error('policy_number') ring-2 ring-red-500 @enderror"
                               placeholder="GKL-123456">
                        @error('policy_number')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Full Name -->
                    <div>
                        <label for="full_name" class="block text-sm font-medium text-apple-gray-700 mb-2">
                            Full Name <span class="text-red-500">*</span>
                        </label>
                        <input id="full_name"
                               type="text"
                               name="full_name"
                               value="{{ old('full_name') }}"
                               required
                               class="block w-full px-4 py-3 bg-apple-gray-50 border-0 rounded-apple focus:ring-2 focus:ring-apple-blue focus:bg-white transition-all duration-200 @error('full_name') ring-2 ring-red-500 @enderror"
                               placeholder="John Doe">
                        @error('full_name')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Phone Number -->
                    <div>
                        <label for="phone_number" class="block text-sm font-medium text-apple-gray-700 mb-2">
                            Phone Number / WhatsApp <span class="text-red-500">*</span>
                        </label>
                        <input id="phone_number"
                               type="tel"
                               name="phone_number"
                               value="{{ old('phone_number') }}"
                               required
                               class="block w-full px-4 py-3 bg-apple-gray-50 border-0 rounded-apple focus:ring-2 focus:ring-apple-blue focus:bg-white transition-all duration-200 @error('phone_number') ring-2 ring-red-500 @enderror"
                               placeholder="263776905912"
                               pattern="[0-9]{12}"
                               maxlength="12">
                        <p class="mt-1 text-xs sm:text-sm text-apple-gray-500 flex items-start">
                            <svg class="w-3 h-3 sm:w-4 sm:h-4 text-blue-500 mr-1 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                            </svg>
                            <span>Enter in international format: 263776905912 (country code + number, no spaces or +)</span>
                        </p>
                        @error('phone_number')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Location -->
                    <div>
                        <label for="location" class="block text-sm font-medium text-apple-gray-700 mb-2">
                            Address <span class="text-red-500">*</span>
                        </label>
                        <input id="location"
                               type="text"
                               name="location"
                               value="{{ old('location') }}"
                               required
                               class="block w-full px-4 py-3 bg-apple-gray-50 border-0 rounded-apple focus:ring-2 focus:ring-apple-blue focus:bg-white transition-all duration-200 @error('location') ring-2 ring-red-500 @enderror"
                               placeholder="Glenview">
                        @error('location')
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
                            <option value="">Select Branch</option>
                            @foreach($branches as $branch)
                                <option value="{{ $branch->id }}" {{ old('branch_id') == $branch->id ? 'selected' : '' }}>
                                    {{ $branch->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('branch_id')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Department -->
                    <div>
                        <label for="department_id" class="block text-sm font-medium text-apple-gray-700 mb-2">
                            Department <span class="text-red-500">*</span>
                        </label>
                        <select id="department_id"
                                name="department_id"
                                required
                                class="block w-full px-4 py-3 bg-apple-gray-50 border-0 rounded-apple focus:ring-2 focus:ring-apple-blue focus:bg-white transition-all duration-200 @error('department_id') ring-2 ring-red-500 @enderror">
                            <option value="">Select Department</option>
                            @foreach($departments as $department)
                                <option value="{{ $department->id }}" {{ old('department_id') == $department->id ? 'selected' : '' }}>
                                    {{ $department->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('department_id')
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
                            <option value="">Select Employer</option>
                            @foreach($employers as $employer)
                                <option value="{{ $employer->id }}" {{ old('employer_id') == $employer->id ? 'selected' : '' }}>
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
                            <option value="">Select Payment Method</option>
                            @foreach($paymentMethods as $paymentMethod)
                                <option value="{{ $paymentMethod->id }}" {{ old('payment_method_id') == $paymentMethod->id ? 'selected' : '' }}>
                                    {{ $paymentMethod->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('payment_method_id')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Priority -->
                    <div>
                        <label for="priority" class="block text-sm font-medium text-apple-gray-700 mb-2">
                            Priority Level <span class="text-red-500">*</span>
                        </label>
                        <select id="priority"
                                name="priority"
                                required
                                class="block w-full px-4 py-3 bg-apple-gray-50 border-0 rounded-apple focus:ring-2 focus:ring-apple-blue focus:bg-white transition-all duration-200 @error('priority') ring-2 ring-red-500 @enderror">
                            <option value="">Select Priority</option>
                            <option value="low" {{ old('priority') === 'low' ? 'selected' : '' }}>Low</option>
                            <option value="medium" {{ old('priority') === 'medium' ? 'selected' : '' }}>Medium</option>
                            <option value="high" {{ old('priority') === 'high' ? 'selected' : '' }}>High</option>
                            <option value="urgent" {{ old('priority') === 'urgent' ? 'selected' : '' }}>Urgent</option>
                        </select>
                        @error('priority')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Complaint Text -->
                <div>
                    <label for="complaint_text" class="block text-sm font-medium text-apple-gray-700 mb-2">
                        Complaint Details <span class="text-red-500">*</span>
                    </label>
                    <textarea id="complaint_text"
                              name="complaint_text"
                              rows="6"
                              required
                              class="block w-full px-4 py-3 bg-apple-gray-50 border-0 rounded-apple focus:ring-2 focus:ring-apple-blue focus:bg-white transition-all duration-200 @error('complaint_text') ring-2 ring-red-500 @enderror"
                              placeholder="Please provide detailed information about the complaint...">{{ old('complaint_text') }}</textarea>
                    @error('complaint_text')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Info Box -->
                <div class="bg-blue-50 border border-blue-200 rounded-apple p-4">
                    <div class="flex items-start">
                        <svg class="w-5 h-5 text-blue-600 mt-0.5 mr-3" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                        </svg>
                        <div class="text-sm text-blue-800">
                            <p class="font-medium mb-1">Ticket Information</p>
                            <p>Upon submission, a unique ticket number will be generated. You will be able to track the progress of this complaint.</p>
                            <p class="mt-1"><strong>Captured by:</strong> {{ Auth::user()->name }}</p>
                        </div>
                    </div>
                </div>

                <!-- Submit Buttons -->
                <div class="flex items-center justify-end space-x-3 pt-4 border-t border-apple-gray-100">
                    <a href="{{ route('complaints.index') }}"
                       class="px-6 py-3 bg-apple-gray-100 text-apple-gray-700 font-medium rounded-apple hover:bg-apple-gray-200 transition-all duration-200">
                        Cancel
                    </a>
                    <button type="submit"
                            class="px-6 py-3 bg-apple-blue text-white font-semibold rounded-apple hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-apple-blue transition-all duration-200 shadow-apple hover:shadow-apple-md transform hover:scale-105">
                        Submit Complaint
                    </button>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const phoneInput = document.getElementById('phone_number');

            // Auto-format phone number to WhatsApp international format
            phoneInput.addEventListener('input', function(e) {
                let value = e.target.value;

                // Remove all non-numeric characters
                value = value.replace(/\D/g, '');

                // If starts with 0, replace with 263 (Zimbabwe country code)
                if (value.startsWith('0')) {
                    value = '263' + value.substring(1);
                }

                // If doesn't start with country code, add 263
                if (!value.startsWith('263') && value.length > 0) {
                    value = '263' + value;
                }

                // Limit to 12 digits (263 + 9 digits)
                value = value.substring(0, 12);

                e.target.value = value;
            });

            // Format on blur to ensure proper format
            phoneInput.addEventListener('blur', function(e) {
                let value = e.target.value.replace(/\D/g, '');

                if (value.length > 0) {
                    // Ensure it starts with 263
                    if (value.startsWith('0')) {
                        value = '263' + value.substring(1);
                    } else if (!value.startsWith('263')) {
                        value = '263' + value;
                    }

                    // Ensure it's exactly 12 digits
                    if (value.length < 12) {
                        e.target.setCustomValidity('Phone number must be 12 digits (263 + 9 digits)');
                    } else {
                        e.target.setCustomValidity('');
                    }

                    e.target.value = value;
                }
            });

            // Clear custom validity on input
            phoneInput.addEventListener('input', function(e) {
                e.target.setCustomValidity('');
            });
        });
    </script>
    @endpush
</x-app-layout>
