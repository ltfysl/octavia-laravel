<script setup lang="ts">
// A quiet panel: the fundamental surface of the Octavia workspace.
defineProps<{
    title?: string;
    subtitle?: string;
}>();
</script>

<template>
    <section class="rounded-card border border-ink-100 bg-white shadow-panel">
        <header v-if="title || $slots.actions" class="flex items-start justify-between gap-4 px-5 pt-4 pb-1">
            <div>
                <h2 v-if="title" class="font-display text-base font-semibold text-ink-950">{{ title }}</h2>
                <p v-if="subtitle" class="mt-0.5 text-sm text-ink-500">{{ subtitle }}</p>
            </div>
            <div v-if="$slots.actions" class="shrink-0"><slot name="actions" /></div>
        </header>
        <div class="px-5 py-4"><slot /></div>
    </section>
</template>
