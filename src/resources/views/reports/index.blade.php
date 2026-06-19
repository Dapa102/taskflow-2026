@extends('layouts.app')

@section('title', 'Reports - TaskFlow')

@section('content')
<div x-data="reportsPage" class="max-w-3xl mx-auto px-4 py-6 pb-24">
    <a href="/dashboard" class="inline-flex items-center gap-1 text-sm text-zinc-500 hover:text-zinc-300 mb-4 transition-colors">
        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5"/></svg>
        Back
    </a>

    <div class="flex items-center justify-between mb-6">
        <h1 class="text-xl font-semibold text-zinc-100">Reports</h1>
        <button @click="exportCsv" class="px-3 py-1.5 bg-zinc-800 hover:bg-zinc-700 text-zinc-300 text-xs font-medium rounded-lg">Export CSV</button>
    </div>

    <template x-if="loading">
        <div class="grid grid-cols-2 gap-3">
            <template x-for="n in 4" :key="n">
                <div class="bg-zinc-900 rounded-xl p-4 animate-pulse">
                    <div class="h-6 w-12 bg-zinc-800 rounded mb-2"></div>
                    <div class="h-3 w-20 bg-zinc-800/50 rounded"></div>
                </div>
            </template>
        </div>
    </template>

    <template x-if="!loading && summary">
        <div>
            <div class="grid grid-cols-2 gap-3 mb-6">
                <template x-for="s in [{l:'Total Tasks',v:summary.total,c:'bg-violet-500'},{l:'Completed',v:summary.by_status?.done||0,c:'bg-emerald-500'},{l:'In Progress',v:summary.by_status?.on_progress||0,c:'bg-amber-500'},{l:'To Do',v:summary.by_status?.todo||0,c:'bg-rose-500'}]" :key="s.l">
                    <div class="bg-zinc-900/60 border border-zinc-800/80 rounded-xl p-4">
                        <div class="text-2xl font-bold text-white mb-0.5" x-text="s.v"></div>
                        <div class="text-xs text-zinc-500" x-text="s.l"></div>
                        <div class="mt-2 h-1 rounded-full opacity-50" :class="s.c"></div>
                    </div>
                </template>
            </div>

            <div class="grid grid-cols-2 gap-3 mb-6">
                <template x-for="s in [{l:'Overdue',v:summary.overdue||0,c:'bg-red-500'},{l:'With Deadline',v:summary.with_deadline||0,c:'bg-blue-500'},{l:'Completion Rate',v:(summary.completion_rate != null ? summary.completion_rate + '%' : '0%'),c:'bg-violet-500'}]" :key="s.l">
                    <div class="bg-zinc-900/60 border border-zinc-800/80 rounded-xl p-4">
                        <div class="text-2xl font-bold text-white mb-0.5" x-text="s.v"></div>
                        <div class="text-xs text-zinc-500" x-text="s.l"></div>
                        <div class="mt-2 h-1 rounded-full opacity-50" :class="s.c"></div>
                    </div>
                </template>
            </div>

            <div class="bg-zinc-900/60 border border-zinc-800/80 rounded-xl p-4">
                <h3 class="text-sm font-medium text-zinc-400 mb-3">Activity</h3>
                <div class="space-y-2">
                    <template x-for="row in [{l:'Created today',v:summary.activity?.created_today||0},{l:'Created this week',v:summary.activity?.created_this_week||0},{l:'Created this month',v:summary.activity?.created_this_month||0},{l:'Completed today',v:summary.activity?.done_today||0},{l:'Completed this week',v:summary.activity?.done_this_week||0}]" :key="row.l">
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-zinc-500" x-text="row.l"></span>
                            <span class="text-sm font-medium text-zinc-200" x-text="row.v"></span>
                        </div>
                    </template>
                </div>
            </div>
        </div>
    </template>
</div>
@endsection
