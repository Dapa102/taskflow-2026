@extends('layouts.app')

@section('title', 'Profile - TaskFlow')

@section('content')
<div x-data="profilePage" class="max-w-lg mx-auto px-4 py-6">
    <a href="/dashboard" class="inline-flex items-center gap-1 text-sm text-zinc-500 hover:text-zinc-300 mb-6 transition-colors">
        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5"/></svg>
        Back
    </a>

    <div class="flex flex-col items-center mb-8">
        <div class="w-20 h-20 rounded-full bg-gradient-to-br from-violet-500 to-pink-500 flex items-center justify-center text-2xl font-bold text-white shadow-xl shadow-violet-500/20 mb-4">
            {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
        </div>
        <h1 class="text-xl font-semibold text-zinc-100">Profile</h1>
    </div>

    <div x-show="message" class="mb-4 px-4 py-2.5 bg-emerald-500/10 border border-emerald-500/30 rounded-lg text-sm text-emerald-400" x-text="message"></div>
    <div x-show="error" class="mb-4 px-4 py-2.5 bg-rose-500/10 border border-rose-500/30 rounded-lg text-sm text-rose-400" x-text="error"></div>

    <div class="bg-zinc-900/50 border border-zinc-800 rounded-xl p-6 space-y-4">
        <div>
            <label class="block text-xs font-medium text-zinc-500 mb-1">Name</label>
            <input type="text" x-model="form.name" class="w-full bg-zinc-800/50 border border-zinc-700/50 rounded-xl px-4 py-2.5 text-sm text-zinc-200 placeholder-zinc-600 focus:border-violet-500/50 focus:ring-1 focus:ring-violet-500/20 outline-none">
        </div>
        <div>
            <label class="block text-xs font-medium text-zinc-500 mb-1">Email</label>
            <input type="email" x-model="form.email" class="w-full bg-zinc-800/50 border border-zinc-700/50 rounded-xl px-4 py-2.5 text-sm text-zinc-200 placeholder-zinc-600 focus:border-violet-500/50 focus:ring-1 focus:ring-violet-500/20 outline-none">
        </div>
        <div>
            <label class="block text-xs font-medium text-zinc-500 mb-1">Phone <span class="text-zinc-700">(for WhatsApp notifications)</span></label>
            <input type="tel" x-model="form.phone" placeholder="e.g. 6281234567890" class="w-full bg-zinc-800/50 border border-zinc-700/50 rounded-xl px-4 py-2.5 text-sm text-zinc-200 placeholder-zinc-600 focus:border-violet-500/50 focus:ring-1 focus:ring-violet-500/20 outline-none">
        </div>
        <button @click="save" :disabled="saving" class="w-full py-2.5 bg-gradient-to-r from-violet-500 to-pink-500 hover:from-violet-400 hover:to-pink-400 disabled:opacity-50 text-white text-sm font-medium rounded-xl transition-all">
            <span x-text="saving ? 'Saving...' : 'Save Changes'"></span>
        </button>
    </div>
</div>
@endsection
