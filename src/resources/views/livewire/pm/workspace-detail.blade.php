<div>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Workspace Tim Saya</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if(!$workspace)
                <div class="p-6 bg-white shadow sm:rounded-lg">
                    <p class="text-gray-600">Belum punya workspace. Buat workspace dulu dari Dashboard.</p>
                </div>
            @else
                <div class="bg-white shadow sm:rounded-lg overflow-hidden">
                    <div class="p-6 border-b border-gray-200">
                        <div class="flex items-center justify-between">
                            <div>
                                <h3 class="text-xl font-semibold text-gray-900">{{ $workspace->name }}</h3>
                                @if($workspace->description)
                                    <p class="text-sm text-gray-500 mt-1">{{ $workspace->description }}</p>
                                @endif
                            </div>
                            <span class="text-xs px-3 py-1 bg-indigo-50 text-indigo-700 rounded-full font-medium">Aktif</span>
                        </div>
                    </div>

                    @if($stats)
                        <div class="grid grid-cols-3 divide-x divide-gray-200">
                            <div class="p-6 text-center">
                                <div class="text-2xl font-bold text-gray-900">{{ $stats['member_count'] }}</div>
                                <div class="text-xs text-gray-500 mt-1">Anggota</div>
                            </div>
                            <div class="p-6 text-center">
                                <div class="text-2xl font-bold text-indigo-600">{{ $stats['active_tasks'] }}</div>
                                <div class="text-xs text-gray-500 mt-1">Tugas Aktif</div>
                            </div>
                            <div class="p-6 text-center">
                                <div class="text-2xl font-bold text-green-600">{{ $stats['done_tasks'] }}</div>
                                <div class="text-xs text-gray-500 mt-1">Selesai</div>
                            </div>
                        </div>
                    @endif

                    <div class="p-6 border-t border-gray-200">
                        <h4 class="text-sm font-semibold text-gray-700 mb-3">Informasi Workspace</h4>
                        <div class="space-y-2 text-sm">
                            <div class="flex justify-between max-w-sm">
                                <span class="text-gray-500">Project Manager</span>
                                <span class="font-medium">{{ auth()->user()->name }}</span>
                            </div>
                            <div class="flex justify-between max-w-sm">
                                <span class="text-gray-500">Total Anggota</span>
                                <span class="font-medium">{{ $stats ? $stats['member_count'] : 0 }} orang</span>
                            </div>
                            <div class="flex justify-between max-w-sm">
                                <span class="text-gray-500">Total Tugas</span>
                                <span class="font-medium">{{ $stats ? $stats['total_tasks'] : 0 }}</span>
                            </div>
                            <div class="flex justify-between max-w-sm">
                                <span class="text-gray-500">Dibuat</span>
                                <span class="font-medium">{{ $workspace->created_at ? $workspace->created_at->format('d M Y') : '-' }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="p-6 border-t border-gray-200">
                        <h4 class="text-sm font-semibold text-gray-700 mb-3">Semua Anggota</h4>
                        @php $members = $workspace->members()->latest()->get(); @endphp
                        <div class="space-y-2">
                            <div class="flex items-center gap-3 px-3 py-2 bg-purple-50 rounded-lg">
                                <div class="w-8 h-8 rounded-full bg-purple-200 flex items-center justify-center text-purple-700 font-bold text-xs">
                                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                                </div>
                                <div>
                                    <div class="text-sm font-medium text-gray-900">{{ auth()->user()->name }}</div>
                                    <div class="text-xs text-purple-500">Project Manager</div>
                                </div>
                            </div>
                            @forelse($members as $member)
                                <div class="flex items-center gap-3 px-3 py-2 rounded-lg hover:bg-gray-50">
                                    <div class="w-8 h-8 rounded-full bg-gray-100 flex items-center justify-center text-gray-600 font-bold text-xs">
                                        {{ strtoupper(substr($member->name, 0, 1)) }}
                                    </div>
                                    <div>
                                        <div class="text-sm font-medium text-gray-900">{{ $member->name }}</div>
                                        <div class="text-xs text-gray-500">{{ $member->email }}</div>
                                    </div>
                                </div>
                            @empty
                                <p class="text-sm text-gray-400">Belum ada anggota.</p>
                            @endforelse
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
