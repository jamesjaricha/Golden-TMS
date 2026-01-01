<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-semibold text-2xl text-apple-gray-900 leading-tight">
                    {{ __('Branches') }}
                </h2>
                <p class="text-sm text-apple-gray-500 mt-1">Manage organization branches</p>
            </div>
            <a href="{{ route('branches.create') }}"
               class="inline-flex items-center px-4 py-2 bg-apple-blue text-white font-medium rounded-apple hover:bg-blue-600 focus:outline-none transition-all duration-200 shadow-apple hover:shadow-apple-md">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Add Branch
            </a>
        </div>
    </x-slot>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Success/Error Messages -->
        @if(session('success'))
            <div class="mb-6 bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-apple animate-slide-down">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="mb-6 bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-apple animate-slide-down">
                {{ session('error') }}
            </div>
        @endif

        <!-- Branches Table -->
        <div class="bg-white rounded-apple-lg shadow-apple overflow-hidden animate-slide-up">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-apple-gray-200">
                    <thead class="bg-apple-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-apple-gray-500 uppercase tracking-wider">
                                Branch Name
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-apple-gray-500 uppercase tracking-wider">
                                Code
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-apple-gray-500 uppercase tracking-wider">
                                Location
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-apple-gray-500 uppercase tracking-wider">
                                Users
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-apple-gray-500 uppercase tracking-wider">
                                Tickets
                            </th>
                            <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-apple-gray-500 uppercase tracking-wider">
                                Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-apple-gray-200">
                        @forelse($branches as $branch)
                            <tr class="hover:bg-apple-gray-50 transition-colors duration-150">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-apple-gray-900">
                                        {{ $branch->name }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-apple-gray-500">
                                        {{ $branch->code ?? '-' }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-apple-gray-500">
                                        {{ $branch->location ?? '-' }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                        {{ $branch->users_count }} {{ Str::plural('user', $branch->users_count) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-purple-100 text-purple-800">
                                        {{ $branch->complaints_count }} {{ Str::plural('ticket', $branch->complaints_count) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <div class="flex items-center justify-end space-x-2">
                                        <a href="{{ route('branches.edit', $branch) }}"
                                           class="text-apple-blue hover:text-blue-700 transition-colors duration-150"
                                           title="Edit">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                            </svg>
                                        </a>
                                        <form method="POST" action="{{ route('branches.destroy', $branch) }}"
                                              onsubmit="return confirm('Are you sure you want to delete {{ $branch->name }}?');"
                                              class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                    class="text-red-600 hover:text-red-800 transition-colors duration-150"
                                                    title="Delete">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                </svg>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center text-apple-gray-500">
                                    <svg class="mx-auto h-12 w-12 text-apple-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                    </svg>
                                    <p class="mt-4 text-sm">No branches found</p>
                                    <a href="{{ route('branches.create') }}" class="mt-2 inline-block text-apple-blue hover:text-blue-700">
                                        Add your first branch
                                    </a>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($branches->hasPages())
                <div class="bg-white px-4 py-3 border-t border-apple-gray-200 sm:px-6">
                    {{ $branches->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
