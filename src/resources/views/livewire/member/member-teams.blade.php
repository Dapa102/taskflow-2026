<div>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Tim Saya</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if($myTeams->isNotEmpty())
            <div class="bg-white shadow sm:rounded-lg p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Tim</h3>
                <div class="space-y-3">
                    @foreach($myTeams as $tm)
                    <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                        <div>
                            <p class="font-medium text-gray-900">{{ $tm->team->name }}</p>
                            @if($tm->team->owner)
                            <p class="text-sm text-gray-500 mt-1">PM: {{ $tm->team->owner->name }}</p>
                            @endif
                        </div>
                        <button wire:click="showDetail({{ $tm->team->id }})"
                            class="px-3 py-1.5 text-xs font-medium text-indigo-600 bg-indigo-50 rounded-md hover:bg-indigo-100">
                            Detail
                        </button>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            @if($memberWorkspaces->isNotEmpty())
            <div class="bg-white shadow sm:rounded-lg p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Workspace Saya</h3>
                <div class="space-y-3">
                    @foreach($memberWorkspaces as $ws)
                    <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                        <div>
                            <p class="font-medium text-gray-900">{{ $ws->name }}</p>
                            @if($ws->description)
                            <p class="text-sm text-gray-500 mt-1">{{ $ws->description }}</p>
                            @endif
                            @if($ws->pm)
                            <p class="text-sm text-gray-500 mt-1">PM: {{ $ws->pm->name }}</p>
                            @endif
                        </div>
                        <button wire:click="showWorkspaceDetail({{ $ws->id }})"
                            class="px-3 py-1.5 text-xs font-medium text-indigo-600 bg-indigo-50 rounded-md hover:bg-indigo-100">
                            Detail
                        </button>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            @if($myTeams->isEmpty() && $memberWorkspaces->isEmpty())
            <div class="text-center text-gray-400 text-sm py-16">Belum tergabung dalam tim atau workspace mana pun.</div>
            @endif
        </div>
    </div>

    {{-- Modal Detail Tim --}}
    @if($detailModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/40" wire:click.self="$set('detailModal', false)">
        <div class="bg-white rounded-xl shadow-xl max-w-lg w-full mx-4 max-h-[80vh] overflow-y-auto">
            <div class="flex justify-between items-center p-4 border-b sticky top-0 bg-white z-10">
                <h3 class="text-lg font-semibold text-gray-900">{{ $detailTeamName }}</h3>
                <button wire:click="$set('detailModal', false)" class="text-gray-400 hover:text-gray-600 text-xl leading-none">&times;</button>
            </div>
            <div class="p-4 space-y-4">
                @if($detailPm)
                <div>
                    <h4 class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-2">Project Manager</h4>
                    <div class="flex items-center gap-3 p-3 bg-indigo-50 rounded-lg">
                        <div class="w-10 h-10 rounded-full bg-indigo-600 flex items-center justify-center text-white font-bold text-sm">
                            {{ strtoupper(substr($detailPm->name, 0, 1)) }}
                        </div>
                        <div>
                            <p class="font-medium text-gray-900">{{ $detailPm->name }}</p>
                            <p class="text-xs text-gray-500">{{ $detailPm->email }}</p>
                        </div>
                    </div>
                </div>
                @endif

                <div>
                    <h4 class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-2">Anggota Tim ({{ count($detailMembers) }})</h4>
                    <div class="space-y-2">
                        @forelse($detailMembers as $member)
                        <div class="flex items-center gap-3 p-3 bg-gray-50 rounded-lg">
                            <div class="w-8 h-8 rounded-full bg-gray-400 flex items-center justify-center text-white font-bold text-xs">
                                {{ strtoupper(substr($member['name'], 0, 1)) }}
                            </div>
                            <div class="flex-1">
                                <p class="text-sm font-medium text-gray-900">{{ $member['name'] }}</p>
                                <p class="text-xs text-gray-500">{{ $member['email'] }}</p>
                            </div>
                            <span class="text-xs px-2 py-0.5 rounded-full {{ $member['role'] === 'admin' ? 'bg-purple-100 text-purple-700' : 'bg-gray-100 text-gray-600' }}">
                                {{ $member['role'] === 'admin' ? 'Admin' : 'Anggota' }}
                            </span>
                        </div>
                        @empty
                        <p class="text-sm text-gray-400">Belum ada anggota.</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- Modal Detail Workspace --}}
    @if($wsDetailModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/40" wire:click.self="$set('wsDetailModal', false)">
        <div class="bg-white rounded-xl shadow-xl max-w-2xl w-full mx-4 max-h-[80vh] overflow-y-auto">
            <div class="flex justify-between items-center p-4 border-b sticky top-0 bg-white z-10 rounded-t-xl">
                <h3 class="text-lg font-semibold text-gray-900">{{ $wsDetail->name }}</h3>
                <button wire:click="$set('wsDetailModal', false)" class="text-gray-400 hover:text-gray-600 text-xl leading-none">&times;</button>
            </div>
            <div class="p-4 space-y-6">
                @if($wsDetail->description)
                    <p class="text-sm text-gray-600">{{ $wsDetail->description }}</p>
                @endif

                <div class="grid grid-cols-4 gap-3">
                    @php
                        $wid = $wsDetail->id;
                        $totalTask = \App\Models\Task::where('workspace_id', $wid)->count();
                        $activeTask = \App\Models\Task::where('workspace_id', $wid)->whereNotIn('status', ['done','cancelled'])->count();
                        $doneTask = \App\Models\Task::where('workspace_id', $wid)->where('status', 'done')->count();
                        $lateTask = \App\Models\Task::where('workspace_id', $wid)
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

                <div class="text-sm space-y-1">
                    <p><span class="font-medium">PM:</span> {{ $wsDetail->pm?->name ?? '-' }}</p>
                </div>

                <div>
                    <h4 class="text-sm font-semibold text-gray-700 mb-2">Anggota ({{ $wsDetail->members->count() }})</h4>
                    @if($wsDetail->members->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="min-w-full text-sm">
                                <thead>
                                    <tr class="border-b">
                                        <th class="text-left py-2 text-xs font-medium text-gray-500">Nama</th>
                                        <th class="text-left py-2 text-xs font-medium text-gray-500">Email</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($wsDetail->members as $m)
                                    <tr class="border-b border-gray-100">
                                        <td class="py-2">{{ $m->name }}</td>
                                        <td class="py-2 text-gray-500">{{ $m->email }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-sm text-gray-400">Belum ada anggota.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
