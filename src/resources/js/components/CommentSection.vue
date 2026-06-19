<script setup>
import { ref } from "vue";
import axios from "axios";

const props = defineProps({
    taskId: { type: [Number, String], required: true },
});

const emit = defineEmits(["update"]);

const comments = ref([]);
const newComment = ref("");
const loading = ref(false);
const sending = ref(false);

async function fetch() {
    loading.value = true;
    try {
        const res = await axios.get(`/api/tasks/${props.taskId}/comments`);
        comments.value = res.data.data ?? [];
    } catch {}
    loading.value = false;
}

fetch();

async function post() {
    const content = newComment.value.trim();
    if (!content) return;
    sending.value = true;
    try {
        const res = await axios.post(`/api/tasks/${props.taskId}/comments`, { content });
        comments.value.push(res.data.data);
        newComment.value = "";
        emit("update");
    } catch {}
    sending.value = false;
}

async function remove(comment) {
    try {
        await axios.delete(`/api/comments/${comment.id}`);
        comments.value = comments.value.filter((c) => c.id !== comment.id);
        emit("update");
    } catch {}
}

function ago(date) {
    const diff = Date.now() - new Date(date).getTime();
    const mins = Math.floor(diff / 60000);
    if (mins < 1) return "just now";
    if (mins < 60) return `${mins}m ago`;
    const hrs = Math.floor(mins / 60);
    if (hrs < 24) return `${hrs}h ago`;
    return `${Math.floor(hrs / 24)}d ago`;
}
</script>

<template>
    <div class="space-y-3">
        <h4 class="text-sm font-medium text-zinc-400">Comments</h4>

        <div v-if="loading" class="text-xs text-zinc-600">Loading...</div>

        <div v-else class="space-y-2">
            <div v-for="c in comments" :key="c.id" class="flex gap-2 group">
                <div class="w-7 h-7 rounded-full bg-gradient-to-br from-violet-500 to-magenta-500 flex items-center justify-center text-xs font-bold text-white flex-shrink-0 mt-0.5">
                    {{ (c.user?.name ?? "?")[0].toUpperCase() }}
                </div>
                <div class="flex-1 min-w-0">
                    <div class="flex items-center gap-2">
                        <span class="text-xs font-medium text-zinc-300">{{ c.user?.name ?? "Unknown" }}</span>
                        <span class="text-[10px] text-zinc-600">{{ ago(c.created_at) }}</span>
                        <button @click="remove(c)" class="ml-auto p-0.5 text-zinc-700 hover:text-rose-400 opacity-0 group-hover:opacity-100 transition-all">
                            <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                    <p class="text-sm text-zinc-400 mt-0.5 whitespace-pre-wrap">{{ c.content }}</p>
                </div>
            </div>

            <div v-if="!comments.length" class="text-xs text-zinc-700 py-2">No comments yet.</div>
        </div>

        <div class="flex items-start gap-2 pt-1">
            <textarea v-model="newComment" placeholder="Write a comment..." rows="2"
                class="flex-1 bg-zinc-800/50 border border-zinc-700 rounded-lg px-3 py-2 text-sm text-zinc-300 placeholder-zinc-600 focus:border-violet-500/50 focus:ring-0 resize-none outline-none transition-colors" />
            <button @click="post" :disabled="sending || !newComment.trim()"
                class="px-4 py-2 bg-violet-600 hover:bg-violet-500 disabled:bg-zinc-800 disabled:text-zinc-700 text-white text-sm rounded-lg font-medium transition-all">Send</button>
        </div>
    </div>
</template>
