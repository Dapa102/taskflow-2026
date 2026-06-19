<script setup>
import { ref } from "vue";
import axios from "axios";

const props = defineProps({
    taskId: { type: [Number, String], required: true },
});

const emit = defineEmits(["update"]);

const attachments = ref([]);
const loading = ref(false);
const uploading = ref(false);
const fileInput = ref(null);

async function fetch() {
    loading.value = true;
    try {
        const res = await axios.get(`/api/tasks/${props.taskId}/attachments`);
        attachments.value = res.data.data ?? [];
    } catch {}
    loading.value = false;
}

fetch();

async function upload() {
    const file = fileInput.value?.files?.[0];
    if (!file) return;
    uploading.value = true;
    const form = new FormData();
    form.append("file", file);
    try {
        const res = await axios.post(`/api/tasks/${props.taskId}/attachments`, form, {
            headers: { "Content-Type": "multipart/form-data" },
        });
        attachments.value.push(res.data.data);
        fileInput.value.value = "";
        emit("update");
    } catch {}
    uploading.value = false;
}

async function remove(att) {
    try {
        await axios.delete(`/api/attachments/${att.id}`);
        attachments.value = attachments.value.filter((a) => a.id !== att.id);
        emit("update");
    } catch {}
}

function icon(name) {
    const ext = name?.split(".").pop()?.toLowerCase();
    if (["pdf"].includes(ext)) return "📄";
    if (["jpg", "jpeg", "png", "gif", "webp", "svg"].includes(ext)) return "🖼";
    if (["doc", "docx"].includes(ext)) return "📝";
    if (["xls", "xlsx", "csv"].includes(ext)) return "📊";
    if (["zip", "rar", "7z", "tar", "gz"].includes(ext)) return "📦";
    return "📎";
}

function size(bytes) {
    if (!bytes) return "";
    if (bytes < 1024) return `${bytes} B`;
    if (bytes < 1048576) return `${(bytes / 1024).toFixed(0)} KB`;
    return `${(bytes / 1048576).toFixed(1)} MB`;
}
</script>

<template>
    <div class="space-y-3">
        <h4 class="text-sm font-medium text-zinc-400">Attachments ({{ attachments.length }})</h4>

        <div v-if="loading" class="text-xs text-zinc-600">Loading...</div>

        <div v-else class="space-y-1">
            <div v-for="att in attachments" :key="att.id" class="flex items-center gap-3 hover:bg-zinc-800/30 rounded-lg p-1.5 group transition-colors">
                <span class="text-lg">{{ icon(att.filename) }}</span>
                <div class="flex-1 min-w-0">
                    <a :href="`/storage/${att.file_path}`" target="_blank"
                        class="text-sm text-violet-400 hover:text-violet-300 truncate block transition-colors">
                        {{ att.filename }}
                    </a>
                    <span class="text-[11px] text-zinc-600">{{ size(att.file_size) }}</span>
                </div>
                <button @click="remove(att)" class="p-1 text-zinc-700 hover:text-rose-400 opacity-0 group-hover:opacity-100 transition-all">
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <div v-if="!attachments.length" class="text-xs text-zinc-700 py-2">No attachments.</div>
        </div>

        <div class="flex items-center gap-2 pt-1">
            <label class="flex-1 flex items-center gap-2 px-3 py-2 border border-dashed border-zinc-700 hover:border-violet-500/50 rounded-lg cursor-pointer text-xs text-zinc-500 hover:text-zinc-300 transition-colors">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                </svg>
                <span>{{ uploading ? "Uploading..." : "Add file" }}</span>
                <input ref="fileInput" type="file" class="hidden" @change="upload" />
            </label>
        </div>
    </div>
</template>
