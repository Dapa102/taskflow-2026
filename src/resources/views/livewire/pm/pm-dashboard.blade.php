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
                <div class="p-6 bg-white shadow sm:rounded-lg">
                    <p class="text-gray-600">Anda belum ditugaskan ke workspace. Hubungi Super Admin.</p>
                </div>
            @else
                <div class="grid grid-cols-5 gap-4">
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                        <div class="text-gray-500">Total Tasks</div>
                        <div class="text-2xl font-bold">{{ $stats['total'] }}</div>
                    </div>
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                        <div class="text-gray-500">Tugas Masuk</div>
                        <div class="text-2xl font-bold text-blue-600">{{ $stats['incoming'] }}</div>
                    </div>
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                        <div class="text-gray-500">Menunggu Review</div>
                        <div class="text-2xl font-bold text-yellow-600">{{ $stats['pending_review'] }}</div>
                    </div>
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                        <div class="text-gray-500">Revisi</div>
                        <div class="text-2xl font-bold text-orange-600">{{ $stats['revision'] }}</div>
                    </div>
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                        <div class="text-gray-500">Selesai</div>
                        <div class="text-2xl font-bold text-green-600">{{ $stats['done'] }}</div>
                    </div>
                </div>

                <div class="bg-white shadow sm:rounded-lg p-6" data-donut-card>
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Overview Tugas</h3>
                    <div class="flex justify-center items-center gap-10 flex-wrap">
                        <div class="w-56 h-56" wire:ignore>
                            <canvas data-donut='@json($chartData)' class="w-full h-full"></canvas>
                        </div>
                        <div class="space-y-2 min-w-[160px]">
                            @foreach($chartData as $item)
                                <div wire:key="legend-{{ $item['label'] }}" class="flex items-center gap-2">
                                    <span class="w-3 h-3 rounded-full shrink-0" style="background: {{ $item['bg'] }}"></span>
                                    <span class="text-sm text-gray-600">{{ $item['label'] }}</span>
                                    <span class="ml-auto text-sm font-semibold text-gray-900">{{ $item['count'] }}</span>
                                    <button x-on:click="$wire.showDetail('{{ $item['label'] }}')" class="legend-btn p-1 text-gray-400 hover:text-indigo-600 transition cursor-pointer" title="Lihat detail" type="button">
                                        <svg class="w-4 h-4 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                    </button>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <div class="bg-white shadow sm:rounded-lg p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Penyelesaian Tugas (7 Hari)</h3>
                    <div class="h-48" wire:ignore>
                        <canvas data-bar='@json($dailyChartData)' class="w-full h-full"></canvas>
                    </div>
                </div>

                @if($revisionLimitWarnings->isNotEmpty())
                    <div class="p-4 bg-red-50 border-l-4 border-red-500 rounded-lg shadow-sm">
                        <h3 class="text-sm font-semibold text-red-800 mb-2">Peringatan Batas Revisi</h3>
                        <ul class="text-xs text-red-700 space-y-1">
                            @foreach($revisionLimitWarnings as $w)
                                <li>{{ $w['title'] }} — revisi {{ $w['counter'] }}/{{ $w['limit'] }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @if($incomingTasks->isNotEmpty())
                <div class="p-4 bg-white shadow sm:rounded-lg border-l-4 border-blue-400">
                    <h3 class="text-lg font-medium text-gray-900 mb-3">Tugas Masuk (Perlu Ditugaskan)</h3>
                    @foreach($incomingTasks as $task)
                    <div class="p-3 bg-blue-50 rounded-lg mb-2 flex justify-between items-center">
                        <div>
                            <div class="font-medium">{{ $task->title }}</div>
                            <div class="text-xs text-gray-500">Prioritas: {{ ucfirst($task->priority) }}</div>
                            @if($task->deadline)
                            <div class="text-xs {{ $task->isOverdue() ? 'text-red-500' : 'text-gray-500' }}">Deadline: {{ $task->deadline->format('Y-m-d') }}</div>
                            @endif
                        </div>
                        <button wire:click="$set('assignTaskId', {{ $task->id }})"
                            class="px-3 py-1 text-xs bg-indigo-600 text-white rounded-md hover:bg-indigo-700">
                            Assign
                        </button>
                    </div>
                    @endforeach
                </div>
                @endif

                <div class="space-y-6">
                        <div class="p-4 bg-white shadow sm:rounded-lg">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Semua Tugas</h3>
                            <div class="space-y-4">
                                @forelse($tasks as $task)
                                    @php
                                        $cardClass = match($task->status) {
                                            'done' => 'bg-gray-50',
                                            'pending_pm' => 'border-yellow-300 bg-yellow-50',
                                            'revision' => 'border-orange-300 bg-orange-50',
                                            'assigned_pm' => 'border-blue-200 bg-blue-50',
                                            'pending_arbitration' => 'border-red-300 bg-red-50',
                                            default => 'bg-white',
                                        };
                                    @endphp
                                    <div class="p-4 border rounded-lg {{ $cardClass }}">
                                        <div class="flex justify-between items-start">
                                            <div class="flex-1">
                                                <div class="font-medium {{ $task->status === 'done' ? 'line-through text-gray-500' : '' }}">
                                                    {{ $task->title }}
                                                </div>
                                                <div class="text-sm text-gray-500 mt-1">
                                                    Dari: {{ $task->creator?->name ?? 'System' }} |
                                                    Anggota: <span class="font-semibold">{{ $task->assignedMember?->name ?? ($task->assignedPm?->name ?? 'Belum ditugaskan') }}</span> |
                                                    Prioritas: {{ ucfirst($task->priority) }}
                                                </div>
                                                <div class="text-xs mt-1">
                                                    Status:
                                                    <span class="font-semibold
                                                        {{ $task->status === 'done' ? 'text-green-600' : '' }}
                                                        {{ $task->status === 'assigned_member' ? 'text-blue-600' : '' }}
                                                        {{ $task->status === 'pending_pm' ? 'text-yellow-600' : '' }}
                                                        {{ $task->status === 'pending_admin' ? 'text-purple-600' : '' }}
                                                        {{ $task->status === 'revision' ? 'text-orange-600' : '' }}
                                                        {{ $task->status === 'assigned_pm' ? 'text-blue-500' : '' }}
                                                        {{ $task->status === 'pending_arbitration' ? 'text-red-600' : '' }}
                                                        {{ $task->status === 'cancelled' ? 'text-gray-400' : '' }}">
                                                        @switch($task->status)
                                                            @case('assigned_pm') Menunggu Ditugaskan @break
                                                            @case('assigned_member') Dikerjakan Anggota @break
                                                            @case('pending_pm') Menunggu Review @break
                                                            @case('pending_admin') Menunggu Approval Admin @break
                                                            @case('revision') Revisi @break
                                                            @case('pending_arbitration') Arbitrase @break
                                                            @case('done') Selesai @break
                                                            @case('cancelled') Dibatalkan @break
                                                            @default {{ $task->status }}
                                                        @endswitch
                                                    </span>
                                                    @if($task->status === 'revision' || $task->status === 'pending_arbitration')
                                                        <span class="ml-1 text-xs {{ $task->revision_counter >= $task->max_revision_limit ? 'text-red-500' : 'text-orange-500' }}">
                                                            ({{ $task->revision_counter }}/{{ $task->max_revision_limit }})
                                                        </span>
                                                    @endif
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
                                                    <div class="mt-1 text-xs text-gray-500 space-y-0.5">
                                                        @foreach($task->attachments as $att)
                                                            <div class="flex items-center gap-1">
                                                                <a href="{{ Storage::url($att->file_path) }}" target="_blank" class="text-blue-600 hover:underline">
                                                                    {{ $att->filename }}
                                                                </a>
                                                                @if($att->user_id !== auth()->id())
                                                                    <span class="text-gray-400">({{ $att->user?->name ?? 'anggota' }})</span>
                                                                @endif
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                @endif
                                            </div>

                                            <div class="flex items-center gap-2 shrink-0 ml-4">
                                                @if($task->status === 'assigned_pm' || $task->status === 'assigned_member')
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
                                                    @if($task->isRevisiLocked())
                                                        <span class="text-xs text-red-500 font-medium">Batas revisi tercapai</span>
                                                    @else
                                                        <button wire:click="$set('rejectTaskId', {{ $task->id }})"
                                                            class="px-3 py-1 text-xs bg-orange-600 text-white rounded-md hover:bg-orange-700">
                                                            Revisi
                                                        </button>
                                                    @endif
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

    @if($detailModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/40" wire:click.self="$set('detailModal', false)">
        <div class="bg-white rounded-xl shadow-xl max-w-lg w-full mx-4 max-h-[70vh] overflow-y-auto">
            <div class="flex justify-between items-center p-4 border-b sticky top-0 bg-white z-10">
                <h3 class="text-lg font-semibold text-gray-900">Detail: {{ $detailTitle }}</h3>
                <button wire:click="$set('detailModal', false)" class="text-gray-400 hover:text-gray-600 text-xl leading-none">&times;</button>
            </div>
            <div class="p-4 space-y-2">
                @forelse($detailTasks as $task)
                <div class="p-3 rounded-lg border border-gray-100 hover:border-indigo-200 hover:bg-indigo-50 transition">
                    <div class="flex items-center justify-between">
                        <div class="font-medium text-sm text-gray-900">{{ $task->title }}</div>
                        <span class="text-xs px-2 py-0.5 rounded-full font-medium
                            @switch($task->status)
                                @case('assigned_pm') bg-blue-100 text-blue-700 @break
                                @case('assigned_member') bg-indigo-100 text-indigo-700 @break
                                @case('pending_pm') bg-yellow-100 text-yellow-700 @break
                                @case('revision') bg-orange-100 text-orange-700 @break
                                @case('pending_arbitration') bg-red-100 text-red-700 @break
                                @case('done') bg-green-100 text-green-700 @break
                                @default bg-gray-100 text-gray-600
                            @endswitch
                        ">
                            @switch($task->status)
                                @case('assigned_pm') Dikirim @break
                                @case('assigned_member') Dikerjakan @break
                                @case('pending_pm') Review PM @break
                                @case('revision') Revisi @break
                                @case('pending_arbitration') Arbitrase @break
                                @case('done') Selesai @break
                                @default {{ $task->status }}
                            @endswitch
                        </span>
                    </div>
                    <div class="text-xs text-gray-500 mt-1">
                        @if($task->assignedMember) Anggota: {{ $task->assignedMember->name }} · @endif
                        @if($task->creator) Dibuat: {{ $task->creator->name }} · @endif
                        {{ $task->created_at->format('d M Y') }}
                    </div>
                </div>
                @empty
                <p class="text-gray-400 text-sm text-center py-6">Tidak ada tugas dengan status ini.</p>
                @endforelse
            </div>
        </div>
    </div>
    @endif
</div>
