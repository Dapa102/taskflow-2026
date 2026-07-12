<div>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Daftar Workspace</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if (session('message'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg">{{ session('message') }}</div>
            @endif

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
    <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 overflow-y-auto py-10" wire:click.self="$set('showDetailModal', false)">
        <div class="bg-white rounded-xl shadow-xl max-w-2xl w-full mx-4">
            <div class="flex justify-between items-center p-4 border-b sticky top-0 bg-white z-10 rounded-t-xl">
                <h3 class="text-lg font-semibold text-gray-900">{{ $selectedWorkspace->name }}</h3>
                <button wire:click="$set('showDetailModal', false)" class="text-gray-400 hover:text-gray-600 text-xl leading-none">&times;</button>
            </div>

            <div class="p-4 space-y-6 max-h-[70vh] overflow-y-auto">
                {{-- Info --}}
                @if($selectedWorkspace->description)
                    <p class="text-sm text-gray-600">{{ $selectedWorkspace->description }}</p>
                @endif

                {{-- Statistik --}}
                <div class="grid grid-cols-4 gap-3">
                    @php
                        $wsId = $selectedWorkspace->id;
                        $totalTask = \App\Models\Task::where('workspace_id', $wsId)->count();
                        $activeTask = \App\Models\Task::where('workspace_id', $wsId)->whereNotIn('status', ['done','cancelled'])->count();
                        $doneTask = \App\Models\Task::where('workspace_id', $wsId)->where('status', 'done')->count();
                        $lateTask = \App\Models\Task::where('workspace_id', $wsId)
                            ->whereNotIn('status', ['done','cancelled'])
                            ->whereNotNull('deadline')->where('deadline', '<', now())->count();
                    @endphp
                    <div class="bg-gray-50 rounded-lg p-3 text-center">
                        <div class="text-lg font-bold text-gray-800">{{ $totalTask }}</div>
                        <div class="text-xs text-gray-500">Total</div>
                    </div>
                    <div class="bg-blue-50 rounded-lg p-3 text-center">
                        <div class="text-lg font-bold text-blue-700">{{ $activeTask }}</div>
                        <div class="text-xs text-blue-500">Active</div>
                    </div>
                    <div class="bg-green-50 rounded-lg p-3 text-center">
                        <div class="text-lg font-bold text-green-700">{{ $doneTask }}</div>
                        <div class="text-xs text-green-500">Selesai</div>
                    </div>
                    <div class="bg-red-50 rounded-lg p-3 text-center">
                        <div class="text-lg font-bold text-red-700">{{ $lateTask }}</div>
                        <div class="text-xs text-red-500">Terlambat</div>
                    </div>
                </div>

                {{-- Info PM --}}
                <div class="text-sm space-y-1">
                    <p><span class="font-medium">PM:</span> {{ $selectedWorkspace->pm?->name ?? '-' }} ({{ $selectedWorkspace->pm?->email ?? '-' }})</p>
                    @if($selectedWorkspace->deputyPm)
                        <p><span class="font-medium">Deputy PM:</span> {{ $selectedWorkspace->deputyPm->name }} ({{ $selectedWorkspace->deputyPm->email }})</p>
                    @endif
                </div>

                {{-- Daftar Anggota --}}
                <div>
                    <h4 class="text-sm font-semibold text-gray-700 mb-2">Anggota ({{ $selectedWorkspace->members->count() }})</h4>
                    @if($selectedWorkspace->members->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="min-w-full text-sm">
                                <thead>
                                    <tr class="border-b">
                                        <th class="text-left py-2 text-xs font-medium text-gray-500">Nama</th>
                                        <th class="text-left py-2 text-xs font-medium text-gray-500">Email</th>
                                        <th class="text-center py-2 text-xs font-medium text-gray-500">Role</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($selectedWorkspace->members as $m)
                                    <tr class="border-b border-gray-100">
                                        <td class="py-2">{{ $m->name }}</td>
                                        <td class="py-2 text-gray-500">{{ $m->email }}</td>
                                        <td class="py-2 text-center capitalize">{{ $m->role }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-sm text-gray-400">Belum ada anggota.</p>
                    @endif
                </div>

                {{-- Tambah Tim --}}
                <div>
                    <h4 class="text-sm font-semibold text-gray-700 mb-2">Tambah Tim ke Workspace</h4>
                    <div class="flex gap-2">
                        <select wire:model="selectedTeamId" class="flex-1 rounded-md border-gray-300 text-sm shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                            <option value="">Pilih Tim</option>
                            @foreach($teams as $t)
                                <option value="{{ $t->id }}">{{ $t->name }} ({{ $t->members_count }} anggota)</option>
                            @endforeach
                        </select>
                        <button wire:click="addTeam" class="px-4 py-2 text-sm bg-indigo-600 text-white rounded-md hover:bg-indigo-700 font-medium">Tambah</button>
                    </div>
                    @error('selectedTeamId') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                {{-- Tugas Selesai --}}
                <div>
                    <h4 class="text-sm font-semibold text-gray-700 mb-2">Tugas Selesai ({{ $doneTasks->count() }})</h4>
                    @if($doneTasks->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="min-w-full text-sm">
                                <thead>
                                    <tr class="border-b">
                                        <th class="text-left py-2 text-xs font-medium text-gray-500">Judul</th>
                                        <th class="text-left py-2 text-xs font-medium text-gray-500">Dikerjakan</th>
                                        <th class="text-center py-2 text-xs font-medium text-gray-500">Selesai</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($doneTasks as $t)
                                    <tr class="border-b border-gray-100">
                                        <td class="py-2">{{ $t->title }}</td>
                                        <td class="py-2 text-gray-500">{{ $t->assignedMember?->name ?? '-' }}</td>
                                        <td class="py-2 text-center text-xs text-gray-400">{{ $t->updated_at->format('d M Y') }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-sm text-gray-400">Belum ada tugas selesai.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
