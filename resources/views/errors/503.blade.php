<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Service Unavailable - Golden TMS</title>
    <script defer src="https://cdn.tailwindcss.com"></script>
    <style>
        .gradient-bg {
            background: linear-gradient(135deg, #a8edea 0%, #fed6e3 100%);
        }
        .floating-animation {
            animation: float 3s ease-in-out infinite;
        }
        .pulse-dot {
            animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
        }
        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-20px); }
        }
        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.5; }
        }
    </style>
</head>
<body class="bg-gray-50">
    <div class="min-h-screen gradient-bg flex items-center justify-center px-4 sm:px-6 lg:px-8">
        <div class="text-center">
            <!-- Error Icon -->
            <div class="mb-8 floating-animation">
                <svg class="mx-auto h-24 w-24 text-white opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                </svg>
            </div>

            <!-- Error Code -->
            <div class="mb-4">
                <h1 class="text-9xl font-extrabold text-white drop-shadow-lg">503</h1>
            </div>

            <!-- Error Message -->
            <h2 class="text-4xl font-bold text-white mb-4 drop-shadow-md">
                Service Unavailable
            </h2>
            <p class="text-xl text-purple-100 mb-2 max-w-md mx-auto drop-shadow-sm">
                The system is currently under maintenance or experiencing high load.
            </p>
            <p class="text-lg text-purple-100 mb-8 max-w-md mx-auto drop-shadow-sm flex items-center justify-center">
                <span class="pulse-dot inline-block w-2 h-2 bg-white rounded-full mr-2"></span>
                Please check back shortly
            </p>

            <!-- Action Buttons -->
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="javascript:location.reload()"
                   class="inline-flex items-center justify-center px-8 py-3 bg-white text-purple-600 font-semibold rounded-lg hover:bg-gray-100 transition-all duration-300 shadow-lg hover:shadow-xl transform hover:scale-105">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                    </svg>
                    Try Again
                </a>
                <a href="{{ route('dashboard') }}"
                   class="inline-flex items-center justify-center px-8 py-3 bg-purple-400 text-white font-semibold rounded-lg hover:bg-purple-500 transition-all duration-300 shadow-lg hover:shadow-xl transform hover:scale-105">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-3m0 0l7-4 7 4M5 9v10a1 1 0 001 1h12a1 1 0 001-1V9m-9 11l4-4m0 0l4 4m-4-4V6"/>
                    </svg>
                    Home
                </a>
            </div>

            <!-- Status Info -->
            <div class="mt-12 pt-8 border-t border-purple-300">
                <p class="text-purple-100 text-sm mb-2">Status: Maintenance in Progress</p>
                <p class="text-purple-100 text-xs">We appreciate your patience. We'll be back online shortly.</p>
                <p class="text-purple-100 text-xs mt-2">Last updated: {{ now()->format('M d, Y H:i') }} UTC</p>
            </div>
        </div>
    </div>
</body>
</html>
