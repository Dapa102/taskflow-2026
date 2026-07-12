<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased">
        <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 relative" style="background-color: #F8FAFC; background-image: linear-gradient(rgba(37, 99, 235, 0.03) 1px, transparent 1px), linear-gradient(90deg, rgba(37, 99, 235, 0.03) 1px, transparent 1px); background-size: 48px 48px;">
            <div class="absolute inset-0 overflow-hidden pointer-events-none">
                <div class="absolute w-[500px] h-[500px] rounded-full bg-primary-500/5 blur-[100px] -top-24 -right-24"></div>
                <div class="absolute w-[400px] h-[400px] rounded-full bg-primary-600/4 blur-[100px] -bottom-20 -left-20"></div>
            </div>

            <div class="w-full sm:max-w-md px-6 relative z-10">
                <a href="/" class="flex flex-col items-center mb-10 group">
                    <div class="w-20 h-20 mb-4 opacity-90 group-hover:opacity-100 transition-opacity">
                        <img src="{{ asset('images/TaskflowLogo.svg') }}" alt="TaskFlow" class="w-full h-full">
                    </div>
                    <div class="text-center">
                        <h1 class="text-3xl font-bold tracking-tight" style="color: #0F172A;">TaskFlow</h1>
                        <p class="text-sm font-medium mt-1.5 tracking-wide" style="color: #64748B;">Platform Kolaborasi Tugas Antar Tim</p>
                    </div>
                </a>

                <div class="bg-white/90 backdrop-blur-sm border border-[#E5E7EB] rounded-lg px-10 py-10" style="box-shadow: 0 1px 3px 0 rgb(0 0 0 / 0.04), 0 1px 2px -1px rgb(0 0 0 / 0.06);">
                    {{ $slot }}
                </div>

                <p class="text-center mt-10 text-xs" style="color: #64748B;">
                    &copy; {{ date('Y') }} TaskFlow. All rights reserved.
                </p>
            </div>
        </div>

        @stack('scripts')
    </body>
</html>
