<div>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Semua Tugas</h2>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <div class="flex justify-between items-center flex-wrap gap-2">
                <div class="flex gap-2">
                    <select wire:model.live="statusFilter" class="border-gray-300 rounded-md shadow-sm text-sm">
                        <option value="all">Semua</option>
                        <option value="pending">Pending</option>
                        <option value="done">Selesai</option>
                        <option value="overdue">Terlambat</option>
                    </select>
                    <select wire:model.live="projectFilter" class="border-gray-300 rounded-md shadow-sm text-sm">
                        <option value="">Semua Project</option>
                        @foreach($projects as $project)
                        <option value="{{ $project->id }}">{{ $project->name }}</option>
                        @endforeach
                    </select>
                </div>
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
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $task->status_badge_class }}">
                                        {{ $task->status_label }}
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
                            <h4 class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">Deskripsi</h4>
                            <p class="text-sm text-gray-700 bg-gray-50 rounded-lg p-3 border border-gray-100">{{ $detailTask->description }}</p>
                        </div>
                    @endif
                    <table class="w-full text-sm">
                        <tbody>
                            <tr>
                                <td class="py-2 pr-4 align-top w-[140px] text-xs font-semibold text-gray-500 uppercase tracking-wide">Status</td>
                                <td class="py-2"><span class="px-2 py-0.5 text-xs rounded-full {{ $detailTask->status_badge_class }}">{{ $detailTask->status_label }}</span></td>
                            </tr>
                            <tr>
                                <td class="py-2 pr-4 align-top text-xs font-semibold text-gray-500 uppercase tracking-wide">Prioritas</td>
                                <td class="py-2"><span class="px-2 py-0.5 text-xs rounded-full {{ $detailTask->priority === 'high' ? 'bg-red-50 text-red-600' : ($detailTask->priority === 'medium' ? 'bg-yellow-50 text-yellow-600' : 'bg-gray-50 text-gray-500') }}">{{ ucfirst($detailTask->priority) }}</span></td>
                            </tr>
                            @if($detailTask->deadline)
                            <tr>
                                <td class="py-2 pr-4 align-top text-xs font-semibold text-gray-500 uppercase tracking-wide">Deadline</td>
                                <td class="py-2">{{ $detailTask->deadline->format('d M Y') }}</td>
                            </tr>
                            @endif
                            <tr>
                                <td class="py-2 pr-4 align-top text-xs font-semibold text-gray-500 uppercase tracking-wide">Anggota</td>
                                <td class="py-2">{{ $detailTask->assignedMember?->name ?? '—' }}</td>
                            </tr>
                            @if($detailTask->creator)
                            <tr>
                                <td class="py-2 pr-4 align-top text-xs font-semibold text-gray-500 uppercase tracking-wide">Dibuat Oleh</td>
                                <td class="py-2">{{ $detailTask->creator->name }}</td>
                            </tr>
                            @endif
                            @if($detailTask->review_note)
                            <tr>
                                <td class="py-2 pr-4 align-top text-xs font-semibold text-gray-500 uppercase tracking-wide">Catatan Review</td>
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

                    @if($detailTask->status === 'review')
                        <div class="mt-6 pt-4 border-t flex flex-col gap-3">
                            <div class="flex items-center gap-2 justify-end">
                                <button wire:click="approveTask({{ $detailTask->id }})" class="px-4 py-2 text-sm bg-green-600 text-white rounded-md hover:bg-green-700 font-medium">
                                    Setujui
                                </button>
                                <button wire:click="$set('rejectTaskId', {{ $detailTask->id }})" class="px-4 py-2 text-sm bg-orange-600 text-white rounded-md hover:bg-orange-700 font-medium">
                                    Revisi
                                </button>
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
