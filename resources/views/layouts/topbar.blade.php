<!-- Top Bar -->
<header class="bg-white border-b border-apple-gray-200 h-16 flex items-center px-4 sm:px-6 lg:px-8 sticky top-0 z-30">
    <div class="flex items-center justify-between w-full">
        <!-- Left side - Page title or breadcrumb -->
        <div class="flex items-center space-x-4">
            <!-- Mobile menu button (only visible on mobile) -->
            <button class="lg:hidden text-apple-gray-500 hover:text-apple-gray-700 focus:outline-none">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                </svg>
            </button>

            <h1 class="text-xl font-semibold text-apple-gray-900 hidden sm:block">
                @if(request()->routeIs('dashboard'))
                    Dashboard
                @elseif(request()->routeIs('complaints.*'))
                    Tickets
                @elseif(request()->routeIs('analytics.*'))
                    Analytics
                @elseif(request()->routeIs('users.*'))
                    User Management
                @elseif(request()->routeIs('branches.*'))
                    Branches
                @elseif(request()->routeIs('employers.*'))
                    Employers
                @elseif(request()->routeIs('payment-methods.*'))
                    Payment Methods
                @elseif(request()->routeIs('audit-logs.*'))
                    Audit Logs
                @elseif(request()->routeIs('settings.*'))
                    Settings
                @else
                    GKTMS
                @endif
            </h1>
        </div>

        <!-- Right side - Notifications and Profile -->
        <div class="flex items-center space-x-3">
            <!-- Notification Bell -->
            @php
                $unreadCount = \App\Services\NotificationService::getUnreadCount(Auth::id());
                $notifications = \App\Services\NotificationService::getRecent(Auth::id(), 5);
            @endphp

            <x-dropdown align="right" width="96">
                <x-slot name="trigger">
                    <button class="relative inline-flex items-center p-2 rounded-apple text-apple-gray-700 hover:bg-apple-gray-100 focus:outline-none focus:ring-2 focus:ring-apple-blue transition-all duration-200">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                        </svg>
                        @if($unreadCount > 0)
                            <span class="absolute top-0 right-0 inline-flex items-center justify-center px-2 py-1 text-xs font-bold leading-none text-white transform translate-x-1/2 -translate-y-1/2 bg-red-600 rounded-full shadow-lg">
                                {{ $unreadCount > 9 ? '9+' : $unreadCount }}
                            </span>
                        @endif
                    </button>
                </x-slot>

                <x-slot name="content">
                    <div class="w-96">
                        <div class="px-4 py-3 border-b border-apple-gray-200 flex items-center justify-between">
                            <h3 class="font-semibold text-apple-gray-900">Notifications</h3>
                            @if($unreadCount > 0)
                                <form action="{{ route('notifications.mark-all-read') }}" method="POST">
                                    @csrf
                                    <button type="submit" class="text-xs text-apple-blue hover:text-blue-600">
                                        Mark all as read
                                    </button>
                                </form>
                            @endif
                        </div>

                        @if($notifications->count() > 0)
                            <div class="max-h-96 overflow-y-auto">
                                @foreach($notifications as $notification)
                                    <a href="{{ $notification->url ?? '#' }}"
                                       class="block px-4 py-3 hover:bg-apple-gray-50 transition-colors {{ $notification->read ? 'opacity-60' : 'bg-blue-50' }}"
                                       onclick="event.preventDefault(); markAsRead({{ $notification->id }}, '{{ $notification->url }}')">
                                        <div class="flex items-start space-x-3">
                                            <div class="flex-shrink-0">
                                                @if($notification->type === 'ticket_assigned')
                                                    <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center">
                                                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                                        </svg>
                                                    </div>
                                                @else
                                                    <div class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center">
                                                        <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                        </svg>
                                                    </div>
                                                @endif
                                            </div>
                                            <div class="flex-1 min-w-0">
                                                <p class="text-sm font-medium text-apple-gray-900">{{ $notification->title }}</p>
                                                <p class="text-sm text-apple-gray-600 truncate">{{ $notification->message }}</p>
                                                <p class="text-xs text-apple-gray-400 mt-1">{{ $notification->created_at->diffForHumans() }}</p>
                                            </div>
                                        </div>
                                    </a>
                                @endforeach
                            </div>

                            <div class="px-4 py-3 border-t border-apple-gray-200">
                                <a href="{{ route('notifications.index') }}" class="text-sm text-apple-blue hover:text-blue-600 font-medium">
                                    View all notifications
                                </a>
                            </div>
                        @else
                            <div class="px-4 py-8 text-center">
                                <svg class="mx-auto h-12 w-12 text-apple-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                                </svg>
                                <p class="mt-2 text-sm text-apple-gray-500">No new notifications</p>
                            </div>
                        @endif
                    </div>
                </x-slot>
            </x-dropdown>

            <!-- Profile Dropdown -->
            <x-dropdown align="right" width="48">
                <x-slot name="trigger">
                    <button class="flex items-center space-x-3 p-2 rounded-apple hover:bg-apple-gray-100 transition-all duration-200">
                        <div class="w-8 h-8 bg-gradient-to-br from-apple-blue to-blue-600 rounded-full flex items-center justify-center text-white text-sm font-semibold">
                            {{ strtoupper(substr(Auth::user()->name, 0, 2)) }}
                        </div>
                        <div class="hidden md:block text-left">
                            <p class="text-sm font-medium text-apple-gray-900">{{ Auth::user()->name }}</p>
                            <p class="text-xs text-apple-gray-500">{{ ucfirst(str_replace('_', ' ', Auth::user()->role)) }}</p>
                        </div>
                        <svg class="w-4 h-4 text-apple-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>
                </x-slot>

                <x-slot name="content">
                    <div class="px-4 py-3 border-b border-apple-gray-200">
                        <p class="text-sm font-medium text-apple-gray-900">{{ Auth::user()->name }}</p>
                        <p class="text-xs text-apple-gray-500">{{ Auth::user()->email }}</p>
                    </div>

                    <x-dropdown-link :href="route('profile.edit')">
                        <div class="flex items-center">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                            {{ __('Profile') }}
                        </div>
                    </x-dropdown-link>

                    <!-- Authentication -->
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="block w-full px-4 py-2 text-start text-sm leading-5 text-red-600 hover:bg-red-50 focus:outline-none focus:bg-red-50 transition duration-150 ease-in-out">
                            <div class="flex items-center">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                                </svg>
                                {{ __('Log Out') }}
                            </div>
                        </button>
                    </form>
                </x-slot>
            </x-dropdown>
        </div>
    </div>
</header>
