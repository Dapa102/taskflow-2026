export default function BadgePriority({ level = "medium" }) {
    const styles = {
        high: "bg-rose-500/20 text-rose-300 ring-1 ring-rose-500/30",
        medium: "bg-amber-500/20 text-amber-300 ring-1 ring-amber-500/30",
        low: "bg-zinc-500/20 text-zinc-400 ring-1 ring-zinc-500/30",
    };
    return (
        <span className={`inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${styles[level] || styles.medium}`}>
            {level}
        </span>
    );
}
