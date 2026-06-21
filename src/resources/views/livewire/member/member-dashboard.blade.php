<div>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('My Tasks') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if($pm)
            <div class="bg-white shadow sm:rounded-lg p-4 flex items-center justify-between">
                <div class="text-sm text-gray-600">
                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                        {{ $pm->name }}
                        <span class="text-purple-400">(Project Manager)</span>
                    </span>
                    @if($pm->phone) &middot; <span class="text-gray-400">{{ $pm->phone }}</span> @endif
                </div>
                <div x-data="{ open: false }" class="relative">
                    <button @click="open = !open" class="inline-flex items-center gap-1 px-3 py-1.5 bg-blue-50 text-blue-700 rounded-md hover:bg-blue-100 text-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                        Hubungi PM
                    </button>
                    <div x-show="open" @click.outside="open = false" class="absolute right-0 mt-1 w-48 bg-white rounded-md shadow-lg border z-10" x-transition>
                        <a href="mailto:{{ $pm->email }}?subject=Task%20Question"
                           class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                            Kirim Email
                        </a>
                        @if($pm->phone)
                            <a href="https://wa.me/{{ $pm->phone }}" target="_blank"
                               class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                WhatsApp
                            </a>
                        @endif
                    </div>
                </div>
            </div>
            @endif

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
