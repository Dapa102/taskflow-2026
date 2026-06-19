<script setup>
import { ref } from "vue";
import axios from "axios";

const props = defineProps({
    taskId: { type: [Number, String], required: true },
    subtasks: { type: Array, default: () => [] },
});

const emit = defineEmits(["update"]);

const items = ref([...props.subtasks]);
const newTitle = ref("");
const adding = ref(false);

async function toggle(subtask) {
    const prev = subtask.is_completed;
    subtask.is_completed = !subtask.is_completed;
    try {
        await axios.patch(`/api/subtasks/${subtask.id}/toggle`);
        emit("update");
    } catch {
        subtask.is_completed = prev;
    }
}

async function add() {
    const title = newTitle.value.trim();
    if (!title) return;
    adding.value = true;
    try {
        const res = await axios.post(`/api/tasks/${props.taskId}/subtasks`, { title });
        items.value.push(res.data.data);
        newTitle.value = "";
        emit("update");
    } catch {}
    adding.value = false;
}

async function remove(subtask) {
    try {
        await axios.delete(`/api/subtasks/${subtask.id}`);
        items.value = items.value.filter((s) => s.id !== subtask.id);
        emit("update");
    } catch {}
}

function progress() {
    if (!items.value.length) return 0;
    return Math.round((items.value.filter((s) => s.is_completed).length / items.value.length) * 100);
}
</script>

<template>
    <div class="space-y-2">
        <div class="flex items-center justify-between">
            <h4 class="text-sm font-medium text-zinc-400">Sub-tasks ({{ items.filter(s => s.is_completed).length }}/{{ items.length }})</h4>
            <span class="text-xs font-mono text-zinc-600">{{ progress() }}%</span>
        </div>

        <div v-if="progress() > 0" class="h-1 bg-zinc-800 rounded-full overflow-hidden">
            <div class="h-full bg-violet-500 rounded-full transition-all duration-300" :style="{ width: `${progress()}%` }" />
        </div>

        <div class="space-y-1">
            <div v-for="s in items" :key="s.id" class="flex items-center gap-2 group">
                <button @click="toggle(s)" class="w-4 h-4 rounded border-2 flex items-center justify-center flex-shrink-0 transition-all duration-200"
                    :class="s.is_completed ? 'bg-emerald-500 border-emerald-500' : 'border-zinc-600 hover:border-violet-400'">
                    <svg v-if="s.is_completed" class="w-2.5 h-2.5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="4">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                    </svg>
                </button>
                <span class="text-sm flex-1" :class="s.is_completed ? 'line-through text-zinc-600' : 'text-zinc-300'">{{ s.title }}</span>
                <button @click="remove(s)" class="p-0.5 text-zinc-700 hover:text-rose-400 opacity-0 group-hover:opacity-100 transition-all">
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>

        <div class="flex items-center gap-2 mt-2">
            <input v-model="newTitle" type="text" placeholder="Add sub-task..." @keydown.enter="add"
                class="flex-1 bg-transparent border-0 border-b border-zinc-800 pb-1 text-sm text-zinc-300 placeholder-zinc-600 focus:border-violet-500/50 focus:ring-0 transition-colors outline-none" />
            <button @click="add" :disabled="adding || !newTitle.trim()"
                class="text-xs text-violet-400 hover:text-violet-300 disabled:text-zinc-700 font-medium transition-colors">Add</button>
        </div>
    </div>
</template>
