<div>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Buat Tugas Baru</h2>
    </x-slot>

    @if(session('message'))
        <div class="max-w-3xl mx-auto mt-4 px-6">
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg">{{ session('message') }}</div>
        </div>
    @endif

    <div class="py-6 px-6">
        <div class="max-w-3xl mx-auto">
            <div class="bg-white shadow sm:rounded-lg p-6">
                <form wire:submit="save" class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Judul Tugas</label>
                        <input type="text" wire:model="title" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        @error('title') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Deskripsi</label>
                        <textarea wire:model="description" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"></textarea>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Prioritas</label>
                            <select wire:model="priority" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="low">Rendah</option>
                                <option value="medium">Sedang</option>
                                <option value="high">Tinggi</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Deadline</label>
                            <input type="date" wire:model="deadline" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            @error('deadline') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Workspace</label>
                        <select wire:model="workspaceId" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="">Pilih Workspace</option>
                            @foreach($workspaces as $ws)
                            <option value="{{ $ws->id }}">{{ $ws->name }} ({{ $ws->pm->name ?? 'No PM' }})</option>
                            @endforeach
                        </select>
                        @error('workspaceId') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Rekomendasi Project Manager (opsional)</label>
                        <select wire:model="recommendedPmId" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="">Tidak ada rekomendasi</option>
                            @foreach($pms as $pm)
                            <option value="{{ $pm->id }}">{{ $pm->name }} ({{ $pm->email }})</option>
                            @endforeach
                        </select>
                        <p class="text-xs text-gray-400 mt-1">Super Admin dapat merekomendasikan PM saat membuat tugas.</p>
                    </div>
                    <div class="flex justify-end">
                        <button type="submit" class="px-6 py-2.5 bg-indigo-600 text-white rounded-lg text-sm font-semibold hover:bg-indigo-700 shadow-sm">
                            Simpan Tugas
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
