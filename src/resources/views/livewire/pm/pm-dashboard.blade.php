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
                <div class="grid grid-cols-3 md:grid-cols-6 gap-4">
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                        <div class="text-gray-500">Total Tasks</div>
                        <div class="text-2xl font-bold">{{ $stats['total'] }}</div>
                    </div>
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                        <div class="text-gray-500">Project</div>
                        <div class="text-2xl font-bold">{{ $projectCount }}</div>
                    </div>
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                        <div class="text-gray-500">Tugas Masuk</div>
                        <div class="text-2xl font-bold text-blue-600">{{ $stats['incoming'] }}</div>
                    </div>
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                        <div class="text-gray-500">Menunggu Review</div>
                        <div class="text-2xl font-bold text-yellow-600">{{ $stats['pending_pm'] }}</div>
                    </div>
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                        <div class="text-gray-500">In Progress</div>
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
                                <div class="flex items-center gap-2">
                                    <span class="w-3 h-3 rounded-full shrink-0" style="background: {{ $item['bg'] }}"></span>
                                    <span class="text-sm text-gray-600">{{ $item['label'] }}</span>
                                    <span class="ml-auto text-sm font-semibold text-gray-900">{{ $item['count'] }}</span>
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

                @if($projectProgress->isNotEmpty())
                <div class="bg-white shadow sm:rounded-lg p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Progress Project</h3>
                    <div class="space-y-3">
                        @foreach($projectProgress as $p)
                        <div>
                            <div class="flex justify-between text-sm mb-1">
                                <span class="font-medium text-gray-700">{{ $p['name'] }}</span>
                                <span class="text-gray-500">{{ $p['done'] }}/{{ $p['total'] }} ({{ $p['percentage'] }}%)</span>
                            </div>
                            <div class="flex items-center gap-3">
                                <div class="flex-1 w-full bg-gray-200 rounded-full h-2.5">
                                    <div class="bg-indigo-600 h-2.5 rounded-full" style="width: {{ $p['percentage'] }}%"></div>
                                </div>
                                <button wire:click="showProjectDetail({{ $p['id'] }})" class="text-xs text-indigo-600 hover:text-indigo-800 font-medium whitespace-nowrap">Detail</button>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif

    @if($projectDetailModal && $projectDetail)
    @php $prj = $projectDetail; @endphp
    <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/40" wire:click.self="$set('projectDetailModal', false)">
        <div class="bg-white rounded-xl shadow-xl max-w-2xl w-full mx-4 max-h-[80vh] overflow-y-auto">
            <div class="flex justify-between items-center p-4 border-b sticky top-0 bg-white z-10">
                <h3 class="text-lg font-semibold text-gray-900">{{ $prj->name }}</h3>
                <button wire:click="$set('projectDetailModal', false)" class="text-gray-400 hover:text-gray-600 text-xl leading-none">&times;</button>
            </div>
            <div class="p-4 space-y-4">
                @if($prj->description)
                <div>
                    <h4 class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">Deskripsi</h4>
                    <p class="text-sm text-gray-700 bg-gray-50 rounded-lg p-3 border border-gray-100">{{ $prj->description }}</p>
                </div>
                @endif
                <table class="w-full text-sm">
                    <tbody>
                        <tr>
                            <td class="py-2 pr-4 align-top w-[140px] text-xs font-semibold text-gray-500 uppercase tracking-wide">Status</td>
                            <td class="py-2"><span class="px-2 py-0.5 text-xs rounded-full {{ $prj->status === 'active' ? 'bg-green-50 text-green-700' : 'bg-gray-100 text-gray-600' }}">{{ ucfirst($prj->status) }}</span></td>
                        </tr>
                        <tr>
                            <td class="py-2 pr-4 align-top text-xs font-semibold text-gray-500 uppercase tracking-wide">Total Tugas</td>
                            <td class="py-2">{{ $prj->tasks_count }}</td>
                        </tr>
                        <tr>
                            <td class="py-2 pr-4 align-top text-xs font-semibold text-gray-500 uppercase tracking-wide">Selesai</td>
                            <td class="py-2 text-green-600 font-medium">{{ $prj->done_count ?? 0 }}</td>
                        </tr>
                        @if($prj->deadline)
                        <tr>
                            <td class="py-2 pr-4 align-top text-xs font-semibold text-gray-500 uppercase tracking-wide">Deadline</td>
                            <td class="py-2">{{ $prj->deadline->format('d M Y') }}</td>
                        </tr>
                        @endif
                        <tr>
                            <td class="py-2 pr-4 align-top text-xs font-semibold text-gray-500 uppercase tracking-wide">Workspace</td>
                            <td class="py-2">{{ $prj->workspace->name ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td class="py-2 pr-4 align-top text-xs font-semibold text-gray-500 uppercase tracking-wide">Dibuat Oleh</td>
                            <td class="py-2">{{ $prj->creator->name ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td class="py-2 pr-4 align-top text-xs font-semibold text-gray-500 uppercase tracking-wide">Tanggal Dibuat</td>
                            <td class="py-2">{{ $prj->created_at->format('d M Y') }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @endif

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
                            <div class="text-xs text-gray-500">Revisi: <span class="{{ $task->isRevisiLocked() ? 'text-red-600 font-bold' : '' }}">{{ $task->revision_counter }}/{{ $task->max_revision_limit }}</span></div>
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
                                            'review' => 'border-yellow-300 bg-yellow-50',
                                            'in_progress' => $task->review_note ? 'border-orange-300 bg-orange-50' : 'border-blue-200 bg-blue-50',
                                            'todo' => 'border-blue-200 bg-blue-50',
                                            'cancelled' => 'border-red-300 bg-red-50',
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
                                                    {{ $task->status === 'in_progress' && !$task->review_note ? 'text-blue-600' : '' }}
                                                    {{ $task->status === 'review' ? 'text-yellow-600' : '' }}
                                                    {{ $task->status === 'in_progress' && $task->review_note ? 'text-orange-600' : '' }}
                                                    {{ $task->status === 'todo' ? 'text-blue-500' : '' }}
                                                    {{ $task->status === 'cancelled' ? 'text-gray-400' : '' }}">
                                                    @switch($task->status)
                                                        @case('todo') @if($task->assigned_member_id) Ditugaskan @else Menunggu Ditugaskan @endif @break
                                                        @case('in_progress') @if($task->review_note) Revisi @else Dikerjakan Anggota @endif @break
                                                        @case('review') Menunggu Review @break
                                                        @case('done') Selesai @break
                                                        @case('cancelled') Dibatalkan @break
                                                        @default {{ $task->status_label }}
                                                    @endswitch
                                                </span>
                                                </div>
                                                @if($task->deadline)
                                                    <div class="text-sm {{ $task->isOverdue() ? 'text-red-500 font-bold' : 'text-gray-500' }}">
                                                        Deadline: {{ $task->deadline->format('Y-m-d') }}
                                                    </div>
                                                @endif
                                                <div class="text-sm text-gray-500">
                                                    Revisi: <span class="{{ $task->isRevisiLocked() ? 'text-red-600 font-bold' : '' }}">{{ $task->revision_counter }}/{{ $task->max_revision_limit }}</span>
                                                </div>
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
                                                @if($task->status === 'todo' && !$task->assigned_member_id)
                                                    <button wire:click="$set('assignTaskId', {{ $task->id }})"
                                                        class="px-3 py-1 text-xs bg-indigo-600 text-white rounded-md hover:bg-indigo-700">
                                                        Assign
                                                    </button>
                                                @endif
                                                @if($task->status === 'review')
                                                    <button wire:click="approveTask({{ $task->id }})"
                                                        class="px-3 py-1 text-xs bg-green-600 text-white rounded-md hover:bg-green-700">
                                                        Setujui
                                                    </button>
                                                    <button wire:click="$set('rejectTaskId', {{ $task->id }})"
                                                        class="px-3 py-1 text-xs bg-orange-600 text-white rounded-md hover:bg-orange-700">
                                                        Revisi
                                                    </button>
                                                @endif
                                                @if($task->status === 'in_progress' && $task->review_note)
                                                    <button wire:click="requestArbitration({{ $task->id }})"
                                                        class="px-3 py-1 text-xs bg-red-700 text-white rounded-md hover:bg-red-800">
                                                        Ajukan Arbitrase
                                                    </button>
                                                @endif
                                                @if(in_array($task->status, ['todo', 'in_progress', 'review']))
                                                    <button wire:click="confirmCancel({{ $task->id }})"
                                                        class="px-3 py-1 text-xs bg-red-600 text-white rounded-md hover:bg-red-700">
                                                        Batalkan
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

                                        @if($cancelTaskId === $task->id)
                                            <div class="mt-3 p-3 bg-red-50 rounded-md border border-red-200">
                                                <textarea wire:model="cancelNote" placeholder="Alasan pembatalan..." rows="2" class="w-full border-gray-300 rounded-md shadow-sm text-sm mb-2"></textarea>
                                                @error('cancelNote') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                                <div class="flex gap-2">
                                                    <button wire:click="cancelTask({{ $task->id }})" class="px-3 py-1 text-xs bg-red-600 text-white rounded-md hover:bg-red-700">Konfirmasi Batal</button>
                                                    <button wire:click="$set('cancelTaskId', null)" class="px-3 py-1 text-xs text-gray-600 hover:text-gray-900">Batal</button>
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
