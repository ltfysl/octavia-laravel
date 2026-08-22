<script setup lang="ts">
defineProps<{
    title: string;
    description?: string;
}>();
</script>
<template>
    <div class="flex flex-col items-center justify-center rounded-card border border-dashed border-ink-200 bg-paper-100/60 px-8 py-14 text-center">
        <div class="mb-4 flex h-12 w-12 items-center justify-center rounded-md bg-accent-100 text-accent-600">
            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 5.5 19 18H5L12 5.5Z" />
                <circle cx="12" cy="13.5" r="1.25" fill="currentColor" stroke="none" />
            </svg>
        </div>
        <h3 class="font-display text-base font-semibold text-ink-950">{{ title }}</h3>
        <p v-if="description" class="mt-1 max-w-md text-sm text-ink-500">{{ description }}</p>
        <div v-if="$slots.action" class="mt-5">
            <slot name="action" />
        </div>
    </div>
</template>
