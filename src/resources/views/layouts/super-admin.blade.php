<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Laravel') }}</title>
    <link rel="icon" href="{{ asset('images/TaskflowLogo.svg') }}" type="image/svg+xml">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="font-sans antialiased">
    @php $routePrefix = 'super-admin'; @endphp
    <div class="flex min-h-screen">
        <aside class="taskflow-sidebar">
            <div class="h-14 flex items-center px-5 border-b border-border shrink-0">
                <a href="{{ route("{$routePrefix}.dashboard") }}" class="flex items-center gap-2.5">
                    <img src="{{ asset('images/TaskflowLogo.svg') }}" alt="TaskFlow" class="h-7 w-7">
                    <span class="font-bold text-base text-text-primary">TaskFlow</span>
                </a>
            </div>

            <nav class="flex-1 overflow-y-auto px-3 py-3 space-y-0.5">
                <a href="{{ route("{$routePrefix}.dashboard") }}"
                   class="sidebar-link {{ request()->routeIs("{$routePrefix}.dashboard") ? 'active' : '' }}">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6A2.25 2.25 0 0 1 6 3.75h2.25A2.25 2.25 0 0 1 10.5 6v2.25a2.25 2.25 0 0 1-2.25 2.25H6a2.25 2.25 0 0 1-2.25-2.25V6ZM3.75 15.75A2.25 2.25 0 0 1 6 13.5h2.25a2.25 2.25 0 0 1 2.25 2.25V18a2.25 2.25 0 0 1-2.25 2.25H6A2.25 2.25 0 0 1 3.75 18v-2.25ZM13.5 6a2.25 2.25 0 0 1 2.25-2.25H18A2.25 2.25 0 0 1 20.25 6v2.25A2.25 2.25 0 0 1 18 10.5h-2.25a2.25 2.25 0 0 1-2.25-2.25V6ZM13.5 15.75a2.25 2.25 0 0 1 2.25-2.25H18a2.25 2.25 0 0 1 2.25 2.25V18A2.25 2.25 0 0 1 18 20.25h-2.25A2.25 2.25 0 0 1 13.5 18v-2.25Z"/></svg>
                    Dashboard
                </a>
                <a href="{{ route('super-admin.workspaces') }}"
                   class="sidebar-link {{ request()->routeIs('super-admin.workspaces') ? 'active' : '' }}">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 21h19.5m-18-18v18m10.5-18v18m6-13.5V21M6.75 6.75h.75m-.75 3h.75m-.75 3h.75m3-6h.75m-.75 3h.75m-.75 3h.75M6.75 21v-3.375c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21M3 3h12m-.75 4.5H21m-3.75 3.75h.008v.008h-.008v-.008Z"/></svg>
                    Workspace
                </a>
                <a href="{{ route('super-admin.users') }}"
                   class="sidebar-link {{ request()->routeIs('super-admin.users') ? 'active' : '' }}">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0Zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z"/></svg>
                    Pengguna
                </a>
                <a href="{{ route('super-admin.tasks') }}"
                   class="sidebar-link {{ request()->routeIs('super-admin.tasks') ? 'active' : '' }}">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5.586a1 1 0 0 1 .707.293l5.414 5.414a1 1 0 0 1 .293.707V19a2 2 0 0 1-2 2Z"/></svg>
                    Semua Tugas
                </a>
                <a href="{{ route('super-admin.task-approval') }}"
                   class="sidebar-link {{ request()->routeIs('super-admin.task-approval') ? 'active' : '' }}">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/></svg>
                    Approval Tugas
                </a>

                @if(Auth::user()->role === 'super_admin')
                <div class="sidebar-section">Laporan</div>
                <a href="{{ route('super-admin.performa-pm') }}"
                   class="sidebar-link {{ request()->routeIs('super-admin.performa-pm') ? 'active' : '' }}">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 0 1 3 19.875v-6.75ZM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 0 1-1.125-1.125V8.625ZM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 0 1-1.125-1.125V4.125Z"/></svg>
                    Performa PM
                </a>
                <a href="{{ route('super-admin.member-performance') }}"
                   class="sidebar-link {{ request()->routeIs('super-admin.member-performance') ? 'active' : '' }}">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0Zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z"/></svg>
                    Performa Member
                </a>
                <a href="{{ route('super-admin.late-tasks') }}"
                   class="sidebar-link {{ request()->routeIs('super-admin.late-tasks') ? 'active' : '' }}">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/></svg>
                    Tugas Terlambat
                </a>

                <div class="sidebar-section">Sistem</div>
                <a href="{{ route('super-admin.audit-logs') }}"
                   class="sidebar-link {{ request()->routeIs('super-admin.audit-logs') ? 'active' : '' }}">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z"/></svg>
                    Audit Log
                </a>
                <a href="{{ route('super-admin.arbitration-recap') }}"
                   class="sidebar-link {{ request()->routeIs('super-admin.arbitration-recap') ? 'active' : '' }}">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5.586a1 1 0 0 1 .707.293l5.414 5.414a1 1 0 0 1 .293.707V19a2 2 0 0 1-2 2Z"/></svg>
                    Laporan Arbitrase
                </a>
                @endif
            </nav>

            <div class="border-t border-border p-3 shrink-0">
                <div class="flex items-center gap-3 px-2 py-1.5">
                    <div class="w-8 h-8 rounded-lg bg-primary-600 flex items-center justify-center text-white text-sm font-semibold shrink-0">
                        {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="text-sm font-medium text-text-primary truncate">{{ Auth::user()->name }}</div>
                        <div class="text-xs text-text-secondary truncate capitalize">{{ str_replace('_', ' ', Auth::user()->role) }}</div>
                    </div>
                    <div class="flex items-center gap-0.5">
                        <a href="{{ route('profile.edit') }}" class="btn-ghost p-1.5 rounded-lg" title="Profile">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z"/></svg>
                        </a>
                        <button type="button" onclick="document.getElementById('logout-modal').classList.remove('hidden')" class="btn-ghost p-1.5 rounded-lg" title="Logout">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0 0 13.5 3h-6a2.25 2.25 0 0 0-2.25 2.25v13.5A2.25 2.25 0 0 0 7.5 21h6a2.25 2.25 0 0 0 2.25-2.25V15m3 0 3-3m0 0-3-3m3 3H9"/></svg>
                        </button>
                    </div>
                </div>
            </div>
        </aside>

        <div id="logout-modal" class="modal-overlay hidden" onclick="if(event.target===this) this.classList.add('hidden')">
            <div class="modal-panel max-w-sm">
                <div class="modal-body text-center py-8">
                    <div class="mx-auto mb-4 w-12 h-12 rounded-full bg-gray-100 flex items-center justify-center">
                        <svg class="w-6 h-6 text-text-secondary" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0 0 13.5 3h-6a2.25 2.25 0 0 0-2.25 2.25v13.5A2.25 2.25 0 0 0 7.5 21h6a2.25 2.25 0 0 0 2.25-2.25V15m3 0 3-3m0 0-3-3m3 3H9"/></svg>
                    </div>
                    <h3 class="text-base font-semibold text-text-primary mb-1">Konfirmasi Logout</h3>
                    <p class="text-sm text-text-secondary mb-6">Apakah Anda yakin ingin keluar? Sesi Anda akan berakhir.</p>
                    <div class="flex gap-3">
                        <button type="button" onclick="document.getElementById('logout-modal').classList.add('hidden')" class="btn-secondary flex-1">Batal</button>
                        <form method="POST" action="{{ route('logout') }}" class="flex-1">
                            @csrf
                            <button type="submit" class="btn-primary w-full">Ya, Logout</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div class="flex-1 flex flex-col min-w-0 ml-sidebar">
            <header class="taskflow-topbar flex items-center gap-4">
                <div class="flex items-center gap-2 flex-1">
                    <div class="relative max-w-md w-full">
                        <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-text-secondary/60" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z"/></svg>
                        <input type="text" placeholder="Cari tugas..." class="input-field pl-9 max-w-xs">
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    @livewire('notification-bell')
                </div>
            </header>

            <main class="taskflow-content">
                <div class="max-w-7xl mx-auto px-6 py-6">
                    @if (session()->has('message'))
                        <div class="mb-4 p-3 text-sm text-emerald-800 rounded-lg bg-emerald-50 border border-emerald-100">{{ session('message') }}</div>
                    @endif
                    @if (session()->has('error'))
                        <div class="mb-4 p-3 text-sm text-red-800 rounded-lg bg-red-50 border border-red-100">{{ session('error') }}</div>
                    @endif
                    {{ $slot }}
                </div>
            </main>
        </div>
    </div>
    @livewireScripts
</body>
</html>
