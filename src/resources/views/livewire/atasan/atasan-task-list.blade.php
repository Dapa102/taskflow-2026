<div>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Tugas Saya</h2>
    </x-slot>

    <div class="py-6 px-6">
        <div class="max-w-7xl mx-auto space-y-6">

            <div class="flex justify-between items-center">
                <select wire:model.live="statusFilter" class="border-gray-300 rounded-md shadow-sm text-sm">
                    <option value="all">Semua</option>
                    <option value="pending">Belum Diberikan</option>
                    <option value="given">Sudah Diberikan</option>
                    <option value="done">Selesai</option>
                </select>
                <input type="text" wire:model.live.debounce.300ms="search" placeholder="Cari tugas..." class="border-gray-300 rounded-md shadow-sm text-sm">
            </div>

            <div class="bg-white shadow sm:rounded-lg overflow-hidden">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tugas</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Workspace</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Assignee</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Priority</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Deadline</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($tasks as $task)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3">
                                <div class="text-sm font-medium text-gray-900">{{ $task->title }}</div>
                                <div class="text-xs text-gray-500 truncate max-w-[200px]">{{ $task->description }}</div>
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-500">{{ $task->workspace->name ?? '-' }}</td>
                            <td class="px-4 py-3 text-sm text-gray-500">{{ $task->assignee->name ?? 'Belum ditugaskan' }}</td>
                            <td class="px-4 py-3">
                                @if(is_null($task->assigned_to))
                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">Belum Diberikan</span>
                                @elseif($task->status === 'done')
                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">Selesai</span>
                                @else
                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">Sudah Diberikan</span>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                <span class="text-xs font-medium px-2 py-1 rounded {{ $task->priority === 'high' ? 'bg-red-50 text-red-700' : ($task->priority === 'medium' ? 'bg-yellow-50 text-yellow-700' : 'bg-gray-50 text-gray-600') }}">
                                    {{ $task->priority === 'high' ? 'Tinggi' : ($task->priority === 'medium' ? 'Sedang' : 'Rendah') }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-sm {{ $task->isOverdue() ? 'text-red-600 font-bold' : 'text-gray-500' }}">
                                {{ $task->deadline?->format('Y-m-d') ?? '-' }}
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="6" class="px-4 py-8 text-center text-gray-400">Belum ada tugas.</td></tr>
                        @endforelse
                    </tbody>
                </table>
                <div class="p-4">{{ $tasks->links() }}</div>
            </div>

        </div>
    </div>
</div>
