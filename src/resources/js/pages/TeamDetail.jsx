import { useState, useEffect } from "react";
import { useParams, useNavigate } from "react-router-dom";
import api from "../api/client";
import TaskCard from "../components/TaskCard";

export default function TeamDetail() {
    const { id } = useParams();
    const navigate = useNavigate();
    const [team, setTeam] = useState(null);
    const [members, setMembers] = useState([]);
    const [tasks, setTasks] = useState([]);
    const [loading, setLoading] = useState(true);
    const [addEmail, setAddEmail] = useState("");
    const [adding, setAdding] = useState(false);
    const [copied, setCopied] = useState(false);
    const [currentUserId, setCurrentUserId] = useState(null);

    useEffect(() => {
        (async () => {
            try {
                const [teamRes, membersRes] = await Promise.all([
                    api.get(`/teams/${id}`),
                    api.get(`/teams/${id}/members`),
                ]);
                setTeam(teamRes.data.data);
                setMembers(membersRes.data.data || []);
                setTasks(teamRes.data.data.tasks || []);
            } catch {
                navigate("/teams");
            }
            setLoading(false);
        })();
    }, [id]);

    useEffect(() => {
        api.get("/user").then((res) => setCurrentUserId(res.data.data?.id)).catch(() => {});
    }, []);

    const copyInvite = async () => {
        if (!team?.invite_code) return;
        try {
            await navigator.clipboard.writeText(team.invite_code);
            setCopied(true);
            setTimeout(() => setCopied(false), 2000);
        } catch {}
    };

    const addMember = async () => {
        const email = addEmail.trim();
        if (!email) return;
        setAdding(true);
        try {
            const searchRes = await api.get("/users/search", { params: { email } });
            const user = searchRes.data.data;
            const res = await api.post(`/teams/${id}/members`, { user_id: user.id });
            setMembers((prev) => [...prev, res.data.data]);
            setAddEmail("");
        } catch {
            alert("User not found or already a member");
        }
        setAdding(false);
    };

    const removeMember = async (member) => {
        try {
            await api.delete(`/teams/${id}/members/${member.id}`);
            setMembers((prev) => prev.filter((m) => m.id !== member.id));
        } catch {}
    };

    const isOwner = team?.owner_id === currentUserId;
    const isAdminOrOwner = isOwner || members.some((m) => m.user_id === currentUserId && m.role === "admin");

    if (loading) {
        return (
            <div className="min-h-screen bg-[#0A0A0F] flex items-center justify-center">
                <div className="text-zinc-600">Loading...</div>
            </div>
        );
    }

    if (!team) return null;

    return (
        <div className="min-h-screen bg-[#0A0A0F] pb-24">
            <div className="max-w-4xl mx-auto px-4 py-6">
                <button onClick={() => navigate("/teams")} className="flex items-center gap-1 text-sm text-zinc-500 hover:text-zinc-300 mb-4 transition-colors">
                    <svg className="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" strokeWidth={2}>
                        <path strokeLinecap="round" strokeLinejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5" />
                    </svg>
                    Back to Teams
                </button>

                <div className="bg-zinc-900/50 border border-zinc-800 rounded-xl p-6 mb-6">
                    <div className="flex items-start justify-between mb-4">
                        <div>
                            <h1 className="text-2xl font-bold text-white">{team.name}</h1>
                            <p className="text-sm text-zinc-500 mt-1">
                                {members.length} member{members.length !== 1 ? "s" : ""} &middot; {tasks.length} task{tasks.length !== 1 ? "s" : ""}
                            </p>
                        </div>
                    </div>
                    <div className="flex items-center gap-2">
                        <div className="flex-1 bg-zinc-800/50 border border-zinc-700 rounded-lg px-3 py-2 text-sm font-mono text-zinc-400 select-all">
                            {team.invite_code}
                        </div>
                        <button onClick={copyInvite}
                            className="px-3 py-2 text-sm border border-zinc-700 rounded-lg hover:bg-zinc-800 text-zinc-400 hover:text-zinc-200 transition-all">
                            {copied ? "Copied!" : "Copy Code"}
                        </button>
                    </div>
                </div>

                <div className="bg-zinc-900/50 border border-zinc-800 rounded-xl p-6 mb-6">
                    <h2 className="text-sm font-medium text-zinc-400 mb-4">Members</h2>

                    {isAdminOrOwner && (
                        <div className="flex items-center gap-2 mb-4">
                            <input value={addEmail} onChange={(e) => setAddEmail(e.target.value)}
                                onKeyDown={(e) => e.key === "Enter" && addMember()}
                                type="email" placeholder="Add member by email..."
                                className="flex-1 bg-zinc-800 border border-zinc-700 rounded-lg px-3 py-2 text-sm text-zinc-300 placeholder-zinc-600 focus:border-violet-500/50 focus:ring-0 outline-none" />
                            <button onClick={addMember} disabled={adding || !addEmail.trim()}
                                className="px-3 py-2 text-sm bg-violet-600 hover:bg-violet-500 disabled:bg-zinc-800 disabled:text-zinc-700 text-white rounded-lg font-medium transition-all">Add</button>
                        </div>
                    )}

                    <div className="space-y-2">
                        {members.map((m) => (
                            <div key={m.id} className="flex items-center gap-3 py-2">
                                <div className="w-8 h-8 rounded-full bg-gradient-to-br from-violet-500 to-pink-500 flex items-center justify-center text-sm font-bold text-white flex-shrink-0">
                                    {(m.user?.name || "?")[0].toUpperCase()}
                                </div>
                                <div className="flex-1 min-w-0">
                                    <p className="text-sm text-zinc-200 truncate">{m.user?.name || "Unknown"}</p>
                                    <p className="text-xs text-zinc-600">{m.user?.email || ""}</p>
                                </div>
                                <span className={`text-xs font-medium px-2 py-0.5 rounded-full ${
                                    m.role === "admin" ? "bg-violet-500/20 text-violet-400" : "bg-zinc-800 text-zinc-500"
                                }`}>
                                    {m.role}
                                </span>
                                {isOwner && m.role !== "admin" && (
                                    <button onClick={() => removeMember(m)} className="p-1 text-zinc-700 hover:text-rose-400 transition-colors">
                                        <svg className="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" strokeWidth={2}>
                                            <path strokeLinecap="round" strokeLinejoin="round" d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                    </button>
                                )}
                            </div>
                        ))}
                    </div>
                </div>

                <div className="bg-zinc-900/50 border border-zinc-800 rounded-xl p-6">
                    <h2 className="text-sm font-medium text-zinc-400 mb-4">Team Tasks</h2>
                    <div className="space-y-2">
                        {tasks.map((t) => <TaskCard key={t.id} task={t} />)}
                        {!tasks.length && <div className="text-sm text-zinc-700 py-4 text-center">No tasks in this team.</div>}
                    </div>
                </div>
            </div>
        </div>
    );
}
