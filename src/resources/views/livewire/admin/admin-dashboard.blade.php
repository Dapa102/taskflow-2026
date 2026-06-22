<div>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Admin Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-6 px-6">
        <div class="max-w-7xl mx-auto space-y-6">

            <!-- Global Stats -->
            <div class="grid grid-cols-3 gap-4">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 text-center">
                    <div class="text-gray-500">Total Users</div>
                    <div class="text-3xl font-bold">{{ $stats['users'] }}</div>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 text-center">
                    <div class="text-gray-500">Total Workspaces</div>
                    <div class="text-3xl font-bold text-indigo-600">{{ $stats['workspaces'] }}</div>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 text-center">
                    <div class="text-gray-500">Total Tasks</div>
                    <div class="text-3xl font-bold text-blue-600">{{ $stats['tasks']['total'] }}</div>
                </div>
            </div>

            <!-- Task Status Breakdown -->
            <div class="grid grid-cols-6 gap-4">
                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 text-center">
                    <div class="text-sm text-yellow-700 font-semibold">Todo</div>
                    <div class="text-2xl font-bold text-yellow-600">{{ $stats['tasks']['todo'] }}</div>
                </div>
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 text-center">
                    <div class="text-sm text-blue-700 font-semibold">On Progress</div>
                    <div class="text-2xl font-bold text-blue-600">{{ $stats['tasks']['on_progress'] }}</div>
                </div>
                <div class="bg-amber-50 border border-amber-200 rounded-lg p-4 text-center">
                    <div class="text-sm text-amber-700 font-semibold">Review PM</div>
                    <div class="text-2xl font-bold text-amber-600">{{ $stats['tasks']['pending_pm'] }}</div>
                </div>
                <div class="bg-purple-50 border border-purple-200 rounded-lg p-4 text-center">
                    <div class="text-sm text-purple-700 font-semibold">Review Admin</div>
                    <div class="text-2xl font-bold text-purple-600">{{ $stats['tasks']['pending_admin'] }}</div>
                </div>
                <div class="bg-orange-50 border border-orange-200 rounded-lg p-4 text-center">
                    <div class="text-sm text-orange-700 font-semibold">Revisi</div>
                    <div class="text-2xl font-bold text-orange-600">{{ $stats['tasks']['revision'] }}</div>
                </div>
                <div class="bg-green-50 border border-green-200 rounded-lg p-4 text-center">
                    <div class="text-sm text-green-700 font-semibold">Done</div>
                    <div class="text-2xl font-bold text-green-600">{{ $stats['tasks']['done'] }}</div>
                </div>
            </div>

            <div class="p-6 bg-white shadow sm:rounded-lg">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Overview Tugas</h3>
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

            <!-- Workspaces -->
            <div class="p-4 bg-white shadow sm:rounded-lg">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Workspaces</h3>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Name</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">PM</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Description</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($workspaces as $ws)
                            <tr>
                                <td class="px-6 py-4 text-sm text-gray-900">{{ $ws->name }}</td>
                                <td class="px-6 py-4 text-sm text-gray-500">
                                    {{ $ws->pm?->name ?? '-' }}
                                    @php
                                        $wsTeam = $ws->pm ? \App\Models\Team::where('owner_id', $ws->pm->id)->first() : null;
                                    @endphp
                                    @if($wsTeam)
                                    <span class="text-[10px] ml-1 px-1.5 py-0.5 rounded-full bg-purple-50 text-purple-600">{{ $wsTeam->name }}</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-500">{{ $ws->description ?? '-' }}</td>
                            </tr>
                            @empty
                            <tr><td class="px-6 py-4 text-sm text-gray-400" colspan="3">No workspaces.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Teams & Tugas Sedang Dikerjakan -->
            <div class="p-4 bg-white shadow sm:rounded-lg">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Tim & Tugas Sedang Dikerjakan</h3>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tim</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Project Manager</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Anggota</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tugas On Progress</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($teams as $team)
                            <tr>
                                <td class="px-6 py-4 text-sm font-semibold text-gray-900">{{ $team->name }}</td>
                                <td class="px-6 py-4">
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                                        {{ $team->owner?->name ?? '-' }}
                                        <span class="text-purple-400">(Project Manager)</span>
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex flex-wrap gap-1">
                                        @forelse($team->members as $member)
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium {{ $member->role === 'admin' ? 'bg-indigo-100 text-indigo-800' : 'bg-gray-100 text-gray-700' }}">
                                                <span class="w-4 h-4 rounded-full {{ $member->role === 'admin' ? 'bg-indigo-500' : 'bg-gray-400' }} flex items-center justify-center text-[10px] text-white font-bold">{{ strtoupper(substr($member->user?->name ?? '?', 0, 1)) }}</span>
                                                {{ $member->user?->name ?? 'Unknown' }}
                                                <span class="{{ $member->role === 'admin' ? 'text-indigo-500' : 'text-gray-400' }}">({{ $member->role === 'admin' ? 'Project Manager' : 'Anggota' }})</span>
                                            </span>
                                        @empty
                                            <span class="text-gray-400 text-xs">—</span>
                                        @endforelse
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    @php
                                        $onProgressTasks = $team->tasks->where('status', 'on_progress')->take(5);
                                    @endphp
                                    @if($onProgressTasks->isEmpty())
                                        <span class="text-gray-400">—</span>
                                    @else
                                        <div class="space-y-2">
                                            @foreach($onProgressTasks as $task)
                                            <div class="flex items-center justify-between gap-2">
                                                <div class="flex-1 min-w-0">
                                                    <div class="text-sm font-medium truncate">{{ $task->title }}</div>
                                                    <div class="flex gap-2 text-xs text-gray-500">
                                                        <span>{{ ucfirst($task->priority) }}</span>
                                                        <span>·</span>
                                                        <span>{{ $task->assignee?->name ?? 'Unassigned' }}</span>
                                                        @if($task->deadline)
                                                            <span>·</span>
                                                            <span>{{ $task->deadline->format('d M') }}</span>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                            @endforeach
                                        </div>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr><td class="px-6 py-4 text-sm text-gray-400" colspan="4">No teams.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>



        </div>
    </div>
</div>
