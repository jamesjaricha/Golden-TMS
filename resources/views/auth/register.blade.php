<x-guest-layout>
    <div class="mb-6">
        <h2 class="text-2xl font-semibold text-apple-gray-900 mb-1">Create Account</h2>
        <p class="text-sm text-apple-gray-500">Join GKTMS to get started</p>
    </div>

    <form method="POST" action="{{ route('register') }}" class="space-y-5">
        @csrf

        <!-- Name -->
        <div>
            <x-input-label for="name" :value="__('Full Name')" class="text-apple-gray-700 font-medium" />
            <x-text-input id="name"
                class="block mt-2 w-full px-4 py-3 bg-apple-gray-50 border-0 rounded-apple focus:ring-2 focus:ring-apple-blue focus:bg-white transition-all duration-200"
                type="text"
                name="name"
                :value="old('name')"
                required
                autofocus
                autocomplete="name"
                placeholder="John Doe" />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <!-- Email Address -->
        <div>
            <x-input-label for="email" :value="__('Email')" class="text-apple-gray-700 font-medium" />
            <x-text-input id="email"
                class="block mt-2 w-full px-4 py-3 bg-apple-gray-50 border-0 rounded-apple focus:ring-2 focus:ring-apple-blue focus:bg-white transition-all duration-200"
                type="email"
                name="email"
                :value="old('email')"
                required
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
                autocomplete="new-password"
                placeholder="Create a strong password" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Confirm Password -->
        <div>
            <x-input-label for="password_confirmation" :value="__('Confirm Password')" class="text-apple-gray-700 font-medium" />
            <x-text-input id="password_confirmation"
                class="block mt-2 w-full px-4 py-3 bg-apple-gray-50 border-0 rounded-apple focus:ring-2 focus:ring-apple-blue focus:bg-white transition-all duration-200"
                type="password"
                name="password_confirmation"
                required
                autocomplete="new-password"
                placeholder="Confirm your password" />
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="pt-2">
            <x-primary-button class="w-full justify-center py-3 bg-apple-blue hover:bg-blue-600 focus:bg-blue-600 active:bg-blue-700 rounded-apple font-semibold text-base shadow-apple hover:shadow-apple-md transition-all duration-200 transform hover:scale-[1.02] active:scale-[0.98]">
                {{ __('Create Account') }}
            </x-primary-button>
        </div>

        <div class="text-center pt-4 border-t border-apple-gray-100">
            <p class="text-sm text-apple-gray-600">
                Already have an account?
                <a href="{{ route('login') }}" class="text-apple-blue hover:text-blue-600 font-medium transition-colors">Sign in</a>
            </p>
        </div>
    </form>
</x-guest-layout>
