<div>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Admin Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-6 px-6">
        <div class="max-w-7xl mx-auto space-y-6">

            <!-- Global Stats -->
            <div class="grid grid-cols-3 gap-4">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 text-center">
                    <div class="text-gray-500">Total Users</div>
                    <div class="text-3xl font-bold">{{ $stats['users'] }}</div>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 text-center">
                    <div class="text-gray-500">Total Workspaces</div>
                    <div class="text-3xl font-bold text-indigo-600">{{ $stats['workspaces'] }}</div>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 text-center">
                    <div class="text-gray-500">Total Tasks</div>
                    <div class="text-3xl font-bold text-blue-600">{{ $stats['tasks']['total'] }}</div>
                </div>
            </div>

            <!-- Task Status Breakdown -->
            <div class="grid grid-cols-4 gap-4">
                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 text-center">
                    <div class="text-sm text-yellow-700 font-semibold">Todo</div>
                    <div class="text-2xl font-bold text-yellow-600">{{ $stats['tasks']['todo'] }}</div>
                </div>
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 text-center">
                    <div class="text-sm text-blue-700 font-semibold">On Progress</div>
                    <div class="text-2xl font-bold text-blue-600">{{ $stats['tasks']['on_progress'] }}</div>
                </div>
                <div class="bg-green-50 border border-green-200 rounded-lg p-4 text-center">
                    <div class="text-sm text-green-700 font-semibold">Done</div>
                    <div class="text-2xl font-bold text-green-600">{{ $stats['tasks']['done'] }}</div>
                </div>
                <div class="bg-gray-50 border border-gray-200 rounded-lg p-4 text-center">
                    <div class="text-sm text-gray-700 font-semibold">Total</div>
                    <div class="text-2xl font-bold">{{ $stats['tasks']['total'] }}</div>
                </div>
            </div>

            <!-- User Management -->
            <div class="p-4 bg-white shadow sm:rounded-lg">
                <h3 class="text-lg font-medium text-gray-900 mb-4">User Management</h3>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Role</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Phone</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Action</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Contact</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($users as $user)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">{{ $user->name }}</div>
                                    <div class="text-sm text-gray-500">{{ $user->email }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @php
                                        $roleLabel = match($user->role) {
                                            'admin' => 'Admin',
                                            'pm' => 'Project Manager',
                                            'member' => 'Anggota',
                                            default => ucfirst($user->role),
                                        };
                                        $roleClass = match($user->role) {
                                            'admin' => 'bg-red-100 text-red-800',
                                            'pm' => 'bg-purple-100 text-purple-800',
                                            default => 'bg-gray-100 text-gray-800',
                                        };
                                    @endphp
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $roleClass }}">
                                        {{ $roleLabel }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $user->phone ?? '-' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($user->is_active)
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Active</span>
                                    @else
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">Suspended</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    @if($user->role === 'admin')
                                        <span class="text-gray-400 text-xs">—</span>
                                    @else
                                    <button wire:click="toggleUserStatus({{ $user->id }})" wire:confirm="Toggle status for this user?" class="text-indigo-600 hover:text-indigo-900">
                                        {{ $user->is_active ? 'Suspend' : 'Activate' }}
                                    </button>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <div x-data="{ open: false }" class="relative">
                                        <button @click="open = !open" class="inline-flex items-center gap-1 px-3 py-1.5 bg-blue-50 text-blue-700 rounded-md hover:bg-blue-100">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                                            Hubungi
                                        </button>
                                        <div x-show="open" @click.outside="open = false" class="absolute right-0 mt-1 w-48 bg-white rounded-md shadow-lg border z-10" x-transition>
                                            <a href="{{ route('admin.compose.email') }}?recipient={{ $user->id }}"
                                               class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                                Kirim Email
                                            </a>
                                            @if($user->phone)
                                                <a href="https://wa.me/{{ $user->phone }}" target="_blank"
                                                   class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                                    WhatsApp
                                                </a>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Workspaces -->
            <div class="p-4 bg-white shadow sm:rounded-lg">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Workspaces</h3>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Name</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">PM</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Description</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($workspaces as $ws)
                            <tr>
                                <td class="px-6 py-4 text-sm text-gray-900">{{ $ws->name }}</td>
                                <td class="px-6 py-4 text-sm text-gray-500">{{ $ws->pm?->name ?? '-' }}</td>
                                <td class="px-6 py-4 text-sm text-gray-500">{{ $ws->description ?? '-' }}</td>
                            </tr>
                            @empty
                            <tr><td class="px-6 py-4 text-sm text-gray-400" colspan="3">No workspaces.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Teams & Tugas Sedang Dikerjakan -->
            <div class="p-4 bg-white shadow sm:rounded-lg">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Tim & Tugas Sedang Dikerjakan</h3>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tim</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Project Manager</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Anggota</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tugas On Progress</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($teams as $team)
                            <tr>
                                <td class="px-6 py-4 text-sm font-semibold text-gray-900">{{ $team->name }}</td>
                                <td class="px-6 py-4">
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                                        {{ $team->owner?->name ?? '-' }}
                                        <span class="text-purple-400">(Project Manager)</span>
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex flex-wrap gap-1">
                                        @forelse($team->members as $member)
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium {{ $member->role === 'admin' ? 'bg-indigo-100 text-indigo-800' : 'bg-gray-100 text-gray-700' }}">
                                                <span class="w-4 h-4 rounded-full {{ $member->role === 'admin' ? 'bg-indigo-500' : 'bg-gray-400' }} flex items-center justify-center text-[10px] text-white font-bold">{{ strtoupper(substr($member->user?->name ?? '?', 0, 1)) }}</span>
                                                {{ $member->user?->name ?? 'Unknown' }}
                                                <span class="{{ $member->role === 'admin' ? 'text-indigo-500' : 'text-gray-400' }}">({{ $member->role === 'admin' ? 'Admin Tim' : 'Anggota' }})</span>
                                            </span>
                                        @empty
                                            <span class="text-gray-400 text-xs">—</span>
                                        @endforelse
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    @php
                                        $onProgressTasks = $team->tasks->where('status', 'on_progress')->take(5);
                                    @endphp
                                    @if($onProgressTasks->isEmpty())
                                        <span class="text-gray-400">—</span>
                                    @else
                                        <div class="space-y-2">
                                            @foreach($onProgressTasks as $task)
                                            <div class="flex items-center justify-between gap-2">
                                                <div class="flex-1 min-w-0">
                                                    <div class="text-sm font-medium truncate">{{ $task->title }}</div>
                                                    <div class="flex gap-2 text-xs text-gray-500">
                                                        <span>{{ ucfirst($task->priority) }}</span>
                                                        <span>·</span>
                                                        <span>{{ $task->assignee?->name ?? 'Unassigned' }}</span>
                                                        @if($task->deadline)
                                                            <span>·</span>
                                                            <span>{{ $task->deadline->format('d M') }}</span>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                            @endforeach
                                        </div>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr><td class="px-6 py-4 text-sm text-gray-400" colspan="4">No teams.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Daftar Semua Tugas -->
            <div class="p-4 bg-white shadow sm:rounded-lg">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-medium text-gray-900">Daftar Semua Tugas</h3>
                    <a href="{{ route('admin.tasks.oversight') }}" class="text-sm text-indigo-600 hover:underline">Lihat Semua →</a>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Task</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Workspace</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Assignee</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Deadline</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Action</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($tasks as $task)
                            <tr>
                                <td class="px-6 py-4">
                                    <div class="text-sm font-medium text-gray-900">{{ $task->title }}</div>
                                    <div class="text-xs text-gray-500 truncate max-w-xs">{{ $task->description }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $task->workspace->name ?? '-' }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $task->assignee->name ?? 'Unassigned' }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                        {{ $task->status === 'done' ? 'bg-green-100 text-green-800' : ($task->status === 'on_progress' ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-800') }}">
                                        {{ strtoupper(str_replace('_', ' ', $task->status)) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm {{ $task->deadline && $task->deadline < now() && $task->status !== 'done' ? 'text-red-600 font-bold' : 'text-gray-500' }}">
                                    {{ $task->deadline?->format('Y-m-d') ?? '-' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                    <a href="{{ route('admin.tasks.oversight', $task->id) }}" class="text-indigo-600 hover:text-indigo-900 font-medium">Detail</a>
                                </td>
                            </tr>
                            @empty
                            <tr><td class="px-6 py-4 text-center text-gray-400" colspan="6">No tasks.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Tugas Per Workspace -->
            <div class="p-4 bg-white shadow sm:rounded-lg">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Tugas Per Workspace</h3>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Workspace</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">PM</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Todo</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">On Progress</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Done</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($workspaces as $ws)
                            <tr>
                                <td class="px-6 py-4 text-sm font-semibold text-gray-900">{{ $ws->name }}</td>
                                <td class="px-6 py-4 text-sm text-gray-500">{{ $ws->pm?->name ?? '-' }}</td>
                                <td class="px-6 py-4 text-sm text-yellow-600">{{ $ws->tasks->where('status', 'todo')->count() }}</td>
                                <td class="px-6 py-4 text-sm text-blue-600">{{ $ws->tasks->where('status', 'on_progress')->count() }}</td>
                                <td class="px-6 py-4 text-sm text-green-600">{{ $ws->tasks->where('status', 'done')->count() }}</td>
                            </tr>
                            @empty
                            <tr><td class="px-6 py-4 text-sm text-gray-400" colspan="5">No workspaces.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
</div>
