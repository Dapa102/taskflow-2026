<div>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Workspace</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if(session('message'))
                <div class="p-4 text-sm text-green-800 rounded-lg bg-green-50">{{ session('message') }}</div>
            @endif

            <div class="bg-white shadow sm:rounded-lg p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Buat Workspace Baru</h3>
                <form wire:submit="create" class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <input wire:model="name" type="text" placeholder="Nama workspace"
                        class="border-gray-300 rounded-md shadow-sm focus:border-indigo-500 text-sm" />
                    @error('name') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    <input wire:model="description" type="text" placeholder="Deskripsi (opsional)"
                        class="border-gray-300 rounded-md shadow-sm focus:border-indigo-500 text-sm" />
                    <select wire:model="pmId"
                        class="border-gray-300 rounded-md shadow-sm focus:border-indigo-500 text-sm">
                        <option value="">Pilih PM</option>
                        @foreach($pms as $pm)
                            <option value="{{ $pm->id }}">{{ $pm->name }} ({{ $pm->email }})</option>
                        @endforeach
                    </select>
                    @error('pmId') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    <div class="md:col-span-3">
                        <button type="submit" class="px-4 py-2 bg-indigo-600 text-white text-sm rounded-md hover:bg-indigo-700">Buat</button>
                    </div>
                </form>
            </div>

            <div class="bg-white shadow sm:rounded-lg overflow-hidden">
                <div class="p-6 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Semua Workspace ({{ $workspaces->count() }})</h3>
                </div>
                <div class="divide-y divide-gray-100">
                    @forelse($workspaces as $ws)
                        <div class="p-6 hover:bg-gray-50">
                            @if($editId === $ws['id'])
                                <form wire:submit="update" class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-3">
                                    <input wire:model="editName" type="text"
                                        class="border-gray-300 rounded-md shadow-sm focus:border-indigo-500 text-sm" />
                                    <input wire:model="editDesc" type="text"
                                        class="border-gray-300 rounded-md shadow-sm focus:border-indigo-500 text-sm" />
                                    <select wire:model="editPmId"
                                        class="border-gray-300 rounded-md shadow-sm focus:border-indigo-500 text-sm">
                                        @foreach($pms as $pm)
                                            <option value="{{ $pm->id }}">{{ $pm->name }}</option>
                                        @endforeach
                                    </select>
                                    <select wire:model="editDeputyPmId"
                                        class="border-gray-300 rounded-md shadow-sm focus:border-indigo-500 text-sm">
                                        <option value="">Deputy PM (opsional)</option>
                                        @foreach($pms as $pm)
                                            <option value="{{ $pm->id }}">{{ $pm->name }}</option>
                                        @endforeach
                                    </select>
                                    <div class="flex gap-2 md:col-span-4">
                                        <button type="submit" class="px-3 py-1 text-xs bg-indigo-600 text-white rounded-md hover:bg-indigo-700">Simpan</button>
                                        <button type="button" wire:click="$set('editId', null)" class="px-3 py-1 text-xs text-gray-600 hover:text-gray-900">Batal</button>
                                    </div>
                                </form>
                            @else
                                <div class="flex items-start justify-between">
                                    <div class="flex-1">
                                        <div class="font-semibold text-gray-900">{{ $ws['name'] }}</div>
                                        @if($ws['description'])
                                            <div class="text-sm text-gray-500">{{ $ws['description'] }}</div>
                                        @endif
                                        <div class="text-xs text-gray-400 mt-1">
                                            PM: <span class="font-medium text-gray-700">{{ $ws['pm']?->name ?? '—' }}</span>
                                            @if($ws['deputy_pm'])
                                            &middot; Deputy: <span class="font-medium text-gray-700">{{ $ws['deputy_pm']->name }}</span>
                                            @endif
                                            &middot; {{ $ws['project_count'] }} project &middot; {{ $ws['task_count'] }} tugas
                                            &middot; {{ $ws['member_count'] }} anggota
                                            &middot; Dibuat {{ $ws['created_at']?->format('d M Y') }}
                                        </div>
                                    </div>
                                    <div class="flex gap-2 shrink-0 ml-4">
                                        <button wire:click="edit({{ $ws['id'] }})"
                                            class="text-xs text-indigo-600 hover:text-indigo-800">Edit</button>
                                        <button wire:click="delete({{ $ws['id'] }})" wire:confirm="Hapus workspace ini?"
                                            class="text-xs text-red-500 hover:text-red-700">Hapus</button>
                                    </div>
                                </div>
                            @endif
                        </div>
                    @empty
                        <div class="p-6 text-center text-gray-400 text-sm">Belum ada workspace.</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
