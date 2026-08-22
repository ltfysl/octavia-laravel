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
const targetPct = computed(() => Math.round(Math.min(1, Math.max(0, props.target)) * 100));
const tone = computed(() => {
    if (props.target > 0 && props.score >= props.target) return 'bg-mint-500';
    if (props.score >= 0.8) return 'bg-mint-500';
    if (props.score >= 0.5) return 'bg-amber-450';
    return 'bg-rose-450';
});
</script>

<template>
    <div class="flex items-center gap-2">
        <!-- Survey bar: 10% graticule ticks, fill, and a datum line at the target -->
        <div
            class="relative h-2 w-full overflow-hidden rounded-full border border-ink-100 bg-white"
            role="meter"
            :aria-valuenow="pct"
            aria-valuemin="0"
            aria-valuemax="100"
            :aria-valuetext="targetPct > 0 ? `${pct}% (target ${targetPct}%)` : `${pct}%`"
        >
            <div class="absolute inset-0" aria-hidden="true" :style="{ backgroundImage: `repeating-linear-gradient(to right, transparent 0, transparent calc(10% - 1px), var(--color-ink-100) calc(10% - 1px), var(--color-ink-100) 10%)` }" />
            <div class="h-full transition-all duration-500" :class="tone" :style="{ width: pct + '%' }" />
            <div v-if="targetPct > 0" class="absolute top-0 h-full w-0.5 bg-ink-700" :style="{ left: `calc(${targetPct}% - 1px)` }" aria-hidden="true" />
        </div>
        <span v-if="showValue" class="w-10 shrink-0 text-right font-mono text-xs tabular-nums text-ink-700">{{ pct }}%</span>
    </div>
</template>
