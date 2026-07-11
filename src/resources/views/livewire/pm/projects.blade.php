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
                        <div class="bg-white shadow sm:rounded-lg p-6">
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
                                <div class="flex items-center gap-2">
                                    <button wire:click="showProjectDetail({{ $project->id }})" class="px-3 py-1 text-xs font-medium text-indigo-600 bg-indigo-50 rounded-md hover:bg-indigo-100">Detail</button>
                                    <button wire:click="editProject({{ $project->id }})" class="px-3 py-1 text-xs font-medium text-yellow-600 bg-yellow-50 rounded-md hover:bg-yellow-100">Edit</button>
                                    <button wire:click="deleteProject({{ $project->id }})" wire:confirm="Hapus project '{{ $project->name }}'? Semua tugas di dalamnya juga akan terhapus." class="px-3 py-1 text-xs font-medium text-red-600 bg-red-50 rounded-md hover:bg-red-100">Hapus</button>
                                    <span class="text-xs px-2 py-1 rounded-full {{ $project->status === 'active' ? 'bg-green-50 text-green-700' : 'bg-gray-100 text-gray-500' }}">
                                        {{ ucfirst($project->status) }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center text-gray-400 text-sm py-10">Belum ada project. Buat project pertama!</div>
                    @endforelse
                </div>
            @endif
        </div>
    </div>

    {{-- Edit Project Modal --}}
    @if($showEditModal && $editProjectId)
    @php $pj = $projects->firstWhere('id', $editProjectId); @endphp
    <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/40" wire:click.self="$set('showEditModal', false)">
        <div class="bg-white rounded-xl shadow-xl max-w-lg w-full mx-4">
            <div class="flex justify-between items-center p-4 border-b">
                <h3 class="text-lg font-semibold text-gray-900">Edit Project</h3>
                <button wire:click="$set('showEditModal', false)" class="text-gray-400 hover:text-gray-600 text-xl leading-none">&times;</button>
            </div>
            <form wire:submit="updateProject" class="p-4 space-y-4">
                <div>
                    <x-input-label value="Nama Project" />
                    <x-text-input wire:model="editName" type="text" class="mt-1 block w-full" required />
                    <x-input-error :messages="$errors->get('editName')" class="mt-1" />
                </div>
                <div>
                    <x-input-label value="Deskripsi" />
                    <textarea wire:model="editDescription" rows="3" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm text-sm"></textarea>
                    <x-input-error :messages="$errors->get('editDescription')" class="mt-1" />
                </div>
                <div>
                    <x-input-label value="Deadline" />
                    <x-text-input wire:model="editDeadline" type="date" class="mt-1 block w-full" />
                    <x-input-error :messages="$errors->get('editDeadline')" class="mt-1" />
                </div>
                <div class="flex gap-2 justify-end pt-2">
                    <button type="button" wire:click="$set('showEditModal', false)" class="px-4 py-2 text-sm text-gray-600 hover:text-gray-900">Batal</button>
                    <x-primary-button>Simpan</x-primary-button>
                </div>
            </form>
        </div>
    </div>
    @endif

    @if($projectDetailModal && $projectDetail)
    @php $prj = $projectDetail; @endphp
    <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/40" wire:click.self="$set('projectDetailModal', false)">
        <div class="bg-white rounded-xl shadow-xl max-w-2xl w-full mx-4 max-h-[80vh] overflow-y-auto">
            <div class="flex justify-between items-center p-4 border-b sticky top-0 bg-white z-10">
                <h3 class="text-lg font-semibold text-gray-900">{{ $prj->name }}</h3>
                <button wire:click="$set('projectDetailModal', false)" class="text-gray-400 hover:text-gray-600 text-xl leading-none">&times;</button>
            </div>
            <div class="p-4 space-y-4">
                @if($prj->description)
                <div>
                    <h4 class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">Deskripsi</h4>
                    <p class="text-sm text-gray-700 bg-gray-50 rounded-lg p-3 border border-gray-100">{{ $prj->description }}</p>
                </div>
                @endif
                <table class="w-full text-sm">
                    <tbody>
                        <tr>
                            <td class="py-2 pr-4 align-top w-[140px] text-xs font-semibold text-gray-500 uppercase tracking-wide">Status</td>
                            <td class="py-2"><span class="px-2 py-0.5 text-xs rounded-full {{ $prj->status === 'active' ? 'bg-green-50 text-green-700' : 'bg-gray-100 text-gray-600' }}">{{ ucfirst($prj->status) }}</span></td>
                        </tr>
                        <tr>
                            <td class="py-2 pr-4 align-top text-xs font-semibold text-gray-500 uppercase tracking-wide">Total Tugas</td>
                            <td class="py-2">{{ $prj->tasks_count }}</td>
                        </tr>
                        <tr>
                            <td class="py-2 pr-4 align-top text-xs font-semibold text-gray-500 uppercase tracking-wide">Selesai</td>
                            <td class="py-2 text-green-600 font-medium">{{ $prj->done_count ?? 0 }}</td>
                        </tr>
                        @if($prj->deadline)
                        <tr>
                            <td class="py-2 pr-4 align-top text-xs font-semibold text-gray-500 uppercase tracking-wide">Deadline</td>
                            <td class="py-2">{{ $prj->deadline->format('d M Y') }}</td>
                        </tr>
                        @endif
                        <tr>
                            <td class="py-2 pr-4 align-top text-xs font-semibold text-gray-500 uppercase tracking-wide">Workspace</td>
                            <td class="py-2">{{ $prj->workspace->name ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td class="py-2 pr-4 align-top text-xs font-semibold text-gray-500 uppercase tracking-wide">Dibuat Oleh</td>
                            <td class="py-2">{{ $prj->creator->name ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td class="py-2 pr-4 align-top text-xs font-semibold text-gray-500 uppercase tracking-wide">Tanggal Dibuat</td>
                            <td class="py-2">{{ $prj->created_at->format('d M Y') }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @endif
</div>
