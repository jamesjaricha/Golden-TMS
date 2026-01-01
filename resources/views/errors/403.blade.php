<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Access Forbidden - Golden TMS</title>
    <script defer src="https://cdn.tailwindcss.com"></script>
    <style>
        .gradient-bg {
            background: linear-gradient(135deg, #fa709a 0%, #fee140 100%);
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
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                </svg>
            </div>

            <!-- Error Code -->
            <div class="mb-4">
                <h1 class="text-9xl font-extrabold text-white drop-shadow-lg">403</h1>
            </div>

            <!-- Error Message -->
            <h2 class="text-4xl font-bold text-white mb-4 drop-shadow-md">
                Access Forbidden
            </h2>
            <p class="text-xl text-orange-100 mb-8 max-w-md mx-auto drop-shadow-sm">
                You don't have permission to access this resource. If you believe this is an error, please contact your administrator.
            </p>

            <!-- Action Buttons -->
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="javascript:history.back()"
                   class="inline-flex items-center justify-center px-8 py-3 bg-white text-amber-600 font-semibold rounded-lg hover:bg-gray-100 transition-all duration-300 shadow-lg hover:shadow-xl transform hover:scale-105">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Go Back
                </a>
                <a href="{{ route('dashboard') }}"
                   class="inline-flex items-center justify-center px-8 py-3 bg-amber-400 text-white font-semibold rounded-lg hover:bg-amber-500 transition-all duration-300 shadow-lg hover:shadow-xl transform hover:scale-105">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-3m0 0l7-4 7 4M5 9v10a1 1 0 001 1h12a1 1 0 001-1V9m-9 11l4-4m0 0l4 4m-4-4V6"/>
                    </svg>
                    Home
                </a>
            </div>

            <!-- Additional Help -->
            <div class="mt-12 pt-8 border-t border-orange-300">
                <p class="text-orange-100 text-sm mb-4">If you need assistance, contact your administrator</p>
                <p class="text-orange-100 text-xs">Error Code: 403 FORBIDDEN</p>
            </div>
        </div>
    </div>
</body>
</html>
