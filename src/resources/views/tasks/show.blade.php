@extends('layouts.app')

@section('title', 'Tugas - TaskFlow')

@section('content')
<div x-data="taskDetail" class="max-w-3xl mx-auto px-4 py-6 pb-24">
    <template x-if="loading">
        <div class="min-h-screen flex items-center justify-center"><div class="text-zinc-600">Loading...</div></div>
    </template>

    <template x-if="!loading && task">
        <div>
            <a href="/dashboard" class="inline-flex items-center gap-1 text-sm text-zinc-500 hover:text-zinc-300 mb-4 transition-colors">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5"/></svg>
                Back
            </a>

            <div class="flex items-start justify-between gap-4 mb-6">
                <div class="flex-1 min-w-0">
                    <div class="flex items-center gap-2 mb-2">
                        <template x-if="task.category">
                            <span class="text-xs px-2 py-0.5 rounded-full" :style="{ backgroundColor: task.category.color + '20', color: task.category.color }" x-text="task.category.name"></span>
                        </template>
                        <span class="text-[10px] font-semibold px-1.5 py-0.5 rounded" :class="task.priority === 'high' ? 'text-rose-300 bg-rose-500/10' : task.priority === 'low' ? 'text-zinc-500 bg-zinc-800' : 'text-amber-300 bg-amber-500/10'" x-text="task.priority"></span>
                    </div>
                    <h1 class="text-2xl font-bold text-white" x-text="task.title"></h1>
                    <p x-show="task.description" class="text-zinc-400 mt-2 whitespace-pre-wrap" x-text="task.description"></p>
                    <p x-show="task.deadline" class="text-sm text-zinc-600 mt-3"><span class="text-zinc-500">Deadline:</span> <span x-text="formatDate(task.deadline)"></span></p>
                </div>
            </div>

            <div x-show="!['archived','cancelled'].includes(task.status)" class="flex gap-2 mb-8">
                <template x-for="s in [{value:'todo',label:'Todo',color:'bg-zinc-700'},{value:'on_progress',label:'On Progress',color:'bg-blue-500'},{value:'done',label:'Done',color:'bg-emerald-500'}]" :key="s.value">
                    <button @click="updateStatus(s.value)" class="px-3 py-1.5 rounded-lg text-xs font-medium transition-all"
                        :class="task.status === s.value ? s.color + ' text-white' : 'bg-zinc-800/50 text-zinc-500 hover:bg-zinc-800 hover:text-zinc-300'"
                        x-text="s.label"></button>
                </template>
            </div>

            <div class="space-y-8">
                <div class="bg-zinc-900/50 border border-zinc-800 rounded-xl p-4">
                    <div class="flex items-center justify-between mb-3">
                        <h4 class="text-sm font-medium text-zinc-400">Assignees</h4>
                        <button @click="showAssign = !showAssign" class="text-xs text-violet-400 hover:text-violet-300" x-text="showAssign ? 'Cancel' : '+ Assign'"></button>
                    </div>

                    <div x-show="showAssign" class="mb-3 space-y-2">
                        <input type="text" x-model="memberSearch" @input="searchMembers(memberSearch)" placeholder="Search team members..." class="w-full bg-zinc-800/50 border border-zinc-700 rounded-lg px-3 py-1.5 text-sm text-zinc-300 placeholder-zinc-600 focus:border-violet-500/50 focus:ring-0 outline-none">
                        <template x-if="searchResults.length > 0">
                            <div class="bg-zinc-800 border border-zinc-700 rounded-lg overflow-hidden">
                                <template x-for="u in searchResults" :key="u.id">
                                    <button @click="assignUser(u.id)" class="w-full flex items-center gap-2 px-3 py-2 text-sm text-zinc-300 hover:bg-zinc-700/50">
                                        <div class="w-6 h-6 rounded-full bg-gradient-to-br from-violet-500 to-pink-500 flex items-center justify-center text-[10px] font-bold text-white flex-shrink-0" x-text="u.name[0]?.toUpperCase()"></div>
                                        <div class="text-left">
                                            <div class="text-sm text-zinc-300" x-text="u.name"></div>
                                            <div class="text-[10px] text-zinc-500" x-text="u.email"></div>
                                        </div>
                                    </button>
                                </template>
                            </div>
                        </template>
                    </div>

                    <template x-if="assignees.length === 0">
                        <div class="text-xs text-zinc-700">No one assigned yet</div>
                    </template>
                    <template x-for="a in assignees" :key="a.id || a.user_id">
                        <div class="flex items-center gap-2 group mb-1.5">
                            <div class="w-6 h-6 rounded-full bg-gradient-to-br from-violet-500 to-pink-500 flex items-center justify-center text-[10px] font-bold text-white flex-shrink-0" x-text="(a.name || a.user?.name || '?')[0]?.toUpperCase()"></div>
                            <div class="flex-1 min-w-0">
                                <div class="text-sm text-zinc-300 truncate" x-text="a.name || a.user?.name || 'Unknown'"></div>
                                <div x-show="a.email || a.user?.email" class="text-[10px] text-zinc-600 truncate" x-text="a.email || a.user?.email"></div>
                            </div>
                            <button @click="unassignUser(a.id || a.user_id)" class="p-0.5 text-zinc-700 hover:text-rose-400 opacity-0 group-hover:opacity-100">
                                <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                            </button>
                        </div>
                    </template>
                </div>

                <div class="bg-zinc-900/50 border border-zinc-800 rounded-xl p-4">
                    <div class="flex items-center justify-between mb-3">
                        <h4 class="text-sm font-medium text-zinc-400">Subtasks</h4>
                        <span class="text-xs text-zinc-600" x-text="subtaskProgress + '%'"></span>
                    </div>
                    <div class="w-full bg-zinc-800 rounded-full h-1 mb-3">
                        <div class="h-1 rounded-full bg-gradient-to-r from-violet-500 to-pink-500 transition-all" :style="'width: ' + subtaskProgress + '%'"></div>
                    </div>
                    <div class="flex items-center gap-2 mb-3">
                        <input type="text" x-model="newSubtask" @keydown.enter="addSubtask" placeholder="Add subtask..." class="flex-1 bg-zinc-800/50 border border-zinc-700 rounded-lg px-3 py-1.5 text-sm text-zinc-300 placeholder-zinc-600 focus:border-violet-500/50 focus:ring-0 outline-none">
                        <button @click="addSubtask" :disabled="!newSubtask.trim()" class="px-3 py-1.5 text-sm bg-violet-600 hover:bg-violet-500 disabled:bg-zinc-800 disabled:text-zinc-700 text-white rounded-lg font-medium transition-all">Add</button>
                    </div>
                    <div class="space-y-1">
                        <template x-for="st in subtasks" :key="st.id">
                            <div class="flex items-center gap-2 group py-1">
                                <button @click="toggleSubtask(st)" class="w-4 h-4 rounded border-2 flex items-center justify-center flex-shrink-0 transition-all"
                                    :class="st.is_completed ? 'border-emerald-500 bg-emerald-500/20' : 'border-zinc-600'">
                                    <svg x-show="st.is_completed" class="w-2.5 h-2.5 text-emerald-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/></svg>
                                </button>
                                <span class="flex-1 text-sm" :class="st.is_completed ? 'line-through text-zinc-600' : 'text-zinc-300'" x-text="st.title"></span>
                                <button @click="deleteSubtask(st)" class="p-0.5 text-zinc-700 hover:text-rose-400 opacity-0 group-hover:opacity-100">
                                    <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                                </button>
                            </div>
                        </template>
                        <template x-if="subtasks.length === 0">
                            <div class="text-xs text-zinc-700 py-2">No subtasks yet.</div>
                        </template>
                    </div>
                </div>

                <div class="bg-zinc-900/50 border border-zinc-800 rounded-xl p-4">
                    <h4 class="text-sm font-medium text-zinc-400 mb-3">Attachments</h4>
                    <div class="space-y-1">
                        <template x-for="att in attachments" :key="att.id">
                            <div class="flex items-center gap-2 group py-1.5">
                                <span class="text-lg" x-text="fileIcon(att.mime_type)"></span>
                                <a :href="att.url" target="_blank" class="flex-1 text-sm text-zinc-400 hover:text-violet-400 truncate" x-text="att.filename || att.original_name"></a>
                                <span class="text-[10px] text-zinc-600" x-text="(att.human_file_size || (att.file_size ? Math.round(att.file_size/1024) + ' KB' : ''))"></span>
                                <button @click="deleteAttachment(att)" class="p-0.5 text-zinc-700 hover:text-rose-400 opacity-0 group-hover:opacity-100">
                                    <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                                </button>
                            </div>
                        </template>
                        <template x-if="attachments.length === 0">
                            <div class="text-xs text-zinc-700 py-2">No attachments yet.</div>
                        </template>
                    </div>
                    <label class="mt-3 flex items-center gap-2 px-3 py-2 bg-zinc-800/50 border border-dashed border-zinc-700 rounded-lg cursor-pointer hover:bg-zinc-800 transition-colors">
                        <svg class="w-4 h-4 text-zinc-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5m-13.5-9L12 3m0 0l4.5 4.5M12 3v13.5"/></svg>
                        <span class="text-sm text-zinc-500">Upload file</span>
                        <input type="file" @change="uploadAttachment" class="hidden">
                    </label>
                </div>

                <div class="bg-zinc-900/50 border border-zinc-800 rounded-xl p-4">
                    <h4 class="text-sm font-medium text-zinc-400 mb-3">Comments</h4>
                    <div class="space-y-2">
                        <template x-for="c in comments" :key="c.id">
                            <div class="flex gap-2 group">
                                <div class="w-7 h-7 rounded-full bg-gradient-to-br from-violet-500 to-pink-500 flex items-center justify-center text-xs font-bold text-white flex-shrink-0 mt-0.5" x-text="(c.user?.name || '?')[0]?.toUpperCase() || '?'"></div>
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center gap-2">
                                        <span class="text-xs font-medium text-zinc-300" x-text="c.user?.name || 'Unknown'"></span>
                                        <span class="text-[10px] text-zinc-600" x-text="ago(c.created_at)"></span>
                                        <button @click="deleteComment(c)" class="ml-auto p-0.5 text-zinc-700 hover:text-rose-400 opacity-0 group-hover:opacity-100">
                                            <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                                        </button>
                                    </div>
                                    <p class="text-sm text-zinc-400 mt-0.5 whitespace-pre-wrap" x-text="c.content || c.body"></p>
                                </div>
                            </div>
                        </template>
                        <template x-if="comments.length === 0">
                            <div class="text-xs text-zinc-700 py-2">No comments yet.</div>
                        </template>
                    </div>
                    <div class="flex items-start gap-2 mt-3 pt-2 border-t border-zinc-800">
                        <textarea x-model="newComment" placeholder="Write a comment..." rows="2" class="flex-1 bg-zinc-800/50 border border-zinc-700 rounded-lg px-3 py-2 text-sm text-zinc-300 placeholder-zinc-600 focus:border-violet-500/50 focus:ring-0 resize-none outline-none"></textarea>
                        <button @click="postComment" :disabled="!newComment.trim()" class="px-4 py-2 bg-violet-600 hover:bg-violet-500 disabled:bg-zinc-800 disabled:text-zinc-700 text-white text-sm rounded-lg font-medium transition-all">Send</button>
                    </div>
                </div>
            </div>
        </div>
    </template>
</div>
<script>window.taskId = {{ $id }};</script>
@endsection
