<script setup>
import { ref } from "vue";
import { useRouter } from "vue-router";
import { useAuthStore } from "@/stores/authStore";
import { EyeIcon, EyeSlashIcon } from "@heroicons/vue/24/outline";

const router = useRouter();
const auth = useAuthStore();

const email = ref("");
const password = ref("");
const remember = ref(false);
const showPassword = ref(false);
const loading = ref(false);
const error = ref("");

async function handleLogin() {
    error.value = "";
    loading.value = true;
    try {
        const user = await auth.login(email.value, password.value, remember.value);
        const roles = user.roles || [];
        if (roles.includes("super_admin")) {
            window.location.href = "/admin";
        } else {
            router.push("/dashboard");
        }
    } catch (err) {
        error.value = err.response?.data?.message || "Invalid credentials";
    } finally {
        loading.value = false;
    }
}
</script>

<template>
    <div class="min-h-screen flex overflow-hidden bg-[#0A0A0F]">
        <!-- Left: Abstract geometric art -->
        <div class="hidden lg:flex lg:w-1/2 relative items-center justify-center overflow-hidden">
            <div class="absolute inset-0 bg-gradient-to-br from-violet-900/40 via-slate-900/60 to-fuchsia-900/40" />
            <div class="absolute -top-40 -left-40 w-96 h-96 bg-violet-500/20 rounded-full blur-3xl animate-pulse-slow" />
            <div class="absolute -bottom-40 -right-40 w-96 h-96 bg-pink-500/20 rounded-full blur-3xl animate-pulse-slow" style="animation-delay: 2s" />
            <div class="absolute top-1/4 right-1/4 w-64 h-64 bg-cyan-500/10 rounded-full blur-3xl animate-float" />

            <!-- Geometric shapes -->
            <div class="relative z-10 grid grid-cols-3 gap-6 rotate-12 opacity-30">
                <div v-for="n in 9" :key="n"
                    class="w-20 h-20 rounded-2xl border border-white/10 backdrop-blur-sm"
                    :class="[
                        n % 2 === 0 ? 'bg-violet-500/10' : 'bg-pink-500/10',
                        n % 3 === 0 ? 'rotate-12' : n % 3 === 1 ? '-rotate-6' : '',
                    ]"
                />
            </div>

            <div class="absolute bottom-16 left-16 z-10">
                <h1 class="text-5xl font-bold gradient-text">TaskFlow</h1>
                <p class="text-zinc-400 mt-2 text-lg">Your productivity, reimagined.</p>
            </div>
        </div>

        <!-- Right: Form -->
        <div class="w-full lg:w-1/2 flex items-center justify-center p-6">
            <div class="w-full max-w-sm space-y-8">
                <!-- Mobile logo -->
                <div class="lg:hidden text-center">
                    <h1 class="text-3xl font-bold gradient-text">TaskFlow</h1>
                    <p class="text-zinc-500 mt-1 text-sm">Sign in to your account</p>
                </div>

                <div v-if="error" class="glass rounded-xl px-4 py-3 text-sm text-rose-400 border-rose-500/30 animate-fade-in">
                    {{ error }}
                </div>

                <form @submit.prevent="handleLogin" class="space-y-6">
                    <div class="space-y-1.5">
                        <label class="text-sm font-medium text-zinc-400">Email</label>
                        <input
                            v-model="email"
                            type="email"
                            placeholder="you@example.com"
                            required
                            autocomplete="email"
                            class="w-full bg-transparent border-0 border-b-2 border-zinc-700 pb-2 px-1 text-zinc-100 placeholder-zinc-600 focus:border-violet-500 focus:ring-0 transition-colors duration-300 text-lg"
                        />
                    </div>

                    <div class="space-y-1.5">
                        <label class="text-sm font-medium text-zinc-400">Password</label>
                        <div class="relative">
                            <input
                                v-model="password"
                                :type="showPassword ? 'text' : 'password'"
                                placeholder="••••••••"
                                required
                                autocomplete="current-password"
                                class="w-full bg-transparent border-0 border-b-2 border-zinc-700 pb-2 px-1 text-zinc-100 placeholder-zinc-600 focus:border-violet-500 focus:ring-0 transition-colors duration-300 text-lg pr-10"
                            />
                            <button type="button" @click="showPassword = !showPassword" class="absolute right-1 top-1/2 -translate-y-1/2 text-zinc-500 hover:text-zinc-300">
                                <EyeIcon v-if="!showPassword" class="w-5 h-5" />
                                <EyeSlashIcon v-else class="w-5 h-5" />
                            </button>
                        </div>
                    </div>

                    <div class="flex items-center justify-between">
                        <label class="flex items-center gap-2 cursor-pointer group">
                            <input
                                v-model="remember"
                                type="checkbox"
                                class="w-4 h-4 rounded border-zinc-600 bg-zinc-800 text-violet-500 focus:ring-violet-500/30"
                            />
                            <span class="text-sm text-zinc-500 group-hover:text-zinc-300 transition-colors">Remember me</span>
                        </label>
                        <button type="button" class="text-sm text-zinc-600 hover:text-violet-400 transition-colors">
                            Forgot?
                        </button>
                    </div>

                    <button
                        type="submit"
                        :disabled="loading"
                        class="gradient-btn w-full py-3.5 px-6 text-base disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center gap-2"
                        :class="{ 'scale-[1.02]': !loading }"
                    >
                        <svg v-if="loading" class="w-5 h-5 animate-spin" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" />
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z" />
                        </svg>
                        <span v-else>Sign In</span>
                    </button>
                </form>

                <p class="text-center text-sm text-zinc-600">
                    Don&apos;t have an account?
                    <router-link to="/register" class="text-violet-400 hover:text-violet-300 font-medium transition-colors">
                        Sign Up
                    </router-link>
                </p>
            </div>
        </div>
    </div>
</template>
