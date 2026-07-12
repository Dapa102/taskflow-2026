<div>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Tim Saya</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            @if($myTeams->isEmpty())
                <div class="text-center text-gray-400 text-sm py-16">Belum ada tim.</div>
            @else
                <div class="bg-white shadow sm:rounded-lg p-6">
                    <div class="space-y-3">
                        @foreach($myTeams as $team)
                        <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                            <div>
                                <p class="font-medium text-gray-900">{{ $team->name }}</p>
                                <p class="text-sm text-gray-500 mt-1">{{ $team->members->count() }} anggota</p>
                            </div>
                            <button wire:click="showDetail({{ $team->id }})"
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
    @if($detailModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/40" wire:click.self="$set('detailModal', false)">
        <div class="bg-white rounded-xl shadow-xl max-w-lg w-full mx-4 max-h-[80vh] overflow-y-auto">
            <div class="flex justify-between items-center p-4 border-b sticky top-0 bg-white z-10">
                <h3 class="text-lg font-semibold text-gray-900">{{ $detailTeamName }}</h3>
                <button wire:click="$set('detailModal', false)" class="text-gray-400 hover:text-gray-600 text-xl leading-none">&times;</button>
            </div>
            <div class="p-4 space-y-4">
                <div>
                    <h4 class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-2">Anggota ({{ count($detailMembers) }})</h4>
                    <div class="space-y-2">
                        @forelse($detailMembers as $m)
                        <div class="flex items-center gap-3 p-3 bg-gray-50 rounded-lg">
                            <div class="w-8 h-8 rounded-full bg-gray-400 flex items-center justify-center text-white font-bold text-xs">
                                {{ strtoupper(substr($m['name'], 0, 1)) }}
                            </div>
                            <div class="flex-1">
                                <p class="text-sm font-medium text-gray-900">{{ $m['name'] }}</p>
                                <p class="text-xs text-gray-500">{{ $m['email'] }}</p>
                            </div>
                            <span class="text-xs px-2 py-0.5 rounded-full {{ $m['role'] === 'admin' ? 'bg-purple-100 text-purple-700' : 'bg-gray-100 text-gray-600' }}">
                                {{ $m['role'] === 'admin' ? 'Admin' : 'Anggota' }}
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
</div>
