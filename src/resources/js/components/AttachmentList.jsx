import { useState, useEffect, useRef } from "react";
import api from "../api/client";

function icon(name) {
    const ext = name?.split(".").pop()?.toLowerCase();
    if (["pdf"].includes(ext)) return "📄";
    if (["jpg", "jpeg", "png", "gif", "webp", "svg"].includes(ext)) return "🖼";
    if (["doc", "docx"].includes(ext)) return "📝";
    if (["xls", "xlsx", "csv"].includes(ext)) return "📊";
    if (["zip", "rar", "7z", "tar", "gz"].includes(ext)) return "📦";
    return "📎";
}

function size(bytes) {
    if (!bytes) return "";
    if (bytes < 1024) return `${bytes} B`;
    if (bytes < 1048576) return `${(bytes / 1024).toFixed(0)} KB`;
    return `${(bytes / 1048576).toFixed(1)} MB`;
}

export default function AttachmentList({ taskId }) {
    const [attachments, setAttachments] = useState([]);
    const [loading, setLoading] = useState(true);
    const [uploading, setUploading] = useState(false);
    const fileInput = useRef(null);

    useEffect(() => { fetchAttachments(); }, [taskId]);

    const fetchAttachments = async () => {
        setLoading(true);
        try {
            const res = await api.get(`/tasks/${taskId}/attachments`);
            setAttachments(res.data.data || []);
        } catch {}
        setLoading(false);
    };

    const upload = async () => {
        const file = fileInput.current?.files?.[0];
        if (!file) return;
        setUploading(true);
        const form = new FormData();
        form.append("file", file);
        try {
            const res = await api.post(`/tasks/${taskId}/attachments`, form, {
                headers: { "Content-Type": "multipart/form-data" },
            });
            setAttachments((prev) => [...prev, res.data.data]);
            fileInput.current.value = "";
        } catch {}
        setUploading(false);
    };

    const remove = async (att) => {
        try {
            await api.delete(`/attachments/${att.id}`);
            setAttachments((prev) => prev.filter((a) => a.id !== att.id));
        } catch {}
    };

    return (
        <div className="space-y-3">
            <h4 className="text-sm font-medium text-zinc-400">Attachments ({attachments.length})</h4>

            {loading ? (
                <div className="text-xs text-zinc-600">Loading...</div>
            ) : (
                <div className="space-y-1">
                    {attachments.map((att) => (
                        <div key={att.id} className="flex items-center gap-3 hover:bg-zinc-800/30 rounded-lg p-1.5 group transition-colors">
                            <span className="text-lg">{icon(att.filename)}</span>
                            <div className="flex-1 min-w-0">
                                <a
                                    href={`/storage/${att.file_path}`}
                                    target="_blank"
                                    rel="noreferrer"
                                    className="text-sm text-violet-400 hover:text-violet-300 truncate block transition-colors"
                                >
                                    {att.filename}
                                </a>
                                <span className="text-[11px] text-zinc-600">{size(att.file_size)}</span>
                            </div>
                            <button onClick={() => remove(att)} className="p-1 text-zinc-700 hover:text-rose-400 opacity-0 group-hover:opacity-100 transition-all">
                                <svg className="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" strokeWidth={2}>
                                    <path strokeLinecap="round" strokeLinejoin="round" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                    ))}
                    {!attachments.length && <div className="text-xs text-zinc-700 py-2">No attachments.</div>}
                </div>
            )}

            <div className="flex items-center gap-2 pt-1">
                <label className="flex-1 flex items-center gap-2 px-3 py-2 border border-dashed border-zinc-700 hover:border-violet-500/50 rounded-lg cursor-pointer text-xs text-zinc-500 hover:text-zinc-300 transition-colors">
                    <svg className="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" strokeWidth={2}>
                        <path strokeLinecap="round" strokeLinejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                    </svg>
                    <span>{uploading ? "Uploading..." : "Add file"}</span>
                    <input ref={fileInput} type="file" className="hidden" onChange={upload} />
                </label>
            </div>
        </div>
    );
}
