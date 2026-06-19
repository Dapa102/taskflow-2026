import { useState, useEffect } from "react";
import api from "../api/client";

const COLORS = ["#8B5CF6", "#EC4899", "#3B82F6", "#10B981", "#F59E0B", "#EF4444", "#14B8A6", "#F97316", "#6366F1", "#84CC16"];

export default function TaskDrawer({ open, task, onClose, onSave }) {
    const [form, setForm] = useState({ title: "", description: "", priority: "medium", deadline: "", category_id: "" });
    const [categories, setCategories] = useState([]);
    const [saving, setSaving] = useState(false);

    useEffect(() => {
        if (task) {
            setForm({
                title: task.title || "",
                description: task.description || "",
                priority: task.priority || "medium",
                deadline: task.deadline ? task.deadline.split("T")[0] : "",
                category_id: task.category_id || "",
            });
        } else {
            setForm({ title: "", description: "", priority: "medium", deadline: "", category_id: "" });
        }
    }, [task, open]);

    useEffect(() => {
        api.get("/categories").then((res) => setCategories(res.data.data || [])).catch(() => {});
    }, [open]);

    const handleSubmit = async (e) => {
        e.preventDefault();
        if (!form.title.trim()) return;
        setSaving(true);
        try {
            await onSave({ ...form, title: form.title.trim() });
            onClose();
        } catch {}
        setSaving(false);
    };

    if (!open) return null;

    return (
        <div className="fixed inset-0 z-50 flex">
            <div className="absolute inset-0 bg-black/60 backdrop-blur-sm" onClick={onClose} />
            <div className="relative ml-auto w-full max-w-lg bg-[#0A0A0F] border-l border-white/5 shadow-2xl overflow-y-auto animate-slide-in">
                <div className="p-6">
                    <div className="flex items-center justify-between mb-6">
                        <h2 className="text-lg font-semibold text-white">{task ? "Edit Task" : "New Task"}</h2>
                        <button onClick={onClose} className="p-1.5 text-zinc-500 hover:text-zinc-300 transition-colors rounded-lg hover:bg-white/5">
                            <svg className="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" strokeWidth={2}>
                                <path strokeLinecap="round" strokeLinejoin="round" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <form onSubmit={handleSubmit} className="space-y-5">
                        <div>
                            <label className="block text-sm text-zinc-500 mb-1.5">Title</label>
                            <input
                                type="text"
                                value={form.title}
                                onChange={(e) => setForm({ ...form, title: e.target.value })}
                                placeholder="What needs to be done?"
                                className="w-full bg-zinc-800/50 border border-zinc-700/50 rounded-xl px-4 py-2.5 text-sm text-zinc-100 placeholder-zinc-600 focus:border-violet-500/50 focus:ring-1 focus:ring-violet-500/20 transition-all outline-none"
                                autoFocus
                            />
                        </div>

                        <div>
                            <label className="block text-sm text-zinc-500 mb-1.5">Description</label>
                            <textarea
                                value={form.description}
                                onChange={(e) => setForm({ ...form, description: e.target.value })}
                                rows={3}
                                placeholder="Add details..."
                                className="w-full bg-zinc-800/50 border border-zinc-700/50 rounded-xl px-4 py-2.5 text-sm text-zinc-100 placeholder-zinc-600 focus:border-violet-500/50 focus:ring-1 focus:ring-violet-500/20 transition-all outline-none resize-none"
                            />
                        </div>

                        <div className="grid grid-cols-2 gap-4">
                            <div>
                                <label className="block text-sm text-zinc-500 mb-1.5">Priority</label>
                                <div className="flex gap-1.5">
                                    {["low", "medium", "high"].map((p) => (
                                        <button
                                            key={p}
                                            type="button"
                                            onClick={() => setForm({ ...form, priority: p })}
                                            className={`flex-1 px-3 py-2 rounded-lg text-xs font-medium transition-all ${
                                                form.priority === p
                                                    ? p === "high" ? "bg-rose-500/20 text-rose-300 ring-1 ring-rose-500/30"
                                                    : p === "medium" ? "bg-amber-500/20 text-amber-300 ring-1 ring-amber-500/30"
                                                    : "bg-zinc-500/20 text-zinc-300 ring-1 ring-zinc-500/30"
                                                    : "bg-zinc-800/50 text-zinc-500 hover:text-zinc-300"
                                            }`}
                                        >
                                            {p}
                                        </button>
                                    ))}
                                </div>
                            </div>

                            <div>
                                <label className="block text-sm text-zinc-500 mb-1.5">Deadline</label>
                                <input
                                    type="date"
                                    value={form.deadline}
                                    onChange={(e) => setForm({ ...form, deadline: e.target.value })}
                                    className="w-full bg-zinc-800/50 border border-zinc-700/50 rounded-xl px-3 py-2.5 text-sm text-zinc-100 focus:border-violet-500/50 focus:ring-1 focus:ring-violet-500/20 transition-all outline-none"
                                />
                            </div>
                        </div>

                        <div>
                            <label className="block text-sm text-zinc-500 mb-1.5">Category</label>
                            <select
                                value={form.category_id}
                                onChange={(e) => setForm({ ...form, category_id: e.target.value })}
                                className="w-full bg-zinc-800/50 border border-zinc-700/50 rounded-xl px-4 py-2.5 text-sm text-zinc-100 focus:border-violet-500/50 focus:ring-1 focus:ring-violet-500/20 transition-all outline-none"
                            >
                                <option value="">No category</option>
                                {categories.map((cat) => (
                                    <option key={cat.id} value={cat.id}>{cat.name}</option>
                                ))}
                            </select>
                        </div>

                        <div className="flex justify-end gap-3 pt-2">
                            <button
                                type="button"
                                onClick={onClose}
                                className="px-4 py-2 text-sm text-zinc-400 hover:text-zinc-300 transition-colors rounded-lg hover:bg-white/5"
                            >
                                Cancel
                            </button>
                            <button
                                type="submit"
                                disabled={saving || !form.title.trim()}
                                className="px-5 py-2 text-sm bg-gradient-to-r from-violet-600 to-pink-600 hover:from-violet-500 hover:to-pink-500 disabled:from-zinc-800 disabled:to-zinc-800 disabled:text-zinc-600 text-white rounded-xl font-medium transition-all shadow-lg shadow-violet-500/20"
                            >
                                {saving ? "Saving..." : task ? "Update" : "Create"}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    );
}
