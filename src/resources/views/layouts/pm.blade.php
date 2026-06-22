<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Laravel') }} - PM</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased">
    <div class="min-h-screen flex bg-gray-100">
        <!-- Sidebar -->
        <aside class="w-72 bg-white border-r border-gray-200 flex flex-col shrink-0">
            <div class="h-16 flex items-center px-6 border-b border-gray-200">
                <a href="{{ route('pm.dashboard') }}" class="text-lg font-bold text-gray-800">TaskFlow PM</a>
            </div>

            <nav class="flex-1 overflow-y-auto px-4 pt-4 pb-2 space-y-1">
                <a href="{{ route('pm.dashboard') }}"
                   class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium {{ request()->routeIs('pm.dashboard') ? 'bg-indigo-50 text-indigo-700' : 'text-gray-700 hover:bg-gray-100' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                    Dashboard
                </a>
                <a href="{{ route('pm.dashboard') }}"
                   class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium {{ request()->routeIs('pm.dashboard') ? 'bg-indigo-50 text-indigo-700' : 'text-gray-700 hover:bg-gray-100' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                    Daftar Tugas
                </a>
            </nav>

            <div class="border-t border-gray-200 p-4">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-full bg-indigo-600 flex items-center justify-center text-white text-sm font-bold">
                        {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="text-sm font-medium text-gray-900 truncate">{{ Auth::user()->name }}</div>
                        <div class="text-xs text-gray-500">
                            Project Manager
                            @php
                                $pmTeam = \App\Models\Team::where('owner_id', Auth::id())->first();
                            @endphp
                            @if($pmTeam)
                                <span class="ml-1 text-[10px] px-1 py-0.5 rounded-full bg-purple-50 text-purple-600">{{ $pmTeam->name }}</span>
                            @endif
                        </div>
                    </div>
                    <div class="flex items-center gap-1">
                        <a href="{{ route('profile.edit') }}" class="text-gray-400 hover:text-indigo-600" title="Profile">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                        </a>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="text-gray-400 hover:text-gray-600">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </aside>

        <div class="flex-1 flex flex-col min-w-0">
            <header class="bg-white border-b border-gray-200 h-16 flex items-center px-6">
                <div class="flex-1">
                    @isset($header)
                        {{ $header }}
                    @endisset
                </div>
            </header>

            <main class="flex-1 overflow-y-auto">
                @if (session()->has('message'))
                    <div class="max-w-7xl mx-auto pt-4 px-6">
                        <div class="p-4 text-sm text-green-800 rounded-lg bg-green-50">{{ session('message') }}</div>
                    </div>
                @endif
                @if (session()->has('error'))
                    <div class="max-w-7xl mx-auto pt-4 px-6">
                        <div class="p-4 text-sm text-red-800 rounded-lg bg-red-50">{{ session('error') }}</div>
                    </div>
                @endif
                {{ $slot }}
            </main>
        </div>
    </div>
</body>
</html>
