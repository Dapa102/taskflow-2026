@extends('layouts.app')

@section('title', 'Detail Tim - KerjaanKu')

@section('content')
<div x-data="teamDetail" class="max-w-4xl mx-auto px-4 py-6 pb-24">
    <template x-if="loading">
        <div class="flex items-center justify-center py-20"><div class="text-zinc-600">Loading...</div></div>
    </template>

    <template x-if="!loading && team">
        <div>
            <a href="/teams" class="inline-flex items-center gap-1 text-sm text-zinc-500 hover:text-zinc-300 mb-4 transition-colors">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5"/></svg>
                Back to Teams
            </a>

            <div class="bg-zinc-900/50 border border-zinc-800 rounded-xl p-6 mb-6">
                <div class="flex items-start justify-between mb-4">
                    <div>
                        <h1 class="text-2xl font-bold text-white" x-text="team.name"></h1>
                        <p class="text-sm text-zinc-500 mt-1">
                            <span x-text="members.length"></span> member(s) &middot; <span x-text="tasks.length"></span> task(s)
                        </p>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <div class="flex-1 bg-zinc-800/50 border border-zinc-700 rounded-lg px-3 py-2 text-sm font-mono text-zinc-400 select-all" x-text="team.invite_code"></div>
                    <button @click="copyInvite" class="px-3 py-2 text-sm border border-zinc-700 rounded-lg hover:bg-zinc-800 text-zinc-400 hover:text-zinc-200">
                        <span x-text="copied ? 'Copied!' : 'Copy Code'"></span>
                    </button>
                </div>
            </div>

            <div class="bg-zinc-900/50 border border-zinc-800 rounded-xl p-6 mb-6">
                <h2 class="text-sm font-medium text-zinc-400 mb-4">Members</h2>

                <div class="flex items-center gap-2 mb-4">
                    <input type="email" x-model="addEmail" @keydown.enter="addMember" placeholder="Add member by email..." class="flex-1 bg-zinc-800 border border-zinc-700 rounded-lg px-3 py-2 text-sm text-zinc-300 placeholder-zinc-600 focus:border-violet-500/50 focus:ring-0 outline-none">
                    <button @click="addMember" :disabled="adding || !addEmail.trim()" class="px-3 py-2 text-sm bg-violet-600 hover:bg-violet-500 disabled:bg-zinc-800 disabled:text-zinc-700 text-white rounded-lg font-medium">Add</button>
                </div>

                <input type="text" x-model="memberSearch" x-show="members.length > 5" placeholder="Search members..." class="w-full bg-zinc-800/50 border border-zinc-700 rounded-lg px-3 py-1.5 text-sm text-zinc-300 placeholder-zinc-600 focus:border-violet-500/50 focus:ring-0 outline-none mb-3">

                <div class="space-y-2">
                    <template x-for="m in filteredMembers" :key="m.id">
                        <div class="flex items-center gap-3 py-2">
                            <div class="w-8 h-8 rounded-full bg-gradient-to-br from-violet-500 to-pink-500 flex items-center justify-center text-sm font-bold text-white flex-shrink-0" x-text="(m.user?.name || '?')[0]?.toUpperCase()"></div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm text-zinc-200 truncate" x-text="m.user?.name || 'Unknown'"></p>
                                <p class="text-xs text-zinc-600" x-text="m.user?.email || ''"></p>
                            </div>
                            <span class="text-xs font-medium px-2 py-0.5 rounded-full" :class="m.role === 'admin' ? 'bg-violet-500/20 text-violet-400' : 'bg-zinc-800 text-zinc-500'" x-text="m.role"></span>
                            <button x-show="isOwner && m.role !== 'admin'" @click="removeMember(m)" class="p-1 text-zinc-700 hover:text-rose-400">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                            </button>
                        </div>
                    </template>
                </div>
            </div>

            <div class="bg-zinc-900/50 border border-zinc-800 rounded-xl p-6">
                <h2 class="text-sm font-medium text-zinc-400 mb-4">Team Tasks</h2>
                <div class="space-y-2">
                    <template x-for="t in tasks" :key="t.id">
                        <div @click="window.location.href = '/tasks/' + t.id" class="group bg-zinc-900/60 border border-zinc-800/80 hover:border-zinc-700/80 rounded-xl p-4 cursor-pointer transition-all">
                            <div class="flex items-center gap-2">
                                <h3 class="font-medium text-sm text-zinc-200 truncate" x-text="t.title"></h3>
                            </div>
                        </div>
                    </template>
                    <template x-if="!tasks.length">
                        <div class="text-sm text-zinc-700 py-4 text-center">No tasks in this team.</div>
                    </template>
                </div>
            </div>
        </div>
    </template>
</div>
<script>window.teamId = {{ $id }};</script>
@endsection
