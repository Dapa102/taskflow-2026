<div>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Task Saya</h2>
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
                <div class="bg-white shadow sm:rounded-lg p-6 border-l-4 {{ $task->status === 'done' ? 'border-l-green-500' : ($task->status === 'in_progress' ? 'border-l-yellow-500' : ($task->status === 'pending_review' ? 'border-l-purple-500' : 'border-l-gray-300')) }}">
                    <div class="flex items-start justify-between">
                        <div class="flex-1">
                            <h4 class="font-semibold text-gray-900">{{ $task->title }}</h4>
                            @if($task->description)
                                <p class="text-sm text-gray-500 mt-1">{{ $task->description }}</p>
                            @endif
                            <div class="text-xs text-gray-400 mt-2 flex gap-3">
                                @if($task->project)
                                    <span class="px-2 py-0.5 rounded bg-indigo-50 text-indigo-600">{{ $task->project->name }}</span>
                                @endif
                                <span class="px-2 py-0.5 rounded {{ $task->priority === 'high' ? 'bg-red-50 text-red-600' : ($task->priority === 'medium' ? 'bg-yellow-50 text-yellow-600' : 'bg-gray-50 text-gray-500') }}">
                                    {{ ucfirst($task->priority) }}
                                </span>
                                @if($task->deadline)
                                    <span>Deadline: {{ $task->deadline->format('d M Y') }}</span>
                                @endif
                            </div>
                        </div>
                        <div class="flex items-center gap-2">
                            @if($task->status === 'assigned_member')
                                <button wire:click="updateStatus({{ $task->id }}, 'in_progress')"
                                    class="px-3 py-1 text-xs font-medium bg-yellow-600 text-white rounded-md hover:bg-yellow-700">
                                    Mulai
                                </button>
                            @elseif($task->status === 'in_progress')
                                <button wire:click="updateStatus({{ $task->id }}, 'pending_review')"
                                    class="px-3 py-1 text-xs font-medium bg-purple-600 text-white rounded-md hover:bg-purple-700">
                                    Ajukan Review
                                </button>
                            @elseif($task->status === 'pending_review')
                                <span class="text-xs px-2 py-1 rounded-full bg-purple-50 text-purple-700">Menunggu Review</span>
                            @elseif($task->status === 'done')
                                <span class="text-xs px-2 py-1 rounded-full bg-green-50 text-green-700">Selesai</span>
                            @else
                                <span class="text-xs px-2 py-1 rounded-full bg-gray-100 text-gray-500">{{ str_replace('_', ' ', $task->status) }}</span>
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <div class="text-center text-gray-400 text-sm py-16">Belum ada tugas yang ditugaskan ke Anda.</div>
            @endforelse
        </div>
    </div>
</div>
