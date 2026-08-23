<script setup lang="ts">
withDefaults(
    defineProps<{
        variant?: 'primary' | 'secondary' | 'ghost' | 'danger';
        size?: 'sm' | 'md' | 'lg';
        type?: 'button' | 'submit';
        disabled?: boolean;
    }>(),
    { variant: 'primary', size: 'md', type: 'button', disabled: false },
);
</script>

<template>
    <button
        :type="type"
        :disabled="disabled"
        class="inline-flex items-center justify-center gap-2 font-medium transition-colors duration-150 rounded-lg disabled:opacity-50 disabled:pointer-events-none"
        :class="{
            // Ink key with white label (7:1); signal orange stays identity, not button fill.
            'bg-ink-950 text-white hover:bg-ink-700 active:bg-ink-900': variant === 'primary',
            'bg-card text-ink-900 border border-ink-200 hover:border-ink-500': variant === 'secondary',
            'text-ink-500 hover:text-ink-900 hover:bg-paper-200': variant === 'ghost',
            'border border-rose-450/60 bg-rose-450/10 text-ink-900 hover:bg-rose-450/20': variant === 'danger',
            'px-3 py-1.5 text-sm': size === 'sm',
            'px-4 py-2 text-sm': size === 'md',
            'px-5 py-2.5 text-base': size === 'lg',
        }"
    >
        <slot />
    </button>
</template>
