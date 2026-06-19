import { useState, useEffect } from "react";
import { useNavigate } from "react-router-dom";
import api from "../api/client";

export default function Teams() {
    const [teams, setTeams] = useState([]);
    const [loading, setLoading] = useState(true);
    const [showCreate, setShowCreate] = useState(false);
    const [showJoin, setShowJoin] = useState(false);
    const [form, setForm] = useState({ name: "" });
    const [inviteCode, setInviteCode] = useState("");
    const [creating, setCreating] = useState(false);
    const [joining, setJoining] = useState(false);
    const navigate = useNavigate();

    useEffect(() => { fetchTeams(); }, []);

    const fetchTeams = async () => {
        setLoading(true);
        try {
            const res = await api.get("/teams");
            setTeams(res.data.data || []);
        } catch {}
        setLoading(false);
    };

    const createTeam = async () => {
        if (!form.name.trim()) return;
        setCreating(true);
        try {
            const res = await api.post("/teams", form);
            setTeams((prev) => [res.data.data, ...prev]);
            setShowCreate(false);
            setForm({ name: "" });
        } catch {}
        setCreating(false);
    };

    const joinTeam = async () => {
        if (!inviteCode.trim()) return;
        setJoining(true);
        try {
            await api.post("/teams/join", { invite_code: inviteCode.trim() });
            setShowJoin(false);
            setInviteCode("");
            await fetchTeams();
        } catch {}
        setJoining(false);
    };

    const remove = async (team) => {
        try {
            await api.delete(`/teams/${team.id}`);
            setTeams((prev) => prev.filter((t) => t.id !== team.id));
        } catch {}
    };

    return (
        <div className="min-h-screen bg-[#0A0A0F] pb-24">
            <div className="max-w-4xl mx-auto px-4 py-6">
                <div className="flex items-center justify-between mb-6">
                    <h1 className="text-2xl font-bold text-white">Teams</h1>
                    <div className="flex items-center gap-2">
                        <button onClick={() => setShowJoin(true)}
                            className="px-3 py-1.5 text-sm text-violet-400 hover:text-violet-300 border border-violet-500/30 rounded-lg transition-colors">Join Team</button>
                        <button onClick={() => setShowCreate(true)}
                            className="px-4 py-1.5 text-sm bg-violet-600 hover:bg-violet-500 text-white rounded-lg font-medium transition-all">+ New Team</button>
                    </div>
                </div>

                {/* Create Modal */}
                {showCreate && (
                    <div className="fixed inset-0 bg-black/60 z-50 flex items-center justify-center" onClick={() => setShowCreate(false)}>
                        <div className="bg-zinc-900 border border-zinc-800 rounded-xl p-6 w-full max-w-md mx-4" onClick={(e) => e.stopPropagation()}>
                            <h2 className="text-lg font-semibold text-white mb-4">Create Team</h2>
                            <input value={form.name} onChange={(e) => setForm({ ...form, name: e.target.value })}
                                onKeyDown={(e) => e.key === "Enter" && createTeam()}
                                placeholder="Team name..."
                                className="w-full bg-zinc-800 border border-zinc-700 rounded-lg px-4 py-2.5 text-sm text-white placeholder-zinc-600 focus:border-violet-500/50 focus:ring-0 outline-none mb-4" />
                            <div className="flex justify-end gap-2">
                                <button onClick={() => setShowCreate(false)} className="px-4 py-2 text-sm text-zinc-400 hover:text-zinc-300">Cancel</button>
                                <button onClick={createTeam} disabled={creating || !form.name.trim()}
                                    className="px-4 py-2 text-sm bg-violet-600 hover:bg-violet-500 disabled:bg-zinc-800 disabled:text-zinc-700 text-white rounded-lg font-medium">Create</button>
                            </div>
                        </div>
                    </div>
                )}

                {/* Join Modal */}
                {showJoin && (
                    <div className="fixed inset-0 bg-black/60 z-50 flex items-center justify-center" onClick={() => setShowJoin(false)}>
                        <div className="bg-zinc-900 border border-zinc-800 rounded-xl p-6 w-full max-w-md mx-4" onClick={(e) => e.stopPropagation()}>
                            <h2 className="text-lg font-semibold text-white mb-4">Join Team</h2>
                            <input value={inviteCode} onChange={(e) => setInviteCode(e.target.value)}
                                onKeyDown={(e) => e.key === "Enter" && joinTeam()}
                                placeholder="Enter invite code..."
                                className="w-full bg-zinc-800 border border-zinc-700 rounded-lg px-4 py-2.5 text-sm text-white placeholder-zinc-600 focus:border-violet-500/50 focus:ring-0 outline-none uppercase mb-4" />
                            <div className="flex justify-end gap-2">
                                <button onClick={() => setShowJoin(false)} className="px-4 py-2 text-sm text-zinc-400 hover:text-zinc-300">Cancel</button>
                                <button onClick={joinTeam} disabled={joining || !inviteCode.trim()}
                                    className="px-4 py-2 text-sm bg-violet-600 hover:bg-violet-500 disabled:bg-zinc-800 disabled:text-zinc-700 text-white rounded-lg font-medium">Join</button>
                            </div>
                        </div>
                    </div>
                )}

                {loading ? (
                    <div className="space-y-2">
                        {[1, 2].map((n) => (
                            <div key={n} className="bg-zinc-900 rounded-xl p-5 animate-pulse">
                                <div className="h-5 bg-zinc-800 rounded w-1/3 mb-3" />
                                <div className="h-3 bg-zinc-800/50 rounded w-1/4" />
                            </div>
                        ))}
                    </div>
                ) : (
                    <div className="grid gap-3">
                        {teams.map((team) => (
                            <div key={team.id} onClick={() => navigate(`/teams/${team.id}`)}
                                className="bg-zinc-900/50 border border-zinc-800 hover:border-zinc-700 rounded-xl p-5 cursor-pointer transition-all group">
                                <div className="flex items-start justify-between">
                                    <div>
                                        <h3 className="text-base font-semibold text-zinc-200 group-hover:text-white transition-colors">{team.name}</h3>
                                        <p className="text-sm text-zinc-600 mt-1">{team.members_count ?? 0} members</p>
                                        {team.invite_code && <p className="text-xs font-mono text-zinc-700 mt-1">Code: {team.invite_code}</p>}
                                    </div>
                                    <button onClick={(e) => { e.stopPropagation(); remove(team); }}
                                        className="p-1 text-zinc-700 hover:text-rose-400 opacity-0 group-hover:opacity-100 transition-all">
                                        <svg className="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" strokeWidth={2}>
                                            <path strokeLinecap="round" strokeLinejoin="round" d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        ))}
                        {!teams.length && <div className="text-center py-20 text-zinc-700 text-sm">No teams yet. Create one or join with an invite code.</div>}
                    </div>
                )}
            </div>
        </div>
    );
}
