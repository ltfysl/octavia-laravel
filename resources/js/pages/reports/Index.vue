<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import { useI18n } from 'vue-i18n';
import AppLayout from '../../layouts/AppLayout.vue';
import OPanel from '../../components/ui/OPanel.vue';
import OEmptyState from '../../components/ui/OEmptyState.vue';
import OBadge from '../../components/ui/OBadge.vue';
import OButton from '../../components/ui/OButton.vue';

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

const recommendation = ref<string | null>(null);
const recommendationLoading = ref(false);
const recommendationError = ref('');

const worstPrompt = computed(() => {
    const scored = props.byPrompt.filter((p) => p.avg_score !== null).sort((a, b) => (a.avg_score ?? 0) - (b.avg_score ?? 0));
    return scored[0] ?? null;
});

const fetchRecommendation = async () => {
    recommendationLoading.value = true;
    recommendationError.value = '';
    recommendation.value = null;

    const summary = `Stats: ${props.stats.total} runs, average score ${props.stats.avg_score}%.` +
        (worstPrompt.value ? ` Lowest prompt: "${worstPrompt.value.name}" with ${worstPrompt.value.avg_score}% over ${worstPrompt.value.runs_count} runs.` : '') +
        ` Recent runs: ${props.recentRuns.map((r) => `${r.name} (${r.status}, ${Math.round((r.best_score ?? 0) * 100)}%)`).join(', ')}.`;

    try {
        const res = await fetch('/assistant/chat', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                Accept: 'application/json',
                'X-CSRF-TOKEN': (document.querySelector('meta[name=csrf-token]') as HTMLMetaElement | null)?.content ?? '',
            },
            credentials: 'include',
            body: JSON.stringify({
                messages: [
                    { role: 'user', content: `Given this report, give 1-2 concrete next actions to improve prompt performance. Report: ${summary}` },
                ],
            }),
        });
        if (! res.ok) throw new Error();
        const data: { reply?: string } = await res.json();
        recommendation.value = data.reply ?? '';
    } catch {
        recommendationError.value = t('common.error');
    } finally {
        recommendationLoading.value = false;
    }
};
</script>

<template>
    <AppLayout>
        <Head><title>{{ t('reports.title') }}</title></Head>

        <div>
            <h1 class="font-display text-2xl font-bold tracking-tight text-ink-950">{{ t('reports.title') }}</h1>
            <p class="mt-1 text-sm text-ink-500">{{ t('reports.subtitle') }}</p>
            <OButton variant="secondary" size="sm" class="mt-3" @click="fetchRecommendation" :disabled="recommendationLoading">
                {{ recommendationLoading ? t('common.loading') : t('reports.recommendationButton') }}
            </OButton>
            <div v-if="recommendationLoading" class="mt-3 space-y-2">
                <div class="h-3 w-full rounded bg-ink-100 shimmer" />
                <div class="h-3 w-5/6 rounded bg-ink-100 shimmer" />
            </div>
            <pre v-else-if="recommendation" class="scroll-thin mt-3 max-h-64 overflow-auto whitespace-pre-wrap rounded-xl border border-emerald-200 bg-emerald-50/40 p-4 font-mono text-xs leading-relaxed text-emerald-900 dark:bg-emerald-950/20">{{ recommendation }}</pre>
            <p v-else-if="recommendationError" class="mt-2 rounded-lg bg-rose-50 px-3 py-2 text-xs text-rose-600">{{ recommendationError }}</p>
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
