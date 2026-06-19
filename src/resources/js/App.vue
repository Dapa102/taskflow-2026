<script setup>
import { onMounted } from "vue";
import { useAuthStore } from "@/stores/authStore";

const auth = useAuthStore();

onMounted(() => {
    auth.checkToken();
});
</script>

<template>
    <router-view />
</template>
