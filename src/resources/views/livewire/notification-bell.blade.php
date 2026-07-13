<div class="relative mr-2" x-data="{ open: false }" @toggle-notification.window="open = !open">
    <button x-on:click="open = !open; if(open) $wire.loadNotifications()" class="relative p-2.5 text-gray-500 hover:text-gray-700 focus:outline-none transition-colors bg-gray-100 hover:bg-gray-200 rounded-xl">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
        </svg>
        @if($unreadCount > 0)
            <span class="absolute -top-0.5 -right-0.5 inline-flex items-center justify-center px-1.5 py-0.5 text-xs font-bold leading-none text-white bg-red-500 rounded-full min-w-[18px] ring-2 ring-white">
                {{ $unreadCount > 99 ? '99+' : $unreadCount }}
            </span>
        @endif
    </button>

    <div x-show="open" x-cloak
         @click.outside="open = false"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         class="absolute right-0 sm:right-0 mt-2 w-80 max-w-[calc(100vw-2rem)] sm:max-w-xs bg-white rounded-xl shadow-xl border border-gray-200 z-50 overflow-hidden">
        <div class="p-3 border-b border-gray-100 bg-gray-50">
            <div class="flex items-center justify-between">
                <h3 class="text-sm font-semibold text-gray-900">Notifikasi</h3>
                @if($unreadCount > 0)
                    <span class="text-xs text-gray-500">{{ $unreadCount }} belum dibaca</span>
                @endif
            </div>
        </div>
        <div class="max-h-80 overflow-y-auto divide-y divide-gray-100">
            @forelse($notifications as $n)
                <div class="flex items-start gap-3 p-3 {{ $n['status'] !== 'read' ? 'bg-blue-50/60' : 'hover:bg-gray-50' }} transition-colors">
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-gray-900 leading-snug {{ $n['status'] === 'read' ? '' : 'text-gray-800' }}">{{ $n['subject'] }}</p>
                        <p class="text-xs text-gray-500 mt-0.5 line-clamp-2">{{ $n['message'] }}</p>
                        <div class="flex items-center gap-2 mt-1.5">
                            <span class="text-[11px] text-gray-400">{{ \Carbon\Carbon::parse($n['sent_at'])->diffForHumans() }}</span>
                            @if($n['task_id'])
                                <span class="text-[11px] text-indigo-500">&#183; Lihat Tugas</span>
                            @endif
                        </div>
                    </div>
                    @if($n['status'] !== 'read')
                        <div class="shrink-0 flex flex-col items-center gap-1.5">
                            <span class="w-2 h-2 rounded-full bg-blue-500"></span>
                            <button wire:click="markAsRead({{ $n['id'] }})" class="text-xs text-indigo-600 hover:text-indigo-800 font-medium whitespace-nowrap transition-colors">
                                Baca
                            </button>
                        </div>
                    @else
                        <div class="shrink-0 flex flex-col items-center gap-1.5 pt-1">
                            <span class="w-1.5 h-1.5 rounded-full bg-gray-300"></span>
                        </div>
                    @endif
                </div>
            @empty
                <div class="p-8 text-center">
                    <svg class="w-8 h-8 text-gray-300 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                    </svg>
                    <p class="text-sm text-gray-400">Tidak ada notifikasi</p>
                </div>
            @endforelse
        </div>
        <div class="p-2 border-t border-gray-100 bg-gray-50 text-center">
            <a href="{{ route('tasks.all') }}" class="text-xs text-indigo-600 hover:text-indigo-800 font-medium transition-colors">Lihat Semua Tugas</a>
        </div>
    </div>
</div>
