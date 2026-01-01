<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Page Not Found - Golden TMS</title>
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
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>

            <!-- Error Code -->
            <div class="mb-4">
                <h1 class="text-9xl font-extrabold text-white drop-shadow-lg">404</h1>
            </div>

            <!-- Error Message -->
            <h2 class="text-4xl font-bold text-white mb-4 drop-shadow-md">Page Not Found</h2>
            <p class="text-xl text-purple-100 mb-8 max-w-md mx-auto drop-shadow-sm">
                Oops! The page you're looking for doesn't exist or may have been moved. Let's get you back on track.
            </p>

            <!-- Action Buttons -->
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="{{ route('dashboard') }}"
                   class="inline-flex items-center justify-center px-8 py-3 bg-white text-purple-600 font-semibold rounded-lg hover:bg-gray-100 transition-all duration-300 shadow-lg hover:shadow-xl transform hover:scale-105">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-3m0 0l7-4 7 4M5 9v10a1 1 0 001 1h12a1 1 0 001-1V9m-9 11l4-4m0 0l4 4m-4-4V6"/>
                    </svg>
                    Back to Dashboard
                </a>
                <a href="{{ route('complaints.index') }}"
                   class="inline-flex items-center justify-center px-8 py-3 bg-purple-400 text-white font-semibold rounded-lg hover:bg-purple-500 transition-all duration-300 shadow-lg hover:shadow-xl transform hover:scale-105">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    View Tickets
                </a>
            </div>

            <!-- Additional Help -->
            <div class="mt-12 pt-8 border-t border-purple-300">
                <p class="text-purple-100 text-sm mb-4">Need additional help?</p>
                <a href="{{ route('dashboard') }}" class="inline-flex items-center text-white hover:text-purple-100 transition-colors">
                    <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18.855 7.375l-5.208 5.208a2 2 0 01-2.828 0l-.707-.707a2 2 0 010-2.828L9.318 3.75H7a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2V7.375z" clip-rule="evenodd"/>
                    </svg>
                    Contact Support
                </a>
            </div>
        </div>
    </div>
</body>
</html>
