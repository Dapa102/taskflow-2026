<div>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Dashboard Super Admin</h2>
    </x-slot>

    <div class="py-6 px-6">
        <div class="max-w-7xl mx-auto space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
                <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-200">
                    <p class="text-sm text-gray-500 font-medium">Total Tugas</p>
                    <p class="text-3xl font-bold text-gray-900 mt-1">{{ $total }}</p>
                </div>
                <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-200">
                    <p class="text-sm text-gray-500 font-medium">Perlu Approval</p>
                    <p class="text-3xl font-bold text-purple-600 mt-1">{{ $pendingAdmin }}</p>
                </div>
                <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-200">
                    <p class="text-sm text-gray-500 font-medium">Arbitrase</p>
                    <p class="text-3xl font-bold text-red-600 mt-1">{{ $pendingArbitration }}</p>
                </div>
                <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-200">
                    <p class="text-sm text-gray-500 font-medium">Sedang Dikerjakan</p>
                    <p class="text-3xl font-bold text-indigo-600 mt-1">{{ $inProgress }}</p>
                </div>
                <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-200">
                    <p class="text-sm text-gray-500 font-medium">Selesai</p>
                    <p class="text-3xl font-bold text-green-600 mt-1">{{ $done }}</p>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Overview Tugas</h3>
                <div class="flex justify-center items-center gap-10 flex-wrap">
                    <div class="w-60 h-60" wire:ignore>
                        <canvas data-donut='@json($chartData)' class="w-full h-full"></canvas>
                    </div>
                    <div class="space-y-2 min-w-[160px]">
                        @foreach($chartData as $item)
                            <div class="flex items-center gap-2">
                                <span class="w-3 h-3 rounded-full shrink-0" style="background: {{ $item['bg'] }}"></span>
                                <span class="text-sm text-gray-600">{{ $item['label'] }}</span>
                                <span class="ml-auto text-sm font-semibold text-gray-900">{{ $item['count'] }}</span>
                                <button onclick="if(typeof Livewire!=='undefined')Livewire.dispatch('showDetail',{label:'{{ $item['label'] }}'})" class="p-1 text-gray-400 hover:text-indigo-600 transition cursor-pointer" title="Lihat detail" type="button">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                </button>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Penyelesaian Tugas (7 Hari)</h3>
                <div class="h-48" wire:ignore>
                    <canvas data-bar='@json($dailyChartData)' class="w-full h-full"></canvas>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Beban Kerja Project Manager</h3>
                @if($pms->isEmpty())
                    <p class="text-sm text-gray-500">Belum ada Project Manager.</p>
                @else
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        @foreach($pms as $pm)
                            <button wire:click="selectPm({{ $pm['id'] }})"
                                class="text-left p-4 rounded-xl border transition-all duration-200 {{ $selectedPmId === $pm['id'] ? 'border-indigo-400 bg-indigo-50 ring-2 ring-indigo-200' : 'border-gray-200 bg-white hover:bg-gray-50 hover:border-gray-300' }}">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-full bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center text-white font-bold text-sm">
                                        {{ strtoupper(substr($pm['name'], 0, 1)) }}
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="font-medium text-gray-900 truncate">{{ $pm['name'] }}</p>
                                        <p class="text-xs text-gray-500 truncate">{{ $pm['email'] }}</p>
                                    </div>
                                </div>
                                <div class="mt-2 flex gap-2 text-xs">
                                    <span class="px-2 py-0.5 rounded-full bg-indigo-50 text-indigo-600">{{ $pm['active_tasks'] }} aktif</span>
                                    <span class="px-2 py-0.5 rounded-full bg-yellow-50 text-yellow-600">{{ $pm['pending_review'] }} review</span>
                                    @if($pm['overdue'] > 0)
                                    <span class="px-2 py-0.5 rounded-full bg-red-50 text-red-600">{{ $pm['overdue'] }} terlambat</span>
                                    @endif
                                </div>
                            </button>
                        @endforeach
                    </div>

                    @if($selectedPm)
                        <div class="mt-6 p-5 bg-gradient-to-br from-indigo-50 to-purple-50 rounded-xl border border-indigo-200">
                            <div class="flex items-center justify-between mb-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-12 h-12 rounded-full bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center text-white font-bold text-lg">
                                        {{ strtoupper(substr($selectedPm['name'], 0, 1)) }}
                                    </div>
                                    <div>
                                        <h4 class="text-lg font-semibold text-gray-900">{{ $selectedPm['name'] }}</h4>
                                        <p class="text-sm text-gray-600">{{ $selectedPm['email'] }} @if($selectedPm['phone']) &middot; {{ $selectedPm['phone'] }} @endif</p>
                                    </div>
                                </div>
                                <button wire:click="selectPm(null)" class="p-1 rounded-lg text-gray-400 hover:text-gray-600 hover:bg-white/50">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                </button>
                            </div>

                            @if($selectedPm['workspace'])
                                <div class="mb-4 p-3 bg-white/70 rounded-lg border border-indigo-100">
                                    <p class="text-xs text-gray-500 font-medium uppercase tracking-wide">Workspace</p>
                                    <p class="text-sm font-semibold text-gray-900">{{ $selectedPm['workspace']['name'] }}</p>
                                    @if($selectedPm['workspace']['description'])
                                        <p class="text-xs text-gray-500 mt-0.5">{{ $selectedPm['workspace']['description'] }}</p>
                                    @endif
                                </div>
                            @endif

                            <div class="grid grid-cols-3 gap-3">
                                <div class="p-3 bg-white/70 rounded-lg border border-indigo-100 text-center">
                                    <p class="text-2xl font-bold text-indigo-600">{{ $selectedPm['active_tasks'] }}</p>
                                    <p class="text-xs text-gray-500">Tugas Aktif</p>
                                </div>
                                <div class="p-3 bg-white/70 rounded-lg border border-indigo-100 text-center">
                                    <p class="text-2xl font-bold text-yellow-600">{{ $selectedPm['pending_review'] }}</p>
                                    <p class="text-xs text-gray-500">Menunggu Review</p>
                                </div>
                                <div class="p-3 bg-white/70 rounded-lg border border-indigo-100 text-center">
                                    <p class="text-2xl font-bold text-red-600">{{ $selectedPm['overdue'] }}</p>
                                    <p class="text-xs text-gray-500">Terlambat</p>
                                </div>
                            </div>
                        </div>
                    @endif
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
                                @case('draft') bg-gray-100 text-gray-600 @break
                                @case('assigned_member') bg-indigo-100 text-indigo-700 @break
                                @case('pending_pm') bg-yellow-100 text-yellow-700 @break
                                @case('revision') bg-orange-100 text-orange-700 @break
                                @case('pending_admin') bg-purple-100 text-purple-700 @break
                                @case('pending_arbitration') bg-red-100 text-red-700 @break
                                @case('done') bg-green-100 text-green-700 @break
                                @default bg-gray-100 text-gray-600
                            @endswitch
                        ">
                            @switch($task->status)
                                @case('draft') Draft @break
                                @case('assigned_member') Dikerjakan @break
                                @case('pending_pm') Review PM @break
                                @case('revision') Revisi @break
                                @case('pending_admin') Approval @break
                                @case('pending_arbitration') Arbitrase @break
                                @case('done') Selesai @break
                                @default {{ $task->status }}
                            @endswitch
                        </span>
                    </div>
                    <div class="text-xs text-gray-500 mt-1">
                        @if($task->assignedPm) PM: {{ $task->assignedPm->name }} · @endif
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
