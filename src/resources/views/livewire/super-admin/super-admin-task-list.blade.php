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
                                <button wire:click="viewDetail({{ $task->id }})" class="px-3 py-1 text-xs font-medium text-indigo-600 bg-indigo-50 rounded-md hover:bg-indigo-100">Detail</button>
                                <button wire:click="viewHistory({{ $task->id }})" class="px-3 py-1 text-xs font-medium text-gray-600 bg-gray-50 rounded-md hover:bg-gray-100">Riwayat</button>
                                @if(!in_array($task->status, ['done', 'cancelled']))
                                <button wire:click="confirmCancel({{ $task->id }})" class="px-3 py-1 text-xs font-medium text-red-600 bg-red-50 rounded-md hover:bg-red-100">Batalkan</button>
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
                        <h4 class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">Deskripsi</h4>
                        <p class="text-sm text-gray-700 bg-gray-50 rounded-lg p-3 border border-gray-100">{{ $detailTask->description }}</p>
                    </div>
                @endif
                <table class="w-full text-sm">
                    <tbody>
                        <tr>
                            <td class="py-2 pr-4 align-top w-[140px] text-xs font-semibold text-gray-500 uppercase tracking-wide">Status</td>
                            <td class="py-2"><span class="px-2 py-0.5 text-xs rounded-full
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
                            ">{{ app(\App\Livewire\SuperAdmin\SuperAdminTaskList::class)->getStatusLabel($detailTask->status) }}</span></td>
                        </tr>
                        <tr>
                            <td class="py-2 pr-4 align-top text-xs font-semibold text-gray-500 uppercase tracking-wide">Prioritas</td>
                            <td class="py-2"><span class="px-2 py-0.5 text-xs rounded-full {{ $detailTask->priority === 'high' ? 'bg-red-50 text-red-700' : ($detailTask->priority === 'medium' ? 'bg-yellow-50 text-yellow-700' : 'bg-gray-50 text-gray-600') }}">{{ $detailTask->priority === 'high' ? 'Tinggi' : ($detailTask->priority === 'medium' ? 'Sedang' : 'Rendah') }}</span></td>
                        </tr>
                        @if($detailTask->deadline)
                        <tr>
                            <td class="py-2 pr-4 align-top text-xs font-semibold text-gray-500 uppercase tracking-wide">Deadline</td>
                            <td class="py-2">{{ $detailTask->deadline->format('d M Y') }}</td>
                        </tr>
                        @endif
                        @if($detailTask->assignedPm)
                        <tr>
                            <td class="py-2 pr-4 align-top text-xs font-semibold text-gray-500 uppercase tracking-wide">Project Manager</td>
                            <td class="py-2">{{ $detailTask->assignedPm->name }}</td>
                        </tr>
                        @endif
                        @if($detailTask->assignedMember)
                        <tr>
                            <td class="py-2 pr-4 align-top text-xs font-semibold text-gray-500 uppercase tracking-wide">Anggota</td>
                            <td class="py-2">{{ $detailTask->assignedMember->name }}</td>
                        </tr>
                        @endif
                        @if($detailTask->workspace)
                        <tr>
                            <td class="py-2 pr-4 align-top text-xs font-semibold text-gray-500 uppercase tracking-wide">Workspace</td>
                            <td class="py-2">{{ $detailTask->workspace->name }}</td>
                        </tr>
                        @endif
                        @if($detailTask->review_note)
                        <tr>
                            <td class="py-2 pr-4 align-top text-xs font-semibold text-gray-500 uppercase tracking-wide">Catatan Review</td>
                            <td class="py-2"><span class="inline-block p-2 bg-orange-50 rounded text-sm text-orange-800">{{ $detailTask->review_note }}</span></td>
                        </tr>
                        @endif
                        @if($detailTask->escalated_at)
                        <tr>
                            <td class="py-2 pr-4 align-top text-xs font-semibold text-gray-500 uppercase tracking-wide">Eskalasi PM</td>
                            <td class="py-2 text-red-600">{{ $detailTask->escalated_at->format('d M Y H:i') }}</td>
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
                @if(in_array($detailTask->status, ['pending_admin', 'pending_arbitration']) || ($detailTask->escalated_at && $detailTask->status === 'pending_pm'))
                <div class="border-t pt-4 flex gap-2 justify-end">
                    @if($detailTask->status === 'pending_admin')
                    <button wire:click="approveTask({{ $detailTask->id }})" wire:click="$set('showDetailModal', false)" class="px-4 py-2 text-sm bg-green-600 text-white rounded-md hover:bg-green-700">Setujui</button>
                    @endif
                    @if($detailTask->status === 'pending_arbitration')
                    <button wire:click="confirmArbitration({{ $detailTask->id }}, 'approve')" wire:click="$set('showDetailModal', false)" class="px-4 py-2 text-sm bg-green-600 text-white rounded-md hover:bg-green-700">Setujui</button>
                    <button wire:click="confirmArbitration({{ $detailTask->id }}, 'reject')" wire:click="$set('showDetailModal', false)" class="px-4 py-2 text-sm bg-orange-600 text-white rounded-md hover:bg-orange-700">Tolak</button>
                    @endif
                    @if($detailTask->escalated_at && $detailTask->status === 'pending_pm')
                    <button wire:click="approveEscalatedTask({{ $detailTask->id }})" wire:click="$set('showDetailModal', false)" class="px-4 py-2 text-sm bg-green-600 text-white rounded-md hover:bg-green-700">Setujui</button>
                    <button wire:click="rejectEscalatedTask({{ $detailTask->id }})" wire:click="$set('showDetailModal', false)" class="px-4 py-2 text-sm bg-orange-600 text-white rounded-md hover:bg-orange-700">Tolak</button>
                    <button wire:click="confirmReassign({{ $detailTask->id }})" wire:click="$set('showDetailModal', false)" class="px-4 py-2 text-sm bg-blue-600 text-white rounded-md hover:bg-blue-700">Pindahkan</button>
                    @endif
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
