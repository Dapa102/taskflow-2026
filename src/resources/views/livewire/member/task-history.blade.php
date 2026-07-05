<div>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Riwayat Tugas</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-4">
            @forelse($tasks as $task)
                <div class="bg-white shadow sm:rounded-lg p-5">
                    <div class="flex items-start justify-between">
                        <div class="flex-1">
                            <h4 class="font-semibold text-gray-900">{{ $task->title }}</h4>
                            <div class="text-xs text-gray-400 mt-1 flex flex-wrap gap-3">
                                @if($task->project)
                                    <span class="px-2 py-0.5 rounded bg-indigo-50 text-indigo-600">{{ $task->project->name }}</span>
                                @endif
                                <span>{{ $task->created_at->format('d M Y') }}</span>
                                @if($task->deadline)
                                    <span>Deadline: {{ $task->deadline->format('d M Y') }}</span>
                                @endif
                            </div>
                        </div>
                        <div class="flex items-center gap-2">
                            <button wire:click="showDetail({{ $task->id }})"
                                class="px-3 py-1 text-xs font-medium text-indigo-600 bg-indigo-50 rounded-md hover:bg-indigo-100">
                                Detail
                            </button>
                            <span class="text-xs px-2 py-1 rounded-full {{ $task->status === 'done' ? 'bg-green-50 text-green-700' : 'bg-red-50 text-red-700' }}">
                                {{ $task->status === 'done' ? 'Selesai' : 'Dibatalkan' }}
                            </span>
                        </div>
                    </div>
                </div>
            @empty
                <div class="text-center text-gray-400 text-sm py-16">Belum ada riwayat tugas.</div>
            @endforelse
        </div>
    </div>

    @if($detailModal && $detailTask)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/40" wire:click.self="$set('detailModal', false)">
            <div class="bg-white rounded-xl shadow-xl max-w-2xl w-full mx-4 max-h-[80vh] overflow-y-auto">
                <div class="flex justify-between items-center p-4 border-b sticky top-0 bg-white z-10">
                    <h3 class="text-lg font-semibold text-gray-900">{{ $detailTask->title }}</h3>
                    <button wire:click="$set('detailModal', false)" class="text-gray-400 hover:text-gray-600 text-xl leading-none">&times;</button>
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
                            <span class="inline-block mt-1 px-2 py-0.5 text-xs rounded-full {{ $detailTask->status === 'done' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                {{ $detailTask->status === 'done' ? 'Selesai' : 'Dibatalkan' }}
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
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
