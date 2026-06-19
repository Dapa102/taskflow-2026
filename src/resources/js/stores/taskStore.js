import { defineStore } from "pinia";
import { ref, computed } from "vue";
import axios from "axios";

const API = "/api";

export const useTaskStore = defineStore("task", () => {
    const tasks = ref([]);
    const loading = ref(false);
    const filter = ref({ status: "all", search: "", priority: "all", category_id: null });

    const filteredTasks = computed(() => {
        return tasks.value.filter((t) => {
            if (filter.value.status !== "all" && t.status !== filter.value.status) return false;
            if (filter.value.priority !== "all" && t.priority !== filter.value.priority) return false;
            if (filter.value.category_id && t.category_id !== filter.value.category_id) return false;
            if (filter.value.search) {
                const q = filter.value.search.toLowerCase();
                if (!t.title.toLowerCase().includes(q)) return false;
            }
            return true;
        });
    });

    const statistics = computed(() => {
        const total = tasks.value.length;
        const todo = tasks.value.filter((t) => t.status === "todo").length;
        const progress = tasks.value.filter((t) => t.status === "on_progress" || t.status === "progress").length;
        const done = tasks.value.filter((t) => t.status === "done").length;
        const overdue = tasks.value.filter((t) => {
            if (!t.deadline || t.status === "done") return false;
            return new Date(t.deadline) < new Date();
        }).length;
        const completionRate = total > 0 ? Math.round((done / total) * 100) : 0;
        return { total, todo, progress, done, overdue, completionRate };
    });

    async function fetchTasks() {
        loading.value = true;
        try {
            const res = await axios.get(`${API}/tasks`);
            tasks.value = res.data.data || [];
        } catch {
            tasks.value = [];
        } finally {
            loading.value = false;
        }
    }

    async function addTask(data) {
        const optimistic = {
            id: `temp-${Date.now()}`,
            user_id: null,
            title: data.title,
            description: data.description || "",
            status: "todo",
            priority: data.priority || "medium",
            deadline: data.deadline || null,
            category_id: data.category_id || null,
            created_at: new Date().toISOString(),
            updated_at: new Date().toISOString(),
        };
        tasks.value.unshift(optimistic);

        try {
            const res = await axios.post(`${API}/tasks`, data);
            const idx = tasks.value.findIndex((t) => t.id === optimistic.id);
            if (idx !== -1) tasks.value[idx] = res.data.data;
        } catch {
            tasks.value = tasks.value.filter((t) => t.id !== optimistic.id);
            throw new Error("Failed to create task");
        }
    }

    async function updateTask(id, data) {
        const original = tasks.value.find((t) => t.id === id);
        if (!original) return;

        Object.assign(original, { ...data, updated_at: new Date().toISOString() });

        try {
            const res = await axios.put(`${API}/tasks/${id}`, data);
            const idx = tasks.value.findIndex((t) => t.id === id);
            if (idx !== -1) tasks.value[idx] = res.data.data;
        } catch {
            const idx = tasks.value.findIndex((t) => t.id === id);
            if (idx !== -1) tasks.value[idx] = original;
            throw new Error("Failed to update task");
        }
    }

    async function toggleStatus(id) {
        const task = tasks.value.find((t) => t.id === id);
        if (!task) return;

        const next = { todo: "on_progress", on_progress: "done", progress: "done", done: "todo" };
        const newStatus = next[task.status] || "todo";
        const prev = task.status;

        task.status = newStatus;

        try {
            await axios.put(`${API}/tasks/${id}`, { status: newStatus });
        } catch {
            task.status = prev;
        }
    }

    async function deleteTask(id) {
        const idx = tasks.value.findIndex((t) => t.id === id);
        if (idx === -1) return;

        const [removed] = tasks.value.splice(idx, 1);

        try {
            await axios.delete(`${API}/tasks/${id}`);
        } catch {
            tasks.value.splice(idx, 0, removed);
            throw new Error("Failed to delete task");
        }
    }

    function setFilter(updates) {
        Object.assign(filter.value, updates);
    }

    function resetFilter() {
        filter.value = { status: "all", search: "", priority: "all", category_id: null };
    }

    return {
        tasks,
        loading,
        filter,
        filteredTasks,
        statistics,
        fetchTasks,
        addTask,
        updateTask,
        toggleStatus,
        deleteTask,
        setFilter,
        resetFilter,
    };
});
