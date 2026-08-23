<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
import { ref, computed } from 'vue';
import AppLayout from '../../layouts/AppLayout.vue';
import OEmptyState from '../../components/ui/OEmptyState.vue';
import OBadge from '../../components/ui/OBadge.vue';
import OScoreBar from '../../components/ui/OScoreBar.vue';


const { t } = useI18n();

const props = defineProps<{
    runs: {
        data: Array<{
            id: number;
            name: string;
            status: string;
            mode: string;
            score: number | null;
            target: number;
            prompt?: { id: number; name: string } | null;
            benchmark?: { id: number; name: string } | null;
            owner?: { id: number; name: string } | null;
            created_at: string;
        }>;
        links: Array<{ url: string | null; label: string; active: boolean }>;
    };
}>();

const tone: Record<string, 'mint' | 'amber' | 'rose' | 'neutral' | 'accent'> = {
    completed: 'mint',
    running: 'accent',
    pending: 'neutral',
    failed: 'rose',
    cancelled: 'neutral',
};

const statusFilter = ref<'all' | 'pending' | 'running' | 'completed' | 'failed' | 'cancelled'>('all');
const sortDir = ref<'asc' | 'desc'>('desc');

const statusCounts = computed(() => {
    const counts: Record<string, number> = {};
    for (const run of props.runs.data) counts[run.status] = (counts[run.status] ?? 0) + 1;
    return counts;
});

const visibleRuns = computed(() => {
    let data = props.runs.data;
    if (statusFilter.value !== 'all') data = data.filter((r) => r.status === statusFilter.value);
    return [...data].sort((a, b) => {
        const av = a.score ?? -1;
        const bv = b.score ?? -1;
        return sortDir.value === 'asc' ? av - bv : bv - av;
    });
});

const toggleSort = () => {
    sortDir.value = sortDir.value === 'desc' ? 'asc' : 'desc';
};
</script>

<template>
    <AppLayout>
        <Head><title>{{ t('runs.title') }}</title></Head>

        <div class="flex items-start justify-between gap-4">
            <div>
                <h1 class="font-display text-2xl font-bold tracking-tight text-ink-950">{{ t('runs.title') }}</h1>
                <p class="mt-1 text-sm text-ink-500">{{ t('runs.subtitle') }}</p>
            </div>
            <Link href="/runs/create" class="rounded-lg bg-ink-950 px-4 py-2 text-sm font-medium text-white transition-colors hover:bg-ink-700">+ {{ t('runs.new') }}</Link>
        </div>

        <div class="mt-6 flex flex-wrap items-center gap-2">
            <button
                v-for="option in ['all', 'pending', 'running', 'completed', 'failed', 'cancelled'] as const"
                :key="option"
                type="button"
                class="rounded-full px-3 py-1 text-xs font-medium transition-colors"
                :class="statusFilter === option
                    ? 'bg-ink-950 text-white'
                    : 'border border-ink-100 bg-card text-ink-500 hover:bg-paper-100'"
                @click="statusFilter = option"
            >
                {{ option === 'all' ? t('runs.filterAll') : t(`runs.status.${option}`) }}
                <span v-if="option !== 'all'" class="ml-1 opacity-60">{{ statusCounts[option] ?? 0 }}</span>
            </button>
        </div>

        <OEmptyState v-if="runs.data.length === 0" class="mt-8" :title="t('runs.empty')">
            <template #action>
                <Link href="/runs/create" class="rounded-lg bg-ink-950 px-4 py-2 text-sm font-medium text-white hover:bg-ink-700">{{ t('runs.new') }}</Link>
            </template>
        </OEmptyState>

        <div v-else class="mt-8 overflow-hidden rounded-card border border-ink-100 bg-card shadow-panel">
            <table class="w-full text-left text-sm">
                <thead>
                    <tr class="border-b border-ink-100 text-xs uppercase tracking-wide text-ink-300">
                        <th class="px-5 py-3 font-medium">{{ t('runs.title') }}</th>
                        <th class="hidden px-5 py-3 font-medium md:table-cell">
                            <button type="button" class="inline-flex items-center gap-1 uppercase tracking-wide transition-colors hover:text-ink-950" :title="t('runs.sortByScore')" @click="toggleSort">
                                {{ t('runs.score') }}
                                <span class="text-[10px]">{{ sortDir === 'desc' ? '↓' : '↑' }}</span>
                            </button>
                        </th>
                        <th class="px-5 py-3 font-medium">{{ t('common.actions') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-ink-100">
                    <tr v-for="run in visibleRuns" :key="run.id" class="transition-colors hover:bg-paper-100/60">
                        <td class="px-5 py-3">
                            <Link :href="`/runs/${run.id}`" class="block">
                                <span class="font-medium text-ink-950 hover:text-accent-700">{{ run.name }}</span>
                                <span class="mt-0.5 flex items-center gap-2">
                                    <OBadge :tone="tone[run.status] ?? 'neutral'">{{ t(`runs.status.${run.status}`) }}</OBadge>
                                    <span v-if="run.owner" class="text-xs text-ink-300">· {{ run.owner.name }}</span>
                                    <span class="text-xs text-ink-300">{{ run.mode === 'optimize' ? t('runs.mode.optimize') : t('runs.mode.evaluate') }}</span>
                                </span>
                            </Link>
                        </td>
                        <td class="hidden w-48 px-5 py-3 md:table-cell">
                            <OScoreBar v-if="run.score !== null" :score="run.score" :target="run.target" />
                            <span v-else class="text-xs text-ink-300">—</span>
                        </td>
                        <td class="px-5 py-3">
                            <Link :href="`/runs/${run.id}`" class="text-xs font-medium text-accent-600 hover:text-accent-700">{{ t('runs.steps') }} →</Link>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </AppLayout>
</template>
