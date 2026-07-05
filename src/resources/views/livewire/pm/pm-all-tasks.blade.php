<div>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Semua Tugas</h2>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <div class="flex justify-between items-center">
                <select wire:model.live="statusFilter" class="border-gray-300 rounded-md shadow-sm text-sm">
                    <option value="all">Semua</option>
                    <option value="pending">Pending</option>
                    <option value="done">Selesai</option>
                    <option value="overdue">Terlambat</option>
                </select>
                <input type="text" wire:model.live.debounce.300ms="search" placeholder="Cari tugas..." class="border-gray-300 rounded-md shadow-sm text-sm">
            </div>

            <div class="bg-white shadow sm:rounded-lg overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tugas</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Anggota</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Dibuat Oleh</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Prioritas</th>
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
                                <td class="px-4 py-3 text-sm text-gray-500">{{ $task->assignedMember?->name ?? '-' }}</td>
                                <td class="px-4 py-3 text-sm text-gray-500">{{ $task->creator?->name ?? '-' }}</td>
                                <td class="px-4 py-3">
                                    @php
                                        $statusLabel = match($task->status) {
                                            'assigned_pm' => 'Dikirim ke PM',
                                            'assigned_member' => 'Dikerjakan',
                                            'pending_pm' => 'Menunggu Review',
                                            'pending_admin' => 'Menunggu Admin',
                                            'revision' => 'Revisi',
                                            'pending_arbitration' => 'Arbitrase',
                                            'done' => 'Selesai',
                                            'cancelled' => 'Dibatalkan',
                                            default => $task->status,
                                        };
                                        $statusColor = match($task->status) {
                                            'done' => 'bg-green-100 text-green-800',
                                            'assigned_member' => 'bg-blue-100 text-blue-800',
                                            'pending_pm' => 'bg-yellow-100 text-yellow-800',
                                            'revision' => 'bg-orange-100 text-orange-800',
                                            'pending_arbitration' => 'bg-red-100 text-red-800',
                                            default => 'bg-gray-100 text-gray-800',
                                        };
                                    @endphp
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $statusColor }}">
                                        {{ $statusLabel }}
                                    </span>
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
                </div>
                <div class="p-4">{{ $tasks->links() }}</div>
            </div>

        </div>
    </div>
</div>
