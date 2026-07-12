<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased">
        <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gradient-to-br from-gray-50 via-gray-100 to-gray-200">
            <div class="w-full sm:max-w-md px-8">
                <a href="/" class="flex flex-col items-center mb-8 group">
                    <div class="w-16 h-16 mb-3 opacity-90 group-hover:opacity-100 transition-opacity">
                        <img src="{{ asset('images/TaskflowLogo.svg') }}" alt="Soulmatters" class="w-full h-full">
                    </div>
                    <div class="text-center">
                        <h1 class="text-2xl font-bold text-gray-900 tracking-tight">Soulmatters</h1>
                        <p class="text-sm text-gray-500 font-medium tracking-wide uppercase">Task Management</p>
                    </div>
                </a>

                <div class="bg-white/80 backdrop-blur-sm shadow-xl shadow-gray-200/50 border border-gray-100 rounded-2xl px-8 py-8">
                    {{ $slot }}
                </div>

                <p class="text-center mt-8 text-xs text-gray-400">
                    &copy; {{ date('Y') }} Soulmatters. All rights reserved.
                </p>
            </div>
        </div>
    </body>
</html>
