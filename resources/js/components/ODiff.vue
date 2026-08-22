<script setup lang="ts">
import { computed } from 'vue';
import { diffStats, diffWords } from '../utils/diff';

const props = defineProps<{
    oldText: string;
    newText: string;
}>();

const parts = computed(() => diffWords(props.oldText, props.newText));
const stats = computed(() => diffStats(parts.value));
</script>

<template>
    <div>
        <div class="mb-2 flex items-center gap-3 text-xs">
            <span class="font-medium text-mint-600">+{{ stats.added }}</span>
            <span class="font-medium text-rose-450">−{{ stats.removed }}</span>
            <span v-if="stats.added === 0 && stats.removed === 0" class="text-ink-300">identical</span>
        </div>
        <div class="max-h-96 overflow-auto whitespace-pre-wrap rounded-lg bg-paper-100 p-3 font-mono text-xs leading-relaxed scroll-thin">
            <template v-for="(part, i) in parts" :key="i">
                <mark
                    v-if="part.type === 'add'"
                    class="rounded-sm bg-mint-100 text-mint-600"
                >{{ part.text }}</mark>
                <del
                    v-else-if="part.type === 'remove'"
                    class="rounded-sm bg-rose-100 text-rose-450"
                >{{ part.text }}</del>
                <template v-else>{{ part.text }}</template>
            </template>
        </div>
    </div>
</template>
