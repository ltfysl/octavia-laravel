<script setup lang="ts">
withDefaults(
    defineProps<{
        tone?: 'neutral' | 'accent' | 'mint' | 'amber' | 'rose';
    }>(),
    { tone: 'neutral' },
);
</script>

<template>
    <span
        class="inline-flex items-center gap-1 rounded-full px-2 py-0.5 text-xs font-medium"
        :class="{
            'bg-paper-200 text-ink-700': tone === 'neutral',
            'bg-accent-100 text-accent-700': tone === 'accent',
            'bg-mint-100 text-mint-600': tone === 'mint',
            'bg-amber-100 text-amber-450': tone === 'amber',
            'bg-rose-100 text-rose-450': tone === 'rose',
        }"
    >
        <slot />
    </span>
</template>
