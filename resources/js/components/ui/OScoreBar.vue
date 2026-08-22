<script setup lang="ts">
import { computed } from 'vue';

// Horizontal score meter, 0..1. The single most repeated data visual in
// Octavia — kept deliberately minimal.
const props = withDefaults(
    defineProps<{
        score: number;
        target?: number;
        showValue?: boolean;
    }>(),
    { target: 0, showValue: true },
);

const pct = computed(() => Math.round(Math.min(1, Math.max(0, props.score)) * 100));

const tone = computed(() => {
    if (props.target > 0 && props.score >= props.target) return 'bg-mint-500';
    if (props.score >= 0.8) return 'bg-mint-500';
    if (props.score >= 0.5) return 'bg-amber-450';
    return 'bg-rose-450';
});
</script>

<template>
    <div class="flex items-center gap-2">
        <div class="h-1.5 w-full overflow-hidden rounded-full bg-ink-100" role="meter" :aria-valuenow="pct" aria-valuemin="0" aria-valuemax="100">
            <div class="h-full rounded-full transition-all duration-500" :class="tone" :style="{ width: pct + '%' }" />
        </div>
        <span v-if="showValue" class="w-10 shrink-0 text-right font-mono text-xs tabular-nums text-ink-700">{{ pct }}%</span>
    </div>
</template>
