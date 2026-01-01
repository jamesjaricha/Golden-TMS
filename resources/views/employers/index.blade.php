<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-semibold text-2xl text-apple-gray-900 leading-tight">
                    {{ __('Employers') }}
                </h2>
                <p class="text-sm text-apple-gray-500 mt-1">Manage employer organizations</p>
            </div>
            <a href="{{ route('employers.create') }}"
               class="inline-flex items-center px-4 py-2 bg-apple-blue text-white font-medium rounded-apple hover:bg-apple-blue-dark transition-all duration-200 shadow-sm">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Add Employer
            </a>
        </div>
    </x-slot>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        @if(session('success'))
            <div class="mb-4 bg-green-50 border-l-4 border-green-400 p-4 rounded-apple">
                <p class="text-green-700">{{ session('success') }}</p>
            </div>
        @endif

        @if(session('error'))
            <div class="mb-4 bg-red-50 border-l-4 border-red-400 p-4 rounded-apple">
                <p class="text-red-700">{{ session('error') }}</p>
            </div>
        @endif

        <div class="bg-white rounded-apple-lg shadow-apple overflow-hidden">
            <table class="min-w-full divide-y divide-apple-gray-200">
                <thead class="bg-apple-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-apple-gray-500 uppercase tracking-wider">
                            Name
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-apple-gray-500 uppercase tracking-wider">
                            Tickets
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-apple-gray-500 uppercase tracking-wider">
                            Status
                        </th>
                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-apple-gray-500 uppercase tracking-wider">
                            Actions
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-apple-gray-200">
                    @forelse($employers as $employer)
                        <tr class="hover:bg-apple-gray-50 transition-colors duration-150">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-apple-gray-900">{{ $employer->name }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-apple-gray-500">{{ $employer->complaints_count }} tickets</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($employer->is_active)
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                        Active
                                    </span>
                                @else
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                        Inactive
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <a href="{{ route('employers.edit', $employer) }}" class="text-apple-blue hover:text-apple-blue-dark mr-4">Edit</a>
                                <form action="{{ route('employers.destroy', $employer) }}" method="POST" class="inline-block">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                            onclick="return confirm('Are you sure you want to deactivate this employer?')"
                                            class="text-red-600 hover:text-red-900">
                                        Delete
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-4 text-center text-apple-gray-500">
                                No employers found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>
