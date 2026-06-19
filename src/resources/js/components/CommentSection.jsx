import { useState, useEffect } from "react";
import api from "../api/client";

export default function CommentSection({ taskId }) {
    const [comments, setComments] = useState([]);
    const [newComment, setNewComment] = useState("");
    const [loading, setLoading] = useState(true);
    const [sending, setSending] = useState(false);

    useEffect(() => { fetchComments(); }, [taskId]);

    const fetchComments = async () => {
        setLoading(true);
        try {
            const res = await api.get(`/tasks/${taskId}/comments`);
            setComments(res.data.data || []);
        } catch {}
        setLoading(false);
    };

    const post = async () => {
        const content = newComment.trim();
        if (!content) return;
        setSending(true);
        try {
            const res = await api.post(`/tasks/${taskId}/comments`, { content });
            setComments((prev) => [...prev, res.data.data]);
            setNewComment("");
        } catch {}
        setSending(false);
    };

    const remove = async (comment) => {
        try {
            await api.delete(`/comments/${comment.id}`);
            setComments((prev) => prev.filter((c) => c.id !== comment.id));
        } catch {}
    };

    const ago = (date) => {
        const diff = Date.now() - new Date(date).getTime();
        const mins = Math.floor(diff / 60000);
        if (mins < 1) return "just now";
        if (mins < 60) return `${mins}m ago`;
        const hrs = Math.floor(mins / 60);
        if (hrs < 24) return `${hrs}h ago`;
        return `${Math.floor(hrs / 24)}d ago`;
    };

    return (
        <div className="space-y-3">
            <h4 className="text-sm font-medium text-zinc-400">Comments</h4>

            {loading ? (
                <div className="text-xs text-zinc-600">Loading...</div>
            ) : (
                <div className="space-y-2">
                    {comments.map((c) => (
                        <div key={c.id} className="flex gap-2 group">
                            <div className="w-7 h-7 rounded-full bg-gradient-to-br from-violet-500 to-pink-500 flex items-center justify-center text-xs font-bold text-white flex-shrink-0 mt-0.5">
                                {(c.user?.name || "?")[0].toUpperCase()}
                            </div>
                            <div className="flex-1 min-w-0">
                                <div className="flex items-center gap-2">
                                    <span className="text-xs font-medium text-zinc-300">{c.user?.name || "Unknown"}</span>
                                    <span className="text-[10px] text-zinc-600">{ago(c.created_at)}</span>
                                    <button
                                        onClick={() => remove(c)}
                                        className="ml-auto p-0.5 text-zinc-700 hover:text-rose-400 opacity-0 group-hover:opacity-100 transition-all"
                                    >
                                        <svg className="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" strokeWidth={2}>
                                            <path strokeLinecap="round" strokeLinejoin="round" d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                    </button>
                                </div>
                                <p className="text-sm text-zinc-400 mt-0.5 whitespace-pre-wrap">{c.content}</p>
                            </div>
                        </div>
                    ))}
                    {!comments.length && <div className="text-xs text-zinc-700 py-2">No comments yet.</div>}
                </div>
            )}

            <div className="flex items-start gap-2 pt-1">
                <textarea
                    value={newComment}
                    onChange={(e) => setNewComment(e.target.value)}
                    placeholder="Write a comment..."
                    rows={2}
                    className="flex-1 bg-zinc-800/50 border border-zinc-700 rounded-lg px-3 py-2 text-sm text-zinc-300 placeholder-zinc-600 focus:border-violet-500/50 focus:ring-0 resize-none outline-none transition-colors"
                />
                <button
                    onClick={post}
                    disabled={sending || !newComment.trim()}
                    className="px-4 py-2 bg-violet-600 hover:bg-violet-500 disabled:bg-zinc-800 disabled:text-zinc-700 text-white text-sm rounded-lg font-medium transition-all"
                >
                    Send
                </button>
            </div>
        </div>
    );
}
