<div>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Project</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if(session('message'))
                <div class="p-4 text-sm text-green-800 rounded-lg bg-green-50">{{ session('message') }}</div>
            @endif
            @if(session('error'))
                <div class="p-4 text-sm text-red-800 rounded-lg bg-red-50">{{ session('error') }}</div>
            @endif

            @if(!$workspace)
                <div class="p-6 bg-white shadow sm:rounded-lg">
                    <p class="text-gray-600">Anda belum ditugaskan ke workspace. Hubungi Super Admin.</p>
                </div>
            @else
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-gray-900">Daftar Project ({{ $projects->count() }})</h3>
                    <button wire:click="toggleForm"
                        class="px-4 py-2 bg-indigo-600 text-white text-sm rounded-md hover:bg-indigo-700">
                        + Project Baru
                    </button>
                </div>

                @if($showForm)
                    <div class="bg-white shadow sm:rounded-lg p-6 border border-indigo-100">
                        <h4 class="text-sm font-semibold text-gray-700 mb-4">Buat Project Baru</h4>
                        <form wire:submit="create" class="space-y-4 max-w-lg">
                            <div>
                                <input wire:model="name" type="text" placeholder="Nama project"
                                    class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 text-sm" />
                                @error('name') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <textarea wire:model="description" rows="3" placeholder="Deskripsi project (opsional)"
                                    class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 text-sm"></textarea>
                            </div>
                            <div>
                                <input wire:model="deadline" type="date"
                                    class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 text-sm" />
                                @error('deadline') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>
                            <div class="flex gap-2">
                                <button type="submit" class="px-4 py-2 bg-indigo-600 text-white text-sm rounded-md hover:bg-indigo-700">Simpan</button>
                                <button type="button" wire:click="toggleForm" class="px-4 py-2 text-sm text-gray-600 hover:text-gray-900">Batal</button>
                            </div>
                        </form>
                    </div>
                @endif

                <div class="space-y-4">
                    @forelse($projects as $project)
                        <div class="bg-white shadow sm:rounded-lg p-6 hover:border-l-4 hover:border-l-indigo-500 transition">
                            <div class="flex items-start justify-between">
                                <div>
                                    <h4 class="font-semibold text-gray-900">{{ $project->name }}</h4>
                                    @if($project->description)
                                        <p class="text-sm text-gray-500 mt-1">{{ $project->description }}</p>
                                    @endif
                                    <div class="text-xs text-gray-400 mt-2 flex gap-3">
                                        <span>{{ $project->tasks_count }} tugas</span>
                                        @if($project->deadline)
                                            <span>Deadline: {{ $project->deadline->format('d M Y') }}</span>
                                        @endif
                                        <span>Dibuat {{ $project->created_at->format('d M Y') }}</span>
                                    </div>
                                </div>
                                <span class="text-xs px-2 py-1 rounded-full {{ $project->status === 'active' ? 'bg-green-50 text-green-700' : 'bg-gray-100 text-gray-500' }}">
                                    {{ ucfirst($project->status) }}
                                </span>
                            </div>
                        </div>
                    @empty
                        <div class="text-center text-gray-400 text-sm py-10">Belum ada project. Buat project pertama!</div>
                    @endforelse
                </div>
            @endif
        </div>
    </div>
</div>
