import { HomeIcon, FolderIcon, UsersIcon } from "@heroicons/react/24/outline";

export default function Navbar({ onSearch }) {
    return (
        <header className="sticky top-0 z-50 bg-[#0A0A0F]/80 backdrop-blur-xl border-b border-white/5">
            <div className="max-w-6xl mx-auto px-4 h-16 flex items-center justify-between gap-4">
                <a href="/dashboard" className="flex items-center gap-2.5 flex-shrink-0">
                    <div className="w-8 h-8 bg-gradient-to-br from-violet-500 to-pink-500 rounded-xl flex items-center justify-center shadow-lg shadow-violet-500/20">
                        <span className="text-white font-bold text-sm">T</span>
                    </div>
                    <span className="font-bold text-lg hidden sm:block bg-gradient-to-r from-violet-400 to-pink-400 bg-clip-text text-transparent">
                        TaskFlow
                    </span>
                </a>

                <nav className="hidden md:flex items-center gap-1">
                    <a href="/dashboard" className="flex items-center gap-1.5 px-3 py-1.5 text-sm text-zinc-400 hover:text-zinc-200 transition-colors rounded-lg hover:bg-white/5">
                        <HomeIcon className="w-4 h-4" />
                        Dashboard
                    </a>
                    <a href="/categories" className="flex items-center gap-1.5 px-3 py-1.5 text-sm text-zinc-400 hover:text-zinc-200 transition-colors rounded-lg hover:bg-white/5">
                        <FolderIcon className="w-4 h-4" />
                        Categories
                    </a>
                    <a href="/teams" className="flex items-center gap-1.5 px-3 py-1.5 text-sm text-zinc-400 hover:text-zinc-200 transition-colors rounded-lg hover:bg-white/5">
                        <UsersIcon className="w-4 h-4" />
                        Teams
                    </a>
                </nav>

                <div className="hidden sm:flex items-center gap-3 flex-1 max-w-md ml-auto mr-4">
                    <div className="relative w-full">
                        <input
                            type="text"
                            placeholder="Search tasks..."
                            onChange={(e) => onSearch?.(e.target.value)}
                            className="w-full bg-zinc-800/50 border border-zinc-700/50 rounded-xl pl-4 pr-3 h-9 text-sm text-zinc-100 placeholder-zinc-600 focus:border-violet-500/50 focus:ring-1 focus:ring-violet-500/20 transition-all outline-none"
                        />
                    </div>
                </div>

                <UserMenu />
            </div>

            <MobileNav />
        </header>
    );
}

function UserMenu() {
    const { userName, logout } = { userName: "User", logout: () => {} };
    return (
        <div className="flex items-center gap-3">
            <span className="text-sm text-zinc-400 hidden md:block">{userName}</span>
            <div className="w-8 h-8 rounded-full bg-gradient-to-br from-violet-500 to-pink-500 flex items-center justify-center text-white text-xs font-bold shadow-lg shadow-violet-500/20">
                {userName.charAt(0).toUpperCase()}
            </div>
            <button onClick={logout} className="p-2 text-zinc-500 hover:text-rose-400 transition-colors rounded-lg hover:bg-white/5" title="Logout">
                <svg className="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" strokeWidth={2}>
                    <path strokeLinecap="round" strokeLinejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15m3 0l3-3m0 0l-3-3m3 3H9" />
                </svg>
            </button>
        </div>
    );
}

function MobileNav() {
    return (
        <nav className="md:hidden fixed bottom-0 left-0 right-0 bg-[#0A0A0F]/95 backdrop-blur-xl border-t border-white/5 z-50 flex items-center justify-around py-2">
            <a href="/dashboard" className="flex flex-col items-center gap-0.5 px-4 py-1 text-zinc-500 hover:text-violet-400 transition-colors">
                <HomeIcon className="w-5 h-5" />
                <span className="text-[10px]">Home</span>
            </a>
            <a href="/categories" className="flex flex-col items-center gap-0.5 px-4 py-1 text-zinc-500 hover:text-violet-400 transition-colors">
                <FolderIcon className="w-5 h-5" />
                <span className="text-[10px]">Categories</span>
            </a>
            <a href="/teams" className="flex flex-col items-center gap-0.5 px-4 py-1 text-zinc-500 hover:text-violet-400 transition-colors">
                <UsersIcon className="w-5 h-5" />
                <span className="text-[10px]">Teams</span>
            </a>
        </nav>
    );
}
