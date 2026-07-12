<div>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Late Tasks Report') }}
            </h2>
            <a href="{{ route('super-admin.dashboard') }}" class="text-sm text-gray-500 hover:underline">Back to Dashboard</a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="bg-white shadow sm:rounded-lg p-4">
                <form class="flex flex-wrap gap-4 items-end">
                    <div>
                        <label class="block text-xs font-medium text-gray-500 mb-1">Cari</label>
                        <input type="text" wire:model.live="search" placeholder="Cari tugas, PM, atau Member..." class="rounded-md border-gray-300 text-sm shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 w-64">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-500 mb-1">Workspace</label>
                        <select wire:model.live="workspaceFilter" class="rounded-md border-gray-300 text-sm shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                            <option value="">Semua Workspace</option>
                            @foreach($workspaces as $w)
                                <option value="{{ $w->id }}">{{ $w->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-500 mb-1">Deadline Dari</label>
                        <input type="date" wire:model.live="startDate" class="rounded-md border-gray-300 text-sm shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-500 mb-1">Deadline Sampai</label>
                        <input type="date" wire:model.live="endDate" class="rounded-md border-gray-300 text-sm shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                    </div>
                    <div>
                        <button wire:click="exportPdf" class="inline-flex items-center gap-2 px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 text-sm">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            Export PDF
                        </button>
                    </div>
                </form>
            </div>

            <div class="bg-white shadow sm:rounded-lg p-6">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Task</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Workspace</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">PM</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Member</th>
                                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Deadline</th>
                                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Terlambat</th>
                                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($tasks as $task)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">{{ $task->title }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $task->workspace?->name ?? '-' }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $task->assignedPm?->name ?? '-' }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $task->assignedMember?->name ?? '-' }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-center text-sm">{{ $task->deadline->format('d M Y') }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                        {{ $task->deadline->diffInDays(now()) }} hari
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                        {{ str_replace('_', ' ', ucfirst($task->status)) }}
                                    </span>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="px-6 py-4 text-center text-gray-500">No late tasks found.</td>
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
