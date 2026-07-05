<div>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Semua Tugas</h2>
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
                <div class="flex gap-2 items-center">
                    <select wire:model.live="statusFilter" class="border-gray-300 rounded-md shadow-sm text-sm">
                        <option value="all">Semua Status</option>
                        <option value="draft">Draft</option>
                        <option value="assigned_pm">Dikirim ke PM</option>
                        <option value="assigned_member">Dikerjakan Anggota</option>
                        <option value="pending_pm">Menunggu Review PM</option>
                        <option value="revision">Revisi</option>
                        <option value="pending_arbitration">Arbitrase</option>
                        <option value="pending_admin">Menunggu Approval</option>
                        <option value="done">Selesai</option>
                        <option value="cancelled">Dibatalkan</option>
                    </select>
                    <label class="flex items-center gap-1 text-sm text-gray-600 cursor-pointer">
                        <input type="checkbox" wire:model.live="escalatedFilter" class="rounded border-gray-300 text-red-600">
                        Eskalasi PM
                    </label>
                </div>
                <input type="text" wire:model.live.debounce.300ms="search" placeholder="Cari tugas..." class="border-gray-300 rounded-md shadow-sm text-sm">
            </div>

            <div class="bg-white shadow sm:rounded-lg overflow-hidden">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tugas</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">PM</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Revisi</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Lampiran</th>
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
                            <td class="px-4 py-3 text-sm text-gray-500">
                                {{ $task->assignedPm->name ?? ($task->recommendedPm->name ?? 'Belum ditugaskan') }}
                                @if($task->escalated_at)
                                    <span class="ml-1 px-1.5 py-0.5 text-xs bg-red-100 text-red-700 rounded font-bold">Eskalasi</span>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                <span class="px-2 py-1 text-xs font-semibold rounded-full
                                    @switch($status = $task->status)
                                        @case('draft') bg-gray-100 text-gray-700 @break
                                        @case('assigned_pm') bg-blue-100 text-blue-700 @break
                                        @case('assigned_member') bg-indigo-100 text-indigo-700 @break
                                        @case('pending_pm') bg-yellow-100 text-yellow-700 @break
                                        @case('revision') bg-orange-100 text-orange-700 @break
                                        @case('pending_arbitration') bg-red-100 text-red-700 @break
                                        @case('pending_admin') bg-purple-100 text-purple-700 @break
                                        @case('done') bg-green-100 text-green-700 @break
                                        @case('cancelled') bg-slate-100 text-slate-500 @break
                                        @default bg-gray-100 text-gray-700
                                    @endswitch
                                ">
                                    @switch($task->status)
                                        @case('draft') Draft @break
                                        @case('assigned_pm') Dikirim ke PM @break
                                        @case('assigned_member') Dikerjakan Anggota @break
                                        @case('pending_pm') Menunggu Review PM @break
                                        @case('revision') Revisi @break
                                        @case('pending_arbitration') Arbitrase @break
                                        @case('pending_admin') Menunggu Approval @break
                                        @case('done') Selesai @break
                                        @case('cancelled') Dibatalkan @break
                                        @default {{ $task->status }}
                                    @endswitch
                                </span>
                            </td>
                            <td class="px-4 py-3 text-sm">
                                @if($task->status === 'revision' || $task->status === 'pending_arbitration')
                                    <span class="font-semibold {{ $task->revision_counter >= $task->max_revision_limit ? 'text-red-600' : 'text-orange-600' }}">
                                        {{ $task->revision_counter }}/{{ $task->max_revision_limit }}
                                    </span>
                                @else
                                    <span class="text-gray-400">-</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-xs">
                                @if($task->attachments->count() > 0)
                                    <div class="space-y-0.5 max-w-[150px] truncate">
                                        @foreach($task->attachments as $att)
                                        <a href="{{ Storage::url($att->file_path) }}" target="_blank" class="block text-blue-600 hover:underline truncate">
                                            {{ $att->filename }}
                                        </a>
                                        @endforeach
                                    </div>
                                @else
                                    <span class="text-gray-400">-</span>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                <span class="text-xs font-medium px-2 py-1 rounded {{ $task->priority === 'high' ? 'bg-red-50 text-red-700' : ($task->priority === 'medium' ? 'bg-yellow-50 text-yellow-700' : 'bg-gray-50 text-gray-600') }}">
                                    {{ $task->priority === 'high' ? 'Tinggi' : ($task->priority === 'medium' ? 'Sedang' : 'Rendah') }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-sm {{ $task->isOverdue() ? 'text-red-600 font-bold' : 'text-gray-500' }}">
                                {{ $task->deadline?->format('Y-m-d') ?? '-' }}
                            </td>
                            <td class="px-4 py-3 text-sm space-x-1">
                                <button wire:click="viewDetail({{ $task->id }})" class="text-gray-400 hover:text-indigo-600 transition" title="Lihat detail">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                </button>
                                <button wire:click="viewHistory({{ $task->id }})" class="text-indigo-600 hover:text-indigo-800 text-xs font-medium">Riwayat</button>
                                @if($task->status === 'pending_admin')
                                <button wire:click="approveTask({{ $task->id }})" wire:confirm="Setujui tugas ini?" class="text-green-600 hover:text-green-800 text-xs font-medium">Setujui</button>
                                @endif
                                @if($task->status === 'pending_arbitration')
                                <button wire:click="confirmArbitration({{ $task->id }}, 'approve')" class="text-green-600 hover:text-green-800 text-xs font-medium">Setujui</button>
                                <button wire:click="confirmArbitration({{ $task->id }}, 'reject')" class="text-orange-600 hover:text-orange-800 text-xs font-medium">Tolak</button>
                                @endif
                                @if($task->escalated_at && $task->status === 'pending_pm')
                                <button wire:click="approveEscalatedTask({{ $task->id }})" wire:confirm="Setujui langsung (bypass PM)?" class="text-green-600 hover:text-green-800 text-xs font-medium">Setujui</button>
                                <button wire:click="rejectEscalatedTask({{ $task->id }})" wire:confirm="Kembalikan ke anggota untuk revisi?" class="text-orange-600 hover:text-orange-800 text-xs font-medium">Tolak</button>
                                <button wire:click="confirmReassign({{ $task->id }})" class="text-blue-600 hover:text-blue-800 text-xs font-medium">Pindahkan</button>
                                @endif
                                @if(!in_array($task->status, ['done', 'cancelled']))
                                <button wire:click="confirmCancel({{ $task->id }})" class="text-red-600 hover:text-red-800 text-xs font-medium">Batalkan</button>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="8" class="px-4 py-8 text-center text-gray-400">Belum ada tugas.</td></tr>
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
                        <h4 class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Deskripsi</h4>
                        <p class="text-sm text-gray-700 mt-1">{{ $detailTask->description }}</p>
                    </div>
                @endif
                <div class="grid grid-cols-2 gap-4 text-sm">
                    <div>
                        <h4 class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Status</h4>
                        <span class="inline-block mt-1 px-2 py-0.5 text-xs rounded-full
                            @switch($detailTask->status)
                                @case('draft') bg-gray-100 text-gray-700 @break
                                @case('assigned_pm') bg-blue-100 text-blue-700 @break
                                @case('assigned_member') bg-indigo-100 text-indigo-700 @break
                                @case('pending_pm') bg-yellow-100 text-yellow-700 @break
                                @case('revision') bg-orange-100 text-orange-700 @break
                                @case('pending_arbitration') bg-red-100 text-red-700 @break
                                @case('pending_admin') bg-purple-100 text-purple-700 @break
                                @case('done') bg-green-100 text-green-700 @break
                                @case('cancelled') bg-slate-100 text-slate-500 @break
                                @default bg-gray-100 text-gray-700
                            @endswitch
                        ">
                            {{ app(\App\Livewire\SuperAdmin\SuperAdminTaskList::class)->getStatusLabel($detailTask->status) }}
                        </span>
                    </div>
                    <div>
                        <h4 class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Prioritas</h4>
                        <span class="inline-block mt-1 px-2 py-0.5 text-xs rounded-full {{ $detailTask->priority === 'high' ? 'bg-red-50 text-red-700' : ($detailTask->priority === 'medium' ? 'bg-yellow-50 text-yellow-700' : 'bg-gray-50 text-gray-600') }}">
                            {{ $detailTask->priority === 'high' ? 'Tinggi' : ($detailTask->priority === 'medium' ? 'Sedang' : 'Rendah') }}
                        </span>
                    </div>
                    @if($detailTask->deadline)
                        <div>
                            <h4 class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Deadline</h4>
                            <p class="mt-1">{{ $detailTask->deadline->format('d M Y') }}</p>
                        </div>
                    @endif
                    @if($detailTask->assignedPm)
                        <div>
                            <h4 class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Project Manager</h4>
                            <p class="mt-1">{{ $detailTask->assignedPm->name }}</p>
                        </div>
                    @endif
                    @if($detailTask->assignedMember)
                        <div>
                            <h4 class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Anggota</h4>
                            <p class="mt-1">{{ $detailTask->assignedMember->name }}</p>
                        </div>
                    @endif
                    @if($detailTask->workspace)
                        <div>
                            <h4 class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Workspace</h4>
                            <p class="mt-1">{{ $detailTask->workspace->name }}</p>
                        </div>
                    @endif
                    @if($detailTask->review_note)
                        <div class="col-span-2">
                            <h4 class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Catatan Review</h4>
                            <p class="mt-1 p-2 bg-orange-50 rounded text-sm text-orange-800">{{ $detailTask->review_note }}</p>
                        </div>
                    @endif
                    @if($detailTask->escalated_at)
                        <div class="col-span-2">
                            <h4 class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Eskalasi PM</h4>
                            <p class="mt-1 text-sm text-red-600">{{ $detailTask->escalated_at->format('d M Y H:i') }}</p>
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
            </div>
        </div>
    </div>
    @endif

    {{-- Modal Riwayat --}}
    @if($showHistoryModal && $historyTaskId)
    <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/40" wire:click.self="showHistoryModal = false">
        <div class="bg-white rounded-xl shadow-xl max-w-lg w-full mx-4 max-h-[80vh] overflow-y-auto">
            <div class="flex justify-between items-center p-4 border-b">
                <h3 class="text-lg font-semibold">Riwayat Perubahan Status</h3>
                <button wire:click="$set('showHistoryModal', false)" class="text-gray-400 hover:text-gray-600">&times;</button>
            </div>
            <div class="p-4 space-y-3">
                @forelse($history as $h)
                <div class="flex items-start gap-3 text-sm">
                    <div class="flex flex-col items-center">
                        <div class="w-2 h-2 rounded-full bg-indigo-500 mt-1.5"></div>
                        @if(!$loop->last)<div class="w-0.5 h-full bg-gray-200"></div>@endif
                    </div>
                    <div class="flex-1 pb-3">
                        <div class="font-medium text-gray-900">
                            {{ $h->from_status }} → {{ $h->to_status }}
                        </div>
                        <div class="text-xs text-gray-500">
                            {{ $h->changer->name ?? 'Sistem' }} &middot; {{ $h->created_at->format('d M Y H:i') }}
                        </div>
                        @if($h->notes)
                        <div class="text-xs text-gray-600 mt-1 italic">{{ $h->notes }}</div>
                        @endif
                    </div>
                </div>
                @empty
                <p class="text-sm text-gray-400">Belum ada riwayat.</p>
                @endforelse
            </div>
        </div>
    </div>
    @endif

    {{-- Modal Batal --}}
    @if($showCancelModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/40" wire:click.self="showCancelModal = false">
        <div class="bg-white rounded-xl shadow-xl max-w-md w-full mx-4">
            <div class="p-4 border-b">
                <h3 class="text-lg font-semibold text-red-600">Batalkan Tugas</h3>
            </div>
            <div class="p-4 space-y-3">
                <p class="text-sm text-gray-600">Apakah Anda yakin ingin membatalkan tugas ini? Tugas tidak dapat dilanjutkan setelah dibatalkan.</p>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Catatan Pembatalan (opsional)</label>
                    <textarea wire:model="cancelNote" rows="2" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"></textarea>
                </div>
            </div>
            <div class="flex justify-end gap-2 p-4 border-t bg-gray-50">
                <button wire:click="$set('showCancelModal', false)" class="px-4 py-2 text-sm bg-white border rounded-lg hover:bg-gray-50">Tutup</button>
                <button wire:click="cancelTask" class="px-4 py-2 text-sm bg-red-600 text-white rounded-lg hover:bg-red-700">Ya, Batalkan</button>
            </div>
        </div>
    </div>
    @endif

    {{-- Modal Arbitrase --}}
    @if($showArbitrationModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/40" wire:click.self="showArbitrationModal = false">
        <div class="bg-white rounded-xl shadow-xl max-w-md w-full mx-4">
            <div class="p-4 border-b">
                <h3 class="text-lg font-semibold {{ $arbitrationAction === 'approve' ? 'text-green-600' : 'text-orange-600' }}">
                    {{ $arbitrationAction === 'approve' ? 'Setujui Arbitrase' : 'Tolak & Kembalikan ke Revisi' }}
                </h3>
            </div>
            <div class="p-4 space-y-3">
                <p class="text-sm text-gray-600">
                    @if($arbitrationAction === 'approve')
                        Setujui tugas ini? Status akan berubah menjadi <strong>Menunggu Approval Admin</strong> (bukan langsung selesai).
                    @else
                        Kembalikan tugas ke anggota untuk revisi. Revision counter akan bertambah.
                    @endif
                </p>
                @if($arbitrationAction === 'reject')
                <div>
                    <label class="block text-sm font-medium text-gray-700">Catatan Revisi <span class="text-red-500">*</span></label>
                    <textarea wire:model="arbitrationNote" rows="2" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"></textarea>
                    @error('arbitrationNote') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>
                @endif
            </div>
            <div class="flex justify-end gap-2 p-4 border-t bg-gray-50">
                <button wire:click="$set('showArbitrationModal', false)" class="px-4 py-2 text-sm bg-white border rounded-lg hover:bg-gray-50">Tutup</button>
                <button wire:click="executeArbitration" class="px-4 py-2 text-sm text-white rounded-lg {{ $arbitrationAction === 'approve' ? 'bg-green-600 hover:bg-green-700' : 'bg-orange-600 hover:bg-orange-700' }}">
                    {{ $arbitrationAction === 'approve' ? 'Ya, Setujui' : 'Ya, Kembalikan' }}
                </button>
            </div>
        </div>
    </div>
    @endif

    {{-- Modal Pindahkan PM --}}
    @if($showReassignModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/40" wire:click.self="showReassignModal = false">
        <div class="bg-white rounded-xl shadow-xl max-w-md w-full mx-4">
            <div class="p-4 border-b">
                <h3 class="text-lg font-semibold text-blue-600">Pindahkan ke PM Lain</h3>
            </div>
            <div class="p-4 space-y-3">
                <p class="text-sm text-gray-600">Pilih PM baru untuk tugas ini. Tugas akan dikirim ulang ke PM baru.</p>
                <select wire:model="reassignPmId" class="w-full border-gray-300 rounded-md shadow-sm text-sm">
                    <option value="">Pilih PM...</option>
                    @foreach(\App\Models\User::where('role', 'pm')->where('is_active', true)->get() as $pm)
                        <option value="{{ $pm->id }}">{{ $pm->name }}</option>
                    @endforeach
                </select>
                @error('reassignPmId') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>
            <div class="flex justify-end gap-2 p-4 border-t bg-gray-50">
                <button wire:click="$set('showReassignModal', false)" class="px-4 py-2 text-sm bg-white border rounded-lg hover:bg-gray-50">Tutup</button>
                <button wire:click="reassignPm" class="px-4 py-2 text-sm bg-blue-600 text-white rounded-lg hover:bg-blue-700">Pindahkan</button>
            </div>
        </div>
    </div>
    @endif
</div>
