<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-semibold text-2xl text-apple-gray-900 leading-tight">
                    {{ __('Edit Branch') }}
                </h2>
                <p class="text-sm text-apple-gray-500 mt-1">Update branch information</p>
            </div>
            <a href="{{ route('branches.index') }}"
               class="inline-flex items-center px-4 py-2 bg-apple-gray-100 text-apple-gray-700 font-medium rounded-apple hover:bg-apple-gray-200 focus:outline-none transition-all duration-200">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Back to Branches
            </a>
        </div>
    </x-slot>

    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-white rounded-apple-lg shadow-apple p-8 animate-slide-up">
            <form method="POST" action="{{ route('branches.update', $branch) }}" class="space-y-6">
                @csrf
                @method('PATCH')

                <!-- Branch Name -->
                <div>
                    <label for="name" class="block text-sm font-medium text-apple-gray-700 mb-2">
                        Branch Name <span class="text-red-500">*</span>
                    </label>
                    <input id="name"
                           type="text"
                           name="name"
                           value="{{ old('name', $branch->name) }}"
                           required
                           autofocus
                           class="block w-full px-4 py-3 bg-apple-gray-50 border-0 rounded-apple focus:ring-2 focus:ring-apple-blue focus:bg-white transition-all duration-200 @error('name') ring-2 ring-red-500 @enderror"
                           placeholder="e.g., Harare Central">
                    @error('name')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Branch Code -->
                <div>
                    <label for="code" class="block text-sm font-medium text-apple-gray-700 mb-2">
                        Branch Code
                    </label>
                    <input id="code"
                           type="text"
                           name="code"
                           value="{{ old('code', $branch->code) }}"
                           maxlength="20"
                           class="block w-full px-4 py-3 bg-apple-gray-50 border-0 rounded-apple focus:ring-2 focus:ring-apple-blue focus:bg-white transition-all duration-200 @error('code') ring-2 ring-red-500 @enderror"
                           placeholder="e.g., HAR">
                    @error('code')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <p class="mt-2 text-xs text-apple-gray-500">Optional short code for the branch</p>
                </div>

                <!-- Location -->
                <div>
                    <label for="location" class="block text-sm font-medium text-apple-gray-700 mb-2">
                        Location
                    </label>
                    <input id="location"
                           type="text"
                           name="location"
                           value="{{ old('location', $branch->location) }}"
                           class="block w-full px-4 py-3 bg-apple-gray-50 border-0 rounded-apple focus:ring-2 focus:ring-apple-blue focus:bg-white transition-all duration-200 @error('location') ring-2 ring-red-500 @enderror"
                           placeholder="e.g., Harare">
                    @error('location')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Statistics (Read Only) -->
                <div class="pt-6 border-t border-apple-gray-100">
                    <h3 class="text-sm font-semibold text-apple-gray-900 mb-4">Branch Statistics</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 bg-apple-gray-50 rounded-apple p-4">
                        <div>
                            <p class="text-xs text-apple-gray-500">Assigned Users</p>
                            <p class="text-sm font-medium text-apple-gray-900">{{ $branch->users()->count() }} {{ Str::plural('user', $branch->users()->count()) }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-apple-gray-500">Total Tickets</p>
                            <p class="text-sm font-medium text-apple-gray-900">{{ $branch->complaints()->count() }} {{ Str::plural('ticket', $branch->complaints()->count()) }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-apple-gray-500">Created</p>
                            <p class="text-sm font-medium text-apple-gray-900">{{ $branch->created_at ? $branch->created_at->format('M d, Y') : 'N/A' }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-apple-gray-500">Last Updated</p>
                            <p class="text-sm font-medium text-apple-gray-900">{{ $branch->updated_at ? $branch->updated_at->format('M d, Y') : 'N/A' }}</p>
                        </div>
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="flex items-center justify-end space-x-3 pt-4 border-t border-apple-gray-100">
                    <a href="{{ route('branches.index') }}"
                       class="px-6 py-3 bg-apple-gray-100 text-apple-gray-700 font-medium rounded-apple hover:bg-apple-gray-200 transition-all duration-200">
                        Cancel
                    </a>
                    <button type="submit"
                            class="px-6 py-3 bg-apple-blue text-white font-semibold rounded-apple hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-apple-blue transition-all duration-200 shadow-apple hover:shadow-apple-md transform hover:scale-105">
                        Update Branch
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
