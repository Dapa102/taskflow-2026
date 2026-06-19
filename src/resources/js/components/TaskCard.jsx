import { useNavigate } from "react-router-dom";
import useTaskStore from "../stores/taskStore";
import BadgePriority from "./BadgePriority";

export default function TaskCard({ task }) {
    const navigate = useNavigate();
    const toggleStatus = useTaskStore((s) => s.toggleStatus);
    const deleteTask = useTaskStore((s) => s.deleteTask);

    const formatDeadline = (date) => {
        if (!date) return "";
        return new Date(date).toLocaleDateString("en-US", { month: "short", day: "numeric" });
    };

    const isOverdue = (date, status) => {
        if (!date || status === "done") return false;
        return new Date(date) < new Date();
    };

    const statusStyles = {
        todo: "border-zinc-600",
        on_progress: "border-amber-500 bg-amber-500/10",
        progress: "border-amber-500 bg-amber-500/10",
        done: "border-emerald-500 bg-emerald-500/20",
    };

    return (
        <div
            onClick={() => navigate(`/tasks/${task.id}`)}
            className="group relative bg-zinc-900/60 backdrop-blur-sm border border-zinc-800/80 hover:border-zinc-700/80 rounded-xl p-4 cursor-pointer transition-all duration-300 hover:-translate-y-0.5 hover:shadow-lg hover:shadow-violet-500/5 overflow-hidden"
        >
            <div className="absolute inset-0 bg-gradient-to-r from-violet-500/0 via-transparent to-pink-500/0 opacity-0 group-hover:opacity-100 transition-opacity duration-500 pointer-events-none" />

            <div className="relative flex items-start justify-between gap-3">
                <div className="flex-1 min-w-0">
                    <div className="flex items-center gap-2 mb-1">
                        <button
                            onClick={(e) => { e.stopPropagation(); toggleStatus(task.id); }}
                            className={`w-5 h-5 rounded-full border-2 flex items-center justify-center flex-shrink-0 transition-all duration-200 ${statusStyles[task.status] || statusStyles.todo}`}
                        >
                            {task.status === "done" && (
                                <svg className="w-3 h-3 text-emerald-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" strokeWidth={3}>
                                    <path strokeLinecap="round" strokeLinejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                                </svg>
                            )}
                        </button>
                        <h3 className={`font-medium truncate ${task.status === "done" ? "line-through text-zinc-600" : "text-zinc-200"}`}>
                            {task.title}
                        </h3>
                    </div>

                    {task.description && (
                        <p className="text-sm text-zinc-500 line-clamp-2 ml-7">{task.description}</p>
                    )}

                    <div className="flex items-center gap-3 mt-3 ml-7">
                        <BadgePriority level={task.priority} />

                        <span className={`text-xs font-medium px-2 py-0.5 rounded-full ${
                            task.status === "todo" ? "text-rose-300 bg-rose-500/10" :
                            task.status === "on_progress" || task.status === "progress" ? "text-amber-300 bg-amber-500/10" :
                            "text-emerald-300 bg-emerald-500/10"
                        }`}>
                            {task.status === "todo" ? "To-Do" : task.status === "on_progress" || task.status === "progress" ? "In Progress" : "Done"}
                        </span>

                        {task.deadline && (
                            <span className={`text-xs font-mono ${isOverdue(task.deadline, task.status) ? "text-rose-400" : "text-zinc-500"}`}>
                                {formatDeadline(task.deadline)}
                                {isOverdue(task.deadline, task.status) && <span className="text-rose-400 ml-1">• Overdue</span>}
                            </span>
                        )}
                    </div>
                </div>

                <div className="flex items-center gap-1 opacity-0 group-hover:opacity-100 transition-opacity relative z-10">
                    <button
                        onClick={(e) => { e.stopPropagation(); deleteTask(task.id); }}
                        className="p-1.5 text-zinc-600 hover:text-rose-400 transition-colors rounded-lg hover:bg-white/5"
                    >
                        <svg className="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" strokeWidth={1.5}>
                            <path strokeLinecap="round" strokeLinejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    );
}
