@extends('layouts.app')

@section('title', 'Dashboard - TaskFlow')

@section('content')
<div x-data="dashboard" class="max-w-4xl mx-auto px-4 py-6 pb-24">
    <div class="mb-6">
        <h1 class="text-xl font-semibold text-zinc-100">Hello, {{ auth()->user()->name }}!</h1>
        <p class="text-sm text-zinc-500 mt-0.5">
            <span x-text="stats.total"></span> tasks &middot;
            <span x-text="stats.done"></span> done &middot;
            <span x-show="stats.overdue > 0" x-text="stats.overdue" class="text-rose-400"></span>
            <span x-show="stats.overdue > 0"> overdue</span>
            <span x-show="stats.overdue === 0">all good</span>
        </p>
    </div>

    <div class="flex items-center gap-1 mb-3 bg-zinc-900 rounded-lg p-1 w-fit">
        <template x-for="s in [{key:'all',label:'All'},{key:'todo',label:'To-Do'},{key:'on_progress',label:'Progress'},{key:'done',label:'Done'}]" :key="s.key">
            <button @click="filterStatus = s.key" class="px-3.5 py-1.5 rounded-md text-sm font-medium transition-all"
                :class="filterStatus === s.key ? 'bg-zinc-800 text-zinc-200 shadow-sm' : 'text-zinc-500 hover:text-zinc-300'">
                <span x-text="s.label"></span>
            </button>
        </template>
    </div>

    <div x-show="categories.length > 0" class="flex items-center gap-1.5 mb-4 flex-wrap">
        <button @click="filterCategory = null" class="px-2.5 py-1 rounded-md text-xs font-medium transition-all"
            :class="!filterCategory ? 'bg-zinc-800 text-zinc-200' : 'text-zinc-500 hover:text-zinc-300'">All</button>
        <template x-for="cat in categories" :key="cat.id">
            <button @click="filterCategory = cat.id" class="px-2.5 py-1 rounded-md text-xs font-medium transition-all"
                :class="filterCategory === cat.id ? 'text-white' : 'text-zinc-500 hover:text-zinc-300'"
                :style="filterCategory === cat.id ? { backgroundColor: cat.color + '30', color: cat.color } : {}">
                <span x-text="cat.name"></span>
            </button>
        </template>
    </div>

    <div x-show="selectMode && selectedIds.length > 0" class="mb-3 flex items-center gap-2 px-3 py-2 bg-zinc-900 rounded-lg border border-zinc-800">
        <span class="text-xs text-zinc-400" x-text="selectedIds.length + ' selected'"></span>
        <div class="flex-1"></div>
        <button @click="selectAll()" class="text-xs text-violet-400 hover:text-violet-300" x-text="selectedIds.length === filteredTasks.length ? 'Deselect all' : 'Select all'"></button>
        <div class="w-px h-4 bg-zinc-800"></div>
        <select @change="bulkStatus($event.target.value); $event.target.value = ''" class="bg-zinc-800 border border-zinc-700 rounded-lg px-2 py-1 text-xs text-zinc-300 outline-none cursor-pointer">
            <option value="">Change status</option>
            <option value="todo">To-Do</option>
            <option value="on_progress">In Progress</option>
            <option value="done">Done</option>
        </select>
        <button @click="bulkDelete()" class="px-2 py-1 text-xs text-rose-400 hover:text-rose-300 bg-rose-500/10 rounded-lg">Delete</button>
    </div>

    <div class="mb-4 flex items-center gap-2">
        <button @click="selectMode = !selectMode; selectedIds = []" class="px-2 py-1 text-xs font-medium rounded-lg"
            :class="selectMode ? 'bg-violet-500/20 text-violet-400' : 'text-zinc-600 hover:text-zinc-400 bg-zinc-900'">
            <span x-text="selectMode ? 'Done' : 'Select'"></span>
        </button>
    </div>

    <template x-if="loading">
        <div class="space-y-2">
            <template x-for="n in 3" :key="n">
                <div class="bg-zinc-900 rounded-xl p-4 animate-pulse">
                    <div class="h-4 bg-zinc-800 rounded w-3/4 mb-2"></div>
                    <div class="h-3 bg-zinc-800/50 rounded w-1/3"></div>
                </div>
            </template>
        </div>
    </template>

    <template x-if="!loading">
        <div class="space-y-2">
            <template x-for="task in filteredTasks" :key="task.id">
                <div class="group relative bg-zinc-900/60 backdrop-blur-sm border border-zinc-800/80 hover:border-zinc-700/80 rounded-xl p-4 cursor-pointer transition-all duration-300 hover:-translate-y-0.5 hover:shadow-lg hover:shadow-violet-500/5 overflow-hidden"
                    :class="selectedIds.includes(task.id) ? 'ring-2 ring-violet-500/50 border-violet-500/30' : ''"
                    @click="selectMode || (window.location.href = '/tasks/' + task.id)">
                    <div class="relative flex items-start justify-between gap-3">
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-2 mb-1">
                                <template x-if="selectMode">
                                    <input type="checkbox" :checked="selectedIds.includes(task.id)" @click.stop="toggleSelect(task.id)" class="w-4 h-4 rounded border-zinc-600 bg-zinc-800 text-violet-500 focus:ring-violet-500/30 cursor-pointer flex-shrink-0">
                                </template>
                                <template x-if="!selectMode">
                                    <button @click.stop="toggleStatus(task.id)" class="w-5 h-5 rounded-full border-2 flex items-center justify-center flex-shrink-0 transition-all"
                                        :class="task.status === 'done' ? 'border-emerald-500 bg-emerald-500/20' : task.status === 'on_progress' || task.status === 'progress' ? 'border-amber-500 bg-amber-500/10' : 'border-zinc-600'">
                                        <svg x-show="task.status === 'done'" class="w-3 h-3 text-emerald-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/></svg>
                                    </button>
                                </template>
                                <h3 class="font-medium truncate" :class="task.status === 'done' ? 'line-through text-zinc-600' : 'text-zinc-200'" x-text="task.title"></h3>
                            </div>
                            <p x-show="task.description" class="text-sm text-zinc-500 line-clamp-2 ml-7" x-text="task.description"></p>
                            <div class="flex items-center gap-3 mt-3 ml-7">
                                <span class="text-[10px] font-semibold px-1.5 py-0.5 rounded" :class="task.priority === 'high' ? 'text-rose-300 bg-rose-500/10' : task.priority === 'low' ? 'text-zinc-500 bg-zinc-800' : 'text-amber-300 bg-amber-500/10'" x-text="task.priority"></span>
                                <span class="text-xs font-medium px-2 py-0.5 rounded-full"
                                    :class="task.status === 'todo' ? 'text-rose-300 bg-rose-500/10' : task.status === 'on_progress' || task.status === 'progress' ? 'text-amber-300 bg-amber-500/10' : 'text-emerald-300 bg-emerald-500/10'"
                                    x-text="task.status === 'todo' ? 'To-Do' : (task.status === 'on_progress' || task.status === 'progress') ? 'In Progress' : 'Done'"></span>
                                <span x-show="task.deadline" class="text-xs font-mono" :class="!task.deadline || task.status === 'done' ? '' : (new Date(task.deadline) < new Date() ? 'text-rose-400' : 'text-zinc-500')">
                                    <span x-text="new Date(task.deadline).toLocaleDateString('en-US', {month:'short',day:'numeric'})"></span>
                                    <span x-show="task.deadline && task.status !== 'done' && new Date(task.deadline) < new Date()" class="text-rose-400 ml-1">• Overdue</span>
                                </span>
                            </div>
                        </div>
                        <div x-show="!selectMode" class="flex items-center gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
                            <button @click.stop="openEdit(task)" class="p-1.5 text-zinc-600 hover:text-violet-400 rounded-lg hover:bg-white/5">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10"/></svg>
                            </button>
                            <button @click.stop="deleteTask(task.id)" class="p-1.5 text-zinc-600 hover:text-rose-400 rounded-lg hover:bg-white/5">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0"/></svg>
                            </button>
                        </div>
                    </div>
                </div>
            </template>
            <template x-if="filteredTasks.length === 0">
                <div class="text-center py-16 text-sm text-zinc-700">No tasks match your filters</div>
            </template>
        </div>
    </template>

    <button @click="openCreate()" class="fixed bottom-6 right-6 z-30 w-12 h-12 bg-gradient-to-r from-violet-500 to-pink-500 hover:from-violet-400 hover:to-pink-400 text-white rounded-full shadow-lg shadow-violet-500/30 flex items-center justify-center hover:scale-110 active:scale-95 transition-all duration-200">
        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
    </button>

    <div x-show="drawerOpen" class="fixed inset-0 z-40 flex" @keydown.escape="drawerOpen = false">
        <div class="absolute inset-0 bg-black/50" @click="drawerOpen = false"></div>
        <div class="relative ml-auto w-full max-w-lg bg-slate-950 border-l border-white/5 shadow-2xl overflow-y-auto">
            <div class="p-6">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-lg font-semibold text-white" x-text="editingTask ? 'Edit Task' : 'New Task'"></h2>
                    <button @click="drawerOpen = false" class="p-1 text-zinc-500 hover:text-zinc-300">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <div class="space-y-4">
                    <div>
                        <label class="block text-xs text-zinc-500 mb-1">Title <span class="text-zinc-700" x-text="'(' + remainingChars(form.title) + ')'"></span></label>
                        <input type="text" x-model="form.title" maxlength="255" placeholder="What needs to be done?" class="w-full bg-zinc-900 border border-zinc-800 rounded-xl px-4 py-2.5 text-sm text-zinc-200 placeholder-zinc-600 focus:border-violet-500/50 focus:ring-1 focus:ring-violet-500/20 outline-none">
                    </div>
                    <div>
                        <label class="block text-xs text-zinc-500 mb-1">Description</label>
                        <textarea x-model="form.description" rows="3" placeholder="Add details..." class="w-full bg-zinc-900 border border-zinc-800 rounded-xl px-4 py-2.5 text-sm text-zinc-200 placeholder-zinc-600 focus:border-violet-500/50 focus:ring-1 focus:ring-violet-500/20 outline-none resize-none"></textarea>
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs text-zinc-500 mb-1">Priority</label>
                            <select x-model="form.priority" class="w-full bg-zinc-900 border border-zinc-800 rounded-xl px-4 py-2.5 text-sm text-zinc-200 focus:border-violet-500/50 focus:ring-1 focus:ring-violet-500/20 outline-none">
                                <option value="low">Low</option>
                                <option value="medium">Medium</option>
                                <option value="high">High</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs text-zinc-500 mb-1">Deadline</label>
                            <input type="date" x-model="form.deadline" class="w-full bg-zinc-900 border border-zinc-800 rounded-xl px-4 py-2.5 text-sm text-zinc-200 focus:border-violet-500/50 focus:ring-1 focus:ring-violet-500/20 outline-none [color-scheme:dark]">
                        </div>
                    </div>
                    <div x-show="categories.length > 0">
                        <label class="block text-xs text-zinc-500 mb-1">Category</label>
                        <select x-model="form.category_id" class="w-full bg-zinc-900 border border-zinc-800 rounded-xl px-4 py-2.5 text-sm text-zinc-200 focus:border-violet-500/50 focus:ring-1 focus:ring-violet-500/20 outline-none">
                            <option value="">No category</option>
                            <template x-for="cat in categories" :key="cat.id">
                                <option :value="cat.id" x-text="cat.name"></option>
                            </template>
                        </select>
                    </div>
                    <button @click="save()" :disabled="saving || !form.title.trim()" class="w-full py-2.5 bg-gradient-to-r from-violet-500 to-pink-500 hover:from-violet-400 hover:to-pink-400 disabled:opacity-50 text-white rounded-xl font-medium transition-all shadow-lg shadow-violet-500/20">
                        <span x-text="saving ? 'Saving...' : (editingTask ? 'Update Task' : 'Create Task')"></span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
