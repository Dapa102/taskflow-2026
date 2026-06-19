<script setup>
import { ref, computed, onMounted } from "vue";
import { useRoute, useRouter } from "vue-router";
import axios from "axios";
import { useTaskStore } from "../stores/taskStore";
import SubtaskList from "../components/SubtaskList.vue";
import CommentSection from "../components/CommentSection.vue";
import AttachmentList from "../components/AttachmentList.vue";
import BadgePriority from "../components/BadgePriority.vue";

const route = useRoute();
const router = useRouter();
const taskStore = useTaskStore();

const task = ref(null);
const loading = ref(true);

const taskId = computed(() => route.params.id);

onMounted(async () => {
    try {
        const res = await axios.get(`/api/tasks/${taskId.value}`, {
            params: { include: "category,subtasks,comments.user,attachments" },
        });
        task.value = res.data.data ?? res.data;
    } catch {
        router.replace("/dashboard");
    }
    loading.value = false;
});

async function updateStatus(status) {
    if (!task.value) return;
    const prev = { ...task.value };
    task.value.status = status;
    try {
        await taskStore.updateTask(task.value.id, { status });
    } catch {
        task.value = prev;
    }
}

const statuses = [
    { value: "todo", label: "Todo", color: "bg-zinc-700" },
    { value: "on_progress", label: "On Progress", color: "bg-blue-500" },
    { value: "done", label: "Done", color: "bg-emerald-500" },
];

const priorityBadge = computed(() => {
    if (!task.value) return "";
    const map = { low: "Low", medium: "Medium", high: "High", urgent: "Urgent" };
    const colors = { low: "bg-zinc-700 text-zinc-300", medium: "bg-yellow-500/20 text-yellow-400", high: "bg-orange-500/20 text-orange-400", urgent: "bg-rose-500/20 text-rose-400" };
    return { label: map[task.value.priority] ?? task.value.priority, class: colors[task.value.priority] ?? "bg-zinc-700" };
});

function formatDate(d) {
    if (!d) return "";
    return new Date(d).toLocaleDateString("id-ID", { weekday: "long", year: "numeric", month: "long", day: "numeric" });
}
</script>

<template>
    <div class="min-h-screen bg-[#0A0A0F]">
        <div class="max-w-3xl mx-auto px-4 py-6">
            <button @click="router.back()" class="flex items-center gap-1 text-sm text-zinc-500 hover:text-zinc-300 mb-4 transition-colors">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5" />
                </svg>
                Back
            </button>

            <div v-if="loading" class="text-center text-zinc-600 py-20">Loading...</div>

            <template v-else-if="task">
                <div class="flex items-start justify-between gap-4 mb-6">
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-2 mb-2">
                            <span v-if="task.category" class="text-xs px-2 py-0.5 rounded-full" :style="{ backgroundColor: task.category.color + '20', color: task.category.color }">
                                {{ task.category.name }}
                            </span>
                            <BadgePriority :level="task.priority" />
                        </div>
                        <h1 class="text-2xl font-bold text-white">{{ task.title }}</h1>
                        <p v-if="task.description" class="text-zinc-400 mt-2 whitespace-pre-wrap">{{ task.description }}</p>
                        <p v-if="task.deadline" class="text-sm text-zinc-600 mt-3">
                            <span class="text-zinc-500">Deadline:</span> {{ formatDate(task.deadline) }}
                        </p>
                    </div>
                </div>

                <div class="flex gap-2 mb-8" v-if="!['archived', 'cancelled'].includes(task.status)">
                    <button v-for="s in statuses" :key="s.value" @click="updateStatus(s.value)"
                        class="px-3 py-1.5 rounded-lg text-xs font-medium transition-all"
                        :class="task.status === s.value ? s.color + ' text-white' : 'bg-zinc-800/50 text-zinc-500 hover:bg-zinc-800 hover:text-zinc-300'">
                        {{ s.label }}
                    </button>
                </div>

                <div class="space-y-8">
                    <div class="bg-zinc-900/50 border border-zinc-800 rounded-xl p-4">
                        <SubtaskList :task-id="task.id" :subtasks="task.subtasks ?? []" @update="task.subtasks = $event" />
                    </div>

                    <div class="bg-zinc-900/50 border border-zinc-800 rounded-xl p-4">
                        <AttachmentList :task-id="task.id" @update="task.attachments = $event" />
                    </div>

                    <div class="bg-zinc-900/50 border border-zinc-800 rounded-xl p-4">
                        <CommentSection :task-id="task.id" @update="task.comments = $event" />
                    </div>
                </div>
            </template>
        </div>
    </div>
</template>
