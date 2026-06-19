<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#8B5CF6">
    <link rel="manifest" href="/manifest.json">
    <link rel="icon" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 32 32'><rect width='32' height='32' rx='8' fill='%238B5CF6'/><text x='16' y='22' text-anchor='middle' fill='white' font-size='18' font-weight='bold'>T</text></svg>">
    <title>@yield('title', 'TaskFlow')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script>
        window.currentUser = @json(auth()->user());
        window.currentUserId = {{ auth()->id() ?? 'null' }};
    </script>
</head>
<body class="bg-slate-950 text-zinc-100 antialiased min-h-screen">
    <div class="fixed inset-0 pointer-events-none z-0">
        <div class="absolute top-0 -left-40 w-96 h-96 bg-violet-500/5 rounded-full blur-[128px]"></div>
        <div class="absolute bottom-0 -right-40 w-96 h-96 bg-pink-500/5 rounded-full blur-[128px]"></div>
    </div>

    @auth
        <x-navbar />
    @endauth

    <main class="relative z-10">
        @yield('content')
    </main>

    @auth
        <x-mobile-nav />
    @endauth
</body>
</html>
