<div>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Semua Tugas</h2>
        </div>
    </x-slot>

    <div class="py-6 px-6">
        <div class="max-w-7xl mx-auto space-y-6">

            <div class="flex justify-between items-center">
                <select wire:model.live="statusFilter" class="border-gray-300 rounded-md shadow-sm text-sm">
                    <option value="all">Semua</option>
                    <option value="pending">Pending</option>
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
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Dibuat Oleh</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Priority</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Deadline</th>
                                <th class="px-4 py-3"></th>
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
                                <td class="px-4 py-3 text-sm text-gray-500">{{ $task->creator->name ?? '-' }}</td>
                                <td class="px-4 py-3">
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $task->status === 'done' ? 'bg-green-100 text-green-800' : ($task->status === 'on_progress' ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-800') }}">
                                        {{ $task->status === 'done' ? 'Selesai' : ($task->status === 'on_progress' ? 'Dikerjakan' : 'Menunggu') }}
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
                                <td class="px-4 py-3">
                                    <button wire:click="showDetail({{ $task->id }})"
                                        class="px-3 py-1 text-xs font-medium text-indigo-600 bg-indigo-50 rounded-md hover:bg-indigo-100">
                                        Detail
                                    </button>
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="8" class="px-4 py-8 text-center text-gray-400">Belum ada tugas.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="p-4">{{ $tasks->links() }}</div>
            </div>

        </div>
    </div>

    {{-- Detail Modal --}}
    @if($detailModal && $detailTask)
    @php $task = $detailTask; @endphp
    <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/40" wire:click.self="$set('detailModal', false)">
        <div class="bg-white rounded-xl shadow-xl max-w-2xl w-full mx-4 max-h-[80vh] overflow-y-auto">
            <div class="flex justify-between items-center p-4 border-b sticky top-0 bg-white z-10">
                <h3 class="text-lg font-semibold text-gray-900">{{ $task->title }}</h3>
                <button wire:click="$set('detailModal', false)" class="text-gray-400 hover:text-gray-600 text-xl leading-none">&times;</button>
            </div>
            <div class="p-4 space-y-4">
                @if($task->description)
                <div>
                    <h4 class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Deskripsi</h4>
                    <p class="text-sm text-gray-700 mt-1">{{ $task->description }}</p>
                </div>
                @endif
                <div class="grid grid-cols-2 gap-4 text-sm">
                    <div>
                        <h4 class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Status</h4>
                        <span class="inline-block mt-1 px-2 py-0.5 text-xs rounded-full
                            @switch($task->status)
                                @case('assigned_member') bg-blue-100 text-blue-700 @break
                                @case('pending_pm') bg-purple-100 text-purple-700 @break
                                @case('revision') bg-orange-100 text-orange-700 @break
                                @case('pending_admin') bg-indigo-100 text-indigo-700 @break
                                @case('pending_arbitration') bg-red-100 text-red-700 @break
                                @case('done') bg-green-100 text-green-700 @break
                                @default bg-gray-100 text-gray-600
                            @endswitch
                        ">
                            @switch($task->status)
                                @case('assigned_member') Dikerjakan @break
                                @case('pending_pm') Review PM @break
                                @case('revision') Revisi @break
                                @case('pending_admin') Approval @break
                                @case('pending_arbitration') Arbitrase @break
                                @case('done') Selesai @break
                                @default {{ $task->status }}
                            @endswitch
                        </span>
                    </div>
                    <div>
                        <h4 class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Prioritas</h4>
                        <span class="inline-block mt-1 px-2 py-0.5 text-xs rounded-full {{ $task->priority === 'high' ? 'bg-red-50 text-red-600' : ($task->priority === 'medium' ? 'bg-yellow-50 text-yellow-600' : 'bg-gray-50 text-gray-500') }}">
                            {{ ucfirst($task->priority) }}
                        </span>
                    </div>
                    @if($task->deadline)
                    <div>
                        <h4 class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Deadline</h4>
                        <p class="mt-1 {{ $task->isOverdue() ? 'text-red-600 font-bold' : '' }}">{{ $task->deadline->format('d M Y') }}</p>
                    </div>
                    @endif
                    <div>
                        <h4 class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Workspace</h4>
                        <p class="mt-1">{{ $task->workspace->name ?? '-' }}</p>
                    </div>
                    <div>
                        <h4 class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Assignee</h4>
                        <p class="mt-1">{{ $task->assignee->name ?? '-' }}</p>
                    </div>
                    <div>
                        <h4 class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Dibuat Oleh</h4>
                        <p class="mt-1">{{ $task->creator->name ?? '-' }}</p>
                    </div>
                    <div>
                        <h4 class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Tanggal Dibuat</h4>
                        <p class="mt-1">{{ $task->created_at->format('d M Y H:i') }}</p>
                    </div>
                </div>
                @if($task->attachments->count())
                <div>
                    <h4 class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-2">Lampiran</h4>
                    <div class="space-y-1">
                        @foreach($task->attachments as $att)
                        <div class="flex items-center gap-2 text-sm text-gray-600">
                            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            <a href="{{ Storage::url($att->file_path) }}" target="_blank" class="hover:text-indigo-600">{{ $att->filename }}</a>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
    @endif
</div>
