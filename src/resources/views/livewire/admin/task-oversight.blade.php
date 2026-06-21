<div>
    <x-slot name="header">
        <div class="flex space-x-4 items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Global Task Oversight') }}
            </h2>
            <a href="{{ route('admin.dashboard') }}" class="text-sm text-gray-500 hover:underline">Back to Dashboard</a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
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
                                    <div class="text-xs text-gray-500">PM: {{ $task->creator->name ?? 'N/A' }}</div>
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
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="px-6 py-4 text-center text-gray-500">No tasks found matching criteria.</td>
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
</div>
