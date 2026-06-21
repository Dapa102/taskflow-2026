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
                <div class="grid grid-cols-4 gap-4">
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                        <div class="text-gray-500">Total Tasks</div>
                        <div class="text-2xl font-bold">{{ $stats['total'] }}</div>
                    </div>
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                        <div class="text-gray-500">Done</div>
                        <div class="text-2xl font-bold text-green-600">{{ $stats['done'] }}</div>
                    </div>
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                        <div class="text-gray-500">Menunggu Review</div>
                        <div class="text-2xl font-bold text-yellow-600">{{ $stats['pending_review'] }}</div>
                    </div>
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                        <div class="text-gray-500">Revisi</div>
                        <div class="text-2xl font-bold text-orange-600">{{ $stats['revision'] }}</div>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
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
                                                @php
                                                    $teamMember = \App\Models\TeamMember::where('user_id', $member->id)->first();
                                                @endphp
                                                @if($teamMember && $teamMember->team)
                                                    <span class="text-gray-400">({{ $teamMember->team->name }} - Anggota)</span>
                                                @else
                                                    <span class="text-gray-400">(Anggota)</span>
                                                @endif
                                            </span>
                                            @if($member->phone)
                                                <span class="text-gray-400 text-xs">{{ $member->phone }}</span>
                                            @endif
                                        </div>
                                        <button wire:click="removeMember({{ $member->id }})" wire:confirm="Remove member?" class="text-red-500 hover:text-red-700 text-xs">Remove</button>
                                    </li>
                                @empty
                                    <li class="text-sm text-gray-500">No members yet.</li>
                                @endforelse
                            </ul>
                        </div>
                    </div>

                    <div class="md:col-span-2 space-y-6">
                        <div class="p-4 bg-white shadow sm:rounded-lg">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Daftar Tugas Team</h3>
                            <div class="space-y-4">
                                @forelse($tasks as $task)
                                    <div class="p-4 border rounded-lg {{ $task->status === 'done' ? 'bg-gray-50' : ($task->status === 'pending_pm' ? 'border-yellow-300 bg-yellow-50' : ($task->status === 'revision' ? 'border-orange-300 bg-orange-50' : 'bg-white')) }}">
                                        <div class="flex justify-between items-start">
                                            <div class="flex-1">
                                                <div class="font-medium {{ $task->status === 'done' ? 'line-through text-gray-500' : '' }}">
                                                    {{ $task->title }}
                                                </div>
                                                <div class="text-sm text-gray-500 mt-1">
                                                    Dibuat oleh: {{ $task->creator?->name ?? 'System' }} |
                                                    Assignee: <span class="font-semibold">{{ $task->assignee?->name ?? 'Unassigned' }}</span> |
                                                    Priority: {{ ucfirst($task->priority) }}
                                                </div>
                                                <div class="text-xs mt-1">
                                                    Status:
                                                    <span class="font-semibold
                                                        {{ $task->status === 'done' ? 'text-green-600' : '' }}
                                                        {{ $task->status === 'on_progress' ? 'text-blue-600' : '' }}
                                                        {{ $task->status === 'pending_pm' ? 'text-yellow-600' : '' }}
                                                        {{ $task->status === 'pending_admin' ? 'text-purple-600' : '' }}
                                                        {{ $task->status === 'revision' ? 'text-orange-600' : '' }}
                                                        {{ $task->status === 'todo' ? 'text-gray-600' : '' }}">
                                                        {{ $task->status === 'done' ? 'Selesai' : ($task->status === 'on_progress' ? 'Dikerjakan' : ($task->status === 'pending_pm' ? 'Menunggu Review PM' : ($task->status === 'pending_admin' ? 'Menunggu Review Admin' : ($task->status === 'revision' ? 'Revisi' : 'Menunggu')))) }}
                                                    </span>
                                                </div>
                                                @if($task->deadline)
                                                    <div class="text-sm {{ $task->isOverdue() ? 'text-red-500 font-bold' : 'text-gray-500' }}">
                                                        Deadline: {{ $task->deadline->format('Y-m-d') }}
                                                    </div>
                                                @endif
                                                @if($task->review_note)
                                                    <div class="mt-1 text-xs text-orange-600 bg-orange-50 p-1 rounded">
                                                        Catatan Revisi: {{ $task->review_note }}
                                                    </div>
                                                @endif
                                                @if($task->attachments->count() > 0)
                                                    <div class="mt-1 text-xs text-gray-500">
                                                        File: {{ $task->attachments->count() }} lampiran
                                                    </div>
                                                @endif
                                            </div>

                                            <div class="flex items-center gap-2 shrink-0 ml-4">
                                                @if($task->status === 'todo' || $task->status === 'on_progress')
                                                    <button wire:click="$set('assignTaskId', {{ $task->id }})"
                                                        class="px-3 py-1 text-xs bg-indigo-600 text-white rounded-md hover:bg-indigo-700">
                                                        Assign
                                                    </button>
                                                @endif
                                                @if($task->status === 'pending_pm')
                                                    <button wire:click="approveTask({{ $task->id }})"
                                                        wire:confirm="Approve this task? It will be sent to admin."
                                                        class="px-3 py-1 text-xs bg-green-600 text-white rounded-md hover:bg-green-700">
                                                        Approve
                                                    </button>
                                                    <button wire:click="$set('rejectTaskId', {{ $task->id }})"
                                                        class="px-3 py-1 text-xs bg-orange-600 text-white rounded-md hover:bg-orange-700">
                                                        Revisi
                                                    </button>
                                                @endif
                                            </div>
                                        </div>

                                        @if($assignTaskId === $task->id)
                                            <div class="mt-3 p-3 bg-gray-50 rounded-md border">
                                                <select wire:model="assignMemberId" class="w-full border-gray-300 rounded-md shadow-sm text-sm mb-2">
                                                    <option value="">Pilih Anggota...</option>
                                                    @foreach($members as $m)
                                                        <option value="{{ $m->id }}">{{ $m->name }}</option>
                                                    @endforeach
                                                </select>
                                                <div class="flex gap-2">
                                                    <button wire:click="assignToMember({{ $task->id }})" class="px-3 py-1 text-xs bg-indigo-600 text-white rounded-md hover:bg-indigo-700">Simpan</button>
                                                    <button wire:click="$set('assignTaskId', null)" class="px-3 py-1 text-xs text-gray-600 hover:text-gray-900">Batal</button>
                                                </div>
                                            </div>
                                        @endif

                                        @if($rejectTaskId === $task->id)
                                            <div class="mt-3 p-3 bg-orange-50 rounded-md border border-orange-200">
                                                <textarea wire:model="reviewNote" placeholder="Catatan revisi..." rows="2" class="w-full border-gray-300 rounded-md shadow-sm text-sm mb-2"></textarea>
                                                @error('reviewNote') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                                <div class="flex gap-2">
                                                    <button wire:click="rejectTask({{ $task->id }})" class="px-3 py-1 text-xs bg-orange-600 text-white rounded-md hover:bg-orange-700">Kirim Revisi</button>
                                                    <button wire:click="$set('rejectTaskId', null)" class="px-3 py-1 text-xs text-gray-600 hover:text-gray-900">Batal</button>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                @empty
                                    <div class="text-gray-500 text-sm">Belum ada tugas.</div>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
