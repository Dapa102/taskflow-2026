<div>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Project Manager Dashboard') }}
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

            @if(!$workspace)
                <!-- Create Workspace Form -->
                <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Create Workspace</h3>
                    <form wire:submit="createWorkspace" class="space-y-4 max-w-xl">
                        <div>
                            <x-input-label for="workspaceName" value="Workspace Name" />
                            <x-text-input id="workspaceName" wire:model="workspaceName" type="text" class="mt-1 block w-full" required />
                            <x-input-error :messages="$errors->get('workspaceName')" class="mt-2" />
                        </div>
                        <div>
                            <x-input-label for="workspaceDesc" value="Description" />
                            <textarea wire:model="workspaceDesc" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"></textarea>
                            <x-input-error :messages="$errors->get('workspaceDesc')" class="mt-2" />
                        </div>
                        <x-primary-button>Create Workspace</x-primary-button>
                    </form>
                </div>
            @else
                <!-- Stats -->
                <div class="grid grid-cols-3 gap-4">
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                        <div class="text-gray-500">Total Tasks</div>
                        <div class="text-2xl font-bold">{{ $stats['total'] }}</div>
                    </div>
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                        <div class="text-gray-500">Done</div>
                        <div class="text-2xl font-bold text-green-600">{{ $stats['done'] }}</div>
                    </div>
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                        <div class="text-gray-500">Overdue</div>
                        <div class="text-2xl font-bold text-red-600">{{ $stats['overdue'] }}</div>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <!-- Members Column -->
                    <div class="md:col-span-1 space-y-6">
                        <div class="p-4 bg-white shadow sm:rounded-lg">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Team Members</h3>
                            
                            <form wire:submit="inviteMember" class="flex gap-2 mb-4">
                                <x-text-input wire:model="inviteEmail" type="email" placeholder="Member email" class="w-full" required />
                                <x-primary-button>Add</x-primary-button>
                            </form>
                            <x-input-error :messages="$errors->get('inviteEmail')" class="mb-4" />

                            <ul class="space-y-2">
                                <li class="flex items-center text-sm p-2 bg-purple-50 rounded border border-purple-200">
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                                        {{ auth()->user()->name }}
                                        <span class="text-purple-400">(Project Manager)</span>
                                    </span>
                                </li>
                                @forelse($members as $member)
                                    <li class="flex justify-between items-center text-sm p-2 bg-gray-50 rounded">
                                        <div class="flex items-center gap-2">
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-700">
                                                {{ $member->name }}
                                                <span class="text-gray-400">(Anggota)</span>
                                            </span>
                                            @if($member->phone)
                                                <span class="text-gray-400 text-xs">{{ $member->phone }}</span>
                                            @endif
                                        </div>
                                        <div class="flex items-center gap-2">
                                            <div x-data="{ open: false }" class="relative">
                                                <button @click="open = !open" class="text-blue-600 hover:text-blue-800 text-xs font-medium">
                                                    Hubungi
                                                </button>
                                                <div x-show="open" @click.outside="open = false" class="absolute right-0 mt-1 w-40 bg-white rounded-md shadow-lg border z-10" x-transition>
                                                    <a href="{{ route('pm.compose.email') }}?recipient={{ $member->id }}"
                                                       class="block px-3 py-2 text-xs text-gray-700 hover:bg-gray-100">
                                                        Kirim Email
                                                    </a>
                                                    @if($member->phone)
                                                        <a href="https://wa.me/{{ $member->phone }}" target="_blank"
                                                           class="block px-3 py-2 text-xs text-gray-700 hover:bg-gray-100">
                                                            WhatsApp
                                                        </a>
                                                    @endif
                                                </div>
                                            </div>
                                            <button wire:click="removeMember({{ $member->id }})" wire:confirm="Remove member?" class="text-red-500 hover:text-red-700">Remove</button>
                                        </div>
                                    </li>
                                @empty
                                    <li class="text-sm text-gray-500">No members yet.</li>
                                @endforelse
                            </ul>
                        </div>
                    </div>

                    <!-- Tasks Column -->
                    <div class="md:col-span-2 space-y-6">
                        <!-- Create Task -->
                        <div class="p-4 bg-white shadow sm:rounded-lg">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Assign New Task</h3>
                            <form wire:submit="createTask" class="space-y-4">
                                <div class="grid grid-cols-2 gap-4">
                                    <div class="col-span-2">
                                        <x-text-input wire:model="taskTitle" type="text" placeholder="Task Title" class="w-full" required />
                                        <x-input-error :messages="$errors->get('taskTitle')" />
                                    </div>
                                    <div>
                                        <select wire:model="taskAssignee" class="w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                                            <option value="">Select Assignee...</option>
                                            @foreach($members as $member)
                                                <option value="{{ $member->id }}">{{ $member->name }}</option>
                                            @endforeach
                                        </select>
                                        <x-input-error :messages="$errors->get('taskAssignee')" />
                                    </div>
                                    <div>
                                        <select wire:model="taskPriority" class="w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                            <option value="low">Low</option>
                                            <option value="medium">Medium</option>
                                            <option value="high">High</option>
                                        </select>
                                    </div>
                                    <div>
                                        <x-text-input wire:model="taskDeadline" type="date" class="w-full" />
                                        <x-input-error :messages="$errors->get('taskDeadline')" />
                                    </div>
                                    <div class="flex items-end">
                                        <x-primary-button>Create Task</x-primary-button>
                                    </div>
                                </div>
                            </form>
                        </div>

                        <!-- Task List -->
                        <div class="p-4 bg-white shadow sm:rounded-lg">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Team Tasks</h3>
                            <div class="space-y-4">
                                @forelse($tasks as $task)
                                    <div class="p-4 border rounded-lg flex justify-between items-start {{ $task->status === 'done' ? 'bg-gray-50' : 'bg-white' }}">
                                        <div>
                                            <div class="font-medium {{ $task->status === 'done' ? 'line-through text-gray-500' : '' }}">
                                                {{ $task->title }}
                                            </div>
                                            <div class="text-sm text-gray-500 mt-1">
                                                Assignee: {{ $task->assignee->name ?? 'Unknown' }} | 
                                                Priority: {{ ucfirst($task->priority) }} | 
                                                Status: <span class="font-semibold">{{ strtoupper($task->status) }}</span>
                                            </div>
                                            @if($task->deadline)
                                                <div class="text-sm {{ $task->deadline < now() && $task->status != 'done' ? 'text-red-500 font-bold' : 'text-gray-500' }}">
                                                    Deadline: {{ $task->deadline->format('Y-m-d') }}
                                                </div>
                                            @endif
                                        </div>
                                        <button wire:click="deleteTask({{ $task->id }})" wire:confirm="Delete task?" class="text-red-500 hover:text-red-700 text-sm">Delete</button>
                                    </div>
                                @empty
                                    <div class="text-gray-500 text-sm">No tasks created yet.</div>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
