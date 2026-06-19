<script setup>
import { ref, watch } from "vue";
import { XMarkIcon } from "@heroicons/vue/24/outline";

const props = defineProps({
    open: { type: Boolean, default: false },
    task: { type: Object, default: null },
});

const emit = defineEmits(["close", "save"]);

const title = ref("");
const description = ref("");
const priority = ref("medium");
const deadline = ref("");
const status = ref("todo");

watch(
    () => props.open,
    (val) => {
        if (val && props.task) {
            title.value = props.task.title || "";
            description.value = props.task.description || "";
            priority.value = props.task.priority || "medium";
            deadline.value = props.task.deadline || "";
            status.value = props.task.status || "todo";
        } else if (val) {
            title.value = "";
            description.value = "";
            priority.value = "medium";
            deadline.value = "";
            status.value = "todo";
        }
    }
);

const priorities = [
    { key: "low", label: "Low", class: "border-zinc-600 text-zinc-400 hover:border-zinc-500" },
    { key: "medium", label: "Medium", class: "border-amber-500/50 text-amber-400 hover:border-amber-400" },
    { key: "high", label: "High", class: "border-rose-500/50 text-rose-400 hover:border-rose-400" },
];

function submit() {
    if (!title.value.trim()) return;
    emit("save", {
        title: title.value,
        description: description.value,
        priority: priority.value,
        deadline: deadline.value || null,
        status: status.value,
    });
}

function handleBackdrop(e) {
    if (e.target === e.currentTarget) emit("close");
}
</script>

<template>
    <Teleport to="body">
        <Transition name="overlay">
            <div
                v-if="open"
                class="fixed inset-0 z-50 bg-black/60 backdrop-blur-md"
                @click="handleBackdrop"
            >
                <Transition name="drawer" appear>
                    <div
                        v-if="open"
                        class="absolute inset-y-0 right-0 w-full max-w-2xl bg-[#0A0A0F] border-l border-white/10 shadow-2xl flex flex-col"
                    >
                        <!-- Header -->
                        <div class="flex items-center justify-between px-6 py-5 border-b border-white/5">
                            <h2 class="text-lg font-semibold text-zinc-100">
                                {{ task ? "Edit Task" : "New Task" }}
                            </h2>
                            <button
                                @click="emit('close')"
                                class="p-2 text-zinc-500 hover:text-zinc-300 transition-colors rounded-lg hover:bg-white/5"
                            >
                                <XMarkIcon class="w-5 h-5" />
                            </button>
                        </div>

                        <!-- Body -->
                        <div class="flex-1 overflow-y-auto px-6 py-6 space-y-6">
                            <!-- Title -->
                            <div class="space-y-1.5">
                                <label class="text-sm font-medium text-zinc-400">Title</label>
                                <input
                                    v-model="title"
                                    type="text"
                                    placeholder="What needs to be done?"
                                    class="w-full bg-zinc-800/50 border border-zinc-700 rounded-xl px-4 h-12 text-zinc-100 placeholder-zinc-600 focus:border-violet-500/50 focus:ring-2 focus:ring-violet-500/20 transition-all duration-300"
                                />
                            </div>

                            <!-- Description -->
                            <div class="space-y-1.5">
                                <label class="text-sm font-medium text-zinc-400">Description</label>
                                <textarea
                                    v-model="description"
                                    rows="3"
                                    placeholder="Add details..."
                                    class="w-full bg-zinc-800/50 border border-zinc-700 rounded-xl px-4 py-3 text-zinc-100 placeholder-zinc-600 focus:border-violet-500/50 focus:ring-2 focus:ring-violet-500/20 transition-all duration-300 resize-none"
                                />
                            </div>

                            <!-- Priority Chips -->
                            <div class="space-y-1.5">
                                <label class="text-sm font-medium text-zinc-400">Priority</label>
                                <div class="flex gap-2">
                                    <button
                                        v-for="p in priorities"
                                        :key="p.key"
                                        type="button"
                                        @click="priority = p.key"
                                        class="px-4 py-2 rounded-lg border text-sm font-medium transition-all duration-200"
                                        :class="[
                                            priority === p.key
                                                ? p.key === 'high'
                                                    ? 'bg-rose-500/20 border-rose-500 text-rose-300'
                                                    : p.key === 'medium'
                                                        ? 'bg-amber-500/20 border-amber-500 text-amber-300'
                                                        : 'bg-zinc-500/20 border-zinc-500 text-zinc-300'
                                                : p.class,
                                        ]"
                                    >
                                        {{ p.label }}
                                    </button>
                                </div>
                            </div>

                            <!-- Deadline -->
                            <div class="space-y-1.5">
                                <label class="text-sm font-medium text-zinc-400">Deadline</label>
                                <input
                                    v-model="deadline"
                                    type="date"
                                    class="w-full bg-zinc-800/50 border border-zinc-700 rounded-xl px-4 h-12 text-zinc-100 focus:border-violet-500/50 focus:ring-2 focus:ring-violet-500/20 transition-all duration-300"
                                />
                            </div>
                        </div>

                        <!-- Footer -->
                        <div class="flex items-center justify-end gap-3 px-6 py-5 border-t border-white/5">
                            <button
                                @click="emit('close')"
                                class="px-5 py-2.5 text-sm font-medium text-zinc-400 hover:text-zinc-200 transition-colors"
                            >
                                Cancel
                            </button>
                            <button
                                @click="submit"
                                :disabled="!title.trim()"
                                class="gradient-btn px-6 py-2.5 text-sm disabled:opacity-50 disabled:cursor-not-allowed flex items-center gap-2"
                            >
                                {{ task ? "Update" : "Create" }}
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3" />
                                </svg>
                            </button>
                        </div>
                    </div>
                </Transition>
            </div>
        </Transition>
    </Teleport>
</template>

<style scoped>
.overlay-enter-active { transition: opacity 0.3s ease; }
.overlay-leave-active { transition: opacity 0.2s ease; }
.overlay-enter-from,
.overlay-leave-to { opacity: 0; }

.drawer-enter-active { transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1); }
.drawer-leave-active { transition: transform 0.2s cubic-bezier(0.4, 0, 0.2, 1); }
.drawer-enter-from,
.drawer-leave-to { transform: translateX(100%); }
</style>
