import { useState, useEffect } from "react";
import { useParams, useNavigate } from "react-router-dom";
import api from "../api/client";
import useTaskStore from "../stores/taskStore";
import SubtaskList from "../components/SubtaskList";
import CommentSection from "../components/CommentSection";
import AttachmentList from "../components/AttachmentList";
import BadgePriority from "../components/BadgePriority";

const statuses = [
    { value: "todo", label: "Todo", color: "bg-zinc-700" },
    { value: "on_progress", label: "On Progress", color: "bg-blue-500" },
    { value: "done", label: "Done", color: "bg-emerald-500" },
];

export default function TaskDetail() {
    const { id } = useParams();
    const navigate = useNavigate();
    const updateTask = useTaskStore((s) => s.updateTask);
    const [task, setTask] = useState(null);
    const [loading, setLoading] = useState(true);

    useEffect(() => {
        (async () => {
            try {
                const res = await api.get(`/tasks/${id}`, { params: { include: "category,subtasks,comments.user,attachments" } });
                setTask(res.data.data);
            } catch {
                navigate("/dashboard");
            }
            setLoading(false);
        })();
    }, [id]);

    const updateStatus = async (status) => {
        if (!task) return;
        const prev = { ...task };
        setTask({ ...task, status });
        try {
            await updateTask(task.id, { status });
        } catch {
            setTask(prev);
        }
    };

    const formatDate = (d) => {
        if (!d) return "";
        return new Date(d).toLocaleDateString("id-ID", { weekday: "long", year: "numeric", month: "long", day: "numeric" });
    };

    if (loading) {
        return (
            <div className="min-h-screen bg-[#0A0A0F] flex items-center justify-center">
                <div className="text-zinc-600">Loading...</div>
            </div>
        );
    }

    if (!task) return null;

    return (
        <div className="min-h-screen bg-[#0A0A0F]">
            <div className="max-w-3xl mx-auto px-4 py-6 pb-24">
                <button onClick={() => navigate(-1)} className="flex items-center gap-1 text-sm text-zinc-500 hover:text-zinc-300 mb-4 transition-colors">
                    <svg className="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" strokeWidth={2}>
                        <path strokeLinecap="round" strokeLinejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5" />
                    </svg>
                    Back
                </button>

                <div className="flex items-start justify-between gap-4 mb-6">
                    <div className="flex-1 min-w-0">
                        <div className="flex items-center gap-2 mb-2">
                            {task.category && (
                                <span className="text-xs px-2 py-0.5 rounded-full" style={{ backgroundColor: task.category.color + "20", color: task.category.color }}>
                                    {task.category.name}
                                </span>
                            )}
                            <BadgePriority level={task.priority} />
                        </div>
                        <h1 className="text-2xl font-bold text-white">{task.title}</h1>
                        {task.description && <p className="text-zinc-400 mt-2 whitespace-pre-wrap">{task.description}</p>}
                        {task.deadline && (
                            <p className="text-sm text-zinc-600 mt-3">
                                <span className="text-zinc-500">Deadline:</span> {formatDate(task.deadline)}
                            </p>
                        )}
                    </div>
                </div>

                {!["archived", "cancelled"].includes(task.status) && (
                    <div className="flex gap-2 mb-8">
                        {statuses.map((s) => (
                            <button
                                key={s.value}
                                onClick={() => updateStatus(s.value)}
                                className={`px-3 py-1.5 rounded-lg text-xs font-medium transition-all ${
                                    task.status === s.value ? s.color + " text-white" : "bg-zinc-800/50 text-zinc-500 hover:bg-zinc-800 hover:text-zinc-300"
                                }`}
                            >
                                {s.label}
                            </button>
                        ))}
                    </div>
                )}

                <div className="space-y-8">
                    <div className="bg-zinc-900/50 border border-zinc-800 rounded-xl p-4">
                        <SubtaskList taskId={task.id} subtasks={task.subtasks || []} />
                    </div>
                    <div className="bg-zinc-900/50 border border-zinc-800 rounded-xl p-4">
                        <AttachmentList taskId={task.id} />
                    </div>
                    <div className="bg-zinc-900/50 border border-zinc-800 rounded-xl p-4">
                        <CommentSection taskId={task.id} />
                    </div>
                </div>
            </div>
        </div>
    );
}
