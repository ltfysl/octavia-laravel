<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
import AppLayout from '../../layouts/AppLayout.vue';
import OPanel from '../../components/ui/OPanel.vue';
import OEmptyState from '../../components/ui/OEmptyState.vue';
import OBadge from '../../components/ui/OBadge.vue';

const props = defineProps<{
    stats: { total: number; completed: number; avg_score: number };
    byBenchmark: Array<{ id: number; name: string; runs_count: number; avg_score: number | null; last_run: string | null }>;
    byPrompt: Array<{ id: number; name: string; runs_count: number; avg_score: number | null; last_run: string | null }>;
    recentRuns: Array<{
        id: number;
        name: string;
        status: string;
        mode: string;
        best_score: number | null;
        prompt: string | null;
        benchmark: string | null;
        created_at: string;
    }>;
}>();

const { t } = useI18n();

const tone: Record<string, 'mint' | 'amber' | 'rose' | 'neutral' | 'accent'> = {
    completed: 'mint',
    running: 'accent',
    pending: 'neutral',
    failed: 'rose',
    cancelled: 'neutral',
};

const pct = (n: number | null) => n === null ? '—' : `${Math.round(n)}%`;
</script>

<template>
    <AppLayout>
        <Head><title>{{ t('reports.title') }}</title></Head>

        <div>
            <h1 class="font-display text-2xl font-bold tracking-tight text-ink-950">{{ t('reports.title') }}</h1>
            <p class="mt-1 text-sm text-ink-500">{{ t('reports.subtitle') }}</p>
        </div>

        <div class="mt-6 grid gap-3 sm:grid-cols-3">
            <OPanel>
                <p class="eyebrow">{{ t('reports.totalRuns') }}</p>
                <p class="mt-1 font-display text-3xl font-semibold text-ink-950">{{ stats.total }}</p>
            </OPanel>
            <OPanel>
                <p class="eyebrow">{{ t('reports.completed') }}</p>
                <p class="mt-1 font-display text-3xl font-semibold text-ink-950">{{ stats.completed }}</p>
            </OPanel>
            <OPanel>
                <p class="eyebrow">{{ t('reports.avgScore') }}</p>
                <p class="mt-1 font-display text-3xl font-semibold text-ink-950">{{ pct(stats.avg_score) }}</p>
            </OPanel>
        </div>

        <div class="mt-8 grid gap-6 lg:grid-cols-2">
            <OPanel :title="t('reports.byBenchmark')">
                <OEmptyState v-if="byBenchmark.length === 0" :title="t('reports.noData')" />
                <table v-else class="w-full text-left text-sm">
                    <thead>
                        <tr class="border-b border-ink-100 text-xs uppercase tracking-wide text-ink-300">
                            <th class="py-2 font-medium">{{ t('reports.name') }}</th>
                            <th class="py-2 font-medium">{{ t('reports.runs') }}</th>
                            <th class="py-2 font-medium">{{ t('reports.score') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-ink-100">
                        <tr v-for="b in byBenchmark" :key="b.id">
                            <td class="py-2">
                                <Link :href="`/benchmarks/${b.id}`" class="font-medium text-ink-950 hover:text-accent-700">{{ b.name }}</Link>
                            </td>
                            <td class="py-2 font-mono text-xs">{{ b.runs_count }}</td>
                            <td class="py-2 font-mono text-xs">{{ pct(b.avg_score) }}</td>
                        </tr>
                    </tbody>
                </table>
            </OPanel>

            <OPanel :title="t('reports.byPrompt')">
                <OEmptyState v-if="byPrompt.length === 0" :title="t('reports.noData')" />
                <table v-else class="w-full text-left text-sm">
                    <thead>
                        <tr class="border-b border-ink-100 text-xs uppercase tracking-wide text-ink-300">
                            <th class="py-2 font-medium">{{ t('reports.name') }}</th>
                            <th class="py-2 font-medium">{{ t('reports.runs') }}</th>
                            <th class="py-2 font-medium">{{ t('reports.score') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-ink-100">
                        <tr v-for="p in byPrompt" :key="p.id">
                            <td class="py-2">
                                <Link :href="`/prompts/${p.id}`" class="font-medium text-ink-950 hover:text-accent-700">{{ p.name }}</Link>
                            </td>
                            <td class="py-2 font-mono text-xs">{{ p.runs_count }}</td>
                            <td class="py-2 font-mono text-xs">{{ pct(p.avg_score) }}</td>
                        </tr>
                    </tbody>
                </table>
            </OPanel>
        </div>

        <OPanel class="mt-8" :title="t('reports.recentRuns')">
            <OEmptyState v-if="recentRuns.length === 0" :title="t('reports.noData')" />
            <table v-else class="w-full text-left text-sm">
                <thead>
                    <tr class="border-b border-ink-100 text-xs uppercase tracking-wide text-ink-300">
                        <th class="py-2 font-medium">{{ t('reports.name') }}</th>
                        <th class="py-2 font-medium">{{ t('runs.score') }}</th>
                        <th class="py-2 font-medium">{{ t('common.status') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-ink-100">
                    <tr v-for="run in recentRuns" :key="run.id" class="hover:bg-paper-100/60">
                        <td class="py-2">
                            <Link :href="`/runs/${run.id}`" class="font-medium text-ink-950 hover:text-accent-700">{{ run.name }}</Link>
                            <p v-if="run.prompt && run.benchmark" class="text-xs text-ink-400">{{ run.prompt }} × {{ run.benchmark }}</p>
                        </td>
                        <td class="py-2 font-mono text-xs">{{ pct(run.best_score) }}</td>
                        <td class="py-2">
                            <OBadge :tone="tone[run.status] ?? 'neutral'">{{ t(`runs.status.${run.status}`) }}</OBadge>
                        </td>
                    </tr>
                </tbody>
            </table>
        </OPanel>
    </AppLayout>
</template>
