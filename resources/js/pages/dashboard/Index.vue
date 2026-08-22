<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
import AppLayout from '../../layouts/AppLayout.vue';
import OPanel from '../../components/ui/OPanel.vue';
import OEmptyState from '../../components/ui/OEmptyState.vue';
import OBadge from '../../components/ui/OBadge.vue';
import OScoreBar from '../../components/ui/OScoreBar.vue';

defineProps<{
    stats: { prompts: number; benchmarks: number; activeRuns: number; bestScore: number };
    recentRuns: Array<{
        id: number;
        name: string;
        status: string;
        mode: string;
        score: number | null;
        prompt?: { id: number; name: string } | null;
        benchmark?: { id: number; name: string } | null;
        created_at: string;
    }>;
}>();

const { t } = useI18n();

const statusTone: Record<string, 'mint' | 'amber' | 'rose' | 'neutral' | 'violet'> = {
    completed: 'mint',
    running: 'violet',
    pending: 'neutral',
    failed: 'rose',
    cancelled: 'neutral',
};
</script>

<template>
    <AppLayout>
        <Head><title>{{ t('dashboard.title') }}</title></Head>

        <h1 class="font-display text-2xl font-bold tracking-tight text-ink-950">{{ t('dashboard.title') }}</h1>
        <p class="mt-1 text-sm text-ink-500">{{ t('dashboard.subtitle') }}</p>

        <!-- Stats -->
        <div class="mt-6 grid grid-cols-2 gap-4 lg:grid-cols-4">
            <OPanel v-for="stat in [
                { label: t('dashboard.totalPrompts'), value: stats.prompts },
                { label: t('dashboard.totalBenchmarks'), value: stats.benchmarks },
                { label: t('dashboard.activeRuns'), value: stats.activeRuns },
                { label: t('dashboard.avgScore'), value: Math.round(stats.bestScore * 100) + '%' },
            ]" :key="stat.label" class="!p-0">
                <p class="text-xs font-medium uppercase tracking-wide text-ink-300">{{ stat.label }}</p>
                <p class="mt-2 font-display text-3xl font-bold tabular-nums text-ink-950">{{ stat.value }}</p>
            </OPanel>
        </div>

        <!-- Quick actions -->
        <section class="mt-8">
            <h2 class="mb-3 text-sm font-semibold uppercase tracking-wide text-ink-300">{{ t('dashboard.quickActions') }}</h2>
            <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-4">
                <Link href="/prompts/create" class="group rounded-card border border-ink-100 bg-white p-4 shadow-panel transition-colors hover:border-violet-200">
                    <span class="font-display text-sm font-semibold text-ink-950 group-hover:text-violet-700">+ {{ t('dashboard.createPrompt') }}</span>
                </Link>
                <Link href="/benchmarks/wizard" class="group rounded-card border border-ink-100 bg-white p-4 shadow-panel transition-colors hover:border-violet-200">
                    <span class="font-display text-sm font-semibold text-ink-950 group-hover:text-violet-700">+ {{ t('dashboard.createBenchmark') }}</span>
                </Link>
                <Link href="/runs/create" class="group rounded-card border border-ink-100 bg-white p-4 shadow-panel transition-colors hover:border-violet-200">
                    <span class="font-display text-sm font-semibold text-ink-950 group-hover:text-violet-700">▶ {{ t('dashboard.startRun') }}</span>
                </Link>
                <Link href="/marketplace" class="group rounded-card border border-ink-100 bg-white p-4 shadow-panel transition-colors hover:border-violet-200">
                    <span class="font-display text-sm font-semibold text-ink-950 group-hover:text-violet-700">{{ t('dashboard.browseMarketplace') }}</span>
                </Link>
            </div>
        </section>

        <!-- Recent runs -->
        <section class="mt-8">
            <h2 class="mb-3 text-sm font-semibold uppercase tracking-wide text-ink-300">{{ t('dashboard.recentRuns') }}</h2>
            <OPanel v-if="recentRuns.length > 0" :title="undefined">
                <ul class="divide-y divide-ink-100">
                    <li v-for="run in recentRuns" :key="run.id">
                        <Link :href="`/runs/${run.id}`" class="-mx-2 flex items-center gap-4 rounded-lg px-2 py-3 transition-colors hover:bg-paper-100">
                            <OBadge :tone="statusTone[run.status] ?? 'neutral'">{{ t(`runs.status.${run.status}`) }}</OBadge>
                            <span class="min-w-0 flex-1 truncate text-sm font-medium text-ink-900">{{ run.name }}</span>
                            <OScoreBar v-if="run.score !== null" :score="run.score" :show-value="true" class="hidden w-40 sm:flex" />
                            <svg class="h-4 w-4 shrink-0 text-ink-300" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" /></svg>
                        </Link>
                    </li>
                </ul>
            </OPanel>
            <OEmptyState
                v-else
                :title="t('runs.empty')"
            >
                <template #action>
                    <Link href="/runs/create" class="rounded-lg bg-violet-600 px-4 py-2 text-sm font-medium text-white hover:bg-violet-700">{{ t('runs.new') }}</Link>
                </template>
            </OEmptyState>
        </section>
    </AppLayout>
</template>
