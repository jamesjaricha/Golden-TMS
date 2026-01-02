<!-- Sidebar -->
<aside x-data="{ open: true }"
       :class="open ? 'w-64' : 'w-20'"
       class="bg-white border-r border-apple-gray-200 transition-all duration-300 ease-in-out flex-shrink-0 hidden lg:block sticky top-0 h-screen overflow-y-auto">

    <!-- Logo -->
    <div class="p-4 border-b border-apple-gray-200 flex items-center justify-between">
        <a href="{{ route('dashboard') }}" class="flex items-center space-x-3 group">
            <div class="w-10 h-10 bg-gradient-to-br from-apple-blue to-blue-600 rounded-apple shadow-apple flex items-center justify-center transition-transform group-hover:scale-105 flex-shrink-0">
                <span class="text-white font-bold text-lg">GT</span>
            </div>
            <span x-show="open" x-transition class="text-apple-gray-900 font-semibold text-lg whitespace-nowrap">GKTMS</span>
        </a>
        <button @click="open = !open" class="text-apple-gray-500 hover:text-apple-gray-700 focus:outline-none">
            <svg x-show="open" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 19l-7-7 7-7m8 14l-7-7 7-7"/>
            </svg>
            <svg x-show="!open" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 5l7 7-7 7M5 5l7 7-7 7"/>
            </svg>
        </button>
    </div>

    <!-- Navigation -->
    <nav class="p-4 space-y-6">
        <!-- Main Section -->
        <div>
            <h3 x-show="open" x-transition class="text-xs font-semibold text-apple-gray-400 uppercase tracking-wider mb-3">Main</h3>
            <div class="space-y-1">
                <a href="{{ route('dashboard') }}"
                   class="flex items-center px-3 py-2 text-sm font-medium rounded-apple transition-all {{ request()->routeIs('dashboard') ? 'bg-apple-blue text-white' : 'text-apple-gray-700 hover:bg-apple-gray-100' }}">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                    </svg>
                    <span x-show="open" x-transition class="ml-3">Dashboard</span>
                </a>

                <div x-data="{ ticketsOpen: {{ request()->routeIs('complaints.*') ? 'true' : 'false' }} }">
                    <button @click="ticketsOpen = !ticketsOpen"
                            class="flex items-center justify-between w-full px-3 py-2 text-sm font-medium rounded-apple transition-all {{ request()->routeIs('complaints.*') ? 'bg-apple-blue text-white' : 'text-apple-gray-700 hover:bg-apple-gray-100' }}">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            <span x-show="open" x-transition class="ml-3">Tickets</span>
                        </div>
                        <svg x-show="open" :class="ticketsOpen ? 'rotate-90' : ''" class="w-4 h-4 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </button>
                    <div x-show="ticketsOpen && open" x-collapse class="ml-8 mt-1 space-y-1">
                        <a href="{{ route('complaints.index') }}"
                           class="flex items-center px-3 py-1.5 text-sm rounded-apple transition-all {{ !request()->has('status') && request()->routeIs('complaints.index') ? 'text-apple-blue font-medium' : 'text-apple-gray-600 hover:text-apple-gray-900 hover:bg-apple-gray-50' }}">
                            All Tickets
                        </a>
                        <a href="{{ route('complaints.index', ['status' => 'pending']) }}"
                           class="flex items-center px-3 py-1.5 text-sm rounded-apple transition-all {{ request()->get('status') === 'pending' ? 'text-apple-blue font-medium' : 'text-apple-gray-600 hover:text-apple-gray-900 hover:bg-apple-gray-50' }}">
                            Pending
                        </a>
                        <a href="{{ route('complaints.index', ['status' => 'assigned']) }}"
                           class="flex items-center px-3 py-1.5 text-sm rounded-apple transition-all {{ request()->get('status') === 'assigned' ? 'text-apple-blue font-medium' : 'text-apple-gray-600 hover:text-apple-gray-900 hover:bg-apple-gray-50' }}">
                            Assigned
                        </a>
                        <a href="{{ route('complaints.index', ['status' => 'in_progress']) }}"
                           class="flex items-center px-3 py-1.5 text-sm rounded-apple transition-all {{ request()->get('status') === 'in_progress' ? 'text-apple-blue font-medium' : 'text-apple-gray-600 hover:text-apple-gray-900 hover:bg-apple-gray-50' }}">
                            In Progress
                        </a>
                        <a href="{{ route('complaints.index', ['status' => 'partial_closed']) }}"
                           class="flex items-center px-3 py-1.5 text-sm rounded-apple transition-all {{ request()->get('status') === 'partial_closed' ? 'text-apple-blue font-medium' : 'text-apple-gray-600 hover:text-apple-gray-900 hover:bg-apple-gray-50' }}">
                            Partial Closed
                        </a>
                        <a href="{{ route('complaints.index', ['status' => 'resolved']) }}"
                           class="flex items-center px-3 py-1.5 text-sm rounded-apple transition-all {{ request()->get('status') === 'resolved' ? 'text-apple-blue font-medium' : 'text-apple-gray-600 hover:text-apple-gray-900 hover:bg-apple-gray-50' }}">
                            Resolved
                        </a>
                        <a href="{{ route('complaints.index', ['status' => 'closed']) }}"
                           class="flex items-center px-3 py-1.5 text-sm rounded-apple transition-all {{ request()->get('status') === 'closed' ? 'text-apple-blue font-medium' : 'text-apple-gray-600 hover:text-apple-gray-900 hover:bg-apple-gray-50' }}">
                            Closed
                        </a>
                        <a href="{{ route('complaints.index', ['status' => 'escalated']) }}"
                           class="flex items-center px-3 py-1.5 text-sm rounded-apple transition-all {{ request()->get('status') === 'escalated' ? 'text-apple-blue font-medium' : 'text-apple-gray-600 hover:text-apple-gray-900 hover:bg-apple-gray-50' }}">
                            Escalated
                        </a>
                    </div>
                </div>

                @if(Auth::user()->isAdmin())
                <a href="{{ route('reports.wizard') }}"
                   class="flex items-center px-3 py-2 text-sm font-medium rounded-apple transition-all {{ request()->routeIs('reports.*') || request()->routeIs('complaints.export.*') ? 'bg-apple-blue text-white' : 'text-apple-gray-700 hover:bg-apple-gray-100' }}">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    <span x-show="open" x-transition class="ml-3">Reports</span>
                </a>
                @endif

                @if(Auth::user()->isAdmin())
                    <a href="{{ route('analytics.index') }}"
                       class="flex items-center px-3 py-2 text-sm font-medium rounded-apple transition-all {{ request()->routeIs('analytics.*') ? 'bg-apple-blue text-white' : 'text-apple-gray-700 hover:bg-apple-gray-100' }}">
                        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                        </svg>
                        <span x-show="open" x-transition class="ml-3">Analytics</span>
                    </a>
                @endif
            </div>
        </div>

        <!-- Administration Section -->
        @if(Auth::user()->isSuperAdmin())
            <div>
                <h3 x-show="open" x-transition class="text-xs font-semibold text-apple-gray-400 uppercase tracking-wider mb-3">Administration</h3>
                <div class="space-y-1">
                    <a href="{{ route('users.index') }}"
                       class="flex items-center px-3 py-2 text-sm font-medium rounded-apple transition-all {{ request()->routeIs('users.*') ? 'bg-apple-blue text-white' : 'text-apple-gray-700 hover:bg-apple-gray-100' }}">
                        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                        </svg>
                        <span x-show="open" x-transition class="ml-3">Users</span>
                    </a>

                    <a href="{{ route('audit-logs.index') }}"
                       class="flex items-center px-3 py-2 text-sm font-medium rounded-apple transition-all {{ request()->routeIs('audit-logs.*') ? 'bg-apple-blue text-white' : 'text-apple-gray-700 hover:bg-apple-gray-100' }}">
                        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        <span x-show="open" x-transition class="ml-3">Audit Logs</span>
                    </a>
                </div>
            </div>

            <!-- Configuration Section -->
            <div>
                <h3 x-show="open" x-transition class="text-xs font-semibold text-apple-gray-400 uppercase tracking-wider mb-3">Configuration</h3>
                <div class="space-y-1">
                    <a href="{{ route('branches.index') }}"
                       class="flex items-center px-3 py-2 text-sm font-medium rounded-apple transition-all {{ request()->routeIs('branches.*') ? 'bg-apple-blue text-white' : 'text-apple-gray-700 hover:bg-apple-gray-100' }}">
                        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                        </svg>
                        <span x-show="open" x-transition class="ml-3">Branches</span>
                    </a>

                    <a href="{{ route('employers.index') }}"
                       class="flex items-center px-3 py-2 text-sm font-medium rounded-apple transition-all {{ request()->routeIs('employers.*') ? 'bg-apple-blue text-white' : 'text-apple-gray-700 hover:bg-apple-gray-100' }}">
                        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                        </svg>
                        <span x-show="open" x-transition class="ml-3">Employers</span>
                    </a>

                    <a href="{{ route('departments.index') }}"
                       class="flex items-center px-3 py-2 text-sm font-medium rounded-apple transition-all {{ request()->routeIs('departments.*') ? 'bg-apple-blue text-white' : 'text-apple-gray-700 hover:bg-apple-gray-100' }}">
                        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                        </svg>
                        <span x-show="open" x-transition class="ml-3">Departments</span>
                    </a>

                    <a href="{{ route('payment-methods.index') }}"
                       class="flex items-center px-3 py-2 text-sm font-medium rounded-apple transition-all {{ request()->routeIs('payment-methods.*') ? 'bg-apple-blue text-white' : 'text-apple-gray-700 hover:bg-apple-gray-100' }}">
                        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                        </svg>
                        <span x-show="open" x-transition class="ml-3">Payment Methods</span>
                    </a>
                </div>
            </div>

            <!-- Settings Section -->
            <div>
                <h3 x-show="open" x-transition class="text-xs font-semibold text-apple-gray-400 uppercase tracking-wider mb-3">Settings</h3>
                <div class="space-y-1">
                    <a href="{{ route('settings.twilio') }}"
                       class="flex items-center px-3 py-2 text-sm font-medium rounded-apple transition-all {{ request()->routeIs('settings.twilio') ? 'bg-apple-blue text-white' : 'text-apple-gray-700 hover:bg-apple-gray-100' }}">
                        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/>
                        </svg>
                        <span x-show="open" x-transition class="ml-3">WhatsApp Settings</span>
                    </a>
                </div>
            </div>
        @endif
    </nav>
</aside>

<!-- Mobile Sidebar Overlay -->
<div x-data="{ mobileOpen: false }" class="lg:hidden">
    <!-- Mobile Menu Button -->
    <button @click="mobileOpen = true" class="fixed bottom-4 right-4 z-50 p-3 bg-apple-blue text-white rounded-full shadow-lg">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
        </svg>
    </button>

    <!-- Overlay -->
    <div x-show="mobileOpen"
         x-transition:enter="transition-opacity ease-linear duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition-opacity ease-linear duration-300"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         @click="mobileOpen = false"
         class="fixed inset-0 bg-black bg-opacity-50 z-40"></div>

    <!-- Mobile Sidebar -->
    <aside x-show="mobileOpen"
           x-transition:enter="transition ease-in-out duration-300 transform"
           x-transition:enter-start="-translate-x-full"
           x-transition:enter-end="translate-x-0"
           x-transition:leave="transition ease-in-out duration-300 transform"
           x-transition:leave-start="translate-x-0"
           x-transition:leave-end="-translate-x-full"
           class="fixed inset-y-0 left-0 z-50 w-64 bg-white border-r border-apple-gray-200 overflow-y-auto">

        <!-- Logo -->
        <div class="p-4 border-b border-apple-gray-200 flex items-center justify-between">
            <a href="{{ route('dashboard') }}" class="flex items-center space-x-3">
                <div class="w-10 h-10 bg-gradient-to-br from-apple-blue to-blue-600 rounded-apple shadow-apple flex items-center justify-center">
                    <span class="text-white font-bold text-lg">GT</span>
                </div>
                <span class="text-apple-gray-900 font-semibold text-lg">GKTMS</span>
            </a>
            <button @click="mobileOpen = false" class="text-apple-gray-500 hover:text-apple-gray-700">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        <!-- Navigation (Same as desktop) -->
        <nav class="p-4 space-y-6">
            <!-- Main Section -->
            <div>
                <h3 class="text-xs font-semibold text-apple-gray-400 uppercase tracking-wider mb-3">Main</h3>
                <div class="space-y-1">
                    <a href="{{ route('dashboard') }}"
                       class="flex items-center px-3 py-2 text-sm font-medium rounded-apple transition-all {{ request()->routeIs('dashboard') ? 'bg-apple-blue text-white' : 'text-apple-gray-700 hover:bg-apple-gray-100' }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                        </svg>
                        <span class="ml-3">Dashboard</span>
                    </a>

                    <div x-data="{ ticketsOpen: {{ request()->routeIs('complaints.*') ? 'true' : 'false' }} }">
                        <button @click="ticketsOpen = !ticketsOpen"
                                class="flex items-center justify-between w-full px-3 py-2 text-sm font-medium rounded-apple transition-all {{ request()->routeIs('complaints.*') ? 'bg-apple-blue text-white' : 'text-apple-gray-700 hover:bg-apple-gray-100' }}">
                            <div class="flex items-center">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                                <span class="ml-3">Tickets</span>
                            </div>
                            <svg :class="ticketsOpen ? 'rotate-90' : ''" class="w-4 h-4 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                            </svg>
                        </button>
                        <div x-show="ticketsOpen" x-collapse class="ml-8 mt-1 space-y-1">
                            <a href="{{ route('complaints.index') }}"
                               class="flex items-center px-3 py-1.5 text-sm rounded-apple transition-all {{ !request()->has('status') && request()->routeIs('complaints.index') ? 'text-apple-blue font-medium' : 'text-apple-gray-600 hover:text-apple-gray-900 hover:bg-apple-gray-50' }}">
                                All Tickets
                            </a>
                            <a href="{{ route('complaints.index', ['status' => 'pending']) }}"
                               class="flex items-center px-3 py-1.5 text-sm rounded-apple transition-all {{ request()->get('status') === 'pending' ? 'text-apple-blue font-medium' : 'text-apple-gray-600 hover:text-apple-gray-900 hover:bg-apple-gray-50' }}">
                                Pending
                            </a>
                            <a href="{{ route('complaints.index', ['status' => 'assigned']) }}"
                               class="flex items-center px-3 py-1.5 text-sm rounded-apple transition-all {{ request()->get('status') === 'assigned' ? 'text-apple-blue font-medium' : 'text-apple-gray-600 hover:text-apple-gray-900 hover:bg-apple-gray-50' }}">
                                Assigned
                            </a>
                            <a href="{{ route('complaints.index', ['status' => 'in_progress']) }}"
                               class="flex items-center px-3 py-1.5 text-sm rounded-apple transition-all {{ request()->get('status') === 'in_progress' ? 'text-apple-blue font-medium' : 'text-apple-gray-600 hover:text-apple-gray-900 hover:bg-apple-gray-50' }}">
                                In Progress
                            </a>
                            <a href="{{ route('complaints.index', ['status' => 'partial_closed']) }}"
                               class="flex items-center px-3 py-1.5 text-sm rounded-apple transition-all {{ request()->get('status') === 'partial_closed' ? 'text-apple-blue font-medium' : 'text-apple-gray-600 hover:text-apple-gray-900 hover:bg-apple-gray-50' }}">
                                Partial Closed
                            </a>
                            <a href="{{ route('complaints.index', ['status' => 'resolved']) }}"
                               class="flex items-center px-3 py-1.5 text-sm rounded-apple transition-all {{ request()->get('status') === 'resolved' ? 'text-apple-blue font-medium' : 'text-apple-gray-600 hover:text-apple-gray-900 hover:bg-apple-gray-50' }}">
                                Resolved
                            </a>
                            <a href="{{ route('complaints.index', ['status' => 'closed']) }}"
                               class="flex items-center px-3 py-1.5 text-sm rounded-apple transition-all {{ request()->get('status') === 'closed' ? 'text-apple-blue font-medium' : 'text-apple-gray-600 hover:text-apple-gray-900 hover:bg-apple-gray-50' }}">
                                Closed
                            </a>
                            <a href="{{ route('complaints.index', ['status' => 'escalated']) }}"
                               class="flex items-center px-3 py-1.5 text-sm rounded-apple transition-all {{ request()->get('status') === 'escalated' ? 'text-apple-blue font-medium' : 'text-apple-gray-600 hover:text-apple-gray-900 hover:bg-apple-gray-50' }}">
                                Escalated
                            </a>
                        </div>
                    </div>

                    @if(Auth::user()->isAdmin())
                    <a href="{{ route('reports.wizard') }}"
                       class="flex items-center px-3 py-2 text-sm font-medium rounded-apple transition-all {{ request()->routeIs('reports.*') || request()->routeIs('complaints.export.*') ? 'bg-apple-blue text-white' : 'text-apple-gray-700 hover:bg-apple-gray-100' }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        <span class="ml-3">Reports</span>
                    </a>
                    @endif

                    @if(Auth::user()->isAdmin())
                        <a href="{{ route('analytics.index') }}"
                           class="flex items-center px-3 py-2 text-sm font-medium rounded-apple transition-all {{ request()->routeIs('analytics.*') ? 'bg-apple-blue text-white' : 'text-apple-gray-700 hover:bg-apple-gray-100' }}">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                            </svg>
                            <span class="ml-3">Analytics</span>
                        </a>
                    @endif
                </div>
            </div>

            @if(Auth::user()->isSuperAdmin())
                <!-- Administration Section -->
                <div>
                    <h3 class="text-xs font-semibold text-apple-gray-400 uppercase tracking-wider mb-3">Administration</h3>
                    <div class="space-y-1">
                        <a href="{{ route('users.index') }}"
                           class="flex items-center px-3 py-2 text-sm font-medium rounded-apple transition-all {{ request()->routeIs('users.*') ? 'bg-apple-blue text-white' : 'text-apple-gray-700 hover:bg-apple-gray-100' }}">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                            </svg>
                            <span class="ml-3">Users</span>
                        </a>

                        <a href="{{ route('audit-logs.index') }}"
                           class="flex items-center px-3 py-2 text-sm font-medium rounded-apple transition-all {{ request()->routeIs('audit-logs.*') ? 'bg-apple-blue text-white' : 'text-apple-gray-700 hover:bg-apple-gray-100' }}">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            <span class="ml-3">Audit Logs</span>
                        </a>
                    </div>
                </div>

                <!-- Configuration Section -->
                <div>
                    <h3 class="text-xs font-semibold text-apple-gray-400 uppercase tracking-wider mb-3">Configuration</h3>
                    <div class="space-y-1">
                        <a href="{{ route('branches.index') }}"
                           class="flex items-center px-3 py-2 text-sm font-medium rounded-apple transition-all {{ request()->routeIs('branches.*') ? 'bg-apple-blue text-white' : 'text-apple-gray-700 hover:bg-apple-gray-100' }}">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                            </svg>
                            <span class="ml-3">Branches</span>
                        </a>

                        <a href="{{ route('employers.index') }}"
                           class="flex items-center px-3 py-2 text-sm font-medium rounded-apple transition-all {{ request()->routeIs('employers.*') ? 'bg-apple-blue text-white' : 'text-apple-gray-700 hover:bg-apple-gray-100' }}">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                            </svg>
                            <span class="ml-3">Employers</span>
                        </a>

                        <a href="{{ route('departments.index') }}"
                           class="flex items-center px-3 py-2 text-sm font-medium rounded-apple transition-all {{ request()->routeIs('departments.*') ? 'bg-apple-blue text-white' : 'text-apple-gray-700 hover:bg-apple-gray-100' }}">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                            </svg>
                            <span class="ml-3">Departments</span>
                        </a>

                        <a href="{{ route('payment-methods.index') }}"
                           class="flex items-center px-3 py-2 text-sm font-medium rounded-apple transition-all {{ request()->routeIs('payment-methods.*') ? 'bg-apple-blue text-white' : 'text-apple-gray-700 hover:bg-apple-gray-100' }}">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                            </svg>
                            <span class="ml-3">Payment Methods</span>
                        </a>
                    </div>
                </div>

                <!-- Settings Section -->
                <div>
                    <h3 class="text-xs font-semibold text-apple-gray-400 uppercase tracking-wider mb-3">Settings</h3>
                    <div class="space-y-1">
                        <a href="{{ route('settings.twilio') }}"
                           class="flex items-center px-3 py-2 text-sm font-medium rounded-apple transition-all {{ request()->routeIs('settings.twilio') ? 'bg-apple-blue text-white' : 'text-apple-gray-700 hover:bg-apple-gray-100' }}">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/>
                            </svg>
                            <span class="ml-3">WhatsApp Settings</span>
                        </a>
                    </div>
                </div>
            @endif
        </nav>
    </aside>
</div>
