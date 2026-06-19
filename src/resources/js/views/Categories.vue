<script setup>
import { ref, onMounted } from "vue";
import axios from "axios";
import TaskCard from "../components/TaskCard.vue";
import EmptyState from "../components/EmptyState.vue";

const categories = ref([]);
const tasks = ref([]);
const loading = ref(false);
const taskLoading = ref(false);
const editing = ref(null);
const form = ref({ name: "", color: "#8B5CF6" });
const selectedCategory = ref(null);

const colors = ["#8B5CF6", "#EC4899", "#3B82F6", "#10B981", "#F59E0B", "#EF4444", "#14B8A6", "#F97316", "#6366F1", "#84CC16"];

onMounted(() => fetchCategories());

async function fetchCategories() {
    loading.value = true;
    try {
        const res = await axios.get("/api/categories");
        categories.value = res.data.data ?? [];
    } catch {}
    loading.value = false;
}

async function fetchTasks(categoryId) {
    taskLoading.value = true;
    selectedCategory.value = categoryId;
    try {
        const res = await axios.get("/api/tasks", { params: { category_id: categoryId } });
        tasks.value = res.data.data ?? [];
    } catch {}
    taskLoading.value = false;
}

function startEdit(cat) {
    editing.value = cat.id;
    form.value = { name: cat.name, color: cat.color };
}

function cancelEdit() {
    editing.value = null;
    form.value = { name: "", color: "#8B5CF6" };
}

async function save(cat) {
    try {
        const res = await axios.put(`/api/categories/${cat.id}`, form.value);
        Object.assign(cat, res.data.data ?? res.data);
        editing.value = null;
    } catch {}
}

async function create() {
    if (!form.value.name.trim()) return;
    try {
        const res = await axios.post("/api/categories", form.value);
        categories.value.push(res.data.data);
        form.value = { name: "", color: "#8B5CF6" };
    } catch {}
}

async function remove(cat) {
    try {
        await axios.delete(`/api/categories/${cat.id}`);
        categories.value = categories.value.filter((c) => c.id !== cat.id);
        if (selectedCategory.value === cat.id) {
            selectedCategory.value = null;
            tasks.value = [];
        }
    } catch {}
}
</script>

<template>
    <div class="min-h-screen bg-[#0A0A0F]">
        <div class="max-w-4xl mx-auto px-4 py-6">
            <h1 class="text-2xl font-bold text-white mb-6">Categories</h1>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <div class="lg:col-span-1">
                    <div class="bg-zinc-900/50 border border-zinc-800 rounded-xl p-4 space-y-3">
                        <h2 class="text-sm font-medium text-zinc-400">All Categories</h2>

                        <div v-if="loading" class="text-xs text-zinc-600">Loading...</div>

                        <div v-else class="space-y-1">
                            <button v-for="cat in categories" :key="cat.id" @click="fetchTasks(cat.id)"
                                class="w-full flex items-center gap-2 px-3 py-2 rounded-lg text-sm text-left transition-all"
                                :class="selectedCategory === cat.id ? 'bg-zinc-800 text-white' : 'text-zinc-400 hover:bg-zinc-800/50 hover:text-zinc-300'">

                                <div v-if="editing === cat.id" class="flex-1 flex items-center gap-2" @click.stop>
                                    <input v-model="form.name" class="flex-1 bg-zinc-800 border border-zinc-700 rounded px-2 py-1 text-xs text-white outline-none" />
                                    <input v-model="form.color" type="color" class="w-6 h-6 rounded cursor-pointer border-0" />
                                    <button @click.stop="save(cat)" class="text-emerald-400 text-xs font-medium hover:text-emerald-300">Save</button>
                                    <button @click.stop="cancelEdit" class="text-zinc-600 text-xs hover:text-zinc-400">Cancel</button>
                                </div>

                                <template v-else>
                                    <div class="w-3 h-3 rounded-full flex-shrink-0" :style="{ backgroundColor: cat.color }" />
                                    <span class="flex-1 truncate">{{ cat.name }}</span>
                                    <span class="text-xs text-zinc-600">{{ cat.tasks_count ?? 0 }}</span>
                                    <button @click.stop="startEdit(cat)" class="p-0.5 text-zinc-700 hover:text-violet-400 opacity-0 hover:opacity-100 transition-all">
                                        <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" />
                                        </svg>
                                    </button>
                                    <button @click.stop="remove(cat)" class="p-0.5 text-zinc-700 hover:text-rose-400 opacity-0 hover:opacity-100 transition-all">
                                        <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                    </button>
                                </template>
                            </button>
                        </div>

                        <div class="pt-2 border-t border-zinc-800">
                            <div class="flex items-center gap-2" v-if="!editing">
                                <input v-model="form.name" placeholder="New category..." @keydown.enter="create"
                                    class="flex-1 bg-transparent border-0 border-b border-zinc-800 pb-1 text-sm text-zinc-300 placeholder-zinc-600 focus:border-violet-500/50 focus:ring-0 outline-none transition-colors" />
                                <div class="flex items-center gap-1">
                                    <div class="relative">
                                        <div class="w-5 h-5 rounded cursor-pointer" :style="{ backgroundColor: form.color }" @click="$refs.colorInput.click()" />
                                        <input ref="colorInput" v-model="form.color" type="color" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer" />
                                    </div>
                                    <button @click="create" :disabled="!form.name.trim()" class="text-xs text-violet-400 hover:text-violet-300 disabled:text-zinc-700 font-medium">Add</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="lg:col-span-2">
                    <div v-if="!selectedCategory" class="text-center py-20 text-zinc-700 text-sm">
                        Select a category to view its tasks
                    </div>

                    <div v-else-if="taskLoading" class="text-center py-20 text-zinc-600 text-sm">Loading tasks...</div>

                    <div v-else class="space-y-2">
                        <h2 class="text-sm font-medium text-zinc-400 mb-3">{{ categories.find(c => c.id === selectedCategory)?.name }} Tasks ({{ tasks.length }})</h2>
                        <TaskCard v-for="t in tasks" :key="t.id" :task="t" />
                        <EmptyState v-if="!tasks.length" message="No tasks in this category" />
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
