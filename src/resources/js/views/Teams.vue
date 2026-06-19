<script setup>
import { ref, onMounted } from "vue";
import { useRouter } from "vue-router";
import axios from "axios";

const router = useRouter();
const teams = ref([]);
const loading = ref(true);
const showCreate = ref(false);
const showJoin = ref(false);
const form = ref({ name: "" });
const inviteCode = ref("");
const creating = ref(false);
const joining = ref(false);

onMounted(() => fetchTeams());

async function fetchTeams() {
    loading.value = true;
    try {
        const res = await axios.get("/api/teams");
        teams.value = res.data.data ?? [];
    } catch {}
    loading.value = false;
}

async function createTeam() {
    if (!form.value.name.trim()) return;
    creating.value = true;
    try {
        const res = await axios.post("/api/teams", form.value);
        teams.value.unshift(res.data.data);
        form.value = { name: "" };
        showCreate.value = false;
    } catch {}
    creating.value = false;
}

async function joinTeam() {
    if (!inviteCode.value.trim()) return;
    joining.value = true;
    try {
        await axios.post("/api/teams/join", { invite_code: inviteCode.value });
        inviteCode.value = "";
        showJoin.value = false;
        await fetchTeams();
    } catch {}
    joining.value = false;
}

async function remove(team) {
    try {
        await axios.delete(`/api/teams/${team.id}`);
        teams.value = teams.value.filter((t) => t.id !== team.id);
    } catch {}
}
</script>

<template>
    <div class="min-h-screen bg-[#0A0A0F]">
        <div class="max-w-4xl mx-auto px-4 py-6">
            <div class="flex items-center justify-between mb-6">
                <h1 class="text-2xl font-bold text-white">Teams</h1>
                <div class="flex items-center gap-2">
                    <button @click="showJoin = true"
                        class="px-3 py-1.5 text-sm text-violet-400 hover:text-violet-300 border border-violet-500/30 rounded-lg transition-colors">
                        Join Team
                    </button>
                    <button @click="showCreate = true"
                        class="px-4 py-1.5 text-sm bg-violet-600 hover:bg-violet-500 text-white rounded-lg font-medium transition-all">
                        + New Team
                    </button>
                </div>
            </div>

            <!-- Create Modal -->
            <div v-if="showCreate" class="fixed inset-0 bg-black/60 z-50 flex items-center justify-center" @click.self="showCreate = false">
                <div class="bg-zinc-900 border border-zinc-800 rounded-xl p-6 w-full max-w-md mx-4">
                    <h2 class="text-lg font-semibold text-white mb-4">Create Team</h2>
                    <input v-model="form.name" @keydown.enter="createTeam" placeholder="Team name..."
                        class="w-full bg-zinc-800 border border-zinc-700 rounded-lg px-4 py-2.5 text-sm text-white placeholder-zinc-600 focus:border-violet-500/50 focus:ring-0 outline-none mb-4" />
                    <div class="flex justify-end gap-2">
                        <button @click="showCreate = false" class="px-4 py-2 text-sm text-zinc-400 hover:text-zinc-300 transition-colors">Cancel</button>
                        <button @click="createTeam" :disabled="creating || !form.name.trim()"
                            class="px-4 py-2 text-sm bg-violet-600 hover:bg-violet-500 disabled:bg-zinc-800 disabled:text-zinc-700 text-white rounded-lg font-medium transition-all">Create</button>
                    </div>
                </div>
            </div>

            <!-- Join Modal -->
            <div v-if="showJoin" class="fixed inset-0 bg-black/60 z-50 flex items-center justify-center" @click.self="showJoin = false">
                <div class="bg-zinc-900 border border-zinc-800 rounded-xl p-6 w-full max-w-md mx-4">
                    <h2 class="text-lg font-semibold text-white mb-4">Join Team</h2>
                    <input v-model="inviteCode" @keydown.enter="joinTeam" placeholder="Enter invite code..."
                        class="w-full bg-zinc-800 border border-zinc-700 rounded-lg px-4 py-2.5 text-sm text-white placeholder-zinc-600 focus:border-violet-500/50 focus:ring-0 outline-none uppercase mb-4" />
                    <div class="flex justify-end gap-2">
                        <button @click="showJoin = false" class="px-4 py-2 text-sm text-zinc-400 hover:text-zinc-300 transition-colors">Cancel</button>
                        <button @click="joinTeam" :disabled="joining || !inviteCode.trim()"
                            class="px-4 py-2 text-sm bg-violet-600 hover:bg-violet-500 disabled:bg-zinc-800 disabled:text-zinc-700 text-white rounded-lg font-medium transition-all">Join</button>
                    </div>
                </div>
            </div>

            <div v-if="loading" class="space-y-2">
                <div v-for="n in 2" :key="n" class="bg-zinc-900 rounded-xl p-5 animate-pulse">
                    <div class="h-5 bg-zinc-800 rounded w-1/3 mb-3" />
                    <div class="h-3 bg-zinc-800/50 rounded w-1/4" />
                </div>
            </div>

            <div v-else class="grid gap-3">
                <div v-for="team in teams" :key="team.id"
                    @click="router.push(`/teams/${team.id}`)"
                    class="bg-zinc-900/50 border border-zinc-800 hover:border-zinc-700 rounded-xl p-5 cursor-pointer transition-all group">
                    <div class="flex items-start justify-between">
                        <div>
                            <h3 class="text-base font-semibold text-zinc-200 group-hover:text-white transition-colors">{{ team.name }}</h3>
                            <p class="text-sm text-zinc-600 mt-1">{{ team.members_count ?? 0 }} members</p>
                            <p v-if="team.invite_code" class="text-xs font-mono text-zinc-700 mt-1">Code: {{ team.invite_code }}</p>
                        </div>
                        <button @click.stop="remove(team)" class="p-1 text-zinc-700 hover:text-rose-400 opacity-0 group-hover:opacity-100 transition-all">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                </div>

                <div v-if="!teams.length" class="text-center py-20 text-zinc-700 text-sm">
                    No teams yet. Create one or join with an invite code.
                </div>
            </div>
        </div>
    </div>
</template>
