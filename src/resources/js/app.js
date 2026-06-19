import "./../css/app.css";
import Alpine from "alpinejs";
import api from "./api/client";

window.api = api;
window.Alpine = Alpine;

Alpine.data("dashboard", () => ({
    tasks: [],
    categories: [],
    teams: [],
    loading: true,
    search: "",
    filterStatus: "all",
    filterCategory: null,
    selectedIds: [],
    selectMode: false,
    drawerOpen: false,
    editingTask: null,
    form: { title: "", description: "", priority: "medium", status: "todo", deadline: "", category_id: "" },
    saving: false,

    init() {
        this.loadData();
    },

    async loadData() {
        this.loading = true;
        try {
            const [tasksRes, catsRes, teamsRes] = await Promise.all([
                api.get("/tasks"),
                api.get("/categories"),
                api.get("/teams"),
            ]);
            this.tasks = tasksRes.data.data || [];
            this.categories = catsRes.data.data || [];
            this.teams = teamsRes.data.data || [];
        } catch {}
        this.loading = false;
    },

    get filteredTasks() {
        return this.tasks.filter((t) => {
            if (this.filterStatus !== "all" && t.status !== this.filterStatus) return false;
            if (this.filterCategory && t.category_id !== this.filterCategory) return false;
            if (this.search) {
                const q = this.search.toLowerCase();
                if (!t.title.toLowerCase().includes(q)) return false;
            }
            return true;
        });
    },

    get stats() {
        const total = this.tasks.length;
        const todo = this.tasks.filter((t) => t.status === "todo").length;
        const done = this.tasks.filter((t) => t.status === "done").length;
        const overdue = this.tasks.filter((t) => t.deadline && t.status !== "done" && new Date(t.deadline) < new Date()).length;
        return { total, todo, done, overdue };
    },

    openCreate() {
        this.editingTask = null;
        this.form = { title: "", description: "", priority: "medium", status: "todo", deadline: "", category_id: "" };
        this.drawerOpen = true;
    },

    openEdit(task) {
        this.editingTask = task;
        this.form = {
            title: task.title,
            description: task.description || "",
            priority: task.priority,
            status: task.status,
            deadline: task.deadline ? task.deadline.split("T")[0] : "",
            category_id: task.category_id || "",
        };
        this.drawerOpen = true;
    },

    async save() {
        this.saving = true;
        try {
            if (this.editingTask) {
                const res = await api.put(`/tasks/${this.editingTask.id}`, this.form);
                this.tasks = this.tasks.map((t) => (t.id === this.editingTask.id ? res.data.data : t));
            } else {
                const res = await api.post("/tasks", this.form);
                this.tasks = [res.data.data, ...this.tasks];
            }
            this.drawerOpen = false;
        } catch {}
        this.saving = false;
    },

    async toggleStatus(taskId) {
        const task = this.tasks.find((t) => t.id === taskId);
        if (!task) return;
        const next = { todo: "on_progress", on_progress: "done", progress: "done", done: "todo" };
        const newStatus = next[task.status] || "todo";
        const prev = task.status;
        task.status = newStatus;
        try {
            await api.put(`/tasks/${taskId}`, { status: newStatus });
        } catch {
            task.status = prev;
        }
    },

    async deleteTask(taskId) {
        const idx = this.tasks.findIndex((t) => t.id === taskId);
        if (idx === -1) return;
        const [removed] = this.tasks.splice(idx, 1);
        try {
            await api.delete(`/tasks/${taskId}`);
        } catch {
            this.tasks.splice(idx, 0, removed);
        }
    },

    toggleSelect(id) {
        const i = this.selectedIds.indexOf(id);
        if (i === -1) this.selectedIds.push(id);
        else this.selectedIds.splice(i, 1);
    },

    selectAll() {
        const ids = this.filteredTasks.map((t) => t.id);
        this.selectedIds = this.selectedIds.length === ids.length ? [] : [...ids];
    },

    async bulkDelete() {
        if (!this.selectedIds.length || !confirm(`Delete ${this.selectedIds.length} tasks?`)) return;
        try {
            await api.post("/tasks/bulk/delete", { ids: this.selectedIds });
            this.tasks = this.tasks.filter((t) => !this.selectedIds.includes(t.id));
            this.selectedIds = [];
        } catch {}
    },

    async bulkStatus(status) {
        if (!this.selectedIds.length) return;
        try {
            await api.post("/tasks/bulk/status", { ids: this.selectedIds, status });
            this.tasks = this.tasks.map((t) => (this.selectedIds.includes(t.id) ? { ...t, status } : t));
            this.selectedIds = [];
        } catch {}
    },

    remainingChars(str) {
        return 255 - (str || "").length;
    },
}));

Alpine.data("taskDetail", () => ({
    task: null,
    loading: true,
    assignees: [],
    subtasks: [],
    comments: [],
    attachments: [],
    newComment: "",
    newSubtask: "",
    memberSearch: "",
    searchResults: [],
    showAssign: false,
    teamMembers: [],

    init() {
        this.fetchTask();
    },

    async fetchTask() {
        try {
            const res = await api.get(`/tasks/${window.taskId}`, {
                params: { include: "category,subtasks,comments.user,attachments,assignees" },
            });
            this.task = res.data.data;
            this.subtasks = this.task.subtasks || [];
            this.comments = this.task.comments || [];
            this.attachments = this.task.attachments || [];
            this.assignees = this.task.assignees || [];
            if (this.task.team_id) {
                const membersRes = await api.get(`/teams/${this.task.team_id}/members`);
                this.teamMembers = membersRes.data.data || [];
            }
        } catch {}
        this.loading = false;
    },

    async updateStatus(status) {
        if (!this.task) return;
        const prev = this.task.status;
        this.task.status = status;
        try {
            await api.put(`/tasks/${this.task.id}`, { status });
        } catch {
            this.task.status = prev;
        }
    },

    formatDate(d) {
        if (!d) return "";
        return new Date(d).toLocaleDateString("id-ID", {
            weekday: "long", year: "numeric", month: "long", day: "numeric",
        });
    },

    async addSubtask() {
        const title = this.newSubtask.trim();
        if (!title) return;
        try {
            const res = await api.post(`/tasks/${this.task.id}/subtasks`, { title });
            this.subtasks.push(res.data.data);
            this.newSubtask = "";
        } catch {}
    },

    async toggleSubtask(subtask) {
        subtask.is_completed = !subtask.is_completed;
        try {
            await api.patch(`/subtasks/${subtask.id}/toggle`);
        } catch {
            subtask.is_completed = !subtask.is_completed;
        }
    },

    async deleteSubtask(subtask) {
        try {
            await api.delete(`/subtasks/${subtask.id}`);
            this.subtasks = this.subtasks.filter((s) => s.id !== subtask.id);
        } catch {}
    },

    async postComment() {
        const content = this.newComment.trim();
        if (!content) return;
        try {
            const res = await api.post(`/tasks/${this.task.id}/comments`, { content });
            this.comments.push(res.data.data);
            this.newComment = "";
        } catch {}
    },

    async deleteComment(comment) {
        try {
            await api.delete(`/comments/${comment.id}`);
            this.comments = this.comments.filter((c) => c.id !== comment.id);
        } catch {}
    },

    async uploadAttachment(e) {
        const file = e.target.files[0];
        if (!file) return;
        const formData = new FormData();
        formData.append("file", file);
        try {
            const res = await api.post(`/tasks/${this.task.id}/attachments`, formData);
            this.attachments.push(res.data.data);
        } catch {}
        e.target.value = "";
    },

    async deleteAttachment(attachment) {
        try {
            await api.delete(`/attachments/${attachment.id}`);
            this.attachments = this.attachments.filter((a) => a.id !== attachment.id);
        } catch {}
    },

    async searchMembers(q) {
        this.memberSearch = q;
        if (q.length < 1) { this.searchResults = []; return; }
        if (this.teamMembers.length > 0) {
            const assignedIds = this.assignees.map((a) => a.id || a.user_id);
            this.searchResults = this.teamMembers
                .filter((m) => {
                    const name = (m.user?.name || m.name || "").toLowerCase();
                    const email = (m.user?.email || m.email || "").toLowerCase();
                    const id = m.user_id || m.id;
                    return !assignedIds.includes(id) && (name.includes(q.toLowerCase()) || email.includes(q.toLowerCase()));
                })
                .map((m) => ({ id: m.user_id || m.id, name: m.user?.name || m.name, email: m.user?.email || m.email }));
        }
    },

    async assignUser(userId) {
        try {
            await api.post(`/tasks/${this.task.id}/assign`, { user_id: userId });
            this.memberSearch = "";
            this.searchResults = [];
            this.showAssign = false;
            const res = await api.get(`/tasks/${this.task.id}/assignees`);
            this.assignees = res.data.data || [];
        } catch {}
    },

    async unassignUser(userId) {
        try {
            await api.delete(`/tasks/${this.task.id}/assign/${userId}`);
            this.assignees = this.assignees.filter((a) => a.id !== userId && a.user_id !== userId);
        } catch {}
    },

    ago(date) {
        const diff = Date.now() - new Date(date).getTime();
        const mins = Math.floor(diff / 60000);
        if (mins < 1) return "just now";
        if (mins < 60) return `${mins}m ago`;
        const hrs = Math.floor(mins / 60);
        if (hrs < 24) return `${hrs}h ago`;
        return `${Math.floor(hrs / 24)}d ago`;
    },

    fileIcon(mime) {
        if (!mime) return "📄";
        if (mime.startsWith("image")) return "🖼";
        if (mime.includes("pdf")) return "📕";
        if (mime.includes("zip") || mime.includes("rar")) return "📦";
        if (mime.includes("word") || mime.includes("document")) return "📝";
        if (mime.includes("sheet") || mime.includes("excel")) return "📊";
        return "📄";
    },

    get subtaskProgress() {
        if (!this.subtasks.length) return 0;
        return Math.round((this.subtasks.filter((s) => s.is_completed).length / this.subtasks.length) * 100);
    },
}));

Alpine.data("categoriesPage", () => ({
    categories: [],
    tasks: [],
    loading: true,
    selectedCategory: null,
    editing: null,
    form: { name: "", color: "#8B5CF6" },

    init() { this.fetch(); },

    async fetch() {
        this.loading = true;
        try {
            const res = await api.get("/categories");
            this.categories = res.data.data || [];
        } catch {}
        this.loading = false;
    },

    async fetchTasks(catId) {
        this.selectedCategory = catId;
        try {
            const res = await api.get("/tasks", { params: { category_id: catId } });
            this.tasks = res.data.data || [];
        } catch {}
    },

    startEdit(cat) {
        this.editing = cat.id;
        this.form = { name: cat.name, color: cat.color };
    },

    cancelEdit() {
        this.editing = null;
    },

    async save(cat) {
        try {
            const res = await api.put(`/categories/${cat.id}`, this.form);
            this.categories = this.categories.map((c) => (c.id === cat.id ? res.data.data : c));
            this.editing = null;
        } catch {}
    },

    async create() {
        if (!this.form.name.trim()) return;
        try {
            const res = await api.post("/categories", this.form);
            this.categories.push(res.data.data);
            this.form = { name: "", color: "#8B5CF6" };
        } catch {}
    },

    async remove(cat) {
        try {
            await api.delete(`/categories/${cat.id}`);
            this.categories = this.categories.filter((c) => c.id !== cat.id);
            if (this.selectedCategory === cat.id) {
                this.selectedCategory = null;
                this.tasks = [];
            }
        } catch {}
    },
}));

Alpine.data("teamsPage", () => ({
    teams: [],
    loading: true,
    showCreate: false,
    showJoin: false,
    form: { name: "" },
    inviteCode: "",
    creating: false,
    joining: false,

    init() { this.fetch(); },

    async fetch() {
        this.loading = true;
        try {
            const res = await api.get("/teams");
            this.teams = res.data.data || [];
        } catch {}
        this.loading = false;
    },

    async create() {
        if (!this.form.name.trim()) return;
        this.creating = true;
        try {
            const res = await api.post("/teams", this.form);
            this.teams.unshift(res.data.data);
            this.showCreate = false;
            this.form = { name: "" };
        } catch {}
        this.creating = false;
    },

    async join() {
        if (!this.inviteCode.trim()) return;
        this.joining = true;
        try {
            await api.post("/teams/join", { invite_code: this.inviteCode.trim() });
            this.showJoin = false;
            this.inviteCode = "";
            await this.fetch();
        } catch {}
        this.joining = false;
    },

    async remove(team) {
        try {
            await api.delete(`/teams/${team.id}`);
            this.teams = this.teams.filter((t) => t.id !== team.id);
        } catch {}
    },
}));

Alpine.data("teamDetail", () => ({
    team: null,
    members: [],
    tasks: [],
    loading: true,
    addEmail: "",
    adding: false,
    copied: false,
    memberSearch: "",

    init() {
        this.fetch();
    },

    async fetch() {
        try {
            const [teamRes, membersRes] = await Promise.all([
                api.get(`/teams/${window.teamId}`),
                api.get(`/teams/${window.teamId}/members`),
            ]);
            this.team = teamRes.data.data;
            this.members = membersRes.data.data || [];
            this.tasks = teamRes.data.data.tasks || [];
        } catch {
            window.location.href = "/teams";
        }
        this.loading = false;
    },

    async copyInvite() {
        if (!this.team?.invite_code) return;
        try {
            await navigator.clipboard.writeText(this.team.invite_code);
            this.copied = true;
            setTimeout(() => (this.copied = false), 2000);
        } catch {}
    },

    async addMember() {
        const email = this.addEmail.trim();
        if (!email) return;
        this.adding = true;
        try {
            const searchRes = await api.get("/users/search", { params: { email } });
            const user = searchRes.data.data;
            const res = await api.post(`/teams/${this.team.id}/members`, { user_id: user.id });
            this.members.push(res.data.data);
            this.addEmail = "";
        } catch {
            alert("User not found or already a member");
        }
        this.adding = false;
    },

    async removeMember(member) {
        try {
            await api.delete(`/teams/${this.team.id}/members/${member.id}`);
            this.members = this.members.filter((m) => m.id !== member.id);
        } catch {}
    },

    get filteredMembers() {
        if (!this.memberSearch) return this.members;
        const q = this.memberSearch.toLowerCase();
        return this.members.filter(
            (m) =>
                (m.user?.name || "").toLowerCase().includes(q) ||
                (m.user?.email || "").toLowerCase().includes(q)
        );
    },

    get isOwner() {
        return this.team?.owner_id === window.currentUserId;
    },
}));

Alpine.data("notifications", () => ({
    items: [],
    unreadCount: 0,
    open: false,

    init() {
        this.fetch();
        setInterval(() => this.fetch(), 30000);
    },

    async fetch() {
        try {
            const res = await api.get("/notifications");
            this.items = res.data.data || [];
            this.unreadCount = res.data.unread_count || 0;
        } catch {}
    },

    async markRead(id) {
        try {
            await api.post(`/notifications/${id}/read`);
            const n = this.items.find((n) => n.id === id);
            if (n && !n.read_at) {
                n.read_at = new Date().toISOString();
                this.unreadCount = Math.max(0, this.unreadCount - 1);
            }
        } catch {}
    },

    async markAllRead() {
        try {
            await api.post("/notifications/read-all");
            this.items.forEach((n) => { if (!n.read_at) n.read_at = new Date().toISOString(); });
            this.unreadCount = 0;
        } catch {}
    },

    async remove(id) {
        try {
            await api.delete(`/notifications/${id}`);
            const n = this.items.find((n) => n.id === id);
            this.items = this.items.filter((n) => n.id !== id);
            if (n && !n.read_at) this.unreadCount = Math.max(0, this.unreadCount - 1);
        } catch {}
    },

    ago(date) {
        const diff = Date.now() - new Date(date).getTime();
        const mins = Math.floor(diff / 60000);
        if (mins < 1) return "just now";
        if (mins < 60) return `${mins}m ago`;
        const hrs = Math.floor(mins / 60);
        if (hrs < 24) return `${hrs}h ago`;
        return `${Math.floor(hrs / 24)}d ago`;
    },

    icon(type) {
        if (type.includes("TaskAssigned")) return "👤";
        if (type.includes("TaskComment")) return "💬";
        if (type.includes("DeadlineReminder")) return "⏰";
        return "🔔";
    },
}));

Alpine.data("profilePage", () => ({
    form: {
        name: window.currentUser?.name || "",
        email: window.currentUser?.email || "",
        phone: window.currentUser?.phone || "",
    },
    saving: false,
    message: null,
    error: null,

    async save() {
        this.saving = true;
        this.message = null;
        this.error = null;
        try {
            const res = await api.put("/user", this.form);
            window.currentUser = res.data.data;
            this.message = "Profile updated successfully";
        } catch (err) {
            this.error = err.response?.data?.message || "Failed to update profile";
        }
        this.saving = false;
    },
}));

Alpine.data("reportsPage", () => ({
    summary: null,
    loading: true,

    init() {
        this.fetch();
    },

    async fetch() {
        this.loading = true;
        try {
            const res = await api.get("/reports/summary");
            this.summary = res.data.data;
        } catch {}
        this.loading = false;
    },

    exportCsv() {
        const token = localStorage.getItem("token") || sessionStorage.getItem("token");
        window.open("/api/reports/export?token=" + token, "_blank");
    },
}));

Alpine.start();
