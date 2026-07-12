<div>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Daftar Workspace</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="bg-white shadow sm:rounded-lg p-6">
                @if(count($workspaces) === 0)
                    <p class="text-gray-400 text-center py-8">Belum ada workspace.</p>
                @else
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        @foreach($workspaces as $ws)
                        <div class="border border-gray-200 rounded-xl p-5 hover:shadow-md transition-shadow">
                            <div class="flex items-start justify-between mb-3">
                                <div>
                                    <h3 class="font-semibold text-gray-900">{{ $ws['name'] }}</h3>
                                    @if($ws['description'])
                                        <p class="text-xs text-gray-500 mt-1 line-clamp-2">{{ $ws['description'] }}</p>
                                    @endif
                                </div>
                            </div>
                            <div class="flex items-center gap-4 text-xs text-gray-500 mb-4">
                                <span>{{ $ws['member_count'] }} anggota</span>
                                <span>{{ $ws['task_count'] }} tugas</span>
                            </div>
                            <div class="text-xs text-gray-400 mb-4">
                                PM: {{ $ws['pm']?->name ?? '-' }}
                                @if($ws['deputy_pm'])
                                    <br>Deputy: {{ $ws['deputy_pm']->name }}
                                @endif
                            </div>
                            <button wire:click="viewDetail({{ $ws['id'] }})"
                                class="w-full px-3 py-2 text-sm font-medium text-indigo-600 bg-indigo-50 rounded-lg hover:bg-indigo-100 transition-colors">
                                Detail
                            </button>
                        </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Modal Detail --}}
    @if($showDetailModal && $selectedWorkspace)
    <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/40" wire:click.self="$set('showDetailModal', false)">
        <div class="bg-white rounded-xl shadow-xl max-w-lg w-full mx-4">
            <div class="flex justify-between items-center p-4 border-b">
                <h3 class="text-lg font-semibold text-gray-900">{{ $selectedWorkspace->name }}</h3>
                <button wire:click="$set('showDetailModal', false)" class="text-gray-400 hover:text-gray-600 text-xl leading-none">&times;</button>
            </div>
            <div class="p-4 space-y-4">
                @if($selectedWorkspace->description)
                    <p class="text-sm text-gray-600">{{ $selectedWorkspace->description }}</p>
                @endif
                <table class="w-full text-sm">
                    <tbody>
                        <tr>
                            <td class="py-2 pr-4 align-top w-[130px] text-xs font-semibold text-gray-500 uppercase tracking-wide">PM</td>
                            <td class="py-2">{{ $selectedWorkspace->pm?->name ?? '-' }}</td>
                        </tr>
                        @if($selectedWorkspace->deputyPm)
                        <tr>
                            <td class="py-2 pr-4 align-top text-xs font-semibold text-gray-500 uppercase tracking-wide">Deputy PM</td>
                            <td class="py-2">{{ $selectedWorkspace->deputyPm->name }}</td>
                        </tr>
                        @endif
                        <tr>
                            <td class="py-2 pr-4 align-top text-xs font-semibold text-gray-500 uppercase tracking-wide">Anggota</td>
                            <td class="py-2">{{ $selectedWorkspace->members->count() }} orang</td>
                        </tr>
                        <tr>
                            <td class="py-2 pr-4 align-top text-xs font-semibold text-gray-500 uppercase tracking-wide">Total Tugas</td>
                            <td class="py-2">{{ \App\Models\Task::where('workspace_id', $selectedWorkspace->id)->count() }}</td>
                        </tr>
                        <tr>
                            <td class="py-2 pr-4 align-top text-xs font-semibold text-gray-500 uppercase tracking-wide">Active</td>
                            <td class="py-2">{{ \App\Models\Task::where('workspace_id', $selectedWorkspace->id)->whereNotIn('status', ['done','cancelled'])->count() }}</td>
                        </tr>
                        <tr>
                            <td class="py-2 pr-4 align-top text-xs font-semibold text-gray-500 uppercase tracking-wide">Selesai</td>
                            <td class="py-2">{{ \App\Models\Task::where('workspace_id', $selectedWorkspace->id)->where('status', 'done')->count() }}</td>
                        </tr>
                        <tr>
                            <td class="py-2 pr-4 align-top text-xs font-semibold text-gray-500 uppercase tracking-wide">Terlambat</td>
                            <td class="py-2">
                                @php
                                    $lateCount = \App\Models\Task::where('workspace_id', $selectedWorkspace->id)
                                        ->whereNotIn('status', ['done','cancelled'])
                                        ->whereNotNull('deadline')
                                        ->where('deadline', '<', now())
                                        ->count();
                                @endphp
                                <span class="{{ $lateCount > 0 ? 'text-red-600 font-medium' : '' }}">{{ $lateCount }}</span>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @endif
</div>
