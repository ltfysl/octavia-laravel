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

const statusTone: Record<string, 'mint' | 'amber' | 'rose' | 'neutral' | 'accent'> = {
    completed: 'mint',
    running: 'accent',
    pending: 'neutral',
    failed: 'rose',
    cancelled: 'neutral',
};
</script>

<template>
    <AppLayout>
        <Head><title>{{ t('dashboard.title') }}</title></Head>

        <!-- Basecamp: the fitness landscape and today's elevation -->
        <section class="relative overflow-hidden rounded-card border border-ink-100 bg-white">
            <svg
                class="pointer-events-none absolute inset-y-0 right-0 hidden h-full w-3/5 sm:block"
                viewBox="0 0 640 240"
                preserveAspectRatio="xMaxYMid slice"
                fill="none"
                aria-hidden="true"
            >
                <path d="M-20 208 C120 188 190 216 310 196 S530 158 680 176" stroke="currentColor" class="text-ink-200" />
                <path d="M-20 166 C130 146 230 180 350 160 S550 122 680 136" stroke="currentColor" class="text-ink-200" opacity=".7" />
                <path d="M60 116 C190 98 290 126 410 108 S600 76 700 90" stroke="currentColor" class="text-ink-200" opacity=".45" />
                <path d="M150 64 C260 50 340 74 440 60 S610 36 700 46" stroke="currentColor" class="text-ink-200" opacity=".25" />
                <path d="M-20 232 C140 214 250 240 400 224 S640 192 740 206" stroke="currentColor" class="text-accent-400" stroke-dasharray="4 5" />
                <path d="M596 170 l9 -16 9 16 z" class="fill-accent-600" />
            </svg>
            <div class="relative flex flex-wrap items-end justify-between gap-x-8 gap-y-4 p-6 sm:p-8">
                <div>
                    <p class="eyebrow">Octavia</p>
                    <h1 class="mt-2 font-display text-2xl font-bold tracking-tight text-ink-950 sm:text-3xl">{{ t('dashboard.title') }}</h1>
                    <p class="mt-1 text-sm text-ink-500">{{ t('dashboard.subtitle') }}</p>
                </div>
                <div class="text-left sm:text-right">
                    <p class="eyebrow">{{ t('dashboard.avgScore') }}</p>
                    <p class="mt-1 font-display text-5xl font-bold leading-none tabular-nums tracking-tight text-ink-950">
                        {{ Math.round(stats.bestScore * 100) }}<span class="text-accent-600">%</span>
                    </p>
                </div>
            </div>
        </section>

        <!-- Survey strip -->
        <div class="grid grid-cols-3 divide-x divide-ink-100 overflow-hidden rounded-card border border-t-0 border-ink-100 bg-white max-sm:grid-cols-1 max-sm:divide-y">
            <div v-for="stat in [
                { label: t('dashboard.totalPrompts'), value: stats.prompts },
                { label: t('dashboard.totalBenchmarks'), value: stats.benchmarks },
                { label: t('dashboard.activeRuns'), value: stats.activeRuns },
            ]" :key="stat.label" class="px-5 py-4">
                <p class="eyebrow">{{ stat.label }}</p>
                <p class="mt-1 font-display text-xl font-semibold tabular-nums text-ink-950">{{ stat.value }}</p>
            </div>
        </div>

        <!-- Quick actions -->
        <section class="mt-8">
            <h2 class="eyebrow mb-3">{{ t('dashboard.quickActions') }}</h2>
            <div class="flex flex-wrap gap-2">
                <Link href="/runs/create" class="inline-flex items-center gap-1.5 rounded-md bg-accent-600 px-3.5 py-2 text-sm font-semibold text-ink-950 transition-colors hover:bg-accent-500">
                    ▶ {{ t('dashboard.startRun') }}
                </Link>
                <Link href="/prompts/create" class="inline-flex items-center gap-1.5 rounded-md border border-ink-200 bg-white px-3.5 py-2 text-sm font-medium text-ink-900 transition-colors hover:border-ink-500">
                    + {{ t('dashboard.createPrompt') }}
                </Link>
                <Link href="/benchmarks/wizard" class="inline-flex items-center gap-1.5 rounded-md border border-ink-200 bg-white px-3.5 py-2 text-sm font-medium text-ink-900 transition-colors hover:border-ink-500">
                    + {{ t('dashboard.createBenchmark') }}
                </Link>
                <Link href="/marketplace" class="inline-flex items-center gap-1.5 rounded-md border border-ink-200 bg-white px-3.5 py-2 text-sm font-medium text-ink-900 transition-colors hover:border-ink-500">
                    {{ t('dashboard.browseMarketplace') }}
                </Link>
            </div>
        </section>

        <!-- Recent runs -->
        <section class="mt-8">
            <h2 class="eyebrow mb-3">{{ t('dashboard.recentRuns') }}</h2>

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
                    <Link href="/runs/create" class="rounded-md bg-ink-950 px-4 py-2 text-sm font-medium text-white transition-colors hover:bg-ink-700">{{ t('runs.new') }}</Link>
                </template>
            </OEmptyState>
        </section>
    </AppLayout>
</template>
