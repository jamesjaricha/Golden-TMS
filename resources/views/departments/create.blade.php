<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-semibold text-2xl text-apple-gray-900 leading-tight">
                    {{ __('Create Department') }}
                </h2>
                <p class="text-sm text-apple-gray-500 mt-1">Add a new department to the system</p>
            </div>
            <a href="{{ route('departments.index') }}"
               class="inline-flex items-center px-4 py-2 bg-apple-gray-200 text-apple-gray-700 font-medium rounded-apple hover:bg-apple-gray-300 transition-all duration-200">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Back to Departments
            </a>
        </div>
    </x-slot>

    <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-white rounded-apple-lg shadow-apple overflow-hidden animate-slide-up">
            <form method="POST" action="{{ route('departments.store') }}" class="p-6 space-y-6">
                @csrf

                <!-- Department Name -->
                <div>
                    <label for="name" class="block text-sm font-medium text-apple-gray-700 mb-2">
                        Department Name <span class="text-red-500">*</span>
                    </label>
                    <input type="text"
                           id="name"
                           name="name"
                           value="{{ old('name') }}"
                           required
                           autofocus
                           class="block w-full px-4 py-3 bg-apple-gray-50 border-0 rounded-apple focus:ring-2 focus:ring-apple-blue focus:bg-white transition-all duration-200 @error('name') ring-2 ring-red-500 @enderror"
                           placeholder="e.g., Billing, Claims, IT Support">
                    @error('name')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Description -->
                <div>
                    <label for="description" class="block text-sm font-medium text-apple-gray-700 mb-2">
                        Description
                    </label>
                    <textarea id="description"
                              name="description"
                              rows="4"
                              class="block w-full px-4 py-3 bg-apple-gray-50 border-0 rounded-apple focus:ring-2 focus:ring-apple-blue focus:bg-white transition-all duration-200 @error('description') ring-2 ring-red-500 @enderror"
                              placeholder="Brief description of this department">{{ old('description') }}</textarea>
                    @error('description')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <p class="mt-2 text-xs text-apple-gray-500">Describe the department's responsibilities and functions</p>
                </div>

                <!-- Active Status -->
                <div class="flex items-center">
                    <input type="checkbox"
                           id="is_active"
                           name="is_active"
                           value="1"
                           {{ old('is_active', true) ? 'checked' : '' }}
                           class="h-4 w-4 text-apple-blue focus:ring-apple-blue border-apple-gray-300 rounded">
                    <label for="is_active" class="ml-2 block text-sm text-apple-gray-900">
                        Active
                    </label>
                </div>
                <p class="text-xs text-apple-gray-500 -mt-4 ml-6">Inactive departments won't be available for ticket assignment</p>

                <!-- Form Actions -->
                <div class="flex items-center justify-end space-x-3 pt-4 border-t border-apple-gray-200">
                    <a href="{{ route('departments.index') }}"
                       class="px-6 py-3 text-apple-gray-700 bg-white border border-apple-gray-300 rounded-apple hover:bg-apple-gray-50 focus:outline-none transition-all duration-200">
                        Cancel
                    </a>
                    <button type="submit"
                            class="px-6 py-3 bg-apple-blue text-white font-medium rounded-apple hover:bg-blue-600 focus:outline-none transition-all duration-200 shadow-apple hover:shadow-apple-md">
                        <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        Create Department
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
