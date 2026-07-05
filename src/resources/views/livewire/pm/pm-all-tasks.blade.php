<div>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Semua Tugas</h2>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

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
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Anggota</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Dibuat Oleh</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Prioritas</th>
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
                                <td class="px-4 py-3 text-sm text-gray-500">{{ $task->assignedMember?->name ?? '-' }}</td>
                                <td class="px-4 py-3 text-sm text-gray-500">{{ $task->creator?->name ?? '-' }}</td>
                                <td class="px-4 py-3">
                                    @php
                                        $statusLabel = match($task->status) {
                                            'assigned_pm' => 'Dikirim ke PM',
                                            'assigned_member' => 'Dikerjakan',
                                            'pending_pm' => 'Menunggu Review',
                                            'pending_admin' => 'Menunggu Admin',
                                            'revision' => 'Revisi',
                                            'pending_arbitration' => 'Arbitrase',
                                            'done' => 'Selesai',
                                            'cancelled' => 'Dibatalkan',
                                            default => $task->status,
                                        };
                                        $statusColor = match($task->status) {
                                            'done' => 'bg-green-100 text-green-800',
                                            'assigned_member' => 'bg-blue-100 text-blue-800',
                                            'pending_pm' => 'bg-yellow-100 text-yellow-800',
                                            'revision' => 'bg-orange-100 text-orange-800',
                                            'pending_arbitration' => 'bg-red-100 text-red-800',
                                            default => 'bg-gray-100 text-gray-800',
                                        };
                                    @endphp
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $statusColor }}">
                                        {{ $statusLabel }}
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
                            <tr><td colspan="7" class="px-4 py-8 text-center text-gray-400">Belum ada tugas.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="p-4">{{ $tasks->links() }}</div>
            </div>

        {{-- Modal Detail --}}
        @if($showDetailModal && $detailTask)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/40" wire:click.self="$set('showDetailModal', false)">
            <div class="bg-white rounded-xl shadow-xl max-w-2xl w-full mx-4 max-h-[80vh] overflow-y-auto">
                <div class="flex justify-between items-center p-4 border-b sticky top-0 bg-white z-10">
                    <h3 class="text-lg font-semibold text-gray-900">{{ $detailTask->title }}</h3>
                    <button wire:click="$set('showDetailModal', false)" class="text-gray-400 hover:text-gray-600 text-xl leading-none">&times;</button>
                </div>
                <div class="p-4 space-y-4">
                    @if($detailTask->description)
                        <div>
                            <h4 class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Deskripsi</h4>
                            <p class="text-sm text-gray-700 mt-1">{{ $detailTask->description }}</p>
                        </div>
                    @endif
                    <div class="grid grid-cols-2 gap-4 text-sm">
                        <div>
                            <h4 class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Status</h4>
                            <span class="inline-block mt-1 px-2 py-0.5 text-xs rounded-full
                                @switch($detailTask->status)
                                    @case('assigned_pm') bg-blue-100 text-blue-700 @break
                                    @case('assigned_member') bg-indigo-100 text-indigo-700 @break
                                    @case('pending_pm') bg-purple-100 text-purple-700 @break
                                    @case('revision') bg-orange-100 text-orange-700 @break
                                    @case('pending_admin') bg-indigo-100 text-indigo-700 @break
                                    @case('pending_arbitration') bg-red-100 text-red-700 @break
                                    @case('done') bg-green-100 text-green-700 @break
                                    @case('cancelled') bg-slate-100 text-slate-500 @break
                                    @default bg-gray-100 text-gray-600
                                @endswitch
                            ">
                                @switch($detailTask->status)
                                    @case('assigned_pm') Dikirim ke PM @break
                                    @case('assigned_member') Dikerjakan @break
                                    @case('pending_pm') Menunggu Review @break
                                    @case('revision') Revisi @break
                                    @case('pending_admin') Menunggu Approval @break
                                    @case('pending_arbitration') Arbitrase @break
                                    @case('done') Selesai @break
                                    @case('cancelled') Dibatalkan @break
                                    @default {{ $detailTask->status }}
                                @endswitch
                            </span>
                        </div>
                        <div>
                            <h4 class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Prioritas</h4>
                            <span class="inline-block mt-1 px-2 py-0.5 text-xs rounded-full {{ $detailTask->priority === 'high' ? 'bg-red-50 text-red-600' : ($detailTask->priority === 'medium' ? 'bg-yellow-50 text-yellow-600' : 'bg-gray-50 text-gray-500') }}">
                                {{ ucfirst($detailTask->priority) }}
                            </span>
                        </div>
                        @if($detailTask->deadline)
                            <div>
                                <h4 class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Deadline</h4>
                                <p class="mt-1">{{ $detailTask->deadline->format('d M Y') }}</p>
                            </div>
                        @endif
                        <div>
                            <h4 class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Anggota</h4>
                            <p class="mt-1">{{ $detailTask->assignedMember?->name ?? '—' }}</p>
                        </div>
                        @if($detailTask->creator)
                            <div>
                                <h4 class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Dibuat Oleh</h4>
                                <p class="mt-1">{{ $detailTask->creator->name }}</p>
                            </div>
                        @endif
                        @if($detailTask->review_note)
                            <div class="col-span-2">
                                <h4 class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Catatan Review</h4>
                                <p class="mt-1 p-2 bg-orange-50 rounded text-sm text-orange-800">{{ $detailTask->review_note }}</p>
                            </div>
                        @endif
                    </div>
                    @if($detailTask->attachments->count())
                        <div>
                            <h4 class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-2">Lampiran</h4>
                            <div class="space-y-1">
                                @foreach($detailTask->attachments as $att)
                                    <div class="flex items-center gap-2 text-sm text-gray-600">
                                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                        <a href="{{ Storage::url($att->file_path) }}" target="_blank" class="hover:text-indigo-600">{{ $att->filename }}</a>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    @if($detailTask->status === 'pending_pm')
                        <div class="mt-6 pt-4 border-t flex flex-col gap-3">
                            <div class="flex items-center gap-2 justify-end">
                                <button wire:click="approveTask({{ $detailTask->id }})" wire:confirm="Setujui tugas ini dan kirim ke Admin?" class="px-4 py-2 text-sm bg-green-600 text-white rounded-md hover:bg-green-700 font-medium">
                                    Menyetujui
                                </button>
                                
                                @if($detailTask->isRevisiLocked())
                                    <span class="text-xs text-red-500 font-medium px-2">Batas revisi tercapai</span>
                                @else
                                    <button wire:click="$set('rejectTaskId', {{ $detailTask->id }})" class="px-4 py-2 text-sm bg-orange-600 text-white rounded-md hover:bg-orange-700 font-medium">
                                        Revisi
                                    </button>
                                @endif
                            </div>

                            @if($rejectTaskId === $detailTask->id)
                                <div class="mt-2 p-3 bg-orange-50 rounded-md border border-orange-200">
                                    <textarea wire:model="reviewNote" placeholder="Catatan revisi..." rows="2" class="w-full border-gray-300 rounded-md shadow-sm text-sm mb-2"></textarea>
                                    @error('reviewNote') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                    <div class="flex gap-2 justify-end mt-2">
                                        <button wire:click="rejectTask({{ $detailTask->id }})" class="px-3 py-1 text-xs bg-orange-600 text-white rounded-md hover:bg-orange-700">Kirim Revisi</button>
                                        <button wire:click="$set('rejectTaskId', null)" class="px-3 py-1 text-xs text-gray-600 hover:text-gray-900">Batal</button>
                                    </div>
                                </div>
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        </div>
        @endif
    </div>
</div>
