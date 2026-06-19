import { useState, useEffect } from "react";
import api from "../api/client";
import TaskCard from "../components/TaskCard";
import EmptyState from "../components/EmptyState";

const COLORS = ["#8B5CF6", "#EC4899", "#3B82F6", "#10B981", "#F59E0B", "#EF4444", "#14B8A6", "#F97316", "#6366F1", "#84CC16"];

export default function Categories() {
    const [categories, setCategories] = useState([]);
    const [tasks, setTasks] = useState([]);
    const [loading, setLoading] = useState(true);
    const [taskLoading, setTaskLoading] = useState(false);
    const [selectedCategory, setSelectedCategory] = useState(null);
    const [editing, setEditing] = useState(null);
    const [form, setForm] = useState({ name: "", color: "#8B5CF6" });

    useEffect(() => { fetchCategories(); }, []);

    const fetchCategories = async () => {
        setLoading(true);
        try {
            const res = await api.get("/categories");
            setCategories(res.data.data || []);
        } catch {}
        setLoading(false);
    };

    const fetchTasks = async (categoryId) => {
        setTaskLoading(true);
        setSelectedCategory(categoryId);
        try {
            const res = await api.get("/tasks", { params: { category_id: categoryId } });
            setTasks(res.data.data || []);
        } catch {}
        setTaskLoading(false);
    };

    const startEdit = (cat) => {
        setEditing(cat.id);
        setForm({ name: cat.name, color: cat.color });
    };

    const save = async (cat) => {
        try {
            const res = await api.put(`/categories/${cat.id}`, form);
            setCategories((prev) => prev.map((c) => c.id === cat.id ? (res.data.data || res.data) : c));
            setEditing(null);
        } catch {}
    };

    const create = async () => {
        if (!form.name.trim()) return;
        try {
            const res = await api.post("/categories", form);
            setCategories((prev) => [...prev, res.data.data]);
            setForm({ name: "", color: "#8B5CF6" });
        } catch {}
    };

    const remove = async (cat) => {
        try {
            await api.delete(`/categories/${cat.id}`);
            setCategories((prev) => prev.filter((c) => c.id !== cat.id));
            if (selectedCategory === cat.id) {
                setSelectedCategory(null);
                setTasks([]);
            }
        } catch {}
    };

    return (
        <div className="min-h-screen bg-[#0A0A0F] pb-24">
            <div className="max-w-4xl mx-auto px-4 py-6">
                <h1 className="text-2xl font-bold text-white mb-6">Categories</h1>

                <div className="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <div className="lg:col-span-1">
                        <div className="bg-zinc-900/50 border border-zinc-800 rounded-xl p-4 space-y-3">
                            <h2 className="text-sm font-medium text-zinc-400">All Categories</h2>

                            {loading ? (
                                <div className="text-xs text-zinc-600">Loading...</div>
                            ) : (
                                <div className="space-y-1">
                                    {categories.map((cat) => (
                                        <div key={cat.id} className="flex items-center gap-2 px-3 py-2 rounded-lg cursor-pointer transition-all"
                                            style={selectedCategory === cat.id ? { backgroundColor: "rgba(255,255,255,0.05)" } : {}}
                                            onClick={() => fetchTasks(cat.id)}
                                        >
                                            {editing === cat.id ? (
                                                <div className="flex-1 flex items-center gap-2" onClick={(e) => e.stopPropagation()}>
                                                    <input value={form.name} onChange={(e) => setForm({ ...form, name: e.target.value })}
                                                        className="flex-1 bg-zinc-800 border border-zinc-700 rounded px-2 py-1 text-xs text-white outline-none" />
                                                    <input type="color" value={form.color} onChange={(e) => setForm({ ...form, color: e.target.value })}
                                                        className="w-6 h-6 rounded cursor-pointer border-0" />
                                                    <button onClick={() => save(cat)} className="text-emerald-400 text-xs font-medium">Save</button>
                                                    <button onClick={() => setEditing(null)} className="text-zinc-600 text-xs">Cancel</button>
                                                </div>
                                            ) : (
                                                <>
                                                    <div className="w-3 h-3 rounded-full flex-shrink-0" style={{ backgroundColor: cat.color }} />
                                                    <span className="flex-1 text-sm text-zinc-400 truncate">{cat.name}</span>
                                                    <span className="text-xs text-zinc-600">{cat.tasks_count ?? 0}</span>
                                                    <button onClick={(e) => { e.stopPropagation(); startEdit(cat); }}
                                                        className="p-0.5 text-zinc-700 hover:text-violet-400 opacity-0 hover:opacity-100 transition-all">
                                                        <svg className="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" strokeWidth={2}>
                                                            <path strokeLinecap="round" strokeLinejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" />
                                                        </svg>
                                                    </button>
                                                    <button onClick={(e) => { e.stopPropagation(); remove(cat); }}
                                                        className="p-0.5 text-zinc-700 hover:text-rose-400 opacity-0 hover:opacity-100 transition-all">
                                                        <svg className="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" strokeWidth={2}>
                                                            <path strokeLinecap="round" strokeLinejoin="round" d="M6 18L18 6M6 6l12 12" />
                                                        </svg>
                                                    </button>
                                                </>
                                            )}
                                        </div>
                                    ))}
                                </div>
                            )}

                            <div className="pt-2 border-t border-zinc-800">
                                <div className="flex items-center gap-2">
                                    <input value={form.name} onChange={(e) => setForm({ ...form, name: e.target.value })}
                                        onKeyDown={(e) => e.key === "Enter" && create()}
                                        placeholder="New category..."
                                        className="flex-1 bg-transparent border-0 border-b border-zinc-800 pb-1 text-sm text-zinc-300 placeholder-zinc-600 focus:border-violet-500/50 focus:ring-0 outline-none transition-colors" />
                                    <div className="flex items-center gap-1">
                                        <input type="color" value={form.color} onChange={(e) => setForm({ ...form, color: e.target.value })}
                                            className="w-5 h-5 rounded cursor-pointer border-0" />
                                        <button onClick={create} disabled={!form.name.trim()}
                                            className="text-xs text-violet-400 hover:text-violet-300 disabled:text-zinc-700 font-medium">Add</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div className="lg:col-span-2">
                        {!selectedCategory ? (
                            <div className="text-center py-20 text-zinc-700 text-sm">Select a category to view its tasks</div>
                        ) : taskLoading ? (
                            <div className="text-center py-20 text-zinc-600 text-sm">Loading tasks...</div>
                        ) : (
                            <div className="space-y-2">
                                <h2 className="text-sm font-medium text-zinc-400 mb-3">
                                    {categories.find((c) => c.id === selectedCategory)?.name} Tasks ({tasks.length})
                                </h2>
                                {tasks.map((t) => <TaskCard key={t.id} task={t} />)}
                                {!tasks.length && <EmptyState message="No tasks in this category" />}
                            </div>
                        )}
                    </div>
                </div>
            </div>
        </div>
    );
}
