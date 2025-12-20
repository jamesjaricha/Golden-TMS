<x-guest-layout>
    <div class="mb-6">
        <h2 class="text-2xl font-semibold text-apple-gray-900 mb-1">Welcome Back</h2>
        <p class="text-sm text-apple-gray-500">Sign in to continue to GKTMS</p>
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}" class="space-y-5">
        @csrf

        <!-- Email Address -->
        <div>
            <x-input-label for="email" :value="__('Email')" class="text-apple-gray-700 font-medium" />
            <x-text-input id="email"
                class="block mt-2 w-full px-4 py-3 bg-apple-gray-50 border-0 rounded-apple focus:ring-2 focus:ring-apple-blue focus:bg-white transition-all duration-200"
                type="email"
                name="email"
                :value="old('email')"
                required
                autofocus
                autocomplete="username"
                placeholder="your@email.com" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div>
            <x-input-label for="password" :value="__('Password')" class="text-apple-gray-700 font-medium" />
            <x-text-input id="password"
                class="block mt-2 w-full px-4 py-3 bg-apple-gray-50 border-0 rounded-apple focus:ring-2 focus:ring-apple-blue focus:bg-white transition-all duration-200"
                type="password"
                name="password"
                required
                autocomplete="current-password"
                placeholder="Enter your password" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Remember Me & Forgot Password -->
        <div class="flex items-center justify-between">
            <label for="remember_me" class="inline-flex items-center cursor-pointer group">
                <input id="remember_me"
                    type="checkbox"
                    class="rounded border-apple-gray-300 text-apple-blue shadow-sm focus:ring-apple-blue transition-all"
                    name="remember">
                <span class="ms-2 text-sm text-apple-gray-600 group-hover:text-apple-gray-900 transition-colors">{{ __('Remember me') }}</span>
            </label>

            @if (Route::has('password.request'))
                <a class="text-sm text-apple-blue hover:text-blue-600 font-medium transition-colors"
                   href="{{ route('password.request') }}">
                    {{ __('Forgot password?') }}
                </a>
            @endif
        </div>

        <div class="pt-2">
            <x-primary-button class="w-full justify-center py-3 bg-apple-blue hover:bg-blue-600 focus:bg-blue-600 active:bg-blue-700 rounded-apple font-semibold text-base shadow-apple hover:shadow-apple-md transition-all duration-200 transform hover:scale-[1.02] active:scale-[0.98]">
                {{ __('Sign In') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
