<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-semibold text-2xl text-apple-gray-900 leading-tight">
                    {{ __('Edit Employer') }}
                </h2>
                <p class="text-sm text-apple-gray-500 mt-1">Update employer details</p>
            </div>
            <a href="{{ route('employers.index') }}"
               class="inline-flex items-center px-4 py-2 bg-apple-gray-100 text-apple-gray-700 font-medium rounded-apple hover:bg-apple-gray-200 transition-all duration-200">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Back
            </a>
        </div>
    </x-slot>

    <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-white rounded-apple-lg shadow-apple p-8">
            <!-- Statistics -->
            <div class="mb-6 p-4 bg-apple-gray-50 rounded-apple">
                <h3 class="text-sm font-semibold text-apple-gray-700 mb-2">Statistics</h3>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <p class="text-xs text-apple-gray-500">Total Tickets</p>
                        <p class="text-lg font-semibold text-apple-gray-900">{{ $employer->complaints_count }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-apple-gray-500">Created</p>
                        <p class="text-sm text-apple-gray-900">{{ $employer->created_at ? $employer->created_at->format('M d, Y') : 'N/A' }}</p>
                    </div>
                </div>
            </div>

            <form method="POST" action="{{ route('employers.update', $employer) }}" class="space-y-6">
                @csrf
                @method('PATCH')

                <div>
                    <label for="name" class="block text-sm font-medium text-apple-gray-700 mb-2">
                        Employer Name <span class="text-red-500">*</span>
                    </label>
                    <input id="name"
                           type="text"
                           name="name"
                           value="{{ old('name', $employer->name) }}"
                           required
                           autofocus
                           class="block w-full px-4 py-3 bg-apple-gray-50 border-0 rounded-apple focus:ring-2 focus:ring-apple-blue focus:bg-white transition-all duration-200 @error('name') ring-2 ring-red-500 @enderror"
                           placeholder="e.g., ZNA, ZPS, Self Employed">
                    @error('name')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex items-center">
                    <input id="is_active"
                           type="checkbox"
                           name="is_active"
                           value="1"
                           {{ old('is_active', $employer->is_active) ? 'checked' : '' }}
                           class="h-4 w-4 text-apple-blue focus:ring-apple-blue border-apple-gray-300 rounded">
                    <label for="is_active" class="ml-2 block text-sm text-apple-gray-700">
                        Active
                    </label>
                </div>

                <div class="flex items-center justify-end space-x-4 pt-4">
                    <a href="{{ route('employers.index') }}"
                       class="px-6 py-3 bg-apple-gray-100 text-apple-gray-700 font-medium rounded-apple hover:bg-apple-gray-200 transition-all duration-200">
                        Cancel
                    </a>
                    <button type="submit"
                            class="px-6 py-3 bg-apple-blue text-white font-medium rounded-apple hover:bg-apple-blue-dark transition-all duration-200 shadow-sm">
                        Update Employer
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
