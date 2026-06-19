import { create } from "zustand";
import api from "../api/client";

const useTaskStore = create((set, get) => ({
    tasks: [],
    loading: false,
    filter: { status: "all", search: "", priority: "all", category_id: null },

    filteredTasks: () => {
        const { tasks, filter } = get();
        return tasks.filter((t) => {
            if (filter.status !== "all" && t.status !== filter.status) return false;
            if (filter.priority !== "all" && t.priority !== filter.priority) return false;
            if (filter.category_id && t.category_id !== filter.category_id) return false;
            if (filter.search) {
                const q = filter.search.toLowerCase();
                if (!t.title.toLowerCase().includes(q)) return false;
            }
            return true;
        });
    },

    statistics: () => {
        const tasks = get().tasks;
        const total = tasks.length;
        const todo = tasks.filter((t) => t.status === "todo").length;
        const progress = tasks.filter((t) => t.status === "on_progress" || t.status === "progress").length;
        const done = tasks.filter((t) => t.status === "done").length;
        const overdue = tasks.filter((t) => {
            if (!t.deadline || t.status === "done") return false;
            return new Date(t.deadline) < new Date();
        }).length;
        return { total, todo, progress, done, overdue };
    },

    fetchTasks: async () => {
        set({ loading: true });
        try {
            const res = await api.get("/tasks");
            set({ tasks: res.data.data || [] });
        } catch {
            set({ tasks: [] });
        } finally {
            set({ loading: false });
        }
    },

    addTask: async (data) => {
        const optimistic = {
            id: `temp-${Date.now()}`,
            title: data.title,
            description: data.description || "",
            status: "todo",
            priority: data.priority || "medium",
            deadline: data.deadline || null,
            category_id: data.category_id || null,
            created_at: new Date().toISOString(),
            updated_at: new Date().toISOString(),
        };
        set((s) => ({ tasks: [optimistic, ...s.tasks] }));
        try {
            const res = await api.post("/tasks", data);
            set((s) => ({
                tasks: s.tasks.map((t) => (t.id === optimistic.id ? res.data.data : t)),
            }));
        } catch {
            set((s) => ({ tasks: s.tasks.filter((t) => t.id !== optimistic.id) }));
            throw new Error("Failed to create task");
        }
    },

    updateTask: async (id, data) => {
        const original = get().tasks.find((t) => t.id === id);
        if (!original) return;
        set((s) => ({
            tasks: s.tasks.map((t) => (t.id === id ? { ...t, ...data, updated_at: new Date().toISOString() } : t)),
        }));
        try {
            const res = await api.put(`/tasks/${id}`, data);
            set((s) => ({
                tasks: s.tasks.map((t) => (t.id === id ? res.data.data : t)),
            }));
        } catch {
            if (original) {
                set((s) => ({ tasks: s.tasks.map((t) => (t.id === id ? original : t)) }));
            }
            throw new Error("Failed to update task");
        }
    },

    toggleStatus: async (id) => {
        const task = get().tasks.find((t) => t.id === id);
        if (!task) return;
        const next = { todo: "on_progress", on_progress: "done", progress: "done", done: "todo" };
        const newStatus = next[task.status] || "todo";
        const prev = task.status;
        set((s) => ({
            tasks: s.tasks.map((t) => (t.id === id ? { ...t, status: newStatus } : t)),
        }));
        try {
            await api.put(`/tasks/${id}`, { status: newStatus });
        } catch {
            set((s) => ({
                tasks: s.tasks.map((t) => (t.id === id ? { ...t, status: prev } : t)),
            }));
        }
    },

    deleteTask: async (id) => {
        const idx = get().tasks.findIndex((t) => t.id === id);
        if (idx === -1) return;
        const [removed] = get().tasks.splice(idx, 1);
        set((s) => ({ tasks: s.tasks.filter((t) => t.id !== id) }));
        try {
            await api.delete(`/tasks/${id}`);
        } catch {
            set((s) => {
                const tasks = [...s.tasks];
                tasks.splice(idx, 0, removed);
                return { tasks };
            });
            throw new Error("Failed to delete task");
        }
    },

    setFilter: (updates) => {
        set((s) => ({ filter: { ...s.filter, ...updates } }));
    },
}));

export default useTaskStore;
