<div>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Arbitrase & Laporan Revisi</h2>
    </x-slot>

    <div class="py-6 px-6">
        <div class="max-w-7xl mx-auto space-y-6">

            <div class="p-4 bg-white shadow sm:rounded-lg">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Semua Tugas yang Pernah Masuk Arbitrase</h3>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tugas</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">PM</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Anggota</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Revisi</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Hasil Akhir</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Waktu Arbitrase</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Detail</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($tasks as $t)
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3 text-sm font-medium text-gray-900">{{ $t['title'] }}</td>
                                <td class="px-4 py-3 text-sm text-gray-500">{{ $t['pm'] ?? '-' }}</td>
                                <td class="px-4 py-3 text-sm text-gray-500">{{ $t['member'] ?? '-' }}</td>
                                <td class="px-4 py-3 text-sm">{{ $t['revision_counter'] }}/{{ $t['max_revision_limit'] }}</td>
                                <td class="px-4 py-3">
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full
                                        {{ $t['final_status'] === 'done' ? 'bg-green-100 text-green-700' : '' }}
                                        {{ $t['final_status'] === 'revision' ? 'bg-orange-100 text-orange-700' : '' }}
                                        {{ $t['final_status'] === 'pending_admin' ? 'bg-purple-100 text-purple-700' : '' }}
                                        {{ $t['final_status'] === 'cancelled' ? 'bg-red-100 text-red-700' : '' }}
                                        {{ $t['final_status'] === 'pending_arbitration' ? 'bg-yellow-100 text-yellow-700' : '' }}">
                                        {{ $t['final_status'] }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-500">
                                    {{ $t['arbitration_time']?->format('Y-m-d H:i') ?? '-' }}
                                </td>
                                <td class="px-4 py-3">
                                    <button wire:click="$set('selectedTaskId', {{ $t['id'] }})" class="text-indigo-600 hover:text-indigo-800 text-xs font-medium">Lihat</button>
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="7" class="px-4 py-8 text-center text-gray-400">Belum ada tugas yang masuk arbitrase.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            @if($detail)
            <div class="p-4 bg-white shadow sm:rounded-lg">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-medium text-gray-900">Detail: {{ $detail['task']->title }}</h3>
                    <button wire:click="$set('selectedTaskId', null)" class="text-gray-400 hover:text-gray-600">&times; Tutup</button>
                </div>
                <div class="space-y-2 text-sm text-gray-600 mb-4">
                    <div>PM: {{ $detail['task']->assignedPm?->name ?? '-' }}</div>
                    <div>Anggota: {{ $detail['task']->assignedMember?->name ?? '-' }}</div>
                    <div>Pembuat: {{ $detail['task']->creator?->name ?? '-' }}</div>
                    <div>Revisi: {{ $detail['task']->revision_counter }}/{{ $detail['task']->max_revision_limit }}</div>
                    <div>Status: {{ $detail['task']->status }}</div>
                </div>
                <div class="space-y-2">
                    @forelse($detail['histories'] as $h)
                    <div class="flex items-start gap-3 text-sm p-2 {{ $h->to_status === 'pending_arbitration' ? 'bg-red-50 rounded' : '' }}">
                        <div class="w-2 h-2 rounded-full bg-indigo-500 mt-1.5 shrink-0"></div>
                        <div>
                            <div class="font-medium text-gray-900">{{ $h->from_status }} → {{ $h->to_status }}</div>
                            <div class="text-xs text-gray-500">{{ $h->changer?->name ?? 'Sistem' }} &middot; {{ $h->created_at->format('d M Y H:i') }}</div>
                            @if($h->notes)<div class="text-xs text-gray-600 mt-0.5 italic">{{ $h->notes }}</div>@endif
                        </div>
                    </div>
                    @empty
                    <p class="text-sm text-gray-400">Belum ada riwayat.</p>
                    @endforelse
                </div>
            </div>
            @endif

        </div>
    </div>
</div>
