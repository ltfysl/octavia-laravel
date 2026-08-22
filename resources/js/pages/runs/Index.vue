<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
import AppLayout from '../../layouts/AppLayout.vue';
import OEmptyState from '../../components/ui/OEmptyState.vue';
import OBadge from '../../components/ui/OBadge.vue';
import OScoreBar from '../../components/ui/OScoreBar.vue';

defineProps<{
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
            created_at: string;
        }>;
        links: Array<{ url: string | null; label: string; active: boolean }>;
    };
}>();

const { t } = useI18n();

const tone: Record<string, 'mint' | 'amber' | 'rose' | 'neutral' | 'violet'> = {
    completed: 'mint',
    running: 'violet',
    pending: 'neutral',
    failed: 'rose',
    cancelled: 'neutral',
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
            <Link href="/runs/create" class="rounded-lg bg-violet-600 px-4 py-2 text-sm font-medium text-white transition-colors hover:bg-violet-700">+ {{ t('runs.new') }}</Link>
        </div>

        <OEmptyState v-if="runs.data.length === 0" class="mt-8" :title="t('runs.empty')">
            <template #action>
                <Link href="/runs/create" class="rounded-lg bg-violet-600 px-4 py-2 text-sm font-medium text-white hover:bg-violet-700">{{ t('runs.new') }}</Link>
            </template>
        </OEmptyState>

        <div v-else class="mt-8 overflow-hidden rounded-card border border-ink-100 bg-white shadow-panel">
            <table class="w-full text-left text-sm">
                <thead>
                    <tr class="border-b border-ink-100 text-xs uppercase tracking-wide text-ink-300">
                        <th class="px-5 py-3 font-medium">{{ t('runs.title') }}</th>
                        <th class="hidden px-5 py-3 font-medium md:table-cell">{{ t('runs.score') }}</th>
                        <th class="px-5 py-3 font-medium">{{ t('common.actions') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-ink-100">
                    <tr v-for="run in runs.data" :key="run.id" class="transition-colors hover:bg-paper-100/60">
                        <td class="px-5 py-3">
                            <Link :href="`/runs/${run.id}`" class="block">
                                <span class="font-medium text-ink-950 hover:text-violet-700">{{ run.name }}</span>
                                <span class="mt-0.5 flex items-center gap-2">
                                    <OBadge :tone="tone[run.status] ?? 'neutral'">{{ t(`runs.status.${run.status}`) }}</OBadge>
                                    <span class="text-xs text-ink-300">{{ run.mode === 'optimize' ? t('runs.mode.optimize') : t('runs.mode.evaluate') }}</span>
                                </span>
                            </Link>
                        </td>
                        <td class="hidden w-48 px-5 py-3 md:table-cell">
                            <OScoreBar v-if="run.score !== null" :score="run.score" :target="run.target" />
                            <span v-else class="text-xs text-ink-300">—</span>
                        </td>
                        <td class="px-5 py-3">
                            <Link :href="`/runs/${run.id}`" class="text-xs font-medium text-violet-600 hover:text-violet-700">{{ t('runs.steps') }} →</Link>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </AppLayout>
</template>
