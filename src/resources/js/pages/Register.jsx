import { useState } from "react";
import { useNavigate } from "react-router-dom";
import useAuthStore from "../stores/authStore";

export default function Register() {
    const [form, setForm] = useState({ name: "", email: "", password: "", password_confirmation: "" });
    const [loading, setLoading] = useState(false);
    const [error, setError] = useState("");
    const register = useAuthStore((s) => s.register);
    const navigate = useNavigate();

    const handleSubmit = async (e) => {
        e.preventDefault();
        if (form.password !== form.password_confirmation) {
            setError("Passwords do not match");
            return;
        }
        setLoading(true);
        setError("");
        try {
            await register({
                name: form.name,
                email: form.email,
                password: form.password,
                password_confirmation: form.password_confirmation,
            });
            navigate("/dashboard");
        } catch (err) {
            setError(err.response?.data?.message || "Registration failed");
        }
        setLoading(false);
    };

    return (
        <div className="min-h-screen bg-[#0A0A0F] flex">
            <div className="hidden lg:flex lg:w-1/2 relative overflow-hidden bg-gradient-to-br from-pink-900/40 via-[#0A0A0F] to-violet-900/40 items-center justify-center">
                <div className="absolute inset-0">
                    <div className="absolute top-1/3 right-1/3 w-96 h-96 bg-violet-500/20 rounded-full blur-[128px]" />
                    <div className="absolute bottom-1/3 left-1/3 w-96 h-96 bg-pink-500/20 rounded-full blur-[128px]" />
                </div>
                <div className="relative text-center">
                    <div className="w-20 h-20 mx-auto mb-6 bg-gradient-to-br from-violet-500 to-pink-500 rounded-2xl flex items-center justify-center shadow-2xl shadow-violet-500/30">
                        <span className="text-white font-bold text-3xl">T</span>
                    </div>
                    <h1 className="text-4xl font-bold text-white mb-2">Join TaskFlow</h1>
                    <p className="text-zinc-500 text-lg">Start organizing your tasks</p>
                </div>
            </div>

            <div className="flex-1 flex items-center justify-center p-8">
                <div className="w-full max-w-sm">
                    <div className="lg:hidden text-center mb-8">
                        <div className="w-14 h-14 mx-auto mb-4 bg-gradient-to-br from-violet-500 to-pink-500 rounded-xl flex items-center justify-center shadow-lg shadow-violet-500/20">
                            <span className="text-white font-bold text-xl">T</span>
                        </div>
                        <h1 className="text-2xl font-bold text-white">Create account</h1>
                        <p className="text-zinc-500 text-sm mt-1">Get started with TaskFlow</p>
                    </div>

                    <form onSubmit={handleSubmit} className="space-y-4">
                        <div>
                            <label className="block text-sm text-zinc-500 mb-1.5">Name</label>
                            <input
                                type="text"
                                value={form.name}
                                onChange={(e) => setForm({ ...form, name: e.target.value })}
                                placeholder="John Doe"
                                className="w-full bg-zinc-800/50 border border-zinc-700/50 rounded-xl px-4 py-2.5 text-sm text-zinc-100 placeholder-zinc-600 focus:border-violet-500/50 focus:ring-1 focus:ring-violet-500/20 transition-all outline-none"
                                required
                            />
                        </div>

                        <div>
                            <label className="block text-sm text-zinc-500 mb-1.5">Email</label>
                            <input
                                type="email"
                                value={form.email}
                                onChange={(e) => setForm({ ...form, email: e.target.value })}
                                placeholder="you@example.com"
                                className="w-full bg-zinc-800/50 border border-zinc-700/50 rounded-xl px-4 py-2.5 text-sm text-zinc-100 placeholder-zinc-600 focus:border-violet-500/50 focus:ring-1 focus:ring-violet-500/20 transition-all outline-none"
                                required
                            />
                        </div>

                        <div className="grid grid-cols-2 gap-3">
                            <div>
                                <label className="block text-sm text-zinc-500 mb-1.5">Password</label>
                                <input
                                    type="password"
                                    value={form.password}
                                    onChange={(e) => setForm({ ...form, password: e.target.value })}
                                    placeholder="••••••••"
                                    className="w-full bg-zinc-800/50 border border-zinc-700/50 rounded-xl px-4 py-2.5 text-sm text-zinc-100 placeholder-zinc-600 focus:border-violet-500/50 focus:ring-1 focus:ring-violet-500/20 transition-all outline-none"
                                    required
                                />
                            </div>
                            <div>
                                <label className="block text-sm text-zinc-500 mb-1.5">Confirm</label>
                                <input
                                    type="password"
                                    value={form.password_confirmation}
                                    onChange={(e) => setForm({ ...form, password_confirmation: e.target.value })}
                                    placeholder="••••••••"
                                    className="w-full bg-zinc-800/50 border border-zinc-700/50 rounded-xl px-4 py-2.5 text-sm text-zinc-100 placeholder-zinc-600 focus:border-violet-500/50 focus:ring-1 focus:ring-violet-500/20 transition-all outline-none"
                                    required
                                />
                            </div>
                        </div>

                        {error && (
                            <div className="text-sm text-rose-400 bg-rose-500/10 border border-rose-500/20 rounded-lg px-4 py-2">{error}</div>
                        )}

                        <button
                            type="submit"
                            disabled={loading}
                            className="w-full py-2.5 bg-gradient-to-r from-violet-600 to-pink-600 hover:from-violet-500 hover:to-pink-500 disabled:opacity-50 text-white rounded-xl font-medium transition-all shadow-lg shadow-violet-500/20"
                        >
                            {loading ? "Creating account..." : "Create account"}
                        </button>

                        <p className="text-center text-sm text-zinc-600">
                            Already have an account?{" "}
                            <a href="/login" className="text-violet-400 hover:text-violet-300 transition-colors">Sign in</a>
                        </p>
                    </form>
                </div>
            </div>
        </div>
    );
}
