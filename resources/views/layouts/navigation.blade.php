<nav x-data="{ open: false }" class="bg-white/80 backdrop-blur-xl border-b border-apple-gray-100 sticky top-0 z-50 shadow-apple-sm">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex items-center">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}" class="flex items-center space-x-3 group">
                        <div class="w-10 h-10 bg-gradient-to-br from-apple-blue to-blue-600 rounded-apple shadow-apple flex items-center justify-center transition-transform group-hover:scale-105">
                            <span class="text-white font-bold text-lg">GT</span>
                        </div>
                        <span class="text-apple-gray-900 font-semibold text-lg hidden sm:block">GKTMS</span>
                    </a>
                </div>

                <!-- Navigation Links -->
                <div class="hidden space-x-2 sm:ms-10 sm:flex">
                    <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')"
                        class="px-4 py-2 rounded-apple transition-all duration-200">
                        {{ __('Dashboard') }}
                    </x-nav-link>

                    <x-nav-link :href="route('complaints.index')" :active="request()->routeIs('complaints.*')"
                        class="px-4 py-2 rounded-apple transition-all duration-200">
                        {{ __('Tickets') }}
                    </x-nav-link>

                    @if(Auth::user()->isAdmin())
                        <x-nav-link :href="route('analytics.index')" :active="request()->routeIs('analytics.*')"
                            class="px-4 py-2 rounded-apple transition-all duration-200">
                            {{ __('Analytics') }}
                        </x-nav-link>
                    @endif

                    @if(Auth::user()->isSuperAdmin())
                        <x-nav-link :href="route('users.index')" :active="request()->routeIs('users.*')"
                            class="px-4 py-2 rounded-apple transition-all duration-200">
                            {{ __('Users') }}
                        </x-nav-link>
                    @endif
                </div>
            </div>

            <!-- Settings Dropdown -->
            <div class="hidden sm:flex sm:items-center sm:ms-6 space-x-3">
                <!-- Notification Bell -->
                @php
                    $unreadCount = \App\Services\NotificationService::getUnreadCount(Auth::id());
                    $notifications = \App\Services\NotificationService::getRecent(Auth::id(), 5);
                @endphp

                <x-dropdown align="right" width="80">
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
                                                    <p class="text-sm text-apple-gray-600 mt-1">{{ $notification->message }}</p>
                                                    <p class="text-xs text-apple-gray-500 mt-1">{{ $notification->created_at->diffForHumans() }}</p>
                                                </div>
                                                @if(!$notification->read)
                                                    <div class="flex-shrink-0">
                                                        <span class="inline-block w-2 h-2 bg-blue-600 rounded-full"></span>
                                                    </div>
                                                @endif
                                            </div>
                                        </a>
                                    @endforeach
                                </div>
                                <div class="px-4 py-3 border-t border-apple-gray-200 text-center">
                                    <a href="{{ route('notifications.index') }}" class="text-sm text-apple-blue hover:text-blue-600">
                                        View all notifications
                                    </a>
                                </div>
                            @else
                                <div class="px-4 py-8 text-center">
                                    <svg class="w-12 h-12 text-apple-gray-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                                    </svg>
                                    <p class="text-sm text-apple-gray-500">No notifications</p>
                                </div>
                            @endif
                        </div>
                    </x-slot>
                </x-dropdown>

                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center px-4 py-2 border border-transparent text-sm leading-4 font-medium rounded-apple text-apple-gray-700 bg-apple-gray-50 hover:bg-apple-gray-100 focus:outline-none focus:ring-2 focus:ring-apple-blue transition-all duration-200">
                            <div class="flex items-center space-x-2">
                                <div class="w-8 h-8 bg-gradient-to-br from-apple-blue to-blue-600 rounded-full flex items-center justify-center text-white text-xs font-semibold">
                                    {{ strtoupper(substr(Auth::user()->name, 0, 2)) }}
                                </div>
                                <span>{{ Auth::user()->name }}</span>
                            </div>

                            <div class="ms-2">
                                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <x-dropdown-link :href="route('profile.edit')">
                            {{ __('Profile') }}
                        </x-dropdown-link>

                        <!-- Authentication -->
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf

                            <x-dropdown-link :href="route('logout')"
                                    onclick="event.preventDefault();
                                                this.closest('form').submit();">
                                {{ __('Log Out') }}
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>

            <!-- Hamburger -->
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-apple text-apple-gray-500 hover:text-apple-gray-700 hover:bg-apple-gray-100 focus:outline-none focus:bg-apple-gray-100 focus:text-apple-gray-700 transition duration-200">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden bg-white/95 backdrop-blur-xl border-t border-apple-gray-100">
        <div class="pt-2 pb-3 space-y-1 px-4">
            <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                {{ __('Dashboard') }}
            </x-responsive-nav-link>

            <x-responsive-nav-link :href="route('complaints.index')" :active="request()->routeIs('complaints.*')">
                {{ __('Tickets') }}
            </x-responsive-nav-link>

            @if(Auth::user()->isAdmin())
                <x-responsive-nav-link :href="route('analytics.index')" :active="request()->routeIs('analytics.*')">
                    {{ __('Analytics') }}
                </x-responsive-nav-link>
            @endif

            @if(Auth::user()->isSuperAdmin())
                <x-responsive-nav-link :href="route('users.index')" :active="request()->routeIs('users.*')">
                    {{ __('Users') }}
                </x-responsive-nav-link>
            @endif
        </div>

        <!-- Responsive Settings Options -->
        <div class="pt-4 pb-4 border-t border-apple-gray-100">
            <div class="px-4 flex items-center space-x-3">
                <div class="w-10 h-10 bg-gradient-to-br from-apple-blue to-blue-600 rounded-full flex items-center justify-center text-white text-sm font-semibold">
                    {{ strtoupper(substr(Auth::user()->name, 0, 2)) }}
                </div>
                <div>
                    <div class="font-medium text-base text-apple-gray-900">{{ Auth::user()->name }}</div>
                    <div class="font-medium text-sm text-apple-gray-500">{{ Auth::user()->email }}</div>
                </div>
            </div>

            <div class="mt-3 space-y-1 px-4">
                <x-responsive-nav-link :href="route('profile.edit')">
                    {{ __('Profile') }}
                </x-responsive-nav-link>

                <!-- Authentication -->
                <form method="POST" action="{{ route('logout') }}">
                    @csrf

                    <x-responsive-nav-link :href="route('logout')"
                            onclick="event.preventDefault();
                                        this.closest('form').submit();">
                        {{ __('Log Out') }}
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
</nav>
