<script setup>
defineProps({
    level: { type: String, default: "medium" },
});
</script>

<template>
    <span
        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
        :class="{
            'bg-rose-500/20 text-rose-300 ring-1 ring-rose-500/30': level === 'high',
            'bg-amber-500/20 text-amber-300 ring-1 ring-amber-500/30': level === 'medium',
            'bg-zinc-500/20 text-zinc-400 ring-1 ring-zinc-500/30': level === 'low',
        }"
    >
        {{ level }}
    </span>
</template>
