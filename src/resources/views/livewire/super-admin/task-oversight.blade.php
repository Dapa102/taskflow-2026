<div>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Global Tasks — Dari Atasan') }}
        </h2>
    </x-slot>

    <div class="py-6 px-6">
        <div class="max-w-7xl mx-auto space-y-6">

            <div class="bg-white shadow sm:rounded-lg p-6">
                <div class="flex justify-between mb-6">
                    <div class="flex space-x-2">
                        <select wire:model.live="statusFilter" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                            <option value="all">Semua</option>
                            <option value="pending">Belum Diberikan</option>
                            <option value="given">Sudah Diberikan</option>
                            <option value="done">Selesai</option>
                            <option value="overdue">Terlambat</option>
                        </select>
                    </div>
                    <div>
                        <x-text-input wire:model.live.debounce.300ms="search" type="text" placeholder="Cari tugas atau workspace..." />
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tugas</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Workspace</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Assignee</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Deadline</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($tasks as $task)
                            <tr>
                                <td class="px-6 py-4">
                                    <div class="text-sm font-medium text-gray-900">{{ $task->title }}</div>
                                    <div class="text-xs text-gray-500 truncate max-w-xs">{{ $task->description }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">{{ $task->workspace->name ?? 'N/A' }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $task->assignee->name ?? '—' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if(is_null($task->assigned_to))
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">Belum Diberikan</span>
                                    @elseif($task->status === 'done')
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Selesai</span>
                                    @else
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">Sudah Diberikan</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                    @if($task->deadline)
                                        <span class="{{ $task->deadline < now() && $task->status != 'done' ? 'text-red-600 font-bold' : 'text-gray-500' }}">
                                            {{ $task->deadline->format('Y-m-d') }}
                                        </span>
                                    @else
                                        <span class="text-gray-400">-</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                    <button wire:click="viewDetail({{ $task->id }})" class="text-indigo-600 hover:text-indigo-900 font-medium">
                                        Detail
                                    </button>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="px-6 py-4 text-center text-gray-500">Tidak ada tugas dari Super Admin.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="mt-4">
                    {{ $tasks->links() }}
                </div>
            </div>

        </div>
    </div>

    <!-- Detail Modal -->
    @if($detailTask)
    <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4" wire:click.self="closeDetail">
        <div class="bg-white rounded-xl shadow-xl w-full max-w-2xl max-h-[90vh] overflow-y-auto">
            <div class="sticky top-0 bg-white border-b border-gray-200 px-6 py-4 flex items-center justify-between rounded-t-xl">
                <h3 class="text-lg font-semibold text-gray-900">{{ $detailTask->title }}</h3>
                <button wire:click="closeDetail" class="p-2 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-lg transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <div class="p-6 space-y-5">
                <div>
                    <label class="text-xs text-gray-500 uppercase font-semibold">Deskripsi</label>
                    <p class="mt-1 text-sm text-gray-700">{{ $detailTask->description ?? '—' }}</p>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="text-xs text-gray-500 uppercase font-semibold">Status</label>
                        <p class="mt-1 text-sm font-medium">
                            @if(is_null($detailTask->assigned_to))
                            <span class="text-yellow-600">Belum Diberikan</span>
                            @elseif($detailTask->status === 'done')
                            <span class="text-green-600">Selesai</span>
                            @else
                            <span class="text-blue-600">Sudah Diberikan</span>
                            @endif
                        </p>
                    </div>
                    <div>
                        <label class="text-xs text-gray-500 uppercase font-semibold">Dari Atasan</label>
                        <p class="mt-1 text-sm font-medium">{{ $detailTask->creator->name ?? '—' }}</p>
                    </div>
                    <div>
                        <label class="text-xs text-gray-500 uppercase font-semibold">Priority</label>
                        <p class="mt-1 text-sm font-medium">{{ ucfirst($detailTask->priority) }}</p>
                    </div>
                    <div>
                        <label class="text-xs text-gray-500 uppercase font-semibold">Deadline</label>
                        <p class="mt-1 text-sm">{{ $detailTask->deadline?->format('Y-m-d') ?? '—' }}</p>
                    </div>
                    <div>
                        <label class="text-xs text-gray-500 uppercase font-semibold">Workspace</label>
                        <p class="mt-1 text-sm">{{ $detailTask->workspace->name ?? '—' }}</p>
                    </div>
                    <div>
                        <label class="text-xs text-gray-500 uppercase font-semibold">Assignee</label>
                        <p class="mt-1 text-sm">{{ $detailTask->assignee->name ?? '—' }}</p>
                    </div>
                </div>

                <div class="border-t pt-4">
                    <label class="text-xs text-gray-500 uppercase font-semibold">Assign ke Project Manager</label>
                    @if(is_null($detailTask->assigned_to))
                    <div class="mt-2 flex gap-2">
                        <select id="pm-select-{{ $detailTask->id }}" class="flex-1 border-gray-300 rounded-md shadow-sm text-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="">Pilih PM...</option>
                            @foreach($pms as $pm)
                                <option value="{{ $pm->id }}">{{ $pm->name }}</option>
                            @endforeach
                        </select>
                        <button onclick="assignPm({{ $detailTask->id }})" class="px-4 py-2 bg-indigo-600 text-white rounded-md text-sm hover:bg-indigo-700">
                            Assign
                        </button>
                    </div>
                    @else
                    <p class="mt-2 text-sm text-gray-500">Tugas sudah diberikan ke <strong>{{ $detailTask->assignee->name }}</strong>.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <script>
        function assignPm(taskId) {
            const select = document.getElementById('pm-select-' + taskId);
            const pmId = select.value;
            if (!pmId) { alert('Pilih PM terlebih dahulu.'); return; }
            @this.call('assignToPm', taskId, parseInt(pmId));
        }
    </script>
    @endif
</div>
