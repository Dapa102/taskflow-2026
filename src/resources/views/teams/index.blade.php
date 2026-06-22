@extends('layouts.app')

@section('title', 'Tim - KerjaanKu')

@section('content')
<div x-data="teamsPage" class="max-w-4xl mx-auto px-4 py-6 pb-24">
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-bold text-white">Teams</h1>
        <div class="flex items-center gap-2">
            <button @click="showJoin = true" class="px-3 py-1.5 text-sm text-violet-400 hover:text-violet-300 border border-violet-500/30 rounded-lg">Join Team</button>
            <button @click="showCreate = true" class="px-4 py-1.5 text-sm bg-violet-600 hover:bg-violet-500 text-white rounded-lg font-medium">+ New Team</button>
        </div>
    </div>

    <div x-show="showCreate" class="fixed inset-0 bg-black/60 z-50 flex items-center justify-center" @click="showCreate = false">
        <div class="bg-zinc-900 border border-zinc-800 rounded-xl p-6 w-full max-w-md mx-4" @click.stop>
            <h2 class="text-lg font-semibold text-white mb-4">Create Team</h2>
            <input type="text" x-model="form.name" @keydown.enter="create" placeholder="Team name..." class="w-full bg-zinc-800 border border-zinc-700 rounded-lg px-4 py-2.5 text-sm text-white placeholder-zinc-600 focus:border-violet-500/50 focus:ring-0 outline-none mb-4">
            <div class="flex justify-end gap-2">
                <button @click="showCreate = false" class="px-4 py-2 text-sm text-zinc-400 hover:text-zinc-300">Cancel</button>
                <button @click="create" :disabled="creating || !form.name.trim()" class="px-4 py-2 text-sm bg-violet-600 hover:bg-violet-500 disabled:bg-zinc-800 disabled:text-zinc-700 text-white rounded-lg font-medium">Create</button>
            </div>
        </div>
    </div>

    <div x-show="showJoin" class="fixed inset-0 bg-black/60 z-50 flex items-center justify-center" @click="showJoin = false">
        <div class="bg-zinc-900 border border-zinc-800 rounded-xl p-6 w-full max-w-md mx-4" @click.stop>
            <h2 class="text-lg font-semibold text-white mb-4">Join Team</h2>
            <input type="text" x-model="inviteCode" @keydown.enter="join" placeholder="Enter invite code..." class="w-full bg-zinc-800 border border-zinc-700 rounded-lg px-4 py-2.5 text-sm text-white placeholder-zinc-600 focus:border-violet-500/50 focus:ring-0 outline-none uppercase mb-4">
            <div class="flex justify-end gap-2">
                <button @click="showJoin = false" class="px-4 py-2 text-sm text-zinc-400 hover:text-zinc-300">Cancel</button>
                <button @click="join" :disabled="joining || !inviteCode.trim()" class="px-4 py-2 text-sm bg-violet-600 hover:bg-violet-500 disabled:bg-zinc-800 disabled:text-zinc-700 text-white rounded-lg font-medium">Join</button>
            </div>
        </div>
    </div>

    <template x-if="loading">
        <div class="space-y-2">
            <template x-for="n in 2" :key="n">
                <div class="bg-zinc-900 rounded-xl p-5 animate-pulse">
                    <div class="h-5 bg-zinc-800 rounded w-1/3 mb-3"></div>
                    <div class="h-3 bg-zinc-800/50 rounded w-1/4"></div>
                </div>
            </template>
        </div>
    </template>

    <template x-if="!loading">
        <div class="grid gap-3">
            <template x-for="team in teams" :key="team.id">
                <div @click="window.location.href = '/teams/' + team.id" class="bg-zinc-900/50 border border-zinc-800 hover:border-zinc-700 rounded-xl p-5 cursor-pointer transition-all group">
                    <div class="flex items-start justify-between">
                        <div>
                            <h3 class="text-base font-semibold text-zinc-200 group-hover:text-white" x-text="team.name"></h3>
                            <p class="text-sm text-zinc-600 mt-1"><span x-text="team.members_count ?? 0"></span> members</p>
                            <p x-show="team.invite_code" class="text-xs font-mono text-zinc-700 mt-1">Code: <span x-text="team.invite_code"></span></p>
                        </div>
                        <button @click.stop="remove(team)" class="p-1 text-zinc-700 hover:text-rose-400 opacity-0 group-hover:opacity-100">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>
                </div>
            </template>
            <template x-if="!teams.length">
                <div class="text-center py-20 text-zinc-700 text-sm">No teams yet. Create one or join with an invite code.</div>
            </template>
        </div>
    </template>
</div>
@endsection
