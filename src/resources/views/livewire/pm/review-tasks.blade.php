<div>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Review Tugas</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-4">
            @if(session('message'))
                <div class="p-4 text-sm text-green-800 rounded-lg bg-green-50">{{ session('message') }}</div>
            @endif

            @php
                $pending = $tasks->where('status', 'review');
                $other = $tasks->where('status', '!=', 'review');
            @endphp

            @if($pending->count())
                <h3 class="text-lg font-semibold text-yellow-600">Menunggu Review ({{ $pending->count() }})</h3>
            @endif

            @forelse($pending as $task)
                <div class="bg-white shadow sm:rounded-lg p-6">
                    <div class="flex items-start justify-between">
                        <div class="flex-1">
                            <h4 class="font-semibold text-gray-900">{{ $task->title }}</h4>
                            <div class="text-xs text-gray-400 mt-1 flex gap-3">
                                @if($task->project)<span class="px-2 py-0.5 rounded bg-indigo-50 text-indigo-600">{{ $task->project->name }}</span>@endif
                                <span>Oleh: {{ $task->assignedMember?->name ?? '—' }}</span>
                                <span class="px-2 py-0.5 rounded {{ $task->priority === 'high' ? 'bg-red-50 text-red-600' : ($task->priority === 'medium' ? 'bg-yellow-50 text-yellow-600' : 'bg-gray-50 text-gray-500') }}">{{ ucfirst($task->priority) }}</span>
                                <span class="px-2 py-0.5 rounded {{ $task->isRevisiLocked() ? 'bg-red-100 text-red-700' : 'bg-gray-100 text-gray-600' }}">Revisi: {{ $task->revision_counter }}/{{ $task->max_revision_limit }}</span>
                            </div>
                        </div>
                        <span class="text-xs px-2 py-1 rounded-full {{ $task->status_badge_class }}">{{ $task->status_label }}</span>
                    </div>

                    @if($task->description)
                        <p class="text-sm text-gray-600 mt-3 p-3 bg-gray-50 rounded">{{ $task->description }}</p>
                    @endif

                    <div class="mt-4 flex items-end gap-3">
                        <div class="flex-1">
                            <textarea wire:model="reviewNote" placeholder="Catatan review (wajib untuk tolak)..."
                                class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 text-sm" rows="2"></textarea>
                            @error('reviewNote') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>
                        <button wire:click="showDetail({{ $task->id }})"
                            class="px-4 py-2 bg-indigo-600 text-white text-sm rounded-md hover:bg-indigo-700 whitespace-nowrap">
                            Detail
                        </button>
                        <button wire:click="approve({{ $task->id }})"
                            class="px-4 py-2 bg-green-600 text-white text-sm rounded-md hover:bg-green-700 whitespace-nowrap">
                            Setujui
                        </button>
                        <button wire:click="reject({{ $task->id }})"
                            class="px-4 py-2 bg-orange-600 text-white text-sm rounded-md hover:bg-orange-700 whitespace-nowrap">
                            Tolak
                        </button>
                    </div>
                </div>
            @empty
                @if($pending->count() === 0)
                    <div class="text-center text-gray-400 text-sm py-16">Tidak ada tugas yang menunggu review.</div>
                @endif
            @endforelse

            @if($other->count())
                <div class="mt-8">
                    <h3 class="text-lg font-semibold text-gray-700 mb-4">Semua Tugas ({{ $other->count() }})</h3>
                    <div class="space-y-2">
                        @foreach($other as $task)
                            <div class="bg-white shadow sm:rounded-lg px-5 py-3 flex items-center justify-between">
                                <div>
                                    <span class="text-sm font-medium text-gray-900">{{ $task->title }}</span>
                                    <span class="text-xs text-gray-400 ml-2">— {{ $task->assignedMember?->name ?? '—' }}</span>
                                    <span class="text-xs text-gray-400 ml-2">Revisi: {{ $task->revision_counter }}/{{ $task->max_revision_limit }}</span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <button wire:click="showDetail({{ $task->id }})"
                                        class="px-3 py-1 text-xs font-medium text-indigo-600 bg-indigo-50 rounded-md hover:bg-indigo-100">
                                        Detail
                                    </button>
                                    <span class="text-xs px-2 py-1 rounded-full {{ $task->status_badge_class }}">{{ $task->status_label }}</span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
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
                            <td class="py-2 pr-4 align-top text-xs font-semibold text-gray-500 uppercase tracking-wide">Revisi</td>
                            <td class="py-2"><span class="{{ $detailTask->isRevisiLocked() ? 'text-red-600 font-bold' : '' }}">{{ $detailTask->revision_counter }}/{{ $detailTask->max_revision_limit }}</span></td>
                        </tr>
                        <tr>
                            <td class="py-2 pr-4 align-top text-xs font-semibold text-gray-500 uppercase tracking-wide">Anggota</td>
                            <td class="py-2">{{ $detailTask->assignedMember?->name ?? '—' }}</td>
                        </tr>
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
            </div>
        </div>
    </div>
    @endif
</div>
