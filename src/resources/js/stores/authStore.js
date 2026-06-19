import { defineStore } from "pinia";
import { ref, computed } from "vue";
import axios from "axios";
import router from "@/router";

const API = "/api";

export const useAuthStore = defineStore("auth", () => {
    const user = ref(null);
    const token = ref(null);
    const loading = ref(false);

    const isAuthenticated = computed(() => !!token.value);
    const isSuperAdmin = computed(() => user.value?.roles?.includes("super_admin"));
    const userName = computed(() => user.value?.name || "Guest");

    function setAxiosToken(t) {
        token.value = t;
        axios.defaults.headers.common["Authorization"] = `Bearer ${t}`;
    }

    function loadToken() {
        const t = localStorage.getItem("auth_token") || sessionStorage.getItem("auth_token");
        if (t) setAxiosToken(t);
        return t;
    }

    async function checkToken() {
        const t = loadToken();
        if (!t) {
            loading.value = false;
            return;
        }
        loading.value = true;
        try {
            const res = await axios.get(`${API}/user`);
            user.value = {
                ...res.data.data,
                roles: res.data.data.roles || [],
            };
        } catch {
            logout();
        } finally {
            loading.value = false;
        }
    }

    async function login(email, password, remember = false) {
        const res = await axios.post(`${API}/login`, { email, password });
        const data = res.data;
        const t = data.token;

        if (remember) {
            localStorage.setItem("auth_token", t);
        } else {
            sessionStorage.setItem("auth_token", t);
        }

        setAxiosToken(t);
        user.value = data.user;
        return data.user;
    }

    async function register(name, email, password, passwordConfirmation) {
        const res = await axios.post(`${API}/register`, {
            name,
            email,
            password,
            password_confirmation: passwordConfirmation,
        });
        const data = res.data;
        const t = data.token;

        localStorage.setItem("auth_token", t);
        setAxiosToken(t);
        user.value = data.user;
        return data.user;
    }

    async function logout() {
        try {
            await axios.post(`${API}/logout`);
        } catch {}
        localStorage.removeItem("auth_token");
        sessionStorage.removeItem("auth_token");
        delete axios.defaults.headers.common["Authorization"];
        user.value = null;
        token.value = null;
        router.push("/login");
    }

    return {
        user,
        token,
        loading,
        isAuthenticated,
        isSuperAdmin,
        userName,
        login,
        register,
        logout,
        checkToken,
        loadToken,
    };
});
