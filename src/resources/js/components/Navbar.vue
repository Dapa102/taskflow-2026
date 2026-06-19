<script setup>
import { ref } from "vue";
import { useAuthStore } from "@/stores/authStore";
import { useTaskStore } from "@/stores/taskStore";
import {
    Squares2X2Icon,
    MagnifyingGlassIcon,
    ArrowRightOnRectangleIcon,
} from "@heroicons/vue/24/outline";
const auth = useAuthStore();
const taskStore = useTaskStore();

const searchFocused = ref(false);

function onSearch(e) {
    taskStore.setFilter({ search: e.target.value });
}

function handleKeydown(e) {
    if ((e.metaKey || e.ctrlKey) && e.key === "k") {
        e.preventDefault();
        document.getElementById("cmd-search")?.focus();
    }
}
</script>

<template>
    <header
        class="sticky top-0 z-40 glass border-b border-white/5 px-4 lg:px-8"
        @keydown="handleKeydown"
    >
        <div class="h-16 flex items-center justify-between gap-4 max-w-7xl mx-auto">
            <!-- Logo -->
            <router-link to="/dashboard" class="flex items-center gap-2.5 flex-shrink-0">
                <div class="w-8 h-8 bg-gradient-to-br from-violet-500 to-pink-500 rounded-lg flex items-center justify-center shadow-lg shadow-violet-500/20">
                    <span class="text-white font-bold text-sm">T</span>
                </div>
                <span class="font-bold text-lg hidden sm:block gradient-text">TaskFlow</span>
            </router-link>

            <!-- Command Bar -->
            <div class="flex-1 max-w-xl mx-4 relative">
                <div
                    class="flex items-center gap-3 glass rounded-xl px-4 h-10 transition-all duration-300"
                    :class="searchFocused ? 'ring-2 ring-violet-500/50 border-violet-500/30' : ''"
                >
                    <MagnifyingGlassIcon class="w-4 h-4 text-zinc-500 flex-shrink-0" />
                    <input
                        id="cmd-search"
                        type="text"
                        placeholder="Search tasks..."
                        class="flex-1 bg-transparent border-0 outline-none text-zinc-100 placeholder-zinc-600 text-sm focus:ring-0 p-0"
                        @focus="searchFocused = true"
                        @blur="searchFocused = false"
                        @input="onSearch"
                    />
                    <kbd class="hidden sm:inline-flex items-center px-1.5 py-0.5 text-xs text-zinc-600 bg-zinc-800/50 rounded border border-zinc-700/50 font-mono">
                        ⌘K
                    </kbd>
                </div>
            </div>

            <!-- Nav links -->
            <div class="hidden md:flex items-center gap-1">
                <router-link to="/dashboard" class="px-3 py-1.5 text-sm text-zinc-400 hover:text-zinc-200 transition-colors rounded-lg hover:bg-white/5">
                    Dashboard
                </router-link>
                <router-link to="/categories" class="px-3 py-1.5 text-sm text-zinc-400 hover:text-zinc-200 transition-colors rounded-lg hover:bg-white/5">
                    Categories
                </router-link>
                <router-link to="/teams" class="px-3 py-1.5 text-sm text-zinc-400 hover:text-zinc-200 transition-colors rounded-lg hover:bg-white/5">
                    Teams
                </router-link>
            </div>

            <!-- User Area -->
            <div class="flex items-center gap-3">
                <span class="text-sm text-zinc-400 hidden md:block">{{ auth.userName }}</span>
                <div class="w-8 h-8 rounded-full bg-gradient-to-br from-violet-500 to-pink-500 flex items-center justify-center text-white text-xs font-bold shadow-lg shadow-violet-500/20">
                    {{ auth.userName.charAt(0).toUpperCase() }}
                </div>
                <button @click="auth.logout()" class="p-2 text-zinc-500 hover:text-rose-400 transition-colors rounded-lg hover:bg-white/5" title="Logout">
                    <ArrowRightOnRectangleIcon class="w-5 h-5" />
                </button>
            </div>
        </div>
    </header>
</template>
