<div>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Daftar Tugas</h2>
            <button wire:click="$set('showForm', true)" class="px-5 py-2.5 bg-indigo-600 text-white rounded-lg text-sm font-semibold hover:bg-indigo-700 shadow-sm">
                + Tambah Tugas
            </button>
        </div>
    </x-slot>

    <div class="py-6 px-6">
        <div class="max-w-7xl mx-auto space-y-6">

            @if($showForm)
            <div class="bg-white shadow sm:rounded-lg p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Buat Tugas Baru</h3>
                <form wire:submit="createTask" class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Judul Tugas</label>
                        <input type="text" wire:model="title" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        @error('title') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Deskripsi</label>
                        <textarea wire:model="description" rows="2" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"></textarea>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Priority</label>
                            <select wire:model="priority" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="low">Low</option>
                                <option value="medium">Medium</option>
                                <option value="high">High</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Deadline</label>
                            <input type="date" wire:model="deadline" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            @error('deadline') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Tunjuk Project Manager</label>
                        <select wire:model.live="selectedPm" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="">Pilih Project Manager</option>
                            @foreach($pms as $pm)
                            <option value="{{ $pm->id }}">{{ $pm->name }}</option>
                            @endforeach
                        </select>
                        @error('selectedPm') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    @if($selectedPm && $pmTeams->isNotEmpty())
                    <div class="bg-indigo-50 border border-indigo-200 rounded-lg p-3">
                        <h4 class="text-sm font-semibold text-indigo-800 mb-2">Tim yang dipimpin:</h4>
                        <div class="space-y-2">
                            @foreach($pmTeams as $team)
                            <div class="bg-white rounded-md p-2 border border-indigo-100">
                                <div class="text-sm font-medium text-gray-900">{{ $team->name }}</div>
                                <div class="flex flex-wrap gap-1 mt-1">
                                    @foreach($team->members as $member)
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium {{ $member->role === 'admin' ? 'bg-purple-100 text-purple-700' : 'bg-gray-100 text-gray-600' }}">
                                        {{ $member->user?->name ?? '?' }}
                                        <span class="{{ $member->role === 'admin' ? 'text-purple-500' : 'text-gray-400' }}">({{ $member->role === 'admin' ? 'Project Manager' : 'Anggota' }})</span>
                                    </span>
                                    @endforeach
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endif

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Workspace</label>
                        <select wire:model="workspaceId" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="">Pilih Workspace</option>
                            @foreach($workspaces as $ws)
                            <option value="{{ $ws->id }}">{{ $ws->name }}</option>
                            @endforeach
                        </select>
                        @error('workspaceId') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <div class="flex justify-end gap-2">
                        <button type="button" wire:click="$set('showForm', false)" class="px-4 py-2 text-sm text-gray-700 hover:text-gray-900">Batal</button>
                        <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-md text-sm font-medium hover:bg-indigo-700">Simpan Tugas</button>
                    </div>
                </form>
            </div>
            @endif

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
</div>
