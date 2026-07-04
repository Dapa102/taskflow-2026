<div>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('My Tasks') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if (session()->has('message'))
                <div class="p-4 mb-4 text-sm text-green-800 rounded-lg bg-green-50">{{ session('message') }}</div>
            @endif
            @if (session()->has('error'))
                <div class="p-4 mb-4 text-sm text-red-800 rounded-lg bg-red-50">{{ session('error') }}</div>
            @endif

            @if($pm)
            <div class="bg-white shadow sm:rounded-lg p-4 flex items-center justify-between">
                <div class="text-sm text-gray-600">
                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                        {{ $pm->name }}
                        <span class="text-purple-400">(Project Manager)</span>
                    </span>
                    @if($pm->phone) &middot; <span class="text-gray-400">{{ $pm->phone }}</span> @endif
                </div>
                <div>
                    <button x-data @click="$dispatch('open-modal', 'contact-pm')"
                            class="inline-flex items-center gap-1 px-3 py-1.5 bg-blue-50 text-blue-700 rounded-md hover:bg-blue-100 text-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                        Hubungi PM
                    </button>

                    <x-modal name="contact-pm" maxWidth="md">
                        <div class="p-6">
                            <div class="flex items-center justify-between mb-4">
                                <h3 class="text-lg font-semibold text-gray-900">Hubungi {{ $pm->name }}</h3>
                                <button @click="$dispatch('close-modal', 'contact-pm')" class="text-gray-400 hover:text-gray-600">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                </button>
                            </div>
                            <div class="space-y-3">
                                <p class="text-sm text-gray-600">
                                    {{ $pm->email }}
                                    @if($pm->phone) &middot; {{ $pm->phone }} @endif
                                </p>
                                <a href="mailto:{{ $pm->email }}?subject=Task%20Question"
                                   class="flex items-center gap-2 px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 text-sm">
                                    Kirim Email
                                </a>
                                @if($pm->phone)
                                    <a href="https://wa.me/{{ $pm->phone }}" target="_blank"
                                       class="flex items-center gap-2 px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 text-sm">
                                        WhatsApp (external)
                                    </a>
                                @endif
                            </div>
                        </div>
                    </x-modal>
                </div>
            </div>
            @endif

            @if($myTeams->isNotEmpty())
            <div class="bg-white shadow sm:rounded-lg p-4">
                <h3 class="text-sm font-medium text-gray-700 mb-2">Team Saya</h3>
                <div class="flex flex-wrap gap-2">
                    @foreach($myTeams as $tm)
                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800">
                        {{ $tm->team->name }}
                        <span class="text-indigo-400">
                            ({{ $tm->team->owner?->name ?? '-' }} - Project Manager)
                        </span>
                    </span>
                    @endforeach
                </div>
            </div>
            @endif

            <div class="grid grid-cols-4 gap-4">
                <div class="bg-white shadow sm:rounded-lg p-4">
                    <div class="text-sm text-gray-500">Total Tugas</div>
                    <div class="text-2xl font-bold">{{ $total }}</div>
                </div>
                <div class="bg-white shadow sm:rounded-lg p-4">
                    <div class="text-sm text-gray-500">Selesai</div>
                    <div class="text-2xl font-bold text-green-600">{{ $done }}</div>
                </div>
                <div class="bg-white shadow sm:rounded-lg p-4">
                    <div class="text-sm text-gray-500">Perlu Revisi</div>
                    <div class="text-2xl font-bold text-orange-600">{{ $revisionCount }}</div>
                </div>
                <div class="bg-white shadow sm:rounded-lg p-4">
                    <div class="text-sm text-gray-500">Jumlah Deadline</div>
                    <div class="text-2xl font-bold text-rose-600">{{ $deadlineCount }}</div>
                </div>
            </div>

            <div class="bg-white shadow sm:rounded-lg p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Overview Tugas</h3>
                <div x-data="{ chart: @js($chartData) }">
                    <div class="flex items-end gap-6 h-40 px-4">
                        <template x-for="item in chart" :key="item.label">
                            <div class="flex-1 flex flex-col items-center justify-end h-full">
                                <div class="text-xs font-semibold mb-1" :style="'color: ' + item.bg" x-text="item.count"></div>
                                <div class="w-full rounded-t transition-all duration-300 min-h-[4px]"
                                     :style="'height: ' + (item.count / Math.max(...chart.map(d => d.count), 1) * 100) + '%; background-color: ' + item.bg">
                                </div>
                                <span class="text-xs text-gray-500 mt-2" x-text="item.label"></span>
                            </div>
                        </template>
                    </div>
                </div>
            </div>

            <div class="bg-white shadow sm:rounded-lg p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Tugas Saya</h3>
                <div class="space-y-4">
                    @forelse($tasks as $task)
                        <div class="p-4 border rounded-lg {{ $task->status === 'revision' ? 'border-orange-300 bg-orange-50' : ($task->status === 'done' ? 'bg-gray-50' : ($task->status === 'pending_pm' ? 'border-yellow-200 bg-yellow-50' : ($task->status === 'assigned_member' ? 'border-blue-200 bg-blue-50' : 'bg-white'))) }}">
                            <div class="flex justify-between items-start">
                                <div class="flex-1">
                                    <div class="font-medium {{ $task->status === 'done' ? 'line-through text-gray-500' : '' }}">
                                        {{ $task->title }}
                                        @if($task->status === 'revision')
                                            <span class="ml-2 text-xs bg-orange-100 text-orange-700 px-2 py-0.5 rounded-full">Perlu Revisi</span>
                                        @endif
                                    </div>
                                    <div class="text-sm text-gray-500 mt-1">
                                        Workspace: {{ $task->workspace->name ?? 'Unknown' }} |
                                        Prioritas: {{ ucfirst($task->priority) }} |
                                        Status:
                                        <span class="font-semibold
                                            {{ $task->status === 'done' ? 'text-green-600' : '' }}
                                            {{ $task->status === 'assigned_member' ? 'text-blue-600' : '' }}
                                            {{ $task->status === 'pending_pm' ? 'text-yellow-600' : '' }}
                                            {{ $task->status === 'revision' ? 'text-orange-600' : '' }}
                                            {{ $task->status === 'cancelled' ? 'text-gray-400' : '' }}">
                                            @switch($task->status)
                                                @case('assigned_member') Sedang Dikerjakan @break
                                                @case('pending_pm') Menunggu Review PM @break
                                                @case('revision') Revisi @break
                                                @case('done') Selesai @break
                                                @case('cancelled') Dibatalkan @break
                                                @case('pending_admin') Menunggu Approval Admin @break
                                                @case('pending_arbitration') Arbitrase @break
                                                @default {{ ucfirst($task->status) }}
                                            @endswitch
                                        </span>
                                    </div>
                                    @if($task->status === 'revision' || $task->status === 'pending_arbitration')
                                        <div class="text-xs mt-1 {{ $task->revision_counter >= $task->max_revision_limit ? 'text-red-500 font-bold' : 'text-orange-500' }}">
                                            Revisi: {{ $task->revision_counter }}/{{ $task->max_revision_limit }}
                                        </div>
                                    @endif
                                    @if($task->deadline)
                                        <div class="text-sm {{ $task->isOverdue() ? 'text-red-500 font-bold' : 'text-gray-500' }}">
                                            Deadline: {{ $task->deadline->format('Y-m-d') }}
                                        </div>
                                    @endif
                                    @if($task->review_note)
                                        <div class="mt-2 text-xs text-orange-700 bg-orange-100 p-2 rounded">
                                            <strong>Catatan Revisi:</strong> {{ $task->review_note }}
                                        </div>
                                    @endif
                                    @if($task->attachments->count() > 0)
                                        <div class="mt-1 text-xs text-gray-500 space-y-0.5">
                                            @foreach($task->attachments as $att)
                                            <div class="flex items-center gap-1">
                                                <span>📎 {{ $att->filename }}</span>
                                            </div>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>

                                <div class="shrink-0 ml-4">
                                    @if($task->status === 'assigned_member' || $task->status === 'revision')
                                        <div class="space-y-2">
                                            <input type="file" wire:model="upload.{{ $task->id }}" class="text-xs w-40">
                                            @error("upload.{$task->id}") <span class="text-red-500 text-xs block">{{ $message }}</span> @enderror
                                            <button wire:click="submitTask({{ $task->id }})"
                                                wire:loading.attr="disabled"
                                                class="px-3 py-1 text-xs bg-blue-600 text-white rounded-md hover:bg-blue-700 disabled:opacity-50 w-full">
                                                Selesai & Upload
                                            </button>
                                        </div>
                                    @endif
                                    @if($task->status === 'pending_pm')
                                        <span class="text-xs text-yellow-600 font-medium">Menunggu Review PM</span>
                                    @endif
                                    @if($task->status === 'pending_admin')
                                        <span class="text-xs text-purple-600 font-medium">Menunggu Approval Admin</span>
                                    @endif
                                    @if($task->status === 'done')
                                        <span class="text-xs text-green-600 font-medium">✓ Selesai</span>
                                    @endif
                                    @if($task->status === 'cancelled')
                                        <span class="text-xs text-gray-400 font-medium">✕ Dibatalkan</span>
                                    @endif
                                    @if($task->status === 'pending_arbitration')
                                        <span class="text-xs text-red-600 font-medium">Arbitrase</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center text-gray-500 py-8">
                            Tidak ada tugas.
                        </div>
                    @endforelse
                </div>
            </div>

        </div>
    </div>
</div>
