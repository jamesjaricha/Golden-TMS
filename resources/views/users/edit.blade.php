<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-semibold text-2xl text-apple-gray-900 leading-tight">
                    {{ __('Edit User') }}
                </h2>
                <p class="text-sm text-apple-gray-500 mt-1">Update user information and role</p>
            </div>
            <a href="{{ route('users.index') }}"
               class="inline-flex items-center px-4 py-2 bg-apple-gray-100 text-apple-gray-700 font-medium rounded-apple hover:bg-apple-gray-200 focus:outline-none transition-all duration-200">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Back to Users
            </a>
        </div>
    </x-slot>

    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-white rounded-apple-lg shadow-apple p-8 animate-slide-up">
            <form method="POST" action="{{ route('users.update', $user) }}" class="space-y-6">
                @csrf
                @method('PATCH')

                <!-- Name -->
                <div>
                    <label for="name" class="block text-sm font-medium text-apple-gray-700 mb-2">
                        Full Name
                    </label>
                    <input id="name"
                           type="text"
                           name="name"
                           value="{{ old('name', $user->name) }}"
                           required
                           autofocus
                           class="block w-full px-4 py-3 bg-apple-gray-50 border-0 rounded-apple focus:ring-2 focus:ring-apple-blue focus:bg-white transition-all duration-200 @error('name') ring-2 ring-red-500 @enderror"
                           placeholder="John Doe">
                    @error('name')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Email -->
                <div>
                    <label for="email" class="block text-sm font-medium text-apple-gray-700 mb-2">
                        Email Address
                    </label>
                    <input id="email"
                           type="email"
                           name="email"
                           value="{{ old('email', $user->email) }}"
                           required
                           class="block w-full px-4 py-3 bg-apple-gray-50 border-0 rounded-apple focus:ring-2 focus:ring-apple-blue focus:bg-white transition-all duration-200 @error('email') ring-2 ring-red-500 @enderror"
                           placeholder="john@example.com">
                    @error('email')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Role -->
                <div>
                    <label for="role" class="block text-sm font-medium text-apple-gray-700 mb-2">
                        User Role
                    </label>
                    <select id="role"
                            name="role"
                            required
                            class="block w-full px-4 py-3 bg-apple-gray-50 border-0 rounded-apple focus:ring-2 focus:ring-apple-blue focus:bg-white transition-all duration-200 @error('role') ring-2 ring-red-500 @enderror">
                        @foreach($roles as $role)
                            <option value="{{ $role }}" {{ old('role', $user->role) === $role ? 'selected' : '' }}>
                                {{ ucwords(str_replace('_', ' ', $role)) }}
                            </option>
                        @endforeach
                    </select>
                    @error('role')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Department -->
                <div>
                    <label for="department_id" class="block text-sm font-medium text-apple-gray-700 mb-2">
                        Department (Optional)
                    </label>
                    <select id="department_id"
                            name="department_id"
                            class="block w-full px-4 py-3 bg-apple-gray-50 border-0 rounded-apple focus:ring-2 focus:ring-apple-blue focus:bg-white transition-all duration-200 @error('department_id') ring-2 ring-red-500 @enderror">
                        <option value="">No Department</option>
                        @foreach($departments as $department)
                            <option value="{{ $department->id }}" {{ old('department_id', $user->department_id) == $department->id ? 'selected' : '' }}>
                                {{ $department->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('department_id')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <p class="mt-2 text-xs text-apple-gray-500">
                        Used for partial closure workflow - automatically fills "Completed Department"
                    </p>
                </div>

                <!-- Primary Branch -->
                <div>
                    <label for="branch_ids" class="block text-sm font-medium text-apple-gray-700 mb-2">
                        Primary Branch <span class="text-red-500">*</span>
                    </label>
                    <select id="branch_ids"
                            name="branch_ids[]"
                            required
                            class="block w-full px-4 py-3 bg-apple-gray-50 border-0 rounded-apple focus:ring-2 focus:ring-apple-blue focus:bg-white transition-all duration-200 @error('branch_ids') ring-2 ring-red-500 @enderror">
                        @php
                            $userBranchId = old('branch_ids.0', $user->branches->first()?->id);
                        @endphp
                        @foreach($branches as $branch)
                            <option value="{{ $branch->id }}" {{ $userBranchId == $branch->id ? 'selected' : '' }}>
                                {{ $branch->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('branch_ids')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- WhatsApp Settings Section -->
                <div class="pt-4 border-t border-apple-gray-100">
                    <h3 class="text-sm font-medium text-apple-gray-900 mb-4 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-green-500" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
                        </svg>
                        WhatsApp Notifications
                    </h3>
                    <p class="text-xs text-apple-gray-500 mb-4">Configure WhatsApp number to receive task reminders and updates</p>

                    <!-- WhatsApp Number -->
                    <div class="mb-4">
                        <label for="whatsapp_number" class="block text-sm font-medium text-apple-gray-700 mb-2">
                            WhatsApp Number
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-green-500" viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
                                </svg>
                            </div>
                            <input id="whatsapp_number"
                                   type="tel"
                                   name="whatsapp_number"
                                   value="{{ old('whatsapp_number', $user->whatsapp_number) }}"
                                   class="pl-10 block w-full px-4 py-3 bg-apple-gray-50 border-0 rounded-apple focus:ring-2 focus:ring-green-500 focus:bg-white transition-all duration-200 @error('whatsapp_number') ring-2 ring-red-500 @enderror"
                                   placeholder="+263 77 123 4567">
                        </div>
                        <p class="mt-1 text-xs text-apple-gray-500">Enter with country code (e.g., +263771234567)</p>
                        @error('whatsapp_number')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- WhatsApp Notifications Toggle -->
                    <div class="flex items-center">
                        <input id="whatsapp_notifications_enabled"
                               name="whatsapp_notifications_enabled"
                               type="checkbox"
                               value="1"
                               {{ old('whatsapp_notifications_enabled', $user->whatsapp_notifications_enabled) ? 'checked' : '' }}
                               class="h-4 w-4 text-green-600 focus:ring-green-500 border-gray-300 rounded">
                        <label for="whatsapp_notifications_enabled" class="ml-2 block text-sm text-apple-gray-700">
                            Enable WhatsApp notifications for task reminders
                        </label>
                    </div>
                </div>

                <!-- Password Section -->
                <div class="pt-4 border-t border-apple-gray-100">
                    <h3 class="text-sm font-medium text-apple-gray-900 mb-4">Change Password (Optional)</h3>

                    <!-- Password -->
                    <div class="mb-4">
                        <label for="password" class="block text-sm font-medium text-apple-gray-700 mb-2">
                            New Password
                        </label>
                        <input id="password"
                               type="password"
                               name="password"
                               class="block w-full px-4 py-3 bg-apple-gray-50 border-0 rounded-apple focus:ring-2 focus:ring-apple-blue focus:bg-white transition-all duration-200 @error('password') ring-2 ring-red-500 @enderror"
                               placeholder="Leave blank to keep current password">
                        @error('password')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Password Confirmation -->
                    <div>
                        <label for="password_confirmation" class="block text-sm font-medium text-apple-gray-700 mb-2">
                            Confirm New Password
                        </label>
                        <input id="password_confirmation"
                               type="password"
                               name="password_confirmation"
                               class="block w-full px-4 py-3 bg-apple-gray-50 border-0 rounded-apple focus:ring-2 focus:ring-apple-blue focus:bg-white transition-all duration-200"
                               placeholder="Re-enter new password">
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="flex items-center justify-end space-x-3 pt-4 border-t border-apple-gray-100">
                    <a href="{{ route('users.index') }}"
                       class="px-6 py-3 bg-apple-gray-100 text-apple-gray-700 font-medium rounded-apple hover:bg-apple-gray-200 transition-all duration-200">
                        Cancel
                    </a>
                    <button type="submit"
                            class="px-6 py-3 bg-apple-blue text-white font-semibold rounded-apple hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-apple-blue transition-all duration-200 shadow-apple hover:shadow-apple-md transform hover:scale-105">
                        Update User
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
