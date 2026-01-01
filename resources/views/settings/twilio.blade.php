<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between sm:gap-4">
            <div class="min-w-0 flex-1">
                <h2 class="font-semibold text-xl sm:text-2xl text-apple-gray-900 leading-tight truncate">
                    Twilio WhatsApp Integration
                </h2>
                <p class="text-sm text-apple-gray-500 mt-1 hidden sm:block">Configure Twilio for WhatsApp customer notifications</p>
            </div>
            <div class="flex-shrink-0">
                @if($status['configured'] && $settings['enabled'])
                    <span class="inline-flex items-center px-3 py-1.5 rounded-full text-xs sm:text-sm font-medium bg-green-100 text-green-800">
                        <span class="w-2 h-2 mr-2 bg-green-500 rounded-full animate-pulse"></span>
                        Connected
                    </span>
                @elseif(!$settings['enabled'])
                    <span class="inline-flex items-center px-3 py-1.5 rounded-full text-xs sm:text-sm font-medium bg-yellow-100 text-yellow-800">
                        <span class="w-2 h-2 mr-2 bg-yellow-500 rounded-full"></span>
                        Disabled
                    </span>
                @else
                    <span class="inline-flex items-center px-3 py-1.5 rounded-full text-xs sm:text-sm font-medium bg-red-100 text-red-800">
                        <span class="w-2 h-2 mr-2 bg-red-500 rounded-full"></span>
                        Not Configured
                    </span>
                @endif
            </div>
        </div>
    </x-slot>

    <div class="max-w-4xl mx-auto px-3 sm:px-6 lg:px-8 py-4 sm:py-6">
        @if(session('success'))
            <div class="mb-4 sm:mb-6 bg-green-50 border border-green-200 text-green-800 px-3 sm:px-4 py-3 rounded-lg animate-fade-in">
                <div class="flex items-center">
                    <svg class="w-5 h-5 mr-2 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                    <p class="font-medium text-sm sm:text-base">{{ session('success') }}</p>
                </div>
            </div>
        @endif

        @if($errors->any())
            <div class="mb-4 sm:mb-6 bg-red-50 border border-red-200 text-red-800 px-3 sm:px-4 py-3 rounded-lg">
                <div class="flex items-start">
                    <svg class="w-5 h-5 mr-2 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                    </svg>
                    <div class="min-w-0">
                        <p class="font-medium text-sm">Please fix the following errors:</p>
                        <ul class="mt-1 list-disc list-inside text-xs sm:text-sm">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        @endif

        <!-- Setup Instructions -->
        <div class="bg-gradient-to-r from-red-50 to-orange-50 border border-red-200 rounded-lg p-4 sm:p-6 mb-6">
            <div class="flex flex-col sm:flex-row sm:items-start gap-3 sm:gap-4">
                <div class="flex-shrink-0">
                    <svg class="w-10 h-10 text-red-600" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M12 0C5.373 0 0 5.373 0 12s5.373 12 12 12 12-5.373 12-12S18.627 0 12 0zm0 22c-5.523 0-10-4.477-10-10S6.477 2 12 2s10 4.477 10 10-4.477 10-10 10zm-1-15h2v6h-2zm0 8h2v2h-2z"/>
                    </svg>
                </div>
                <div class="flex-1 min-w-0">
                    <h3 class="text-lg font-semibold text-red-800">Getting Started with Twilio</h3>
                    <ol class="text-sm text-red-700 mt-2 space-y-2 list-decimal list-inside">
                        <li>Go to <a href="https://console.twilio.com" target="_blank" class="underline font-medium">console.twilio.com</a> and sign up or log in</li>
                        <li>Copy your <strong>Account SID</strong> and <strong>Auth Token</strong> from the dashboard</li>
                        <li>Go to Messaging → Try it out → Send a WhatsApp message</li>
                        <li>Follow instructions to connect your phone to the Twilio Sandbox</li>
                        <li>Copy the Sandbox number (e.g., +14155238886) below</li>
                    </ol>
                    @if($settings['sandbox_mode'])
                        <div class="mt-3 p-3 bg-yellow-100 rounded-lg text-yellow-800 text-sm">
                            <strong>⚠️ Sandbox Mode:</strong> Recipients must first send a message to your Twilio WhatsApp number to opt-in before receiving messages.
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <form action="{{ route('settings.twilio.update') }}" method="POST" class="space-y-4 sm:space-y-6">
            @csrf
            @method('PUT')

            <!-- Enable/Disable Toggle -->
            <div class="bg-white rounded-lg shadow-sm border border-apple-gray-200 p-4 sm:p-6">
                <div class="flex items-center justify-between gap-4">
                    <div class="min-w-0">
                        <h3 class="text-base sm:text-lg font-semibold text-apple-gray-900">Enable Twilio WhatsApp</h3>
                        <p class="text-xs sm:text-sm text-apple-gray-500 mt-1">Turn on/off WhatsApp notifications via Twilio</p>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer flex-shrink-0">
                        <input type="checkbox" name="enabled" class="sr-only peer" {{ $settings['enabled'] ? 'checked' : '' }}>
                        <div class="w-11 h-6 bg-apple-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-red-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-apple-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-red-600"></div>
                    </label>
                </div>
            </div>

            <!-- API Credentials -->
            <div class="bg-white rounded-lg shadow-sm border border-apple-gray-200 overflow-hidden">
                <div class="px-4 sm:px-6 py-3 sm:py-4 border-b border-apple-gray-200 bg-apple-gray-50">
                    <h3 class="text-base sm:text-lg font-semibold text-apple-gray-900">Twilio API Credentials</h3>
                    <p class="text-xs sm:text-sm text-apple-gray-500 mt-1">Get these from your Twilio Console dashboard</p>
                </div>
                <div class="p-4 sm:p-6 space-y-4">
                    <!-- Account SID -->
                    <div>
                        <label for="account_sid" class="block text-sm font-medium text-apple-gray-700 mb-1.5">
                            Account SID <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="account_sid" id="account_sid"
                               value="{{ old('account_sid', $settings['account_sid']) }}"
                               placeholder="ACxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx"
                               class="w-full px-3 sm:px-4 py-2.5 sm:py-3 border border-apple-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 text-sm sm:text-base font-mono"
                               required>
                        <p class="mt-1 text-xs text-apple-gray-500">Starts with "AC" - found on your Twilio Console dashboard</p>
                    </div>

                    <!-- Auth Token -->
                    <div>
                        <label for="auth_token" class="block text-sm font-medium text-apple-gray-700 mb-1.5">
                            Auth Token <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <input type="password" name="auth_token" id="auth_token"
                                   value="{{ old('auth_token', $settings['auth_token']) }}"
                                   placeholder="Your Twilio Auth Token"
                                   class="w-full px-3 sm:px-4 py-2.5 sm:py-3 border border-apple-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 text-sm sm:text-base font-mono pr-12"
                                   required>
                            <button type="button" onclick="togglePassword('auth_token')"
                                    class="absolute right-3 top-1/2 transform -translate-y-1/2 text-apple-gray-400 hover:text-apple-gray-600">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                            </button>
                        </div>
                        <p class="mt-1 text-xs text-apple-gray-500">Keep this secret - found on your Twilio Console dashboard</p>
                    </div>

                    <!-- WhatsApp From Number -->
                    <div>
                        <label for="whatsapp_from" class="block text-sm font-medium text-apple-gray-700 mb-1.5">
                            WhatsApp Number <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="whatsapp_from" id="whatsapp_from"
                               value="{{ old('whatsapp_from', $settings['whatsapp_from']) }}"
                               placeholder="+14155238886"
                               class="w-full px-3 sm:px-4 py-2.5 sm:py-3 border border-apple-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 text-sm sm:text-base"
                               required>
                        <p class="mt-1 text-xs text-apple-gray-500">Your Twilio WhatsApp-enabled phone number (with country code)</p>
                    </div>

                    <!-- Sandbox Mode Toggle -->
                    <div class="flex items-center justify-between p-3 bg-apple-gray-50 rounded-lg">
                        <div>
                            <h4 class="text-sm font-medium text-apple-gray-900">Sandbox Mode</h4>
                            <p class="text-xs text-apple-gray-500">Enable if using Twilio Sandbox for testing</p>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" name="sandbox_mode" class="sr-only peer" {{ $settings['sandbox_mode'] ? 'checked' : '' }}>
                            <div class="w-11 h-6 bg-apple-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-red-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-apple-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-red-600"></div>
                        </label>
                    </div>
                </div>
            </div>

            <!-- Notification Settings -->
            <div class="bg-white rounded-lg shadow-sm border border-apple-gray-200 overflow-hidden">
                <div class="px-4 sm:px-6 py-3 sm:py-4 border-b border-apple-gray-200 bg-apple-gray-50">
                    <h3 class="text-base sm:text-lg font-semibold text-apple-gray-900">Notification Settings</h3>
                    <p class="text-xs sm:text-sm text-apple-gray-500 mt-1">Choose when to send WhatsApp notifications</p>
                </div>
                <div class="p-4 sm:p-6 space-y-4">
                    <div class="flex items-center justify-between p-3 bg-apple-gray-50 rounded-lg">
                        <div>
                            <h4 class="text-sm font-medium text-apple-gray-900">🎫 Ticket Created</h4>
                            <p class="text-xs text-apple-gray-500">Notify customer when their ticket is created</p>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" name="notify_on_create" class="sr-only peer" {{ $settings['notify_on_create'] ? 'checked' : '' }}>
                            <div class="w-11 h-6 bg-apple-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-red-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-apple-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-red-600"></div>
                        </label>
                    </div>

                    <div class="flex items-center justify-between p-3 bg-apple-gray-50 rounded-lg">
                        <div>
                            <h4 class="text-sm font-medium text-apple-gray-900">📊 Status Changed</h4>
                            <p class="text-xs text-apple-gray-500">Notify when ticket status is updated</p>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" name="notify_on_status_change" class="sr-only peer" {{ $settings['notify_on_status_change'] ? 'checked' : '' }}>
                            <div class="w-11 h-6 bg-apple-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-red-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-apple-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-red-600"></div>
                        </label>
                    </div>

                    <div class="flex items-center justify-between p-3 bg-apple-gray-50 rounded-lg">
                        <div>
                            <h4 class="text-sm font-medium text-apple-gray-900">✅ Ticket Resolved</h4>
                            <p class="text-xs text-apple-gray-500">Notify when ticket is marked as resolved</p>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" name="notify_on_resolved" class="sr-only peer" {{ $settings['notify_on_resolved'] ? 'checked' : '' }}>
                            <div class="w-11 h-6 bg-apple-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-red-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-apple-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-red-600"></div>
                        </label>
                    </div>
                </div>
            </div>

            <!-- Test Connection -->
            <div class="bg-white rounded-lg shadow-sm border border-apple-gray-200 overflow-hidden">
                <div class="px-4 sm:px-6 py-3 sm:py-4 border-b border-apple-gray-200 bg-apple-gray-50">
                    <h3 class="text-base sm:text-lg font-semibold text-apple-gray-900">Test Connection</h3>
                    <p class="text-xs sm:text-sm text-apple-gray-500 mt-1">Send a test message to verify your setup</p>
                </div>
                <div class="p-4 sm:p-6">
                    <div class="flex flex-col sm:flex-row gap-3">
                        <div class="flex-1">
                            <input type="text" id="test_phone"
                                   placeholder="Phone number (e.g., 263717497641)"
                                   class="w-full px-3 sm:px-4 py-2.5 sm:py-3 border border-apple-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 text-sm sm:text-base">
                        </div>
                        <button type="button" onclick="testConnection()"
                                class="flex-shrink-0 inline-flex items-center justify-center px-6 py-2.5 sm:py-3 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors font-medium text-sm sm:text-base disabled:opacity-50 disabled:cursor-not-allowed"
                                id="testBtn">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                            </svg>
                            Send Test
                        </button>
                    </div>
                    <div id="testResult" class="mt-3 hidden">
                        <div class="p-3 rounded-lg text-sm"></div>
                    </div>
                    @if($settings['sandbox_mode'])
                        <p class="mt-3 text-xs text-yellow-600">
                            <strong>Note:</strong> The recipient must first send a message to your Twilio WhatsApp number to opt-in.
                        </p>
                    @endif
                </div>
            </div>

            <!-- Save Button -->
            <div class="flex flex-col sm:flex-row gap-3 pt-2">
                <button type="submit"
                        class="w-full sm:w-auto inline-flex items-center justify-center px-6 py-3 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors font-medium text-sm sm:text-base">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    Save Settings
                </button>
                <a href="{{ route('dashboard') }}"
                   class="w-full sm:w-auto inline-flex items-center justify-center px-6 py-3 bg-apple-gray-100 text-apple-gray-700 rounded-lg hover:bg-apple-gray-200 transition-colors font-medium text-sm sm:text-base">
                    Cancel
                </a>
            </div>
        </form>
    </div>

    @push('scripts')
    <script>
        function togglePassword(fieldId) {
            const field = document.getElementById(fieldId);
            field.type = field.type === 'password' ? 'text' : 'password';
        }

        function testConnection() {
            const phone = document.getElementById('test_phone').value;
            const btn = document.getElementById('testBtn');
            const resultDiv = document.getElementById('testResult');
            const resultContent = resultDiv.querySelector('div');

            if (!phone) {
                resultDiv.classList.remove('hidden');
                resultContent.className = 'p-3 rounded-lg text-sm bg-yellow-50 text-yellow-800 border border-yellow-200';
                resultContent.textContent = 'Please enter a phone number to test.';
                return;
            }

            btn.disabled = true;
            btn.innerHTML = '<svg class="animate-spin w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> Sending...';

            fetch('{{ route("settings.twilio.test") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ test_phone: phone })
            })
            .then(response => response.json())
            .then(data => {
                resultDiv.classList.remove('hidden');
                if (data.success) {
                    resultContent.className = 'p-3 rounded-lg text-sm bg-green-50 text-green-800 border border-green-200';
                    resultContent.innerHTML = '<strong>✓ Success!</strong> ' + data.message;
                } else {
                    resultContent.className = 'p-3 rounded-lg text-sm bg-red-50 text-red-800 border border-red-200';
                    resultContent.innerHTML = '<strong>✗ Error:</strong> ' + data.message;
                }
            })
            .catch(error => {
                resultDiv.classList.remove('hidden');
                resultContent.className = 'p-3 rounded-lg text-sm bg-red-50 text-red-800 border border-red-200';
                resultContent.textContent = 'Network error. Please try again.';
            })
            .finally(() => {
                btn.disabled = false;
                btn.innerHTML = '<svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg> Send Test';
            });
        }
    </script>
    @endpush
</x-app-layout>
