<div>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Dashboard Atasan</h2>
    </x-slot>

    <div class="py-6 px-6">
        <div class="max-w-7xl mx-auto space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
                <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-200">
                    <p class="text-sm text-gray-500 font-medium">Total Tugas</p>
                    <p class="text-3xl font-bold text-gray-900 mt-1">{{ $total }}</p>
                </div>
                <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-200">
                    <p class="text-sm text-gray-500 font-medium">Belum Diberikan</p>
                    <p class="text-3xl font-bold text-yellow-600 mt-1">{{ $pending }}</p>
                </div>
                <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-200">
                    <p class="text-sm text-gray-500 font-medium">Sudah Diberikan</p>
                    <p class="text-3xl font-bold text-blue-600 mt-1">{{ $given }}</p>
                </div>
                <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-200">
                    <p class="text-sm text-gray-500 font-medium">Selesai</p>
                    <p class="text-3xl font-bold text-green-600 mt-1">{{ $done }}</p>
                </div>
                <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-200">
                    <p class="text-sm text-gray-500 font-medium">Jumlah Deadline</p>
                    <p class="text-3xl font-bold text-rose-600 mt-1">{{ $deadlineCount }}</p>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Overview Tugas</h3>
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

            <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-200">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-900">Aksi Cepat</h3>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <a href="{{ route('atasan.create.task') }}" class="flex items-center gap-4 p-4 bg-indigo-50 rounded-xl border border-indigo-200 hover:bg-indigo-100 transition-colors group">
                        <div class="w-12 h-12 rounded-lg bg-indigo-100 flex items-center justify-center group-hover:bg-indigo-200 transition-colors">
                            <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        </div>
                        <div>
                            <p class="font-medium text-gray-900">Buat Tugas Baru</p>
                            <p class="text-sm text-gray-500">Kirim tugas ke Super Admin</p>
                        </div>
                    </a>
                    <a href="{{ route('atasan.tasks') }}" class="flex items-center gap-4 p-4 bg-gray-50 rounded-xl border border-gray-200 hover:bg-gray-100 transition-colors group">
                        <div class="w-12 h-12 rounded-lg bg-gray-100 flex items-center justify-center group-hover:bg-gray-200 transition-colors">
                            <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                        </div>
                        <div>
                            <p class="font-medium text-gray-900">Lihat Tugas Saya</p>
                            <p class="text-sm text-gray-500">Pantau status tugas yang sudah dibuat</p>
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
