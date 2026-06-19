import { createRouter, createWebHistory } from "vue-router";
import { useAuthStore } from "@/stores/authStore";

const routes = [
    {
        path: "/",
        redirect: "/dashboard",
    },
    {
        path: "/login",
        name: "Login",
        component: () => import("@/views/Login.vue"),
        meta: { guest: true },
    },
    {
        path: "/register",
        name: "Register",
        component: () => import("@/views/Register.vue"),
        meta: { guest: true },
    },
    {
        path: "/dashboard",
        name: "Dashboard",
        component: () => import("@/views/Dashboard.vue"),
        meta: { auth: true },
    },
];

const router = createRouter({
    history: createWebHistory(),
    routes,
});

router.beforeEach((to, from, next) => {
    const auth = useAuthStore();

    if (to.meta.auth && !auth.isAuthenticated) {
        next("/login");
    } else if (to.meta.guest && auth.isAuthenticated) {
        next("/dashboard");
    } else {
        next();
    }
});

export default router;
