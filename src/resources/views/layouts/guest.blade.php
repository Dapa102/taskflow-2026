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
        <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 relative">
            <div class="absolute inset-0 bg-gradient-to-br from-blue-50 via-indigo-50/30 to-slate-100 -z-10"></div>
            <div class="absolute inset-0 opacity-[0.04] -z-10" style="background-image: radial-gradient(circle, #4f46e5 1px, transparent 1px); background-size: 24px 24px;"></div>

            <div class="w-full sm:max-w-md px-6">
                <a href="/" class="flex flex-col items-center mb-10 group">
                    <div class="w-20 h-20 mb-4 opacity-90 group-hover:opacity-100 transition-opacity">
                        <img src="{{ asset('images/TaskflowLogo.svg') }}" alt="TaskFlow" class="w-full h-full">
                    </div>
                    <div class="text-center">
                        <h1 class="text-3xl font-bold text-gray-900 tracking-tight">TaskFlow</h1>
                        <p class="text-sm text-gray-400 font-medium mt-1.5 tracking-wide">Platform Kolaborasi Tugas Antar Tim</p>
                    </div>
                </a>

                <div class="bg-white/90 backdrop-blur-sm shadow-xl shadow-indigo-200/30 border border-indigo-100/50 rounded-2xl px-10 py-10">
                    {{ $slot }}
                </div>

                <p class="text-center mt-10 text-xs text-gray-400">
                    &copy; {{ date('Y') }} TaskFlow. All rights reserved.
                </p>
            </div>
        </div>

        @stack('scripts')
    </body>
</html>
