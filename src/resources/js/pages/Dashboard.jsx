import { useState, useEffect } from "react";
import useTaskStore from "../stores/taskStore";
import useAuthStore from "../stores/authStore";
import TaskCard from "../components/TaskCard";
import TaskDrawer from "../components/TaskDrawer";
import EmptyState from "../components/EmptyState";
import api from "../api/client";

const statusFilters = [
    { key: "all", label: "All" },
    { key: "todo", label: "To-Do" },
    { key: "on_progress", label: "Progress" },
    { key: "done", label: "Done" },
];

export default function Dashboard() {
    const { tasks, loading, filter, filteredTasks, statistics, fetchTasks, addTask, updateTask, setFilter } = useTaskStore();
    const userName = useAuthStore((s) => s.userName);
    const [drawerOpen, setDrawerOpen] = useState(false);
    const [editingTask, setEditingTask] = useState(null);
    const [categories, setCategories] = useState([]);

    useEffect(() => {
        fetchTasks();
        api.get("/categories").then((res) => setCategories(res.data.data || [])).catch(() => {});
    }, []);

    const stats = statistics();
    const tasksToShow = filteredTasks();

    const openCreate = () => {
        setEditingTask(null);
        setDrawerOpen(true);
    };

    const openEdit = (task) => {
        setEditingTask(task);
        setDrawerOpen(true);
    };

    const handleSave = async (data) => {
        if (editingTask) {
            await updateTask(editingTask.id, data);
        } else {
            await addTask(data);
        }
    };

    return (
        <div className="min-h-screen bg-[#0A0A0F]">
            {/* Header */}
            <header className="sticky top-0 z-40 bg-[#0A0A0F]/80 backdrop-blur-lg border-b border-white/5">
                <div className="max-w-4xl mx-auto px-4 h-14 flex items-center justify-between gap-3">
                    <div className="flex items-center gap-2.5">
                        <div className="w-7 h-7 bg-gradient-to-br from-violet-500 to-pink-500 rounded-lg flex items-center justify-center shadow-lg shadow-violet-500/20">
                            <span className="text-white font-bold text-xs">T</span>
                        </div>
                        <span className="font-bold text-base bg-gradient-to-r from-violet-400 to-pink-400 bg-clip-text text-transparent hidden sm:block">TaskFlow</span>
                    </div>

                    <div className="flex-1 max-w-md relative">
                        <input
                            type="text"
                            placeholder="Search tasks..."
                            onChange={(e) => setFilter({ search: e.target.value })}
                            className="w-full bg-zinc-800/50 border border-zinc-700/50 rounded-lg pl-4 pr-3 h-9 text-sm text-zinc-100 placeholder-zinc-600 focus:border-violet-500/50 focus:ring-1 focus:ring-violet-500/20 transition-all outline-none"
                        />
                    </div>

                    <div className="flex items-center gap-2">
                        <a href="/categories" className="text-xs text-zinc-600 hover:text-violet-400 transition-colors hidden sm:block">Categories</a>
                        <a href="/teams" className="text-xs text-zinc-600 hover:text-violet-400 transition-colors hidden sm:block">Teams</a>
                        <span className="text-sm text-zinc-500 hidden sm:block">{userName}</span>
                        <LogoutButton />
                    </div>
                </div>
            </header>

            <main className="max-w-4xl mx-auto px-4 py-6 pb-24">
                {/* Welcome */}
                <div className="mb-6">
                    <h1 className="text-xl font-semibold text-zinc-100">Hello, {userName}!</h1>
                    <p className="text-sm text-zinc-500 mt-0.5">
                        {stats.total} tasks &middot; {stats.done} done &middot;
                        {stats.overdue > 0 ? <span className="text-rose-400"> {stats.overdue} overdue</span> : <span> all good</span>}
                    </p>
                </div>

                {/* Status filters */}
                <div className="flex items-center gap-1 mb-3 bg-zinc-900 rounded-lg p-1 w-fit">
                    {statusFilters.map((s) => (
                        <button
                            key={s.key}
                            onClick={() => setFilter({ status: s.key })}
                            className={`px-3.5 py-1.5 rounded-md text-sm font-medium transition-all duration-200 ${
                                filter.status === s.key ? "bg-zinc-800 text-zinc-200 shadow-sm" : "text-zinc-500 hover:text-zinc-300"
                            }`}
                        >
                            {s.label}
                        </button>
                    ))}
                </div>

                {/* Category filters */}
                {categories.length > 0 && (
                    <div className="flex items-center gap-1.5 mb-4 flex-wrap">
                        <button
                            onClick={() => setFilter({ category_id: null })}
                            className={`px-2.5 py-1 rounded-md text-xs font-medium transition-all duration-200 ${
                                !filter.category_id ? "bg-zinc-800 text-zinc-200" : "text-zinc-500 hover:text-zinc-300"
                            }`}
                        >
                            All
                        </button>
                        {categories.map((cat) => (
                            <button
                                key={cat.id}
                                onClick={() => setFilter({ category_id: cat.id })}
                                className={`px-2.5 py-1 rounded-md text-xs font-medium transition-all duration-200 ${
                                    filter.category_id === cat.id ? "text-white" : "text-zinc-500 hover:text-zinc-300"
                                }`}
                                style={filter.category_id === cat.id ? { backgroundColor: cat.color + "30", color: cat.color } : {}}
                            >
                                {cat.name}
                            </button>
                        ))}
                    </div>
                )}

                {/* Loading */}
                {loading ? (
                    <div className="space-y-2">
                        {[1, 2, 3].map((n) => (
                            <div key={n} className="bg-zinc-900 rounded-xl p-4 animate-pulse">
                                <div className="h-4 bg-zinc-800 rounded w-3/4 mb-2" />
                                <div className="h-3 bg-zinc-800/50 rounded w-1/3" />
                            </div>
                        ))}
                    </div>
                ) : (
                    <div className="space-y-2">
                        {tasksToShow.map((task) => (
                            <TaskCard key={task.id} task={task} />
                        ))}
                        {tasksToShow.length === 0 && <EmptyState message="No tasks match your filters" />}
                    </div>
                )}
            </main>

            {/* FAB */}
            <button
                onClick={openCreate}
                className="fixed bottom-6 right-6 z-30 w-12 h-12 bg-gradient-to-r from-violet-500 to-pink-500 hover:from-violet-400 hover:to-pink-400 text-white rounded-full shadow-lg shadow-violet-500/30 flex items-center justify-center hover:scale-110 active:scale-95 transition-all duration-200"
            >
                <svg className="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" strokeWidth={2}>
                    <path strokeLinecap="round" strokeLinejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                </svg>
            </button>

            <TaskDrawer
                open={drawerOpen}
                task={editingTask}
                onClose={() => { setDrawerOpen(false); setEditingTask(null); }}
                onSave={handleSave}
            />
        </div>
    );
}

function LogoutButton() {
    const logout = useAuthStore((s) => s.logout);
    return (
        <button onClick={logout} className="p-1.5 text-zinc-600 hover:text-rose-400 transition-colors rounded-lg hover:bg-white/5" title="Logout">
            <svg className="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" strokeWidth={2}>
                <path strokeLinecap="round" strokeLinejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15m3 0l3-3m0 0l-3-3m3 3H9" />
            </svg>
        </button>
    );
}
