import { lazy, Suspense, useEffect } from "react";
import { Routes, Route, Navigate } from "react-router-dom";
import useAuthStore from "./stores/authStore";

const Dashboard = lazy(() => import("./pages/Dashboard"));
const Login = lazy(() => import("./pages/Login"));
const Register = lazy(() => import("./pages/Register"));
const TaskDetail = lazy(() => import("./pages/TaskDetail"));
const Categories = lazy(() => import("./pages/Categories"));
const Teams = lazy(() => import("./pages/Teams"));
const TeamDetail = lazy(() => import("./pages/TeamDetail"));

function ProtectedRoute({ children }) {
    const token = useAuthStore((s) => s.token);
    if (!token) return <Navigate to="/login" replace />;
    return children;
}

function GuestRoute({ children }) {
    const token = useAuthStore((s) => s.token);
    if (token) return <Navigate to="/dashboard" replace />;
    return children;
}

function Loading() {
    return (
        <div className="min-h-screen bg-[#0A0A0F] flex items-center justify-center">
            <div className="w-8 h-8 border-2 border-violet-500/30 border-t-violet-500 rounded-full animate-spin" />
        </div>
    );
}

export default function App() {
    const init = useAuthStore((s) => s.init);

    useEffect(() => { init(); }, []);

    return (
        <div className="min-h-screen bg-[#0A0A0F] text-zinc-100 antialiased">
            <Suspense fallback={<Loading />}>
                <Routes>
                    <Route path="/" element={<Navigate to="/dashboard" replace />} />
                    <Route path="/login" element={<GuestRoute><Login /></GuestRoute>} />
                    <Route path="/register" element={<GuestRoute><Register /></GuestRoute>} />
                    <Route path="/dashboard" element={<ProtectedRoute><Dashboard /></ProtectedRoute>} />
                    <Route path="/tasks/:id" element={<ProtectedRoute><TaskDetail /></ProtectedRoute>} />
                    <Route path="/categories" element={<ProtectedRoute><Categories /></ProtectedRoute>} />
                    <Route path="/teams" element={<ProtectedRoute><Teams /></ProtectedRoute>} />
                    <Route path="/teams/:id" element={<ProtectedRoute><TeamDetail /></ProtectedRoute>} />
                </Routes>
            </Suspense>
        </div>
    );
}
