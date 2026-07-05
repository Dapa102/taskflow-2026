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
                $pending = $tasks->where('status', 'pending_review');
                $other = $tasks->where('status', '!=', 'pending_review');
            @endphp

            @if($pending->count())
                <h3 class="text-lg font-semibold text-yellow-600">Menunggu Review ({{ $pending->count() }})</h3>
            @endif

            @forelse($pending as $task)
                <div class="bg-white shadow sm:rounded-lg p-6 border-l-4 border-l-purple-500">
                    <div class="flex items-start justify-between">
                        <div class="flex-1">
                            <h4 class="font-semibold text-gray-900">{{ $task->title }}</h4>
                            <div class="text-xs text-gray-400 mt-1 flex gap-3">
                                @if($task->project)<span class="px-2 py-0.5 rounded bg-indigo-50 text-indigo-600">{{ $task->project->name }}</span>@endif
                                <span>Oleh: {{ $task->assignedMember?->name ?? '—' }}</span>
                                <span class="px-2 py-0.5 rounded {{ $task->priority === 'high' ? 'bg-red-50 text-red-600' : ($task->priority === 'medium' ? 'bg-yellow-50 text-yellow-600' : 'bg-gray-50 text-gray-500') }}">{{ ucfirst($task->priority) }}</span>
                            </div>
                        </div>
                        <span class="text-xs px-2 py-1 rounded-full bg-purple-50 text-purple-700">Pending Review</span>
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
                                </div>
                                <span class="text-xs px-2 py-1 rounded-full
                                    {{ $task->status === 'done' ? 'bg-green-50 text-green-700' : ($task->status === 'assigned_member' ? 'bg-blue-50 text-blue-700' : ($task->status === 'in_progress' ? 'bg-yellow-50 text-yellow-700' : 'bg-gray-100 text-gray-500')) }}">
                                    {{ str_replace('_', ' ', $task->status) }}
                                </span>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
