<div>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Approval Tugas</h2>
        </div>
    </x-slot>

    @if(session('message'))
        <div class="max-w-7xl mx-auto mt-4 px-6">
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg">{{ session('message') }}</div>
        </div>
    @endif
    @if(session('error'))
        <div class="max-w-7xl mx-auto mt-4 px-6">
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg">{{ session('error') }}</div>
        </div>
    @endif

    <div class="py-6 px-6">
        <div class="max-w-7xl mx-auto space-y-6">
            <div class="flex justify-between items-center">
                <p class="text-sm text-gray-500">Tugas yang sudah disetujui PM dan menunggu approval final Super Admin.</p>
                <input type="text" wire:model.live.debounce.300ms="search" placeholder="Cari tugas..." class="border-gray-300 rounded-md shadow-sm text-sm">
            </div>

            <div class="bg-white shadow sm:rounded-lg overflow-hidden">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tugas</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Project</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">PM</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Anggota</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Prioritas</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Deadline</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Revisi</th>
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
                            <td class="px-4 py-3 text-sm text-gray-500">{{ $task->project?->name ?? '-' }}</td>
                            <td class="px-4 py-3 text-sm text-gray-500">{{ $task->assignedPm?->name ?? '-' }}</td>
                            <td class="px-4 py-3 text-sm text-gray-500">{{ $task->assignedMember?->name ?? '-' }}</td>
                            <td class="px-4 py-3">
                                <span class="text-xs font-medium px-2 py-1 rounded {{ $task->priority === 'high' ? 'bg-red-50 text-red-700' : ($task->priority === 'medium' ? 'bg-yellow-50 text-yellow-700' : 'bg-gray-50 text-gray-600') }}">
                                    {{ $task->priority === 'high' ? 'Tinggi' : ($task->priority === 'medium' ? 'Sedang' : 'Rendah') }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-sm {{ $task->isOverdue() ? 'text-red-600 font-bold' : 'text-gray-500' }}">
                                {{ $task->deadline?->format('Y-m-d') ?? '-' }}
                            </td>
                            <td class="px-4 py-3 text-sm">
                                <span class="{{ $task->isRevisiLocked() ? 'text-red-600 font-bold' : 'text-gray-500' }}">
                                    {{ $task->revision_counter }}/{{ $task->max_revision_limit }}
                                </span>
                            </td>
                            <td class="px-4 py-3 flex gap-1">
                                <button wire:click="viewDetail({{ $task->id }})"
                                    class="px-3 py-1 text-xs font-medium text-indigo-600 bg-indigo-50 rounded-md hover:bg-indigo-100">
                                    Detail
                                </button>
                                <button wire:click="viewHistory({{ $task->id }})"
                                    class="px-3 py-1 text-xs font-medium text-gray-600 bg-gray-50 rounded-md hover:bg-gray-100">
                                    Riwayat
                                </button>
                                <button wire:click="approveFinal({{ $task->id }})"
                                    class="px-3 py-1 text-xs font-medium text-green-600 bg-green-50 rounded-md hover:bg-green-100">
                                    Setujui
                                </button>
                                <button wire:click="confirmReject({{ $task->id }})"
                                    class="px-3 py-1 text-xs font-medium text-red-600 bg-red-50 rounded-md hover:bg-red-100">
                                    Tolak
                                </button>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="8" class="px-4 py-8 text-center text-gray-400">Tidak ada tugas menunggu approval.</td></tr>
                        @endforelse
                    </tbody>
                </table>
                <div class="p-4">{{ $tasks->links() }}</div>
            </div>
        </div>
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
                        <h4 class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">Deskripsi</h4>
                        <p class="text-sm text-gray-700 bg-gray-50 rounded-lg p-3 border border-gray-100">{{ $detailTask->description }}</p>
                    </div>
                @endif
                <table class="w-full text-sm">
                    <tbody>
                        <tr>
                            <td class="py-2 pr-4 align-top w-[140px] text-xs font-semibold text-gray-500 uppercase tracking-wide">Status</td>
                            <td class="py-2"><span class="px-2 py-0.5 text-xs rounded-full bg-purple-100 text-purple-700">Menunggu Approval</span></td>
                        </tr>
                        <tr>
                            <td class="py-2 pr-4 align-top text-xs font-semibold text-gray-500 uppercase tracking-wide">Project</td>
                            <td class="py-2">{{ $detailTask->project?->name ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td class="py-2 pr-4 align-top text-xs font-semibold text-gray-500 uppercase tracking-wide">Prioritas</td>
                            <td class="py-2">{{ ucfirst($detailTask->priority) }}</td>
                        </tr>
                        @if($detailTask->deadline)
                        <tr>
                            <td class="py-2 pr-4 align-top text-xs font-semibold text-gray-500 uppercase tracking-wide">Deadline</td>
                            <td class="py-2">{{ $detailTask->deadline->format('d M Y') }}</td>
                        </tr>
                        @endif
                        <tr>
                            <td class="py-2 pr-4 align-top text-xs font-semibold text-gray-500 uppercase tracking-wide">Revisi</td>
                            <td class="py-2"><span class="{{ $detailTask->isRevisiLocked() ? 'text-red-600 font-bold' : '' }}">{{ $detailTask->revision_counter }}/{{ $detailTask->max_revision_limit }}</span></td>
                        </tr>
                        <tr>
                            <td class="py-2 pr-4 align-top text-xs font-semibold text-gray-500 uppercase tracking-wide">Project Manager</td>
                            <td class="py-2">{{ $detailTask->assignedPm?->name ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td class="py-2 pr-4 align-top text-xs font-semibold text-gray-500 uppercase tracking-wide">Anggota</td>
                            <td class="py-2">{{ $detailTask->assignedMember?->name ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td class="py-2 pr-4 align-top text-xs font-semibold text-gray-500 uppercase tracking-wide">Workspace</td>
                            <td class="py-2">{{ $detailTask->workspace?->name ?? '-' }}</td>
                        </tr>
                        @if($detailTask->review_note)
                        <tr>
                            <td class="py-2 pr-4 align-top text-xs font-semibold text-gray-500 uppercase tracking-wide">Catatan PM</td>
                            <td class="py-2"><span class="inline-block p-2 bg-orange-50 rounded text-sm text-orange-800">{{ $detailTask->review_note }}</span></td>
                        </tr>
                        @endif
                    </tbody>
                </table>
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
                <div class="border-t pt-4 flex gap-2 justify-end">
                    <button wire:click="approveFinal({{ $detailTask->id }})"
                        class="px-4 py-2 text-sm bg-green-600 text-white rounded-md hover:bg-green-700 font-medium">
                        Setujui Final
                    </button>
                    <button wire:click="confirmReject({{ $detailTask->id }})"
                        class="px-4 py-2 text-sm bg-red-600 text-white rounded-md hover:bg-red-700 font-medium">
                        Tolak
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- Modal Reject --}}
    @if($showRejectModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/40" wire:click.self="$set('showRejectModal', false)">
        <div class="bg-white rounded-xl shadow-xl max-w-lg w-full mx-4">
            <div class="flex justify-between items-center p-4 border-b">
                <h3 class="text-lg font-semibold text-gray-900">Tolak Tugas</h3>
                <button wire:click="$set('showRejectModal', false)" class="text-gray-400 hover:text-gray-600 text-xl leading-none">&times;</button>
            </div>
            <form wire:submit="rejectTask" class="p-4 space-y-4">
                <div>
                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">Alasan Penolakan</label>
                    <textarea wire:model="rejectNote" rows="3" class="w-full border-gray-300 rounded-md shadow-sm text-sm" placeholder="Catatan revisi..."></textarea>
                    @error('rejectNote') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>
                <div class="flex justify-end gap-2">
                    <button type="button" wire:click="$set('showRejectModal', false)" class="px-4 py-2 text-sm text-gray-600 hover:text-gray-900">Batal</button>
                    <button type="submit" class="px-4 py-2 text-sm bg-red-600 text-white rounded-md hover:bg-red-700 font-medium">Kirim</button>
                </div>
            </form>
        </div>
    </div>
    @endif

    {{-- Modal Riwayat --}}
    @if($showHistoryModal && $history)
    <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/40" wire:click.self="$set('showHistoryModal', false)">
        <div class="bg-white rounded-xl shadow-xl max-w-lg w-full mx-4 max-h-[80vh] overflow-y-auto">
            <div class="flex justify-between items-center p-4 border-b sticky top-0 bg-white z-10">
                <h3 class="text-lg font-semibold text-gray-900">Riwayat Status</h3>
                <button wire:click="$set('showHistoryModal', false)" class="text-gray-400 hover:text-gray-600 text-xl leading-none">&times;</button>
            </div>
            <div class="p-4 space-y-3">
                @forelse($history as $h)
                <div class="flex items-start gap-3 p-3 bg-gray-50 rounded-lg">
                    <div class="flex-1">
                        <div class="text-sm font-medium">{{ $h->from_status }} → {{ $h->to_status }}</div>
                        <div class="text-xs text-gray-500">{{ $h->notes }}</div>
                        <div class="text-xs text-gray-400 mt-1">{{ $h->changer?->name ?? 'Sistem' }} • {{ $h->created_at->format('d M Y H:i') }}</div>
                    </div>
                </div>
                @empty
                <p class="text-sm text-gray-400 text-center py-4">Belum ada riwayat.</p>
                @endforelse
            </div>
        </div>
    </div>
    @endif
</div>
