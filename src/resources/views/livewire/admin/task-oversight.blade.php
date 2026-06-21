<div>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Global Task Oversight') }}
        </h2>
    </x-slot>

    <div class="py-6 px-6">
        <div class="max-w-7xl mx-auto space-y-6">

            <div class="bg-white shadow sm:rounded-lg p-6">
                <div class="flex justify-between mb-6">
                    <div class="flex space-x-2">
                        <select wire:model.live="statusFilter" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                            <option value="all">All Tasks</option>
                            <option value="pending">Pending (To-Do/On Progress)</option>
                            <option value="done">Done</option>
                            <option value="overdue">Overdue</option>
                        </select>
                    </div>
                    <div>
                        <x-text-input wire:model.live.debounce.300ms="search" type="text" placeholder="Search tasks or workspace..." />
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Task</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Workspace</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Assignee</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Deadline</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Action</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($tasks as $task)
                            <tr>
                                <td class="px-6 py-4">
                                    <div class="text-sm font-medium text-gray-900">{{ $task->title }}</div>
                                    <div class="text-xs text-gray-500 truncate max-w-xs">{{ $task->description }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">{{ $task->workspace->name ?? 'N/A' }}</div>
                                    <div class="text-xs text-gray-500">Creator: {{ $task->creator->name ?? 'N/A' }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $task->assignee->name ?? 'Unassigned' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                        {{ $task->status === 'done' ? 'bg-green-100 text-green-800' : 
                                           ($task->status === 'on_progress' ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-800') }}">
                                        {{ strtoupper(str_replace('_', ' ', $task->status)) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                    @if($task->deadline)
                                        <span class="{{ $task->deadline < now() && $task->status != 'done' ? 'text-red-600 font-bold' : 'text-gray-500' }}">
                                            {{ $task->deadline->format('Y-m-d') }}
                                        </span>
                                    @else
                                        <span class="text-gray-400">-</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                    <button wire:click="viewDetail({{ $task->id }})" class="text-indigo-600 hover:text-indigo-900 font-medium">
                                        Detail
                                    </button>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="px-6 py-4 text-center text-gray-500">No tasks found.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="mt-4">
                    {{ $tasks->links() }}
                </div>
            </div>

        </div>
    </div>

    <!-- Detail Modal -->
    @if($detailTask)
    <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50" wire:click.self="closeDetail">
        <div class="bg-white rounded-lg shadow-xl w-full max-w-2xl mx-4 max-h-[90vh] overflow-y-auto">
            <div class="p-6">
                <div class="flex justify-between items-start mb-4">
                    <h3 class="text-lg font-semibold text-gray-900">{{ $detailTask->title }}</h3>
                    <button wire:click="closeDetail" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <div class="space-y-4">
                    <div>
                        <label class="text-xs text-gray-500 uppercase font-semibold">Description</label>
                        <p class="text-sm text-gray-700">{{ $detailTask->description ?? '—' }}</p>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="text-xs text-gray-500 uppercase font-semibold">Status</label>
                            <p class="text-sm font-medium">{{ strtoupper(str_replace('_', ' ', $detailTask->status)) }}</p>
                        </div>
                        <div>
                            <label class="text-xs text-gray-500 uppercase font-semibold">Priority</label>
                            <p class="text-sm font-medium">{{ ucfirst($detailTask->priority) }}</p>
                        </div>
                        <div>
                            <label class="text-xs text-gray-500 uppercase font-semibold">Deadline</label>
                            <p class="text-sm">{{ $detailTask->deadline?->format('Y-m-d') ?? '—' }}</p>
                        </div>
                        <div>
                            <label class="text-xs text-gray-500 uppercase font-semibold">Workspace</label>
                            <p class="text-sm">{{ $detailTask->workspace->name ?? '—' }}</p>
                        </div>
                        <div>
                            <label class="text-xs text-gray-500 uppercase font-semibold">Creator</label>
                            <p class="text-sm">{{ $detailTask->creator->name ?? '—' }}</p>
                        </div>
                        <div>
                            <label class="text-xs text-gray-500 uppercase font-semibold">Current Assignee</label>
                            <p class="text-sm">{{ $detailTask->assignee->name ?? 'Unassigned' }}</p>
                        </div>
                    </div>

                    <!-- Attachments -->
                    <div>
                        <label class="text-xs text-gray-500 uppercase font-semibold">Files</label>
                        @if($detailTask->attachments->isEmpty())
                            <p class="text-sm text-gray-400">No files uploaded.</p>
                        @else
                            <div class="mt-1 space-y-1">
                                @foreach($detailTask->attachments as $file)
                                <div class="flex items-center gap-2 text-sm">
                                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                    <a href="{{ $file->url }}" target="_blank" class="text-indigo-600 hover:underline">{{ $file->filename }}</a>
                                    <span class="text-xs text-gray-400">({{ $file->human_file_size }})</span>
                                </div>
                                @endforeach
                            </div>
                        @endif
                    </div>

                    <!-- Assign to PM -->
                    <div class="border-t pt-4">
                        <label class="text-xs text-gray-500 uppercase font-semibold">Assign to PM</label>
                        <div class="mt-1 flex gap-2">
                            <select id="pm-select-{{ $detailTask->id }}" class="flex-1 border-gray-300 rounded-md shadow-sm text-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="">Select PM...</option>
                                @foreach($pms as $pm)
                                    <option value="{{ $pm->id }}">{{ $pm->name }}</option>
                                @endforeach
                            </select>
                            <button onclick="assignPm({{ $detailTask->id }})" class="px-4 py-2 bg-indigo-600 text-white rounded-md text-sm hover:bg-indigo-700">
                                Assign
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function assignPm(taskId) {
            const select = document.getElementById('pm-select-' + taskId);
            const pmId = select.value;
            if (!pmId) { alert('Select a PM first.'); return; }
            @this.call('assignToPm', taskId, parseInt(pmId));
        }
    </script>
    @endif
</div>
