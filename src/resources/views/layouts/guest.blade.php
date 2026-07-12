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
        <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gradient-to-br from-slate-50 to-slate-100">
            <div class="w-full sm:max-w-md px-6">
                <a href="/" class="flex flex-col items-center mb-10 group">
                    <div class="w-20 h-20 mb-4 opacity-90 group-hover:opacity-100 transition-opacity">
                        <img src="{{ asset('images/TaskflowLogo.svg') }}" alt="TaskFlow" class="w-full h-full">
                    </div>
                    <div class="text-center">
                        <h1 class="text-3xl font-bold text-gray-900 tracking-tight">TaskFlow</h1>
                        <p class="text-sm text-gray-500 font-medium mt-1">Kolaborasi Tugas, Wujudkan Produktivitas</p>
                    </div>
                </a>

                <div class="bg-white shadow-lg shadow-slate-200/60 border border-slate-100 rounded-2xl px-10 py-10">
                    {{ $slot }}
                </div>

                <p class="text-center mt-10 text-xs text-gray-400">
                    &copy; {{ date('Y') }} TaskFlow. All rights reserved.
                </p>
            </div>
        </div>
    </body>
</html>
