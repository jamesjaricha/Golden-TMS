<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <style>
            body {
                -webkit-font-smoothing: antialiased;
                -moz-osx-font-smoothing: grayscale;
            }
        </style>
    </head>
    <body class="font-sans text-apple-gray-900 antialiased">
        <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gradient-to-br from-apple-gray-50 to-white">
            <div class="animate-fade-in">
                <a href="/">
                    <div class="flex items-center justify-center">
                        <div class="w-16 h-16 bg-gradient-to-br from-apple-blue to-blue-600 rounded-apple shadow-apple flex items-center justify-center">
                            <span class="text-white font-bold text-2xl">GT</span>
                        </div>
                    </div>
                </a>
            </div>

            <div class="w-full sm:max-w-md mt-8 px-8 py-8 bg-white/80 backdrop-blur-xl shadow-apple-lg overflow-hidden rounded-apple-lg animate-slide-up">
                {{ $slot }}
            </div>

            <div class="mt-6 text-center text-sm text-apple-gray-500 animate-fade-in">
                <p>Golden Ticket Management System</p>
            </div>
        </div>
    </body>
</html>
