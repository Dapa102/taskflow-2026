import { useState, useEffect } from "react";
import api from "../api/client";

export default function SubtaskList({ taskId, subtasks: initialSubtasks }) {
    const [items, setItems] = useState(initialSubtasks || []);
    const [newTitle, setNewTitle] = useState("");
    const [adding, setAdding] = useState(false);

    useEffect(() => setItems(initialSubtasks || []), [initialSubtasks]);

    const toggle = async (subtask) => {
        const prev = subtask.is_completed;
        setItems((prevItems) => prevItems.map((s) => s.id === subtask.id ? { ...s, is_completed: !s.is_completed } : s));
        try {
            await api.patch(`/subtasks/${subtask.id}/toggle`);
        } catch {
            setItems((prevItems) => prevItems.map((s) => s.id === subtask.id ? { ...s, is_completed: prev } : s));
        }
    };

    const add = async () => {
        const title = newTitle.trim();
        if (!title) return;
        setAdding(true);
        try {
            const res = await api.post(`/tasks/${taskId}/subtasks`, { title });
            setItems((prev) => [...prev, res.data.data]);
            setNewTitle("");
        } catch {}
        setAdding(false);
    };

    const remove = async (subtask) => {
        try {
            await api.delete(`/subtasks/${subtask.id}`);
            setItems((prev) => prev.filter((s) => s.id !== subtask.id));
        } catch {}
    };

    const progress = items.length ? Math.round((items.filter((s) => s.is_completed).length / items.length) * 100) : 0;

    return (
        <div className="space-y-2">
            <div className="flex items-center justify-between">
                <h4 className="text-sm font-medium text-zinc-400">
                    Sub-tasks ({items.filter((s) => s.is_completed).length}/{items.length})
                </h4>
                <span className="text-xs font-mono text-zinc-600">{progress}%</span>
            </div>

            {progress > 0 && (
                <div className="h-1 bg-zinc-800 rounded-full overflow-hidden">
                    <div className="h-full bg-violet-500 rounded-full transition-all duration-300" style={{ width: `${progress}%` }} />
                </div>
            )}

            <div className="space-y-1">
                {items.map((s) => (
                    <div key={s.id} className="flex items-center gap-2 group">
                        <button
                            onClick={() => toggle(s)}
                            className={`w-4 h-4 rounded border-2 flex items-center justify-center flex-shrink-0 transition-all duration-200 ${
                                s.is_completed ? "bg-emerald-500 border-emerald-500" : "border-zinc-600 hover:border-violet-400"
                            }`}
                        >
                            {s.is_completed && (
                                <svg className="w-2.5 h-2.5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" strokeWidth={4}>
                                    <path strokeLinecap="round" strokeLinejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                                </svg>
                            )}
                        </button>
                        <span className={`text-sm flex-1 ${s.is_completed ? "line-through text-zinc-600" : "text-zinc-300"}`}>
                            {s.title}
                        </span>
                        <button onClick={() => remove(s)} className="p-0.5 text-zinc-700 hover:text-rose-400 opacity-0 group-hover:opacity-100 transition-all">
                            <svg className="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" strokeWidth={2}>
                                <path strokeLinecap="round" strokeLinejoin="round" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                ))}
            </div>

            <div className="flex items-center gap-2 mt-2">
                <input
                    value={newTitle}
                    onChange={(e) => setNewTitle(e.target.value)}
                    onKeyDown={(e) => e.key === "Enter" && add()}
                    type="text"
                    placeholder="Add sub-task..."
                    className="flex-1 bg-transparent border-0 border-b border-zinc-800 pb-1 text-sm text-zinc-300 placeholder-zinc-600 focus:border-violet-500/50 focus:ring-0 transition-colors outline-none"
                />
                <button
                    onClick={add}
                    disabled={adding || !newTitle.trim()}
                    className="text-xs text-violet-400 hover:text-violet-300 disabled:text-zinc-700 font-medium transition-colors"
                >
                    Add
                </button>
            </div>
        </div>
    );
}
