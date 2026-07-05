<div>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Tim Saya</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if(session('message'))
                <div class="p-4 text-sm text-green-800 rounded-lg bg-green-50">{{ session('message') }}</div>
            @endif
            @if(session('error'))
                <div class="p-4 text-sm text-red-800 rounded-lg bg-red-50">{{ session('error') }}</div>
            @endif

            @if(!$workspace)
                <div class="p-6 bg-white shadow sm:rounded-lg">
                    <p class="text-gray-600">Belum punya workspace. Buat workspace dulu dari Dashboard.</p>
                </div>
            @else
                <div class="bg-white shadow sm:rounded-lg overflow-hidden">
                    <div class="p-6 border-b border-gray-200 flex items-center justify-between">
                        <h3 class="text-lg font-semibold text-gray-900">Anggota Tim ({{ $members->count() }})</h3>
                        <div class="flex items-center gap-2">
                            <span class="text-xs text-gray-500">PM: {{ auth()->user()->name }}</span>
                        </div>
                    </div>

                    <div class="p-6 border-b border-gray-200 bg-gray-50">
                        <form wire:submit="inviteMember" class="flex gap-3 max-w-md">
                            <input wire:model="inviteEmail" type="email" placeholder="Masukkan email member"
                                class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm" />
                            <button type="submit" class="px-4 py-2 bg-indigo-600 text-white text-sm rounded-md hover:bg-indigo-700 shrink-0">Tambah</button>
                        </form>
                        @error('inviteEmail') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="divide-y divide-gray-100">
                        @forelse($members as $member)
                            @php $wl = $memberWorkload->firstWhere('user.id', $member->id); @endphp
                            <div class="flex items-center justify-between px-6 py-4 hover:bg-gray-50">
                                <div>
                                    <div class="text-sm font-medium text-gray-900">{{ $member->name }}</div>
                                    <div class="text-xs text-gray-500">{{ $member->email }}</div>
                                </div>
                                <div class="flex items-center gap-4">
                                    @if($wl)
                                        <span class="text-xs px-2 py-1 rounded-full {{ $wl['active_tasks'] > 0 ? 'bg-indigo-50 text-indigo-700' : 'bg-gray-100 text-gray-500' }}">
                                            {{ $wl['active_tasks'] }} tugas aktif
                                        </span>
                                    @endif
                                    <button wire:click="removeMember({{ $member->id }})" wire:confirm="Hapus anggota ini?"
                                        class="text-xs text-red-500 hover:text-red-700">Hapus</button>
                                </div>
                            </div>
                        @empty
                            <div class="px-6 py-10 text-center text-gray-400 text-sm">Belum ada anggota tim.</div>
                        @endforelse
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
