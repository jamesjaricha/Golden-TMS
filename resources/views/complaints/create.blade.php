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
                            Phone Number <span class="text-red-500">*</span>
                        </label>
                        <input id="phone_number"
                               type="tel"
                               name="phone_number"
                               value="{{ old('phone_number') }}"
                               required
                               class="block w-full px-4 py-3 bg-apple-gray-50 border-0 rounded-apple focus:ring-2 focus:ring-apple-blue focus:bg-white transition-all duration-200 @error('phone_number') ring-2 ring-red-500 @enderror"
                               placeholder="077 690 5912">
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

                    <!-- Visited Branch -->
                    <div>
                        <label for="visited_branch" class="block text-sm font-medium text-apple-gray-700 mb-2">
                            Branch Visited <span class="text-red-500">*</span>
                        </label>
                        <select id="visited_branch"
                                name="visited_branch"
                                required
                                class="block w-full px-4 py-3 bg-apple-gray-50 border-0 rounded-apple focus:ring-2 focus:ring-apple-blue focus:bg-white transition-all duration-200 @error('visited_branch') ring-2 ring-red-500 @enderror">
                            <option value="">Select Branch</option>
                            <option value="Beitbridge" {{ old('visited_branch') === 'Beitbridge' ? 'selected' : '' }}>Beitbridge</option>
                            <option value="Bindura" {{ old('visited_branch') === 'Bindura' ? 'selected' : '' }}>Bindura</option>
                            <option value="Bulawayo" {{ old('visited_branch') === 'Bulawayo' ? 'selected' : '' }}>Bulawayo</option>
                            <option value="Chinhoyi" {{ old('visited_branch') === 'Chinhoyi' ? 'selected' : '' }}>Chinhoyi</option>
                            <option value="Chipinge" {{ old('visited_branch') === 'Chipinge' ? 'selected' : '' }}>Chipinge</option>
                            <option value="Chiredzi" {{ old('visited_branch') === 'Chiredzi' ? 'selected' : '' }}>Chiredzi</option>
                            <option value="Chitungwiza" {{ old('visited_branch') === 'Chitungwiza' ? 'selected' : '' }}>Chitungwiza</option>
                            <option value="Epworth" {{ old('visited_branch') === 'Epworth' ? 'selected' : '' }}>Epworth</option>
                            <option value="Gokwe" {{ old('visited_branch') === 'Gokwe' ? 'selected' : '' }}>Gokwe</option>
                            <option value="Guruve" {{ old('visited_branch') === 'Guruve' ? 'selected' : '' }}>Guruve</option>
                            <option value="Gwanda" {{ old('visited_branch') === 'Gwanda' ? 'selected' : '' }}>Gwanda</option>
                            <option value="Gweru" {{ old('visited_branch') === 'Gweru' ? 'selected' : '' }}>Gweru</option>
                            <option value="Harare HQ" {{ old('visited_branch') === 'Harare HQ' ? 'selected' : '' }}>Harare HQ</option>
                            <option value="Kadoma" {{ old('visited_branch') === 'Kadoma' ? 'selected' : '' }}>Kadoma</option>
                            <option value="Karoi" {{ old('visited_branch') === 'Karoi' ? 'selected' : '' }}>Karoi</option>
                            <option value="Kwekwe" {{ old('visited_branch') === 'Kwekwe' ? 'selected' : '' }}>Kwekwe</option>
                            <option value="Marondera" {{ old('visited_branch') === 'Marondera' ? 'selected' : '' }}>Marondera</option>
                            <option value="Masvingo" {{ old('visited_branch') === 'Masvingo' ? 'selected' : '' }}>Masvingo</option>
                            <option value="Mutare" {{ old('visited_branch') === 'Mutare' ? 'selected' : '' }}>Mutare</option>
                            <option value="Mutoko" {{ old('visited_branch') === 'Mutoko' ? 'selected' : '' }}>Mutoko</option>
                            <option value="Ngezi" {{ old('visited_branch') === 'Ngezi' ? 'selected' : '' }}>Ngezi</option>
                            <option value="Nyanga" {{ old('visited_branch') === 'Nyanga' ? 'selected' : '' }}>Nyanga</option>
                            <option value="Rusape" {{ old('visited_branch') === 'Rusape' ? 'selected' : '' }}>Rusape</option>
                            <option value="Zvishavane" {{ old('visited_branch') === 'Zvishavane' ? 'selected' : '' }}>Zvishavane</option>
                        </select>
                        @error('visited_branch')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Department -->
                    <div>
                        <label for="department" class="block text-sm font-medium text-apple-gray-700 mb-2">
                            Department <span class="text-red-500">*</span>
                        </label>
                        <select id="department"
                                name="department"
                                required
                                class="block w-full px-4 py-3 bg-apple-gray-50 border-0 rounded-apple focus:ring-2 focus:ring-apple-blue focus:bg-white transition-all duration-200 @error('department') ring-2 ring-red-500 @enderror">
                            <option value="">Select Department</option>
                            <option value="Billing" {{ old('department') === 'Billing' ? 'selected' : '' }}>Billing</option>
                            <option value="Claims" {{ old('department') === 'Claims' ? 'selected' : '' }}>Claims</option>
                            <option value="IT" {{ old('department') === 'IT' ? 'selected' : '' }}>IT</option>
                            <option value="General Support" {{ old('department') === 'General Support' ? 'selected' : '' }}>General Support</option>
                        </select>
                        @error('department')
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
</x-app-layout>
