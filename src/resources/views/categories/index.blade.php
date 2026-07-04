@extends('layouts.app')

@section('title', 'Kategori - TaskFlow')

@section('content')
<div x-data="categoriesPage" class="max-w-4xl mx-auto px-4 py-6 pb-24">
    <h1 class="text-2xl font-bold text-white mb-6">Categories</h1>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-1">
            <div class="bg-zinc-900/50 border border-zinc-800 rounded-xl p-4 space-y-3">
                <h2 class="text-sm font-medium text-zinc-400">All Categories</h2>

                <template x-if="loading">
                    <div class="text-xs text-zinc-600">Loading...</div>
                </template>

                <template x-if="!loading">
                    <div class="space-y-1">
                        <template x-for="cat in categories" :key="cat.id">
                            <div class="flex items-center gap-2 px-3 py-2 rounded-lg cursor-pointer transition-all"
                                :class="selectedCategory === cat.id ? 'bg-white/5' : 'hover:bg-white/5'"
                                @click="fetchTasks(cat.id)">
                                <template x-if="editing === cat.id">
                                    <div class="flex-1 flex items-center gap-2" @click.stop>
                                        <input type="text" x-model="form.name" class="flex-1 bg-zinc-800 border border-zinc-700 rounded px-2 py-1 text-xs text-white outline-none">
                                        <input type="color" x-model="form.color" class="w-6 h-6 rounded cursor-pointer border-0">
                                        <button @click="save(cat)" class="text-emerald-400 text-xs font-medium">Save</button>
                                        <button @click="cancelEdit()" class="text-zinc-600 text-xs">Cancel</button>
                                    </div>
                                </template>
                                <template x-if="editing !== cat.id">
                                    <div class="flex items-center gap-2 w-full">
                                        <div class="w-3 h-3 rounded-full flex-shrink-0" :style="{ backgroundColor: cat.color }"></div>
                                        <span class="flex-1 text-sm text-zinc-400 truncate" x-text="cat.name"></span>
                                        <span class="text-xs text-zinc-600" x-text="cat.tasks_count ?? 0"></span>
                                        <button @click.stop="startEdit(cat)" class="p-0.5 text-zinc-700 hover:text-violet-400 opacity-0 hover:opacity-100">
                                            <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10"/></svg>
                                        </button>
                                        <button @click.stop="remove(cat)" class="p-0.5 text-zinc-700 hover:text-rose-400 opacity-0 hover:opacity-100">
                                            <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                                        </button>
                                    </div>
                                </template>
                            </div>
                        </template>
                    </div>
                </template>

                <div class="pt-2 border-t border-zinc-800">
                    <div class="flex items-center gap-2">
                        <input type="text" x-model="form.name" @keydown.enter="create" placeholder="New category..." class="flex-1 bg-transparent border-0 border-b border-zinc-800 pb-1 text-sm text-zinc-300 placeholder-zinc-600 focus:border-violet-500/50 focus:ring-0 outline-none transition-colors">
                        <input type="color" x-model="form.color" class="w-5 h-5 rounded cursor-pointer border-0">
                        <button @click="create" :disabled="!form.name.trim()" class="text-xs text-violet-400 hover:text-violet-300 disabled:text-zinc-700 font-medium">Add</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="lg:col-span-2">
            <template x-if="!selectedCategory">
                <div class="text-center py-20 text-zinc-700 text-sm">Select a category to view its tasks</div>
            </template>
            <template x-if="selectedCategory">
                <div class="space-y-2">
                    <h2 class="text-sm font-medium text-zinc-400 mb-3">
                        <span x-text="categories.find(c => c.id === selectedCategory)?.name"></span> Tasks
                    </h2>
                    <template x-for="t in tasks" :key="t.id">
                        <div @click="window.location.href = '/tasks/' + t.id" class="group bg-zinc-900/60 border border-zinc-800/80 hover:border-zinc-700/80 rounded-xl p-4 cursor-pointer transition-all hover:-translate-y-0.5 hover:shadow-lg hover:shadow-violet-500/5">
                            <div class="flex items-start justify-between gap-3">
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center gap-2 mb-1">
                                        <h3 class="font-medium truncate text-zinc-200" :class="t.status === 'done' ? 'line-through text-zinc-600' : ''" x-text="t.title"></h3>
                                    </div>
                                    <p x-show="t.description" class="text-sm text-zinc-500 line-clamp-2" x-text="t.description"></p>
                                </div>
                            </div>
                        </div>
                    </template>
                    <template x-if="!tasks.length">
                        <div class="text-center py-12 text-sm text-zinc-700">No tasks in this category</div>
                    </template>
                </div>
            </template>
        </div>
    </div>
</div>
@endsection
