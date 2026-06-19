import { create } from "zustand";
import api from "../api/client";

const useAuthStore = create((set, get) => ({
    token: null,
    user: null,

    init: () => {
        const token = localStorage.getItem("token") || sessionStorage.getItem("token");
        const user = JSON.parse(localStorage.getItem("user") || sessionStorage.getItem("user") || "null");
        if (token) {
            set({ token, user });
        }
    },

    get isAuthenticated() {
        return !!get().token;
    },

    get userName() {
        return get().user?.name || "";
    },

    login: async (credentials, remember = false) => {
        const res = await api.post("/login", credentials);
        const { token, user } = res.data.data;
        const storage = remember ? localStorage : sessionStorage;
        storage.setItem("token", token);
        storage.setItem("user", JSON.stringify(user));
        set({ token, user });
    },

    register: async (data) => {
        const res = await api.post("/register", data);
        const { token, user } = res.data.data;
        localStorage.setItem("token", token);
        localStorage.setItem("user", JSON.stringify(user));
        set({ token, user });
    },

    logout: async () => {
        try { await api.post("/logout"); } catch {}
        localStorage.removeItem("token");
        localStorage.removeItem("user");
        sessionStorage.removeItem("token");
        sessionStorage.removeItem("user");
        set({ token: null, user: null });
    },
}));

export default useAuthStore;
