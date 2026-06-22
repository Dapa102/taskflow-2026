<div>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Dashboard Atasan</h2>
    </x-slot>

    <div class="py-6 px-6">
        <div class="max-w-7xl mx-auto space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
                <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-200">
                    <p class="text-sm text-gray-500 font-medium">Total Tugas</p>
                    <p class="text-3xl font-bold text-gray-900 mt-1">{{ $total }}</p>
                </div>
                <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-200">
                    <p class="text-sm text-gray-500 font-medium">Belum Diberikan</p>
                    <p class="text-3xl font-bold text-yellow-600 mt-1">{{ $pending }}</p>
                </div>
                <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-200">
                    <p class="text-sm text-gray-500 font-medium">Sudah Diberikan</p>
                    <p class="text-3xl font-bold text-blue-600 mt-1">{{ $given }}</p>
                </div>
                <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-200">
                    <p class="text-sm text-gray-500 font-medium">Selesai</p>
                    <p class="text-3xl font-bold text-green-600 mt-1">{{ $done }}</p>
                </div>
                <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-200">
                    <p class="text-sm text-gray-500 font-medium">Jumlah Deadline</p>
                    <p class="text-3xl font-bold text-rose-600 mt-1">{{ $deadlineCount }}</p>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Overview Tugas</h3>
                <div x-data="{ chart: @js($chartData) }">
                    <div class="flex items-end gap-6 h-40 px-4">
                        <template x-for="item in chart" :key="item.label">
                            <div class="flex-1 flex flex-col items-center justify-end h-full">
                                <div class="text-xs font-semibold mb-1" :style="'color: ' + item.bg" x-text="item.count"></div>
                                <div class="w-full rounded-t transition-all duration-300 min-h-[4px]"
                                     :style="'height: ' + (item.count / Math.max(...chart.map(d => d.count), 1) * 100) + '%; background-color: ' + item.bg">
                                </div>
                                <span class="text-xs text-gray-500 mt-2" x-text="item.label"></span>
                            </div>
                        </template>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Project Manager</h3>
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
                                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
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

                            @foreach($selectedPm['teams'] as $team)
                                <div class="mb-3 last:mb-0 p-3 bg-white/70 rounded-lg border border-indigo-100">
                                    <div class="flex items-center justify-between mb-2">
                                        <p class="text-sm font-semibold text-gray-900">{{ $team['name'] }}</p>
                                        <span class="text-xs text-gray-500">{{ $team['member_count'] }} anggota</span>
                                    </div>
                                    @if($team['members']->isNotEmpty())
                                        <div class="flex flex-wrap gap-1.5">
                                            @foreach($team['members'] as $member)
                                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium {{ $member['role'] === 'admin' ? 'bg-purple-100 text-purple-700' : 'bg-gray-100 text-gray-700' }}">
                                                    {{ $member['name'] }}
                                                    @if($member['role'] === 'admin')
                                                        <span class="text-purple-400">(PM)</span>
                                                    @endif
                                                </span>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                            @endforeach

                            @if(!$selectedPm['workspace'] && $selectedPm['teams']->isEmpty())
                                <p class="text-sm text-gray-500 italic">Belum memiliki workspace atau tim.</p>
                            @endif
                        </div>
                    @endif
                @endif
            </div>
        </div>
    </div>
</div>
