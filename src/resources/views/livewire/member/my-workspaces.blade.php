<div>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Workspace Saya</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            @if($workspaces->isEmpty())
                <div class="text-center text-gray-400 text-sm py-16">Belum tergabung dalam workspace.</div>
            @else
                <div class="bg-white shadow sm:rounded-lg p-6">
                    <div class="space-y-3">
                        @foreach($workspaces as $ws)
                        <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                            <div>
                                <p class="font-medium text-gray-900">{{ $ws['name'] }}</p>
                                @if($ws['description'])
                                <p class="text-sm text-gray-500 mt-1">{{ $ws['description'] }}</p>
                                @endif
                                @if($ws['pm'])
                                <p class="text-sm text-gray-500 mt-1">PM: {{ $ws['pm']->name }}</p>
                                @endif
                                <p class="text-xs text-gray-400 mt-1">{{ $ws['member_count'] }} anggota &middot; {{ $ws['task_count'] }} tugas</p>
                            </div>
                            <button wire:click="showDetail({{ $ws['id'] }})"
                                class="px-3 py-1.5 text-xs font-medium text-indigo-600 bg-indigo-50 rounded-md hover:bg-indigo-100">
                                Detail
                            </button>
                        </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </div>

    {{-- Modal Detail --}}
    @if($detailModal && $wsDetail)
    <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/40" wire:click.self="$set('detailModal', false)">
        <div class="bg-white rounded-xl shadow-xl max-w-2xl w-full mx-4 max-h-[80vh] overflow-y-auto">
            <div class="flex justify-between items-center p-4 border-b sticky top-0 bg-white z-10 rounded-t-xl">
                <h3 class="text-lg font-semibold text-gray-900">{{ $wsDetail->name }}</h3>
                <button wire:click="$set('detailModal', false)" class="text-gray-400 hover:text-gray-600 text-xl leading-none">&times;</button>
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
                    @else
                        <p class="text-sm text-gray-400">Belum ada anggota.</p>
                    @endif
                </div>

                <div>
                    <h4 class="text-sm font-semibold text-gray-700 mb-2">Tugas Selesai</h4>
                    @php
                        $doneTasks = \App\Models\Task::with('assignedMember')
                            ->where('workspace_id', $wid)
                            ->where('status', 'done')
                            ->latest()->limit(10)->get();
                    @endphp
                    @if($doneTasks->count() > 0)
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
                    @else
                        <p class="text-sm text-gray-400">Belum ada tugas selesai.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
