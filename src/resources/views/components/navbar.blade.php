@props(['onSearch' => null])
<header class="sticky top-0 z-50 bg-slate-950/80 backdrop-blur-xl border-b border-white/5">
    <div class="max-w-6xl mx-auto px-4 h-16 flex items-center justify-between gap-4">
        <a href="/dashboard" class="flex items-center gap-2.5 flex-shrink-0">
            <div class="w-8 h-8 bg-gradient-to-br from-violet-500 to-pink-500 rounded-xl flex items-center justify-center shadow-lg shadow-violet-500/20">
                <span class="text-white font-bold text-sm">T</span>
            </div>
            <span class="font-bold text-lg hidden sm:block bg-gradient-to-r from-violet-400 to-pink-400 bg-clip-text text-transparent">TaskFlow</span>
        </a>

        <nav class="hidden md:flex items-center gap-1">
            <a href="/dashboard" class="flex items-center gap-1.5 px-3 py-1.5 text-sm text-zinc-400 hover:text-zinc-200 rounded-lg hover:bg-white/5">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12l8.954-8.955a1.126 1.126 0 011.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25"/></svg>
                Dashboard
            </a>
            <a href="/categories" class="flex items-center gap-1.5 px-3 py-1.5 text-sm text-zinc-400 hover:text-zinc-200 rounded-lg hover:bg-white/5">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12.75V12A2.25 2.25 0 014.5 9.75h15A2.25 2.25 0 0121.75 12v.75m-8.69-6.44l-2.12-2.12a1.5 1.5 0 00-1.061-.44H4.5A2.25 2.25 0 002.25 6v12a2.25 2.25 0 002.25 2.25h15A2.25 2.25 0 0021.75 18V9a2.25 2.25 0 00-2.25-2.25h-5.379a1.5 1.5 0 01-1.06-.44z"/></svg>
                Categories
            </a>
            <a href="/teams" class="flex items-center gap-1.5 px-3 py-1.5 text-sm text-zinc-400 hover:text-zinc-200 rounded-lg hover:bg-white/5">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z"/></svg>
                Teams
            </a>
            <a href="/reports" class="flex items-center gap-1.5 px-3 py-1.5 text-sm text-zinc-400 hover:text-zinc-200 rounded-lg hover:bg-white/5">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75zM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V8.625zM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V4.125z"/></svg>
                Reports
            </a>
        </nav>

        <div class="hidden sm:flex items-center gap-3 flex-1 max-w-md ml-auto mr-4">
            <div class="relative w-full">
                <input type="text" placeholder="Search tasks..." x-model="$root.querySelector('[x-data=dashboard]')?.__x.$data.search"
                    class="w-full bg-zinc-800/50 border border-zinc-700/50 rounded-xl pl-4 pr-3 h-9 text-sm text-zinc-100 placeholder-zinc-600 focus:border-violet-500/50 focus:ring-1 focus:ring-violet-500/20 transition-all outline-none">
            </div>
        </div>

        <div x-data="notifications" class="relative">
            <button @click="open = !open" class="relative p-2 text-zinc-500 hover:text-zinc-200 rounded-lg hover:bg-white/5">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0"/></svg>
                <template x-if="unreadCount > 0">
                    <span class="absolute -top-0.5 -right-0.5 w-4.5 h-4.5 bg-rose-500 text-white text-[10px] font-bold rounded-full flex items-center justify-center shadow-lg shadow-rose-500/30" x-text="unreadCount > 9 ? '9+' : unreadCount"></span>
                </template>
            </button>
            <div x-show="open" @click.outside="open = false" class="absolute right-0 top-full mt-2 w-80 bg-zinc-900 border border-zinc-800 rounded-xl shadow-2xl shadow-black/50 overflow-hidden z-50">
                <div class="flex items-center justify-between px-4 py-3 border-b border-zinc-800">
                    <span class="text-sm font-medium text-zinc-200">Notifications</span>
                    <button @click="markAllRead" x-show="unreadCount > 0" class="text-xs text-violet-400 hover:text-violet-300">Mark all read</button>
                </div>
                <div class="max-h-80 overflow-y-auto">
                    <template x-if="items.length === 0">
                        <div class="px-4 py-8 text-center text-sm text-zinc-600">No notifications yet</div>
                    </template>
                    <template x-for="n in items" :key="n.id">
                        <div @click="markRead(n.id)" class="flex gap-3 px-4 py-3 border-b border-zinc-800/50 hover:bg-zinc-800/30 cursor-pointer" :class="{ 'bg-violet-500/5': !n.read_at }">
                            <span class="text-lg mt-0.5" x-text="icon(n.type)"></span>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm text-zinc-300 whitespace-pre-wrap" x-text="n.data?.message || n.data?.body || 'New notification'"></p>
                                <p class="text-[10px] text-zinc-600 mt-1" x-text="ago(n.created_at)"></p>
                            </div>
                            <button @click.stop="remove(n.id)" class="p-0.5 text-zinc-700 hover:text-rose-400 self-start opacity-0 hover:opacity-100">
                                <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                            </button>
                        </div>
                    </template>
                </div>
            </div>
        </div>

        <div class="flex items-center gap-2">
            <a href="/profile" class="flex items-center gap-2 group">
                <span class="text-sm text-zinc-400 hidden md:block group-hover:text-zinc-200">{{ auth()->user()->name }}</span>
                <div class="w-8 h-8 rounded-full bg-gradient-to-br from-violet-500 to-pink-500 flex items-center justify-center text-white text-xs font-bold shadow-lg shadow-violet-500/20">
                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                </div>
            </a>
            <a href="/logout" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" class="p-2 text-zinc-500 hover:text-rose-400 rounded-lg hover:bg-white/5" title="Logout">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15m3 0l3-3m0 0l-3-3m3 3H9"/></svg>
            </a>
            <form id="logout-form" action="/logout" method="POST" class="hidden">@csrf</form>
        </div>
    </div>
</header>
