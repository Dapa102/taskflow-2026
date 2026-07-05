<div>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Tugas Saya</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-4">
            @if(session('message'))
                <div class="p-4 text-sm text-green-800 rounded-lg bg-green-50">{{ session('message') }}</div>
            @endif
            @if(session('error'))
                <div class="p-4 text-sm text-red-800 rounded-lg bg-red-50">{{ session('error') }}</div>
            @endif

            @forelse($tasks as $task)
                <div class="bg-white shadow sm:rounded-lg p-6">
                    <div class="flex items-start justify-between">
                        <div class="flex-1">
                            <h4 class="font-semibold text-gray-900">{{ $task->title }}</h4>
                            @if($task->description)
                                <p class="text-sm text-gray-500 mt-1">{{ Str::limit($task->description, 100) }}</p>
                            @endif
                            <div class="text-xs text-gray-400 mt-2 flex flex-wrap gap-3">
                                @if($task->project)
                                    <span class="px-2 py-0.5 rounded bg-indigo-50 text-indigo-600">{{ $task->project->name }}</span>
                                @endif
                                <span class="px-2 py-0.5 rounded {{ $task->priority === 'high' ? 'bg-red-50 text-red-600' : ($task->priority === 'medium' ? 'bg-yellow-50 text-yellow-600' : 'bg-gray-50 text-gray-500') }}">
                                    {{ ucfirst($task->priority) }}
                                </span>
                                @if($task->deadline)
                                    <span class="flex items-center gap-1">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                        {{ $task->deadline->format('d M Y') }}
                                    </span>
                                @endif
                                @if($task->status === 'revision' && $task->review_note)
                                    <span class="px-2 py-0.5 rounded bg-orange-50 text-orange-600">Catatan: {{ Str::limit($task->review_note, 50) }}</span>
                                @endif
                                @if($task->attachments->count())
                                    <span class="text-gray-400">{{ $task->attachments->count() }} lampiran</span>
                                @endif
                            </div>
                        </div>
                        <div class="flex items-start gap-2 ml-4">
                            <button wire:click="showDetail({{ $task->id }})"
                                class="px-3 py-1 text-xs font-medium text-indigo-600 bg-indigo-50 rounded-md hover:bg-indigo-100">
                                Detail
                            </button>
                            @if(in_array($task->status, ['assigned_member', 'revision']))
                                <button wire:click="submitTask({{ $task->id }})"
                                    wire:confirm="Kirim tugas ini untuk direview PM?"
                                    class="px-3 py-1 text-xs font-medium bg-indigo-600 text-white rounded-md hover:bg-indigo-700">
                                    Ajukan Review
                                </button>
                            @elseif($task->status === 'pending_pm')
                                <span class="text-xs px-2 py-1 rounded-full bg-purple-50 text-purple-700">Review PM</span>
                            @elseif($task->status === 'pending_admin')
                                <span class="text-xs px-2 py-1 rounded-full bg-indigo-50 text-indigo-700">Approval</span>
                            @elseif($task->status === 'pending_arbitration')
                                <span class="text-xs px-2 py-1 rounded-full bg-red-50 text-red-700">Arbitrase</span>
                            @elseif($task->status === 'done')
                                <span class="text-xs px-2 py-1 rounded-full bg-green-50 text-green-700">Selesai</span>
                            @else
                                <span class="text-xs px-2 py-1 rounded-full bg-gray-100 text-gray-500">{{ str_replace('_', ' ', $task->status) }}</span>
                            @endif
                        </div>
                    </div>

                    @if(in_array($task->status, ['assigned_member', 'revision']))
                        <div class="mt-4 pt-4 border-t border-gray-100">
                            <label class="text-xs font-medium text-gray-500 block mb-1">Upload Hasil Pengerjaan</label>
                            <div class="flex items-center gap-3">
                                <input type="file" wire:model="upload.{{ $task->id }}"
                                    class="block w-full text-xs text-gray-500 file:mr-3 file:py-1.5 file:px-3 file:rounded-md file:border-0 file:text-xs file:font-medium file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                                @error("upload.{$task->id}")
                                    <span class="text-red-500 text-xs">{{ $message }}</span>
                                @enderror
                                <div wire:loading wire:target="upload.{{ $task->id }}" class="text-xs text-gray-400">Uploading...</div>
                            </div>
                        </div>
                    @endif
                </div>
            @empty
                <div class="text-center text-gray-400 text-sm py-16">Belum ada tugas yang ditugaskan ke Anda.</div>
            @endforelse
        </div>
    </div>

    @if($detailModal && count($detailTasks))
        @php $task = $detailTasks[0]; @endphp
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
                        @if($task->priority)
                            <div>
                                <h4 class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Prioritas</h4>
                                <span class="inline-block mt-1 px-2 py-0.5 text-xs rounded-full {{ $task->priority === 'high' ? 'bg-red-50 text-red-600' : ($task->priority === 'medium' ? 'bg-yellow-50 text-yellow-600' : 'bg-gray-50 text-gray-500') }}">
                                    {{ ucfirst($task->priority) }}
                                </span>
                            </div>
                        @endif
                        @if($task->deadline)
                            <div>
                                <h4 class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Deadline</h4>
                                <p class="mt-1">{{ $task->deadline->format('d M Y') }}</p>
                            </div>
                        @endif
                        @if($task->assignedPm)
                            <div>
                                <h4 class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Project Manager</h4>
                                <p class="mt-1">{{ $task->assignedPm->name }}</p>
                            </div>
                        @endif
                        @if($task->review_note)
                            <div class="col-span-2">
                                <h4 class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Catatan Review</h4>
                                <p class="mt-1 p-2 bg-orange-50 rounded text-sm text-orange-800">{{ $task->review_note }}</p>
                            </div>
                        @endif
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
