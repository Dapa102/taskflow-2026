<div>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Buat Tugas Baru</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            @if(session('message'))
                <div class="p-4 mb-6 text-sm text-green-800 rounded-lg bg-green-50">{{ session('message') }}</div>
            @endif
            @if(session('error'))
                <div class="p-4 mb-6 text-sm text-red-800 rounded-lg bg-red-50">{{ session('error') }}</div>
            @endif

            @if(!$workspace)
                <div class="p-6 bg-white shadow sm:rounded-lg">
                    <p class="text-gray-600">Anda belum ditugaskan ke workspace.</p>
                </div>
            @else
                <div class="bg-white shadow sm:rounded-lg p-6">
                    <form wire:submit="save" class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Project</label>
                            <select wire:model="projectId" class="mt-1 w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 text-sm">
                                <option value="">Pilih Project</option>
                                @foreach($projects as $p)
                                    <option value="{{ $p->id }}">{{ $p->name }}</option>
                                @endforeach
                            </select>
                            @error('projectId') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Judul Tugas</label>
                            <input wire:model="title" type="text" class="mt-1 w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 text-sm" />
                            @error('title') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Deskripsi</label>
                            <textarea wire:model="description" rows="4" class="mt-1 w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 text-sm"></textarea>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Prioritas</label>
                                <select wire:model="priority" class="mt-1 w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 text-sm">
                                    <option value="low">Low</option>
                                    <option value="medium">Medium</option>
                                    <option value="high">High</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Deadline</label>
                                <input wire:model="deadline" type="date" class="mt-1 w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 text-sm" />
                                @error('deadline') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Tugaskan ke Anggota</label>
                            <select wire:model="assignMemberId" class="mt-1 w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 text-sm">
                                <option value="">Pilih Anggota</option>
                                @foreach($members as $m)
                                    <option value="{{ $m->id }}">{{ $m->name }}</option>
                                @endforeach
                            </select>
                            @error('assignMemberId') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>

                        <div class="pt-2">
                            <button type="submit" class="px-6 py-2 bg-indigo-600 text-white text-sm rounded-md hover:bg-indigo-700">Buat Tugas</button>
                        </div>
                    </form>
                </div>
            @endif
        </div>
    </div>
</div>
