<script setup lang="ts">
import { computed, onBeforeUnmount, onMounted, ref } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
import AppLayout from '../../layouts/AppLayout.vue';
import OPanel from '../../components/ui/OPanel.vue';
import OButton from '../../components/ui/OButton.vue';
import OBadge from '../../components/ui/OBadge.vue';
import OScoreBar from '../../components/ui/OScoreBar.vue';

interface StepCase {
    id: number;
    title: string;
    score: number;
    passed: boolean;
    output: string | null;
    criteria: Array<{ label: string; passed: boolean; detail: Record<string, unknown> | null }>;
}

interface Step {
    id: number;
    number: number;
    phase: 'evaluate' | 'mutate';
    score: number | null;
    mutation_type: string | null;
    rationale: string | null;
    prompt_content: string;
    cases: StepCase[];
}

const props = defineProps<{
    run: {
        id: number;
        name: string;
        status: string;
        mode: string;
        best_score: number | null;
        target_score: number;
        error: string | null;
        prompt?: { id: number; name: string } | null;
        benchmark?: { id: number; name: string } | null;
        steps: Step[];
    };
}>();

const { t } = useI18n();

// Only evaluation steps carry scores; mutation steps document the change.
const evalSteps = computed(() => props.run.steps.filter((s) => s.phase === 'evaluate'));
const selectedStepNumber = ref<number | null>(evalSteps.value.at(-1)?.number ?? null);

const selectedStep = computed(() =>
    props.run.steps.find((s) => s.number === selectedStepNumber.value) ?? evalSteps.value.at(-1) ?? null,
);


const isRunning = computed(() => ['pending', 'running'].includes(props.run.status));

const scoreTrend = computed(() => evalSteps.value.map((s) => ({ n: s.number, score: s.score ?? 0 })));

// Live progress: poll while the run executes; a partial reload refreshes
// the whole step timeline once the run reaches a terminal status.
let pollTimer: number | undefined;

onMounted(() => {
    if (! isRunning.value) return;
    pollTimer = window.setInterval(async () => {
        try {
            const res = await fetch(`/runs/${props.run.id}/status`, { headers: { Accept: 'application/json' } });
            const data: { status: string } = await res.json();
            if (! ['pending', 'running'].includes(data.status)) {
                window.clearInterval(pollTimer);
                router.reload({ only: ['run'] });
            }
        } catch {
            // transient network errors are ignored; next tick retries
        }
    }, 2000);
});

onBeforeUnmount(() => window.clearInterval(pollTimer));

 const cancel = () => router.post(`/runs/${props.run.id}/cancel`);
 </script>
<template>
    <AppLayout>
        <Head><title>{{ run.name }}</title></Head>

        <div class="flex flex-wrap items-start justify-between gap-4">
            <div class="min-w-0">
                <div class="flex flex-wrap items-center gap-3">
                    <h1 class="truncate font-display text-2xl font-bold tracking-tight text-ink-950">{{ run.name }}</h1>
                    <OBadge :tone="run.status === 'completed' ? 'mint' : run.status === 'failed' ? 'rose' : run.status === 'running' ? 'violet' : 'neutral'">
                        {{ t(`runs.status.${run.status}`) }}
                    </OBadge>
                </div>
                <p class="mt-1 text-sm text-ink-500">
                    {{ t(`runs.mode.${run.mode}`) }}
                    <template v-if="run.benchmark"> · {{ run.benchmark.name }}</template>
                </p>
            </div>
            <div class="flex gap-2">
                <OButton v-if="isRunning" variant="secondary" @click="cancel">{{ t('runs.cancel') }}</OButton>
                <Link href="/runs" class="self-center text-sm text-ink-500 hover:text-ink-900">{{ t('common.back') }}</Link>
            </div>
        </div>

        <p v-if="run.error" class="mt-4 rounded-card border border-rose-450/30 bg-rose-50 px-4 py-3 text-sm text-rose-450">{{ run.error }}</p>

        <!-- Score summary -->
        <OPanel class="mt-6">
            <div class="grid gap-6 sm:grid-cols-3">
                <div>
                    <p class="text-xs font-medium uppercase tracking-wide text-ink-300">{{ t('runs.bestScore') }}</p>
                    <p class="mt-1 font-display text-3xl font-bold tabular-nums text-ink-950">
                        {{ run.best_score !== null ? Math.round(run.best_score * 100) + '%' : '—' }}
                    </p>
                </div>
                <div>
                    <p class="text-xs font-medium uppercase tracking-wide text-ink-300">{{ t('runs.target') }}</p>
                    <p class="mt-1 font-display text-3xl font-bold tabular-nums text-ink-950">{{ Math.round(run.target_score * 100) }}%</p>
                </div>
                <div>
                    <p class="text-xs font-medium uppercase tracking-wide text-ink-300">{{ t('runs.steps') }}</p>
                    <p class="mt-1 font-display text-3xl font-bold tabular-nums text-ink-950">{{ evalSteps.length }}</p>
                </div>
            </div>
            <div class="mt-5">
                <OScoreBar :score="run.best_score ?? 0" :target="run.target_score" />
            </div>

            <!-- Score trend sparkline -->
            <div v-if="scoreTrend.length > 1" class="mt-6 flex h-16 items-end gap-1" aria-hidden="true">
                <div
                    v-for="point in scoreTrend"
                    :key="point.n"
                    class="flex-1 rounded-t-sm transition-all"
                    :class="point.score >= run.target_score ? 'bg-mint-500' : 'bg-violet-400'"
                    :style="{ height: Math.max(4, point.score * 100) + '%' }"
                    :title="`Step ${point.n}: ${Math.round(point.score * 100)}%`"
                />
            </div>
        </OPanel>

        <!-- Step timeline -->
        <section v-if="run.steps.length > 0" class="mt-8 grid gap-6 lg:grid-cols-[16rem_1fr]">
            <nav class="space-y-1" aria-label="Run steps">
                <button
                    v-for="step in run.steps"
                    :key="step.id"
                    class="flex w-full items-center gap-3 rounded-lg px-3 py-2 text-left text-sm transition-colors"
                    :class="selectedStep?.number === step.number ? 'bg-violet-50 text-violet-700' : 'text-ink-500 hover:bg-paper-100'"
                    @click="selectedStepNumber = step.number"
                >
                    <span
                        class="flex h-6 w-6 shrink-0 items-center justify-center rounded-full text-[10px] font-semibold"
                        :class="step.phase === 'mutate' ? 'bg-amber-100 text-amber-450' : step.score !== null && step.score >= run.target_score ? 'bg-mint-100 text-mint-600' : 'bg-ink-100 text-ink-500'"
                    >
                        {{ step.number }}
                    </span>
                    <span class="min-w-0 flex-1 truncate">{{ t(`runs.phase.${step.phase}`) }}</span>
                    <span v-if="step.score !== null" class="font-mono text-xs tabular-nums">{{ Math.round(step.score * 100) }}%</span>
                </button>
            </nav>

            <div v-if="selectedStep" class="min-w-0 space-y-4">
                <OPanel :title="t('runs.step', { number: selectedStep.number })" :subtitle="t(`runs.phase.${selectedStep.phase}`)">
                    <p v-if="selectedStep.rationale" class="text-sm text-ink-500">
                        <span class="font-medium text-ink-900">{{ t('runs.rationale') }}:</span> {{ selectedStep.rationale }}
                    </p>
                    <div class="mt-3">
                        <p class="mb-1 text-xs font-semibold uppercase tracking-wide text-ink-300">{{ t('runs.promptUsed') }}</p>
                        <pre class="max-h-64 overflow-auto whitespace-pre-wrap rounded-lg bg-ink-950 p-4 font-mono text-xs leading-relaxed text-ink-100 scroll-thin">{{ selectedStep.prompt_content }}</pre>
                    </div>
                </OPanel>

                <OPanel v-if="selectedStep.phase === 'evaluate'" :title="t('runs.cases')">
                    <div class="space-y-4">
                        <div v-for="c in selectedStep.cases" :key="c.id" class="rounded-lg border border-ink-100 p-4">
                            <div class="flex items-center gap-3">
                                <span :class="c.passed ? 'text-mint-600' : 'text-rose-450'" :aria-label="c.passed ? t('runs.passed') : t('runs.failed')">
                                    <svg v-if="c.passed" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" /></svg>
                                    <svg v-else class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" /></svg>
                                </span>
                                <span class="text-sm font-medium text-ink-950">{{ c.title }}</span>
                                <span class="ml-auto font-mono text-xs tabular-nums text-ink-500">{{ Math.round(c.score * 100) }}%</span>
                            </div>

                            <ul class="mt-3 space-y-1.5">
                                <li v-for="(cr, i) in c.criteria" :key="i" class="flex items-start gap-2 text-xs">
                                    <span class="mt-1 h-1.5 w-1.5 shrink-0 rounded-full" :class="cr.passed ? 'bg-mint-500' : 'bg-rose-450'" aria-hidden="true" />
                                    <span :class="cr.passed ? 'text-ink-700' : 'text-rose-450'">{{ cr.label }}</span>
                                </li>
                            </ul>

                            <details class="mt-3">
                                <summary class="cursor-pointer text-xs font-medium text-violet-600 hover:text-violet-700">{{ t('runs.output') }}</summary>
                                <pre class="mt-2 max-h-48 overflow-auto whitespace-pre-wrap rounded-lg bg-paper-100 p-3 font-mono text-xs leading-relaxed text-ink-700 scroll-thin">{{ c.output }}</pre>
                            </details>
                        </div>
                    </div>
                </OPanel>
            </div>
        </section>

        <OEmptyState v-else-if="!isRunning" class="mt-8" :title="t('runs.empty')" />
        <OPanel v-else class="mt-8" title="…">
            <p class="py-6 text-center text-sm text-ink-500">{{ t('common.loading') }}</p>
        </OPanel>
    </AppLayout>
</template>
