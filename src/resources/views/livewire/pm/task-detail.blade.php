<div>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="{{ route('pm.review-tasks') }}" class="text-sm text-indigo-600 hover:underline">&larr; Kembali</a>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Detail Tugas</h2>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="bg-white shadow sm:rounded-lg p-6">
                <div class="flex items-start justify-between">
                    <div>
                        <h3 class="text-lg font-bold text-gray-900">{{ $task->title }}</h3>
                        @if($task->project)
                            <span class="text-xs px-2 py-0.5 rounded bg-indigo-50 text-indigo-600 mt-2 inline-block">{{ $task->project->name }}</span>
                        @endif
                    </div>
                    <span class="text-xs px-2 py-1 rounded-full
                        match($task->status) { 'done' => 'bg-green-50 text-green-700', 'pending_pm' => 'bg-purple-50 text-purple-700', 'assigned_member' => 'bg-yellow-50 text-yellow-700', default => 'bg-gray-100 text-gray-500' }">
                        {{ str_replace('_', ' ', $task->status) }}
                    </span>
                </div>

                @if($task->description)
                    <p class="text-sm text-gray-600 mt-4 p-4 bg-gray-50 rounded">{{ $task->description }}</p>
                @endif

                <div class="grid grid-cols-3 gap-4 mt-4 text-sm">
                    <div><span class="text-gray-400">Anggota:</span> <span class="font-medium">{{ $task->assignedMember?->name ?? '—' }}</span></div>
                    <div><span class="text-gray-400">Prioritas:</span> <span class="font-medium">{{ ucfirst($task->priority) }}</span></div>
                    <div><span class="text-gray-400">Deadline:</span> <span class="font-medium">{{ $task->deadline?->format('d M Y') ?? '—' }}</span></div>
                </div>

                @if($task->review_note)
                    <div class="mt-4 p-3 bg-orange-50 text-orange-800 text-sm rounded">
                        <strong>Catatan Review:</strong> {{ $task->review_note }}
                    </div>
                @endif
            </div>

            <div class="bg-white shadow sm:rounded-lg p-6">
                <h4 class="font-semibold text-gray-900 mb-4">Komentar ({{ $task->comments->count() }})</h4>

                <div class="space-y-3 mb-4 max-h-80 overflow-y-auto">
                    @forelse($task->comments as $c)
                        <div class="flex gap-3">
                            <div class="w-8 h-8 rounded-full bg-indigo-100 flex items-center justify-center text-xs font-bold text-indigo-700 flex-shrink-0">
                                {{ strtoupper(substr($c->user?->name ?? '?', 0, 1)) }}
                            </div>
                            <div class="flex-1">
                                <div class="text-sm">
                                    <span class="font-medium text-gray-900">{{ $c->user?->name ?? 'Unknown' }}</span>
                                    <span class="text-xs text-gray-400 ml-2">{{ $c->created_at->diffForHumans() }}</span>
                                </div>
                                <p class="text-sm text-gray-600 mt-0.5">{{ $c->content }}</p>
                            </div>
                        </div>
                    @empty
                        <p class="text-sm text-gray-400">Belum ada komentar.</p>
                    @endforelse
                </div>

                <form wire:submit="addComment" class="flex gap-2">
                    <input wire:model="comment" type="text" placeholder="Tulis komentar..."
                        class="flex-1 border-gray-300 rounded-md shadow-sm focus:border-indigo-500 text-sm" />
                    @error('comment') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    <button type="submit" class="px-4 py-2 bg-indigo-600 text-white text-sm rounded-md hover:bg-indigo-700">Kirim</button>
                </form>
            </div>

            @if($task->statusHistories->count())
                <div class="bg-white shadow sm:rounded-lg p-6">
                    <h4 class="font-semibold text-gray-900 mb-4">Riwayat Status</h4>
                    <div class="space-y-2">
                        @foreach($task->statusHistories as $h)
                            <div class="text-sm flex gap-2 text-gray-600">
                                <span class="text-gray-400 w-24 flex-shrink-0">{{ $h->created_at->format('d M H:i') }}</span>
                                <span class="px-1.5 py-0.5 rounded bg-gray-100 text-xs">{{ $h->from_status }}</span>
                                <span>&rarr;</span>
                                <span class="px-1.5 py-0.5 rounded bg-gray-100 text-xs">{{ $h->to_status }}</span>
                                @if($h->note)
                                    <span class="text-gray-400">— {{ $h->note }}</span>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
