<script setup lang="ts">
defineProps<{
    title: string;
    description?: string;
}>();
</script>
<template>
    <div class="relative flex flex-col items-center justify-center overflow-hidden rounded-card border border-ink-200 bg-card bg-field-grid px-8 py-16 text-center">
        <div class="rule-accent absolute left-0 top-0 w-full" aria-hidden="true" />
        <div class="mb-5 flex h-14 w-14 items-center justify-center rounded-xl border border-ink-100 bg-card shadow-panel text-accent-600">
            <svg class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 5 19 18H5L12 5Z" />
                <circle cx="12" cy="13" r="1.5" fill="currentColor" stroke="none" />
            </svg>
        </div>
        <p class="eyebrow">No data — field empty</p>
        <h3 class="display-hero mt-2 text-xl tracking-tight text-ink-950">{{ title }}</h3>
        <p v-if="description" class="mt-2 max-w-md text-sm leading-relaxed text-ink-500">{{ description }}</p>
        <div v-if="$slots.action" class="mt-6">
            <slot name="action" />
        </div>
    </div>
</template>
