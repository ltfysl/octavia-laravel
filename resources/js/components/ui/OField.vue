<script setup lang="ts">
defineProps<{
    label: string;
    for?: string;
    hint?: string;
    required?: boolean;
}>();
</script>

<template>
    <div class="block">
        <label :for="for" class="mb-1.5 block text-sm font-medium text-ink-700">
            {{ label }}
            <span v-if="!required" class="ml-1 font-normal text-ink-300">({{ $t('common.optional') }})</span>
        </label>
        <slot />
        <p v-if="hint" class="mt-1 text-xs text-ink-500">{{ hint }}</p>
    </div>
</template>
