<script setup lang="ts">
withDefaults(
    defineProps<{
        tone?: 'neutral' | 'violet' | 'mint' | 'amber' | 'rose';
    }>(),
    { tone: 'neutral' },
);
</script>

<template>
    <span
        class="inline-flex items-center gap-1 rounded-full px-2 py-0.5 text-xs font-medium"
        :class="{
            'bg-paper-200 text-ink-700': tone === 'neutral',
            'bg-violet-100 text-violet-700': tone === 'violet',
            'bg-mint-100 text-mint-600': tone === 'mint',
            'bg-amber-100 text-amber-450': tone === 'amber',
            'bg-rose-100 text-rose-450': tone === 'rose',
        }"
    >
        <slot />
    </span>
</template>
