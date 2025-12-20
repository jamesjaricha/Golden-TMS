<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-semibold text-2xl text-apple-gray-900 leading-tight">
                    {{ __('User Management') }}
                </h2>
                <p class="text-sm text-apple-gray-500 mt-1">Manage system users and their roles</p>
            </div>
            <a href="{{ route('users.create') }}"
               class="inline-flex items-center px-4 py-2 bg-apple-blue text-white font-semibold rounded-apple hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-apple-blue transition-all duration-200 shadow-apple hover:shadow-apple-md transform hover:scale-105">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                </svg>
                Add New User
            </a>
        </div>
    </x-slot>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        @if(session('success'))
            <div class="mb-6 bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-apple animate-fade-in">
                <p class="font-medium">{{ session('success') }}</p>
            </div>
        @endif

        @if(session('error'))
            <div class="mb-6 bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-apple animate-fade-in">
                <p class="font-medium">{{ session('error') }}</p>
            </div>
        @endif

        <!-- Users Table Card -->
        <div class="bg-white rounded-apple-lg shadow-apple overflow-hidden animate-slide-up">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-apple-gray-200">
                    <thead class="bg-apple-gray-50">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-apple-gray-700 uppercase tracking-wider">
                                User
                            </th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-apple-gray-700 uppercase tracking-wider">
                                Email
                            </th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-apple-gray-700 uppercase tracking-wider">
                                Role
                            </th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-apple-gray-700 uppercase tracking-wider">
                                Joined
                            </th>
                            <th class="px-6 py-4 text-right text-xs font-semibold text-apple-gray-700 uppercase tracking-wider">
                                Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-apple-gray-100">
                        @forelse($users as $user)
                            <tr class="hover:bg-apple-gray-50 transition-colors duration-150">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="w-10 h-10 bg-gradient-to-br from-apple-blue to-blue-600 rounded-full flex items-center justify-center text-white text-sm font-semibold">
                                            {{ strtoupper(substr($user->name, 0, 2)) }}
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-apple-gray-900">
                                                {{ $user->name }}
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-apple-gray-900">{{ $user->email }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full
                                        @if($user->role === 'super_admin') bg-purple-100 text-purple-800
                                        @elseif($user->role === 'manager') bg-blue-100 text-blue-800
                                        @elseif($user->role === 'support_agent') bg-green-100 text-green-800
                                        @else bg-apple-gray-100 text-apple-gray-800
                                        @endif">
                                        {{ ucwords(str_replace('_', ' ', $user->role)) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-apple-gray-500">
                                    {{ $user->created_at->format('M d, Y') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <div class="flex items-center justify-end space-x-2">
                                        <a href="{{ route('users.edit', $user) }}"
                                           class="text-apple-blue hover:text-blue-600 transition-colors">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                            </svg>
                                        </a>
                                        @if($user->id !== auth()->id())
                                            <form action="{{ route('users.destroy', $user) }}" method="POST"
                                                  onsubmit="return confirm('Are you sure you want to delete this user?');" class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:text-red-800 transition-colors">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                    </svg>
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-12 text-center">
                                    <div class="text-apple-gray-400">
                                        <svg class="mx-auto h-12 w-12 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                                        </svg>
                                        <p class="text-sm font-medium">No users found</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($users->hasPages())
                <div class="px-6 py-4 border-t border-apple-gray-200">
                    {{ $users->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
