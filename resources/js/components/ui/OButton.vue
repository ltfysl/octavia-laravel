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
            'bg-violet-600 text-white hover:bg-violet-700 active:bg-violet-900': variant === 'primary',
            'bg-white text-ink-900 border border-ink-200 hover:border-ink-300 hover:bg-paper-100': variant === 'secondary',
            'text-ink-500 hover:text-ink-900 hover:bg-paper-200': variant === 'ghost',
            'bg-rose-450 text-white hover:brightness-95': variant === 'danger',
            'px-3 py-1.5 text-sm': size === 'sm',
            'px-4 py-2 text-sm': size === 'md',
            'px-5 py-2.5 text-base': size === 'lg',
        }"
    >
        <slot />
    </button>
</template>
