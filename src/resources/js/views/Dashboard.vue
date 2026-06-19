<script setup>
import { ref, onMounted } from "vue";
import axios from "axios";
import { useTaskStore } from "@/stores/taskStore";
import { useAuthStore } from "@/stores/authStore";
import { useTaskActions } from "@/composables/useTaskActions";
import TaskCard from "@/components/TaskCard.vue";
import TaskDrawer from "@/components/TaskDrawer.vue";
import EmptyState from "@/components/EmptyState.vue";
import { PlusIcon, ArrowRightOnRectangleIcon, MagnifyingGlassIcon } from "@heroicons/vue/24/outline";

const taskStore = useTaskStore();
const auth = useAuthStore();
const { drawerOpen, editingTask, openCreate, openEdit, closeDrawer, saveTask } = useTaskActions();
const categories = ref([]);

const statusFilters = [
    { key: "all", label: "All" },
    { key: "todo", label: "To-Do" },
    { key: "on_progress", label: "Progress" },
    { key: "done", label: "Done" },
];

onMounted(async () => {
    taskStore.fetchTasks();
    try {
        const res = await axios.get("/api/categories");
        categories.value = res.data.data ?? [];
    } catch {}
});
</script>

<template>
    <div class="min-h-screen bg-[#0A0A0F]">
        <!-- Simple header -->
        <header class="sticky top-0 z-40 bg-[#0A0A0F]/80 backdrop-blur-lg border-b border-white/5">
            <div class="max-w-4xl mx-auto px-4 h-14 flex items-center justify-between gap-3">
                <div class="flex items-center gap-2.5">
                    <div class="w-7 h-7 bg-gradient-to-br from-violet-500 to-pink-500 rounded-lg flex items-center justify-center shadow-lg shadow-violet-500/20">
                        <span class="text-white font-bold text-xs">T</span>
                    </div>
                    <span class="font-bold text-base gradient-text hidden sm:block">TaskFlow</span>
                </div>

                <div class="flex-1 max-w-md relative">
                    <MagnifyingGlassIcon class="w-4 h-4 text-zinc-600 absolute left-3 top-1/2 -translate-y-1/2" />
                    <input
                        type="text"
                        placeholder="Search tasks..."
                        class="w-full bg-zinc-800/50 border border-zinc-700/50 rounded-lg pl-9 pr-3 h-9 text-sm text-zinc-100 placeholder-zinc-600 focus:border-violet-500/50 focus:ring-1 focus:ring-violet-500/20 transition-all outline-none"
                        @input="(e) => taskStore.setFilter({ search: e.target.value })"
                    />
                </div>

                <div class="flex items-center gap-2">
                    <router-link to="/categories" class="text-xs text-zinc-600 hover:text-violet-400 transition-colors hidden sm:block">
                        Categories
                    </router-link>
                    <router-link to="/teams" class="text-xs text-zinc-600 hover:text-violet-400 transition-colors hidden sm:block">
                        Teams
                    </router-link>
                    <span class="text-sm text-zinc-500 hidden sm:block">{{ auth.userName }}</span>
                    <button @click="auth.logout()" class="p-1.5 text-zinc-600 hover:text-rose-400 transition-colors rounded-lg hover:bg-white/5" title="Logout">
                        <ArrowRightOnRectangleIcon class="w-4 h-4" />
                    </button>
                </div>
            </div>
        </header>

        <main class="max-w-4xl mx-auto px-4 py-6">
            <!-- Welcome + quick summary -->
            <div class="mb-6">
                <h1 class="text-xl font-semibold text-zinc-100">Hello, {{ auth.userName }}!</h1>
                <p class="text-sm text-zinc-500 mt-0.5">
                    {{ taskStore.statistics.total }} tasks &middot;
                    {{ taskStore.statistics.done }} done &middot;
                    <span v-if="taskStore.statistics.overdue > 0" class="text-rose-400">{{ taskStore.statistics.overdue }} overdue</span>
                    <span v-else>all good</span>
                </p>
            </div>

            <!-- Filter tabs -->
            <div class="flex items-center gap-1 mb-3 bg-zinc-900 rounded-lg p-1 w-fit">
                <button
                    v-for="s in statusFilters"
                    :key="s.key"
                    @click="taskStore.setFilter({ status: s.key })"
                    class="px-3.5 py-1.5 rounded-md text-sm font-medium transition-all duration-200"
                    :class="taskStore.filter.status === s.key ? 'bg-zinc-800 text-zinc-200 shadow-sm' : 'text-zinc-500 hover:text-zinc-300'"
                >
                    {{ s.label }}
                </button>
            </div>

            <!-- Category filters -->
            <div v-if="categories.length" class="flex items-center gap-1.5 mb-4 flex-wrap">
                <button
                    @click="taskStore.setFilter({ category_id: null })"
                    class="px-2.5 py-1 rounded-md text-xs font-medium transition-all duration-200"
                    :class="!taskStore.filter.category_id ? 'bg-zinc-800 text-zinc-200' : 'text-zinc-500 hover:text-zinc-300'"
                >
                    All
                </button>
                <button
                    v-for="cat in categories"
                    :key="cat.id"
                    @click="taskStore.setFilter({ category_id: cat.id })"
                    class="px-2.5 py-1 rounded-md text-xs font-medium transition-all duration-200"
                    :class="taskStore.filter.category_id === cat.id ? 'text-white' : 'text-zinc-500 hover:text-zinc-300'"
                    :style="taskStore.filter.category_id === cat.id ? { backgroundColor: cat.color + '30', color: cat.color } : {}"
                >
                    {{ cat.name }}
                </button>
            </div>

            <!-- Loading state -->
            <div v-if="taskStore.loading" class="space-y-2">
                <div v-for="n in 3" :key="n" class="bg-zinc-900 rounded-xl p-4 animate-pulse">
                    <div class="h-4 bg-zinc-800 rounded w-3/4 mb-2" />
                    <div class="h-3 bg-zinc-800/50 rounded w-1/3" />
                </div>
            </div>

            <!-- Task list -->
            <div v-else class="space-y-2">
                <TransitionGroup name="list">
                    <TaskCard
                        v-for="task in taskStore.filteredTasks"
                        :key="task.id"
                        :task="task"
                        @edit="openEdit"
                    />
                </TransitionGroup>

                <EmptyState v-if="taskStore.filteredTasks.length === 0 && !taskStore.loading" />
            </div>
        </main>

        <!-- Floating add button -->
        <button
            @click="openCreate"
            class="fixed bottom-6 right-6 w-12 h-12 bg-gradient-to-r from-violet-500 to-pink-500 hover:from-violet-400 hover:to-pink-400 text-white rounded-full shadow-lg shadow-violet-500/30 flex items-center justify-center hover:scale-110 active:scale-95 transition-all duration-200 z-30"
        >
            <PlusIcon class="w-5 h-5" />
        </button>

        <TaskDrawer
            :open="drawerOpen"
            :task="editingTask"
            @close="closeDrawer"
            @save="saveTask"
        />
    </div>
</template>

<style scoped>
.list-enter-active,
.list-leave-active {
    transition: all 0.25s ease;
}
.list-enter-from {
    opacity: 0;
    transform: translateY(10px);
}
.list-leave-to {
    opacity: 0;
    transform: translateX(-10px);
}
.list-move {
    transition: transform 0.25s ease;
}
</style>
