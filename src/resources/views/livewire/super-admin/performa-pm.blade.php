<div>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('PM Performance Metrics') }}
            </h2>
            <a href="{{ route('super-admin.dashboard') }}" class="text-sm text-gray-500 hover:underline">Back to Dashboard</a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="bg-white shadow sm:rounded-lg p-4">
                <form class="flex flex-wrap gap-4 items-end">
                    <div>
                        <label class="block text-xs font-medium text-gray-500 mb-1">Workspace</label>
                        <select wire:model.live="workspaceFilter" class="rounded-md border-gray-300 text-sm shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                            <option value="">Semua Workspace</option>
                            @foreach($workspaces as $w)
                                <option value="{{ $w->id }}">{{ $w->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-500 mb-1">Dari Tanggal</label>
                        <input type="date" wire:model.live="startDate" class="rounded-md border-gray-300 text-sm shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-500 mb-1">Sampai Tanggal</label>
                        <input type="date" wire:model.live="endDate" class="rounded-md border-gray-300 text-sm shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                    </div>
                    <div>
                        <button wire:click="exportPdf" x-data @download-pdf.window="window.location.href = $event.detail.url" class="inline-flex items-center gap-2 px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 text-sm">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            Export PDF
                        </button>
                    </div>
                </form>
            </div>
            
            <div class="bg-white shadow sm:rounded-lg p-6">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Project Manager</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Workspace</th>
                                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Total Tasks</th>
                                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Done</th>
                                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Overdue</th>
                                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Completion Rate</th>
                                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Contact</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($pms as $pm)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">{{ $pm->name }}</div>
                                    <div class="text-xs text-gray-500">{{ $pm->email }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $pm->workspace->name ?? 'No Workspace' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                                    {{ $pm->total_tasks }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium text-green-600">
                                    {{ $pm->done_tasks }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium text-red-600">
                                    {{ $pm->overdue_tasks }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    <span class="text-sm font-medium {{ $pm->on_time_rate > 70 ? 'text-green-600' : ($pm->on_time_rate < 30 && $pm->total_tasks > 0 ? 'text-red-600' : 'text-yellow-600') }}">
                                        {{ $pm->on_time_rate }}%
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <button x-data @click="$dispatch('open-modal', 'contact-pm-{{ $pm->id }}')"
                                            class="inline-flex items-center gap-1 px-3 py-1.5 bg-blue-50 text-blue-700 rounded-md hover:bg-blue-100">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                                        Hubungi
                                    </button>
                                    <x-modal name="contact-pm-{{ $pm->id }}" maxWidth="md">
                                        <div class="p-6">
                                            <div class="flex items-center justify-between mb-4">
                                                <h3 class="text-lg font-semibold text-gray-900">Hubungi {{ $pm->name }}</h3>
                                                <button @click="$dispatch('close-modal', 'contact-pm-{{ $pm->id }}')" class="text-gray-400 hover:text-gray-600">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                                </button>
                                            </div>
                                            <p class="text-sm text-gray-600 mb-4">
                                                {{ $pm->email }} &middot; {{ $pm->phone ?? 'belum ada nomor telepon' }}
                                            </p>
                                            <div class="space-y-3">
                                                <a href="{{ route('super-admin.hubungi-team') }}?recipient={{ $pm->id }}"
                                                   class="flex items-center justify-center gap-2 px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 text-sm">
                                                    Kirim Email
                                                </a>
                                                <a href="{{ route('super-admin.hubungi-team') }}?recipient={{ $pm->id }}&sendType=whatsapp"
                                                   class="flex items-center justify-center gap-2 px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 text-sm">
                                                    Kirim WhatsApp
                                                </a>
                                            </div>
                                        </div>
                                    </x-modal>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="px-6 py-4 text-center text-gray-500">No Project Managers found.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
