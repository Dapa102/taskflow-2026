<div>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Daftar Tugas</h2>
        </div>
    </x-slot>

    <div class="py-6 px-6">
        <div class="max-w-7xl mx-auto space-y-6">

            <div class="flex justify-between items-center">
                <select wire:model.live="statusFilter" class="border-gray-300 rounded-md shadow-sm text-sm">
                    <option value="all">Semua</option>
                    <option value="pending">Pending</option>
                    <option value="pending_admin">Menunggu Admin</option>
                    <option value="done">Selesai</option>
                    <option value="overdue">Terlambat</option>
                </select>
                <input type="text" wire:model.live.debounce.300ms="search" placeholder="Cari tugas..." class="border-gray-300 rounded-md shadow-sm text-sm">
            </div>

            <div class="bg-white shadow sm:rounded-lg overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tugas</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Workspace</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Assignee</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Priority</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Deadline</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($tasks as $task)
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3">
                                    <div class="text-sm font-medium text-gray-900">{{ $task->title }}</div>
                                    <div class="text-xs text-gray-500 truncate max-w-[200px]">{{ $task->description }}</div>
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-500">{{ $task->workspace->name ?? '-' }}</td>
                                <td class="px-4 py-3 text-sm text-gray-500">{{ $task->assignee->name ?? 'Unassigned' }}</td>
                                <td class="px-4 py-3">
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full
                                        {{ $task->status === 'done' ? 'bg-green-100 text-green-800' : '' }}
                                        {{ $task->status === 'on_progress' ? 'bg-blue-100 text-blue-800' : '' }}
                                        {{ $task->status === 'pending_pm' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                        {{ $task->status === 'pending_admin' ? 'bg-purple-100 text-purple-800' : '' }}
                                        {{ $task->status === 'revision' ? 'bg-orange-100 text-orange-800' : '' }}
                                        {{ $task->status === 'todo' ? 'bg-gray-100 text-gray-800' : '' }}">
                                        {{ $task->status === 'done' ? 'Selesai' : ($task->status === 'on_progress' ? 'Dikerjakan' : ($task->status === 'pending_pm' ? 'Review PM' : ($task->status === 'pending_admin' ? 'Review Admin' : ($task->status === 'revision' ? 'Revisi' : 'Menunggu')))) }}
                                    </span>
                                </td>
                                <td class="px-4 py-3">
                                    <span class="text-xs font-medium px-2 py-1 rounded {{ $task->priority === 'high' ? 'bg-red-50 text-red-700' : ($task->priority === 'medium' ? 'bg-yellow-50 text-yellow-700' : 'bg-gray-50 text-gray-600') }}">
                                        {{ $task->priority === 'high' ? 'Tinggi' : ($task->priority === 'medium' ? 'Sedang' : 'Rendah') }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-sm {{ $task->isOverdue() ? 'text-red-600 font-bold' : 'text-gray-500' }}">
                                    {{ $task->deadline?->format('Y-m-d') ?? '-' }}
                                </td>
                                <td class="px-4 py-3 space-x-1">
                                    @if($task->status === 'pending_admin')
                                    <button wire:click="finalApproveTask({{ $task->id }})" wire:confirm="Konfirmasi tugas ini sebagai selesai?" class="text-green-600 hover:text-green-900 text-xs font-medium">Selesai</button>
                                    @endif
                                    <button wire:click="deleteTask({{ $task->id }})" wire:confirm="Hapus tugas ini?" class="text-red-600 hover:text-red-900 text-xs font-medium">Hapus</button>
                                    <button wire:click="viewTask({{ $task->id }})" class="text-indigo-600 hover:text-indigo-900 text-xs font-medium">Detail</button>
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="7" class="px-4 py-8 text-center text-gray-400">Belum ada tugas.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="p-4">{{ $tasks->links() }}</div>
            </div>

        </div>
    </div>

    @if($showDetail && $selectedTask)
    <div
        x-data="{ show: false }"
        x-init="$nextTick(() => show = true)"
        x-show="show"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 z-50 flex items-center justify-center p-4"
        @keydown.escape.window="show = false; setTimeout(() => $wire.closeDetail(), 200)"
    >
        <div class="fixed inset-0 bg-black/40" @click="show = false; setTimeout(() => $wire.closeDetail(), 200)"></div>
        <div
            class="relative bg-white rounded-2xl shadow-2xl w-full max-w-2xl max-h-[85vh] overflow-y-auto"
            x-show="show"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 scale-95 translate-y-4"
            x-transition:enter-end="opacity-100 scale-100 translate-y-0"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100 scale-100 translate-y-0"
            x-transition:leave-end="opacity-0 scale-95 translate-y-4"
        >
            <div class="sticky top-0 bg-white border-b border-gray-200 px-6 py-4 flex items-center justify-between rounded-t-2xl">
                <h3 class="text-lg font-semibold text-gray-900">Detail Tugas</h3>
                <button @click="show = false; setTimeout(() => $wire.closeDetail(), 200)" class="p-2 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-lg transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <div class="px-6 py-5 space-y-6">
                <div class="grid grid-cols-2 gap-6">
                    <div class="space-y-4">
                        <div>
                            <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider">Judul</label>
                            <p class="mt-1 text-sm font-medium text-gray-900">{{ $selectedTask->title }}</p>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider">Deskripsi</label>
                            <p class="mt-1 text-sm text-gray-700">{{ $selectedTask->description ?? '-' }}</p>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider">Workspace</label>
                            <p class="mt-1 text-sm text-gray-900">{{ $selectedTask->workspace->name ?? '-' }}</p>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider">Assignee</label>
                            <p class="mt-1 text-sm text-gray-900">{{ $selectedTask->assignee->name ?? 'Unassigned' }}</p>
                        </div>
                    </div>
                    <div class="space-y-4">
                        <div>
                            <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider">Status</label>
                            <span class="mt-1 inline-flex px-2.5 py-1 text-xs font-semibold rounded-full
                                {{ $selectedTask->status === 'done' ? 'bg-green-100 text-green-800' : '' }}
                                {{ $selectedTask->status === 'on_progress' ? 'bg-blue-100 text-blue-800' : '' }}
                                {{ $selectedTask->status === 'pending_pm' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                {{ $selectedTask->status === 'pending_admin' ? 'bg-purple-100 text-purple-800' : '' }}
                                {{ $selectedTask->status === 'revision' ? 'bg-orange-100 text-orange-800' : '' }}
                                {{ $selectedTask->status === 'todo' ? 'bg-gray-100 text-gray-800' : '' }}">
                                {{ $selectedTask->status === 'done' ? 'Selesai' : ($selectedTask->status === 'on_progress' ? 'Dikerjakan' : ($selectedTask->status === 'pending_pm' ? 'Review PM' : ($selectedTask->status === 'pending_admin' ? 'Review Admin' : ($selectedTask->status === 'revision' ? 'Revisi' : 'Menunggu')))) }}
                            </span>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider">Priority</label>
                            <span class="mt-1 inline-flex text-xs font-medium px-2.5 py-1 rounded {{ $selectedTask->priority === 'high' ? 'bg-red-50 text-red-700' : ($selectedTask->priority === 'medium' ? 'bg-yellow-50 text-yellow-700' : 'bg-gray-50 text-gray-600') }}">
                                {{ $selectedTask->priority === 'high' ? 'Tinggi' : ($selectedTask->priority === 'medium' ? 'Sedang' : 'Rendah') }}
                            </span>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider">Deadline</label>
                            <p class="mt-1 text-sm {{ $selectedTask->isOverdue() ? 'text-red-600 font-bold' : 'text-gray-900' }}">
                                {{ $selectedTask->deadline?->format('Y-m-d') ?? '-' }}
                            </p>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider">Dibuat Oleh</label>
                            <p class="mt-1 text-sm text-gray-900">{{ $selectedTask->creator->name ?? '-' }}</p>
                        </div>
                    </div>
                </div>

                <div class="border-t border-gray-100 pt-5">
                    <h4 class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-3">File Lampiran</h4>
                    @if($selectedTask->attachments->isNotEmpty())
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        @foreach($selectedTask->attachments as $att)
                        <div class="flex items-center gap-3 bg-gray-50 rounded-lg p-3 border border-gray-200 hover:border-indigo-300 transition-colors group">
                            <div class="w-10 h-10 rounded-lg bg-indigo-100 flex items-center justify-center shrink-0">
                                <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-gray-700 truncate group-hover:text-indigo-700 transition-colors">{{ $att->filename }}</p>
                                <p class="text-xs text-gray-400">{{ $att->human_file_size ?? '' }}</p>
                            </div>
                            <a href="{{ $att->url }}" target="_blank" class="shrink-0 p-2 text-gray-400 hover:text-indigo-600 hover:bg-white rounded-lg transition-all">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                            </a>
                        </div>
                        @endforeach
                    </div>
                    @else
                    <div class="flex items-center justify-center h-24 bg-gray-50 rounded-lg border-2 border-dashed border-gray-200">
                        <p class="text-sm text-gray-400">Tidak ada file lampiran.</p>
                    </div>
                    @endif
                </div>
            </div>
            <div class="sticky bottom-0 bg-gray-50 border-t border-gray-200 px-6 py-3 flex justify-end rounded-b-2xl">
                <button @click="show = false; setTimeout(() => $wire.closeDetail(), 200)" class="px-5 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">Tutup</button>
            </div>
        </div>
    </div>
    @endif
</div>
