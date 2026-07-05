<div class="relative mr-2">
    <button wire:click="toggle" class="relative p-2 text-gray-500 hover:text-gray-700 focus:outline-none">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
        </svg>
        @if($unreadCount > 0)
            <span class="absolute -top-0.5 -right-0.5 inline-flex items-center justify-center px-1.5 py-0.5 text-xs font-bold leading-none text-white bg-red-500 rounded-full min-w-[18px]">
                {{ $unreadCount > 99 ? '99+' : $unreadCount }}
            </span>
        @endif
    </button>

    @if($open)
    <div class="absolute right-0 mt-2 w-80 bg-white rounded-xl shadow-xl border z-50"
         x-data @click.outside="$wire.close()"
         x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100">
        <div class="p-3 border-b">
            <h3 class="text-sm font-semibold text-gray-900">Notifikasi</h3>
        </div>
        <div class="max-h-72 overflow-y-auto">
            @forelse($notifications as $n)
                <div class="flex items-start gap-2 p-3 hover:bg-gray-50 border-b last:border-b-0 {{ $n['status'] !== 'read' ? 'bg-blue-50' : '' }}">
                    <div class="flex-1 min-w-0">
                        <p class="text-xs font-medium text-gray-900 truncate">{{ $n['subject'] }}</p>
                        <p class="text-xs text-gray-500 truncate">{{ $n['message'] }}</p>
                        <p class="text-xs text-gray-400 mt-0.5">{{ \Carbon\Carbon::parse($n['sent_at'])->diffForHumans() }}</p>
                    </div>
                    @if($n['status'] !== 'read')
                        <button wire:click="markAsRead({{ $n['id'] }})" class="shrink-0 text-xs text-blue-600 hover:text-blue-800 font-medium">Baca</button>
                    @endif
                </div>
            @empty
                <div class="p-6 text-center text-sm text-gray-400">Tidak ada notifikasi.</div>
            @endforelse
        </div>
        <div class="p-2 border-t text-center">
            <a href="{{ route('tasks.all') }}" class="text-xs text-indigo-600 hover:text-indigo-800 font-medium">Lihat Semua Tugas</a>
        </div>
    </div>
    @endif
</div>
