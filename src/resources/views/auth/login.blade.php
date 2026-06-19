@extends('layouts.app')

@section('title', 'Login - TaskFlow')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-indigo-950 via-slate-900 to-violet-950 flex relative overflow-hidden">
    <div class="absolute inset-0">
        <div class="absolute top-0 -left-40 w-96 h-96 bg-violet-500/10 rounded-full blur-[128px]"></div>
        <div class="absolute bottom-0 -right-40 w-96 h-96 bg-pink-500/10 rounded-full blur-[128px]"></div>
        <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[600px] h-[600px] bg-violet-500/5 rounded-full blur-[100px]"></div>
    </div>

    <div class="hidden lg:flex lg:w-1/2 relative items-center justify-center">
        <div class="relative text-center">
            <div class="w-24 h-24 mx-auto mb-6 bg-gradient-to-br from-violet-500 to-pink-500 rounded-2xl flex items-center justify-center shadow-2xl shadow-violet-500/30 ring-1 ring-white/10">
                <span class="text-white font-bold text-4xl">T</span>
            </div>
            <h1 class="text-4xl font-bold text-white mb-3">TaskFlow</h1>
            <p class="text-zinc-400 text-lg">Manage your tasks, effortlessly</p>
        </div>
    </div>

    <div class="flex-1 flex items-center justify-center p-8 relative z-10">
        <div class="w-full max-w-sm">
            <div class="lg:hidden text-center mb-8">
                <div class="w-14 h-14 mx-auto mb-4 bg-gradient-to-br from-violet-500 to-pink-500 rounded-xl flex items-center justify-center shadow-lg shadow-violet-500/20">
                    <span class="text-white font-bold text-xl">T</span>
                </div>
                <h1 class="text-2xl font-bold text-white">Welcome back</h1>
                <p class="text-zinc-400 text-sm mt-1">Sign in to your account</p>
            </div>

            <div class="bg-white/5 backdrop-blur-xl border border-white/10 rounded-2xl p-8 shadow-2xl shadow-black/20">
                <form method="POST" action="{{ route('login') }}" class="space-y-5">
                    @csrf
                    <div>
                        <label class="block text-sm font-medium text-zinc-300 mb-1.5">Email</label>
                        <input type="email" name="email" value="{{ old('email') }}" placeholder="you@example.com" required
                            class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-2.5 text-sm text-white placeholder-zinc-500 focus:border-violet-500/50 focus:ring-1 focus:ring-violet-500/20 transition-all outline-none">
                        @error('email') <p class="text-xs text-rose-400 mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-zinc-300 mb-1.5">Password</label>
                        <input type="password" name="password" placeholder="••••••••" required
                            class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-2.5 text-sm text-white placeholder-zinc-500 focus:border-violet-500/50 focus:ring-1 focus:ring-violet-500/20 transition-all outline-none">
                        @error('password') <p class="text-xs text-rose-400 mt-1">{{ $message }}</p> @enderror
                    </div>

                    @if(session('error'))
                        <div class="text-sm text-rose-300 bg-rose-500/10 border border-rose-500/20 rounded-lg px-4 py-2">{{ session('error') }}</div>
                    @endif

                    <div class="flex items-center justify-between">
                        <label class="flex items-center gap-2 text-sm text-zinc-400 cursor-pointer">
                            <input type="checkbox" name="remember" class="rounded border-white/10 bg-white/5 text-violet-500 focus:ring-violet-500/20">
                            Remember me
                        </label>
                        <a href="{{ route('register') }}" class="text-sm text-violet-400 hover:text-violet-300">Create account</a>
                    </div>

                    <button type="submit" class="w-full py-2.5 bg-gradient-to-r from-violet-600 to-pink-600 hover:from-violet-500 hover:to-pink-500 text-white rounded-xl font-medium transition-all shadow-lg shadow-violet-500/20">
                        Sign in
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
