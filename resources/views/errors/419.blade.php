<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Session Expired - Golden TMS</title>
    <script defer src="https://cdn.tailwindcss.com"></script>
    <style>
        .gradient-bg {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        .floating-animation {
            animation: float 3s ease-in-out infinite;
        }
        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-20px); }
        }
    </style>
</head>
<body class="bg-gray-50">
    <div class="min-h-screen gradient-bg flex items-center justify-center px-4 sm:px-6 lg:px-8">
        <div class="text-center">
            <!-- Error Icon -->
            <div class="mb-8 floating-animation">
                <svg class="mx-auto h-24 w-24 text-white opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>

            <!-- Error Code -->
            <div class="mb-4">
                <h1 class="text-9xl font-extrabold text-white drop-shadow-lg">419</h1>
            </div>

            <!-- Error Message -->
            <h2 class="text-4xl font-bold text-white mb-4 drop-shadow-md">
                Session Expired
            </h2>
            <p class="text-xl text-indigo-100 mb-8 max-w-md mx-auto drop-shadow-sm">
                Your session has expired for security reasons. Please refresh the page and try again.
            </p>

            <!-- Action Buttons -->
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="javascript:location.reload()"
                   class="inline-flex items-center justify-center px-8 py-3 bg-white text-purple-600 font-semibold rounded-lg hover:bg-gray-100 transition-all duration-300 shadow-lg hover:shadow-xl transform hover:scale-105">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                    </svg>
                    Refresh Page
                </a>
                <a href="{{ route('login') }}"
                   class="inline-flex items-center justify-center px-8 py-3 bg-indigo-400 text-white font-semibold rounded-lg hover:bg-indigo-500 transition-all duration-300 shadow-lg hover:shadow-xl transform hover:scale-105">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v2a2 2 0 01-2 2H7a2 2 0 01-2-2v-2m14-4V7a2 2 0 00-2-2H9a2 2 0 00-2 2v4"/>
                    </svg>
                    Login Again
                </a>
            </div>

            <!-- Additional Help -->
            <div class="mt-12 pt-8 border-t border-indigo-300">
                <p class="text-indigo-100 text-sm mb-4">Why did this happen?</p>
                <ul class="text-indigo-100 text-xs space-y-1 max-w-md mx-auto">
                    <li>• Your session exceeded the maximum allowed duration</li>
                    <li>• You were inactive for too long</li>
                    <li>• Your session was invalidated for security reasons</li>
                </ul>
            </div>
        </div>
    </div>
</body>
</html>
