<script setup>
import { ref, computed, onMounted } from "vue";
import { useRoute, useRouter } from "vue-router";
import { useAuthStore } from "@/stores/authStore";
import axios from "axios";
import TaskCard from "@/components/TaskCard.vue";

const route = useRoute();
const router = useRouter();
const auth = useAuthStore();

const team = ref(null);
const members = ref([]);
const tasks = ref([]);
const loading = ref(true);
const addEmail = ref("");
const adding = ref(false);
const copied = ref(false);

const teamId = computed(() => route.params.id);

onMounted(async () => {
    try {
        const [teamRes, membersRes] = await Promise.all([
            axios.get(`/api/teams/${teamId.value}`),
            axios.get(`/api/teams/${teamId.value}/members`),
        ]);
        team.value = teamRes.data.data;
        members.value = membersRes.data.data ?? [];
        tasks.value = team.value.tasks ?? [];
    } catch {
        router.replace("/teams");
    }
    loading.value = false;
});

async function copyInvite() {
    if (!team.value?.invite_code) return;
    try {
        await navigator.clipboard.writeText(team.value.invite_code);
        copied.value = true;
        setTimeout(() => (copied.value = false), 2000);
    } catch {}
}

async function addMember() {
    const email = addEmail.value.trim();
    if (!email) return;
    adding.value = true;
    try {
        const searchRes = await axios.get("/api/users/search", { params: { email } });
        const user = searchRes.data.data;
        const res = await axios.post(`/api/teams/${teamId.value}/members`, { user_id: user.id });
        members.value.push(res.data.data);
        addEmail.value = "";
    } catch {
        alert("User not found or already a member");
    }
    adding.value = false;
}

async function removeMember(member) {
    try {
        await axios.delete(`/api/teams/${teamId.value}/members/${member.id}`);
        members.value = members.value.filter((m) => m.id !== member.id);
    } catch {}
}

function isOwner() {
    return team.value?.owner_id === auth.user?.id;
}

function isAdminOrOwner() {
    const m = members.value.find((m) => m.user_id === auth.user?.id);
    return isOwner() || m?.role === "admin";
}
</script>

<template>
    <div class="min-h-screen bg-[#0A0A0F]">
        <div class="max-w-4xl mx-auto px-4 py-6">
            <button @click="router.push('/teams')" class="flex items-center gap-1 text-sm text-zinc-500 hover:text-zinc-300 mb-4 transition-colors">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5" />
                </svg>
                Back to Teams
            </button>

            <div v-if="loading" class="text-center py-20 text-zinc-600">Loading...</div>

            <template v-else-if="team">
                <div class="bg-zinc-900/50 border border-zinc-800 rounded-xl p-6 mb-6">
                    <div class="flex items-start justify-between mb-4">
                        <div>
                            <h1 class="text-2xl font-bold text-white">{{ team.name }}</h1>
                            <p class="text-sm text-zinc-500 mt-1">
                                {{ members.length }} member{{ members.length !== 1 ? "s" : "" }}
                                &middot; {{ tasks.length }} task{{ tasks.length !== 1 ? "s" : "" }}
                            </p>
                        </div>
                    </div>

                    <div class="flex items-center gap-2">
                        <div class="flex-1 bg-zinc-800/50 border border-zinc-700 rounded-lg px-3 py-2 text-sm font-mono text-zinc-400 select-all">
                            {{ team.invite_code }}
                        </div>
                        <button @click="copyInvite"
                            class="px-3 py-2 text-sm border border-zinc-700 rounded-lg hover:bg-zinc-800 text-zinc-400 hover:text-zinc-200 transition-all">
                            {{ copied ? "Copied!" : "Copy Code" }}
                        </button>
                    </div>
                </div>

                <div class="bg-zinc-900/50 border border-zinc-800 rounded-xl p-6 mb-6">
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-sm font-medium text-zinc-400">Members</h2>
                    </div>

                    <div v-if="isAdminOrOwner()" class="flex items-center gap-2 mb-4">
                        <input v-model="addEmail" type="email" placeholder="Add member by email..." @keydown.enter="addMember"
                            class="flex-1 bg-zinc-800 border border-zinc-700 rounded-lg px-3 py-2 text-sm text-zinc-300 placeholder-zinc-600 focus:border-violet-500/50 focus:ring-0 outline-none" />
                        <button @click="addMember" :disabled="adding || !addEmail.trim()"
                            class="px-3 py-2 text-sm bg-violet-600 hover:bg-violet-500 disabled:bg-zinc-800 disabled:text-zinc-700 text-white rounded-lg font-medium transition-all">Add</button>
                    </div>

                    <div class="space-y-2">
                        <div v-for="m in members" :key="m.id" class="flex items-center gap-3 py-2">
                            <div class="w-8 h-8 rounded-full bg-gradient-to-br from-violet-500 to-magenta-500 flex items-center justify-center text-sm font-bold text-white flex-shrink-0">
                                {{ (m.user?.name ?? "?")[0].toUpperCase() }}
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm text-zinc-200 truncate">{{ m.user?.name ?? "Unknown" }}</p>
                                <p class="text-xs text-zinc-600">{{ m.user?.email ?? "" }}</p>
                            </div>
                            <span class="text-xs font-medium px-2 py-0.5 rounded-full"
                                :class="m.role === 'admin' ? 'bg-violet-500/20 text-violet-400' : 'bg-zinc-800 text-zinc-500'">
                                {{ m.role }}
                            </span>
                            <button v-if="isOwner() && m.role !== 'admin'" @click="removeMember(m)"
                                class="p-1 text-zinc-700 hover:text-rose-400 transition-colors">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>

                <div class="bg-zinc-900/50 border border-zinc-800 rounded-xl p-6">
                    <h2 class="text-sm font-medium text-zinc-400 mb-4">Team Tasks</h2>
                    <div class="space-y-2">
                        <TaskCard v-for="t in tasks" :key="t.id" :task="t" />
                        <div v-if="!tasks.length" class="text-sm text-zinc-700 py-4 text-center">No tasks in this team.</div>
                    </div>
                </div>
            </template>
        </div>
    </div>
</template>
