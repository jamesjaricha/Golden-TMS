<x-app-layout>
    <div class="min-h-screen gradient-primary py-4 sm:py-8 px-3 sm:px-4 lg:px-8">
        <div class="max-w-6xl mx-auto">
            <!-- Page Header with User Avatar -->
            <div class="text-center mb-6 sm:mb-10">
                <div class="inline-flex items-center justify-center w-16 h-16 sm:w-20 sm:h-20 md:w-24 md:h-24 rounded-full bg-white/20 backdrop-blur-lg border-2 sm:border-4 border-white/40 shadow-2xl mb-3 sm:mb-4">
                    <span class="text-2xl sm:text-3xl md:text-4xl font-bold text-white">{{ strtoupper(substr(Auth::user()->name, 0, 2)) }}</span>
                </div>
                <h1 class="text-2xl sm:text-3xl md:text-4xl lg:text-5xl font-extrabold text-white drop-shadow-2xl mb-2 sm:mb-3 px-2 break-words">
                    {{ Auth::user()->name }}
                </h1>
                <p class="text-sm sm:text-base md:text-lg lg:text-xl text-purple-100 drop-shadow-lg mb-2 sm:mb-1 px-2 break-all">
                    {{ Auth::user()->email }}
                </p>
                <span class="inline-flex items-center px-3 sm:px-4 py-1 sm:py-1.5 rounded-full text-xs sm:text-sm font-semibold bg-white/30 backdrop-blur-sm text-white border border-white/40 shadow-lg">
                    <svg class="w-3 h-3 sm:w-4 sm:h-4 mr-1.5 sm:mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"/>
                    </svg>
                    {{ ucwords(str_replace('_', ' ', Auth::user()->role)) }}
                </span>
            </div>

            <!-- Grid Layout -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 sm:gap-6">
                <!-- Profile Information Card -->
                <div class="bg-white rounded-xl sm:rounded-2xl shadow-2xl overflow-hidden">
                    <div class="bg-gradient-to-r from-indigo-500 to-purple-600 px-4 sm:px-6 py-3 sm:py-4">
                        <h2 class="text-lg sm:text-xl md:text-2xl font-bold text-white flex items-center">
                            <svg class="w-5 h-5 sm:w-6 sm:h-6 mr-2 sm:mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                            <span class="truncate">Profile Information</span>
                        </h2>
                        <p class="text-indigo-100 text-xs sm:text-sm mt-1">Update your account details</p>
                    </div>
                    <div class="p-4 sm:p-6">
                        @include('profile.partials.update-profile-information-form')
                    </div>
                </div>

                <!-- Password Card -->
                <div class="bg-white rounded-xl sm:rounded-2xl shadow-2xl overflow-hidden">
                    <div class="bg-gradient-to-r from-blue-500 to-cyan-600 px-4 sm:px-6 py-3 sm:py-4">
                        <h2 class="text-lg sm:text-xl md:text-2xl font-bold text-white flex items-center">
                            <svg class="w-5 h-5 sm:w-6 sm:h-6 mr-2 sm:mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                            </svg>
                            <span class="truncate">Password Security</span>
                        </h2>
                        <p class="text-blue-100 text-xs sm:text-sm mt-1">Keep your account secure</p>
                    </div>
                    <div class="p-4 sm:p-6">
                        @include('profile.partials.update-password-form')
                    </div>
                </div>
            </div>

            <!-- Danger Zone - Full Width -->
            <div class="mt-4 sm:mt-6 bg-white rounded-xl sm:rounded-2xl shadow-2xl overflow-hidden border-2 border-red-200">
                <div class="bg-gradient-to-r from-red-500 to-pink-600 px-4 sm:px-6 py-3 sm:py-4">
                    <h2 class="text-lg sm:text-xl md:text-2xl font-bold text-white flex items-center">
                        <svg class="w-5 h-5 sm:w-6 sm:h-6 mr-2 sm:mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                            </svg>
                            <span class="truncate">Danger Zone</span>
                        </h2>
                        <p class="text-red-100 text-xs sm:text-sm mt-1">Irreversible actions - proceed with caution</p>
                    </div>
                    <div class="p-4 sm:p-6">
                        @include('profile.partials.delete-user-form')
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
