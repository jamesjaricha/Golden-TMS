<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-2xl text-apple-gray-900 leading-tight">
            {{ __('Notifications') }}
        </h2>
    </x-slot>

    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-white rounded-apple-lg shadow-apple">
            <div class="p-6 border-b border-apple-gray-200 flex items-center justify-between">
                <h3 class="text-lg font-semibold text-apple-gray-900">All Notifications</h3>
                @if($notifications->where('read', false)->count() > 0)
                    <form action="{{ route('notifications.mark-all-read') }}" method="POST">
                        @csrf
                        <button type="submit" class="px-4 py-2 bg-apple-blue text-white rounded-apple hover:bg-blue-600 transition-all">
                            Mark all as read
                        </button>
                    </form>
                @endif
            </div>

            @if($notifications->count() > 0)
                <div class="divide-y divide-apple-gray-200">
                    @foreach($notifications as $notification)
                        <a href="{{ $notification->url ?? '#' }}"
                           class="block p-6 hover:bg-apple-gray-50 transition-colors {{ $notification->read ? '' : 'bg-blue-50' }}"
                           onclick="event.preventDefault(); markAsRead({{ $notification->id }}, '{{ $notification->url }}')">
                            <div class="flex items-start space-x-4">
                                <div class="flex-shrink-0">
                                    @if($notification->type === 'ticket_assigned')
                                        <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center">
                                            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                            </svg>
                                        </div>
                                    @else
                                        <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center">
                                            <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                            </svg>
                                        </div>
                                    @endif
                                </div>
                                <div class="flex-1">
                                    <div class="flex items-center justify-between">
                                        <h4 class="text-base font-semibold text-apple-gray-900">{{ $notification->title }}</h4>
                                        <span class="text-sm text-apple-gray-500">{{ $notification->created_at->diffForHumans() }}</span>
                                    </div>
                                    <p class="mt-1 text-sm text-apple-gray-700">{{ $notification->message }}</p>
                                    @if($notification->data && isset($notification->data['ticket_number']))
                                        <p class="mt-2 text-xs text-apple-gray-500">Ticket: {{ $notification->data['ticket_number'] }}</p>
                                    @endif
                                </div>
                                @if(!$notification->read)
                                    <div class="flex-shrink-0">
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                            New
                                        </span>
                                    </div>
                                @endif
                            </div>
                        </a>
                    @endforeach
                </div>

                <div class="p-6 border-t border-apple-gray-200">
                    {{ $notifications->links() }}
                </div>
            @else
                <div class="p-12 text-center">
                    <svg class="w-16 h-16 text-apple-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                    </svg>
                    <h3 class="text-lg font-medium text-apple-gray-900 mb-2">No notifications</h3>
                    <p class="text-sm text-apple-gray-500">You'll see notifications here when tickets are assigned or updated</p>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
