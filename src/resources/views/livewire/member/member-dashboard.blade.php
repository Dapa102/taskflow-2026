<div>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('My Tasks') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            @if (session()->has('message'))
                <div class="p-4 mb-4 text-sm text-green-800 rounded-lg bg-green-50">{{ session('message') }}</div>
            @endif
            @if (session()->has('error'))
                <div class="p-4 mb-4 text-sm text-red-800 rounded-lg bg-red-50">{{ session('error') }}</div>
            @endif

            <div class="bg-white shadow sm:rounded-lg p-6">
                <div class="space-y-4">
                    @forelse($tasks as $task)
                        <div class="p-4 border rounded-lg flex justify-between items-center {{ $task->status === 'done' ? 'bg-gray-50' : 'bg-white' }}">
                            <div>
                                <div class="font-medium {{ $task->status === 'done' ? 'line-through text-gray-500' : '' }}">
                                    {{ $task->title }}
                                </div>
                                <div class="text-sm text-gray-500 mt-1">
                                    Workspace: {{ $task->workspace->name ?? 'Unknown' }} | 
                                    Priority: {{ ucfirst($task->priority) }}
                                </div>
                                @if($task->deadline)
                                    <div class="text-sm {{ $task->deadline < now() && $task->status != 'done' ? 'text-red-500 font-bold' : 'text-gray-500' }}">
                                        Deadline: {{ $task->deadline->format('Y-m-d') }}
                                    </div>
                                @endif
                            </div>
                            
                            <div class="flex items-center gap-2">
                                <span class="text-sm font-medium text-gray-500 mr-2">Status:</span>
                                <select 
                                    wire:change="updateStatus({{ $task->id }}, $event.target.value)"
                                    class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm text-sm"
                                >
                                    <option value="todo" {{ $task->status === 'todo' ? 'selected' : '' }}>To-Do</option>
                                    <option value="on_progress" {{ $task->status === 'on_progress' ? 'selected' : '' }}>On Progress</option>
                                    <option value="done" {{ $task->status === 'done' ? 'selected' : '' }}>Done</option>
                                </select>
                            </div>
                        </div>
                    @empty
                        <div class="text-center text-gray-500 py-8">
                            You don't have any tasks assigned yet.
                        </div>
                    @endforelse
                </div>
            </div>

        </div>
    </div>
</div>
