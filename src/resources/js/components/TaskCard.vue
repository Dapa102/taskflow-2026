<script setup>
import { useTaskStore } from "@/stores/taskStore";
import BadgePriority from "./BadgePriority.vue";

const props = defineProps({
    task: { type: Object, required: true },
});

const emit = defineEmits(["edit"]);
const taskStore = useTaskStore();

function formatDeadline(date) {
    if (!date) return "";
    return new Date(date).toLocaleDateString("en-US", { month: "short", day: "numeric" });
}

function isOverdue(date, status) {
    if (!date || status === "done") return false;
    return new Date(date) < new Date();
}

function statusLabel(s) {
    const map = { todo: "To-Do", on_progress: "In Progress", progress: "In Progress", done: "Done" };
    return map[s] || s;
}
</script>

<template>
    <div
        class="glass rounded-xl p-4 cursor-pointer transition-all duration-300 hover:-translate-y-1 hover:shadow-lg hover:shadow-violet-500/10 group"
        :class="{
            'priority-high': task.priority === 'high',
            'priority-medium': task.priority === 'medium',
            'priority-low': task.priority === 'low',
        }"
        @click="emit('edit', task)"
    >
        <div class="flex items-start justify-between gap-3">
            <div class="flex-1 min-w-0">
                <div class="flex items-center gap-2 mb-1">
                    <button
                        @click.stop="taskStore.toggleStatus(task.id)"
                        class="w-5 h-5 rounded-full border-2 flex items-center justify-center flex-shrink-0 transition-all duration-200"
                        :class="{
                            'border-emerald-500 bg-emerald-500/20': task.status === 'done',
                            'border-amber-500 bg-amber-500/10': task.status === 'on_progress' || task.status === 'progress',
                            'border-zinc-600 hover:border-violet-400': task.status === 'todo',
                        }"
                    >
                        <svg v-if="task.status === 'done'" class="w-3 h-3 text-emerald-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                        </svg>
                    </button>
                    <h3 class="font-medium text-zinc-200 truncate" :class="{ 'line-through text-zinc-600': task.status === 'done' }">
                        {{ task.title }}
                    </h3>
                </div>

                <p v-if="task.description" class="text-sm text-zinc-500 line-clamp-2 ml-7">{{ task.description }}</p>

                <div class="flex items-center gap-3 mt-3 ml-7">
                    <BadgePriority :level="task.priority" />

                    <span
                        class="text-xs font-medium px-2 py-0.5 rounded-full"
                        :class="{
                            'text-rose-300 bg-rose-500/10': task.status === 'todo',
                            'text-amber-300 bg-amber-500/10': task.status === 'on_progress' || task.status === 'progress',
                            'text-emerald-300 bg-emerald-500/10': task.status === 'done',
                        }"
                    >
                        {{ statusLabel(task.status) }}
                    </span>

                    <span v-if="task.deadline" class="text-xs font-mono text-zinc-500" :class="{ 'text-rose-400': isOverdue(task.deadline, task.status) }">
                        {{ formatDeadline(task.deadline) }}
                        <span v-if="isOverdue(task.deadline, task.status)" class="text-rose-400">• Overdue</span>
                    </span>
                </div>
            </div>

            <div class="flex items-center gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
                <button @click.stop="taskStore.deleteTask(task.id)" class="p-1.5 text-zinc-600 hover:text-rose-400 transition-colors rounded-lg hover:bg-white/5">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                    </svg>
                </button>
            </div>
        </div>
    </div>
</template>
