<script setup lang="ts">
import { useAttrs } from 'vue';

withDefaults(
    defineProps<{
        modelValue: string | number | null;
        type?: string;
        error?: string | null;
    }>(),
    { type: 'text', error: null },
);

const emit = defineEmits<{ (e: 'update:modelValue', value: string): void }>();

const attrs = useAttrs();
</script>

<template>
    <input
        :type="type"
        :value="String(modelValue ?? '')"
        class="w-full rounded-lg border bg-white px-3 py-2 text-sm text-ink-900 placeholder:text-ink-300 transition-colors"
        :class="error ? 'border-rose-450 focus:border-rose-450' : 'border-ink-200 focus:border-violet-500'"
        v-bind="attrs"
        @input="emit('update:modelValue', ($event.target as HTMLInputElement).value)"
    />
    <p v-if="error" class="mt-1 text-xs text-rose-450">{{ error }}</p>
</template>
