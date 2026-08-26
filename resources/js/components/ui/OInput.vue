<script setup lang="ts">
import { computed, useAttrs, useId } from 'vue';

const props = withDefaults(
    defineProps<{
        modelValue: string | number | null;
        type?: string;
        error?: string | null;
    }>(),
    { type: 'text', error: null },
);

const emit = defineEmits<{ (e: 'update:modelValue', value: string): void }>();

const attrs = useAttrs();
const autoId = useId();
const inputId = computed(() => (attrs.id as string | undefined) ?? `oinput-${autoId}`);
const errorId = computed(() => `${inputId.value}-error`);
</script>

<template>
    <input
        :id="inputId"
        :type="type"
        :value="String(modelValue ?? '')"
        class="w-full rounded-lg border bg-card px-3 py-2 text-sm text-ink-900 placeholder:text-ink-300 transition-colors"
        :class="error ? 'border-rose-450 focus:border-rose-450' : 'border-ink-200 focus:border-accent-500'"
        :aria-invalid="error ? true : undefined"
        :aria-describedby="error ? errorId : undefined"
        v-bind="attrs"
        @input="emit('update:modelValue', ($event.target as HTMLInputElement).value)"
    />
    <p v-if="error" :id="errorId" class="mt-1 text-xs text-rose-450" role="alert">
        {{ error }}
    </p>
</template>
