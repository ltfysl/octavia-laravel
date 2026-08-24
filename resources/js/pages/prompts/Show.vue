<script setup lang="ts">
import { computed, nextTick, onBeforeUnmount, onMounted, ref, watch } from 'vue';
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
import AppLayout from '../../layouts/AppLayout.vue';
import OPanel from '../../components/ui/OPanel.vue';
import OButton from '../../components/ui/OButton.vue';
import OInput from '../../components/ui/OInput.vue';
import OBadge from '../../components/ui/OBadge.vue';
import OField from '../../components/ui/OField.vue';
import ODiff from '../../components/ODiff.vue';
import OEmptyState from '../../components/ui/OEmptyState.vue';

const props = defineProps<{
    prompt: {
        id: number;
        name: string;
        description: string | null;
        visibility: string;
        content: string | null;
        current_version: number | null;
        versions: Array<{ id: number; version: number; content: string; changelog: string | null; created_at: string }>;
    };
    benchmarks: Array<{ id: number; name: string; cases_count: number }>;
}>();

const { t } = useI18n();

const tab = ref<'editor' | 'versions' | 'analytics' | 'abTest' | 'regression'>('editor');

const form = useForm({
    name: props.prompt.name,
    description: props.prompt.description ?? '',
    visibility: props.prompt.visibility,
    content: props.prompt.content ?? '',
    changelog: '',
});

const publish = () => {
    router.post('/marketplace/publish', {
        item_type: 'prompt',
        item_id: props.prompt.id,
        summary: props.prompt.description ?? '',
    });
};

const dirty = computed(() => form.content !== (props.prompt.content ?? ''));

const compareWith = ref<number | null>(null);
const currentContent = computed(() => props.prompt.content ?? '');
const copied = ref(false);
const copyCurrent = async () => {
    if (! currentContent.value) return;
    try {
        await navigator.clipboard.writeText(currentContent.value);
        copied.value = true;
        setTimeout(() => copied.value = false, 2000);
    } catch {
        // ignore
    }
};

// Diff between ANY two historical versions — hits the backend LCS endpoint
const diffFrom = ref<number | null>(null);
const diffTo = ref<number | null>(null);
const diffOps = ref<Array<{ op: string; text: string }> | null>(null);
const diffMeta = ref<{ from: number; to: number } | null>(null);
const diffLoading = ref(false);
const diffExplainLoading = ref(false);
const diffExplain = ref<{ summary: string; changes: Array<{ type: string; description: string; impact: string }>; recommendation: string } | null>(null);

const loadDiff = async () => {
    if (!diffFrom.value || !diffTo.value || diffFrom.value === diffTo.value) return;
    diffLoading.value = true;
    try {
        const res = await fetch(
            `/prompts/${props.prompt.id}/diff?from=${diffFrom.value}&to=${diffTo.value}`,
            { headers: { Accept: 'application/json' } },
        );
        if (!res.ok) throw new Error('diff failed');
        const data: { ops: Array<{ op: string; text: string }>; from: { id: number }; to: { id: number } } = await res.json();
        diffOps.value = data.ops;
        diffMeta.value = { from: data.from.id, to: data.to.id };
    } catch {
        diffOps.value = null;
        diffMeta.value = null;
    } finally {
        diffLoading.value = false;
    }
};

const explainDiff = async () => {
    if (!diffFrom.value || !diffTo.value || diffFrom.value === diffTo.value) return;
    diffExplainLoading.value = true;
    try {
        const res = await fetch(`/prompts/${props.prompt.id}/diff-explain`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector<HTMLMetaElement>('meta[name=csrf-token]')?.content ?? '',
                Accept: 'application/json',
            },
            credentials: 'include',
            body: JSON.stringify({
                from_version_id: diffFrom.value,
                to_version_id: diffTo.value,
            }),
        });
        if (!res.ok) throw new Error();
        diffExplain.value = await res.json();
    } catch {
        diffExplain.value = null;
    } finally {
        diffExplainLoading.value = false;
    }
};

// default pair: oldest -> current version
watch(() => props.prompt.versions, (versions: Array<{ id: number; version: number }>) => {
    if (versions.length >= 2 && !diffFrom.value) {
        diffFrom.value = versions[versions.length - 1].id;
        diffTo.value = versions[0].id;
    }
}, { immediate: true });

interface PlaygroundMessage {
    role: 'user' | 'assistant';
    content: string;
}

const playgroundMessages = ref<PlaygroundMessage[]>([]);
const playgroundDraft = ref('');
const playgroundLoading = ref(false);
const playgroundError = ref('');
const playgroundBottom = ref<HTMLElement | null>(null);

const sendPlaygroundMessage = async () => {
    const content = playgroundDraft.value.trim();
    if (!content) return;

    playgroundMessages.value.push({ role: 'user', content });
    playgroundDraft.value = '';
    playgroundLoading.value = true;
    playgroundError.value = '';

    try {
        const res = await fetch(`/prompts/${props.prompt.id}/playground/chat`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector<HTMLMetaElement>('meta[name=csrf-token]')?.content ?? '',
                Accept: 'application/json',
            },
            body: JSON.stringify({
                messages: playgroundMessages.value,
                content: form.content,
            }),
        });

        if (!res.ok) {
            playgroundError.value = t('common.error');
            playgroundMessages.value.pop();
            return;
        }

        const data: { output: string } = await res.json();
        playgroundMessages.value.push({ role: 'assistant', content: data.output });
    } catch {
        playgroundError.value = t('common.error');
        playgroundMessages.value.pop();
    } finally {
        playgroundLoading.value = false;
        nextTick(() => playgroundBottom.value?.scrollIntoView({ behavior: 'smooth', block: 'end' }));
    }
};

const clearPlayground = () => {
    playgroundMessages.value = [];
    playgroundError.value = '';
};

const copyPlaygroundMessage = (text: string) => {
    window.navigator.clipboard.writeText(text);
};

const selectedBenchmarkId = ref<number | ''>('');

interface PromptAnalytics {
    runs_count: number;
    completed_count: number;
    avg_score: number | null;
    best_score: number | null;
    history: Array<{ at: string; score: number }>;
    by_benchmark: Array<{ name: string; runs_count: number; avg_score: number | null; best_score: number | null }>;
    recent_runs: Array<{
        id: number;
        name: string;
        status: string;
        mode: string;
        best_score: number | null;
        benchmark: { id: number; name: string } | null;
        created_at: string;
    }>;
    score_distribution: Array<{ range: string; count: number }>;
}

const analytics = ref<PromptAnalytics | null>(null);
const analyticsLoading = ref(false);
const analyticsError = ref('');

const loadAnalytics = async () => {
    analyticsLoading.value = true;
    analyticsError.value = '';
    try {
        const res = await fetch(`/prompts/${props.prompt.id}/analytics`, { headers: { Accept: 'application/json' } });
        if (!res.ok) throw new Error();
        analytics.value = await res.json();
    } catch {
        analyticsError.value = t('common.error');
    } finally {
        analyticsLoading.value = false;
    }
};

interface AbResult {
    version_a: { version: number; score: number; tokens: number };
    version_b: { version: number; score: number; tokens: number };
    winner: 'a' | 'b' | 'tie';
    benchmark: string;
}

const abVersionA = ref<number | ''>('');
const abVersionB = ref<number | ''>('');
const abResult = ref<AbResult | null>(null);
const abLoading = ref(false);
const abError = ref('');

const runAbTest = async () => {
    if (! abVersionA.value || ! abVersionB.value || ! selectedBenchmarkId.value) return;
    abLoading.value = true;
    abError.value = '';
    abResult.value = null;
    try {
        const res = await fetch(`/prompts/${props.prompt.id}/ab-test`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector<HTMLMetaElement>('meta[name=csrf-token]')?.content ?? '',
                Accept: 'application/json',
            },
            credentials: 'include',
            body: JSON.stringify({
                version_a_id: abVersionA.value,
                version_b_id: abVersionB.value,
                benchmark_id: selectedBenchmarkId.value,
            }),
        });
        if (! res.ok) throw new Error();
        abResult.value = await res.json();
    } catch {
        abError.value = t('common.error');
    } finally {
        abLoading.value = false;
    }
};


interface RegressionResult {
    results: Array<{
        benchmark_id: number;
        benchmark_name: string;
        category: string;
        status: 'pass' | 'fail';
        score: number;
        cases: Array<{ case_id: number; title: string; score: number; passed: boolean; output: string }>;
    }>;
    summary: { total: number; passed: number; failed: number; errors: number; avg_score: number };
}

const regressionBenchmarkIds = ref<number[]>([]);
const regressionSample = ref('');
const regressionResult = ref<RegressionResult | null>(null);
const regressionLoading = ref(false);
const regressionError = ref('');

const runRegression = async () => {
    regressionLoading.value = true;
    regressionError.value = '';
    regressionResult.value = null;
    try {
        const res = await fetch(`/prompts/${props.prompt.id}/regression-test`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector<HTMLMetaElement>('meta[name=csrf-token]')?.content ?? '',
                Accept: 'application/json',
            },
            credentials: 'include',
            body: JSON.stringify({
                benchmark_ids: regressionBenchmarkIds.value.length ? regressionBenchmarkIds.value : null,
                sample_input: regressionSample.value || null,
            }),
        });
        if (! res.ok) throw new Error();
        regressionResult.value = await res.json();
    } catch {
        regressionError.value = t('common.error');
    } finally {
        regressionLoading.value = false;
    }
};
const startRun = (mode: 'evaluate' | 'optimize') => {
    if (! selectedBenchmarkId.value) return;
    router.post('/runs', {
        prompt_id: props.prompt.id,
        benchmark_id: selectedBenchmarkId.value,
        mode,
    });
};

const restore = (versionId: number) => {
    router.post(`/prompts/${props.prompt.id}/versions/${versionId}/restore`);
};

const duplicate = () => {
    router.post(`/prompts/${props.prompt.id}/duplicate`);
};

const destroy = () => {
    if (confirm(t('prompts.deleteConfirm'))) {
        router.delete(`/prompts/${props.prompt.id}`);
    }
};

const saveAsVersion = () => {
    form.changelog = form.changelog || 'Manual edit';
    form.patch(`/prompts/${props.prompt.id}`, {
        onSuccess: () => {
            form.changelog = '';
            tab.value = 'versions';
        },
    });
};

// Cmd/Ctrl+S saves the current content as a new version
const onKeydown = (e: KeyboardEvent) => {
    if ((e.metaKey || e.ctrlKey) && e.key.toLowerCase() === 's') {
        e.preventDefault();
        if (dirty.value && ! form.processing) saveAsVersion();
    }
};
onMounted(() => window.addEventListener('keydown', onKeydown));
onBeforeUnmount(() => window.removeEventListener('keydown', onKeydown));
const insight = ref<string | null>(null);
const insightLoading = ref(false);
const insightError = ref('');

const runInsight = async () => {
    insightLoading.value = true;
    insightError.value = '';
    insight.value = null;

    try {
        const res = await fetch(`/prompts/${props.prompt.id}/insight`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector<HTMLMetaElement>('meta[name=csrf-token]')?.content ?? '',
                Accept: 'application/json',
            },
            credentials: 'include',
        });
        if (! res.ok) throw new Error();
        const data: { insight: string } = await res.json();
        insight.value = data.insight;
    } catch {
        insightError.value = t('common.error');
    } finally {
        insightLoading.value = false;
    }
};
</script>

<template>
    <AppLayout>
        <Head><title>{{ prompt.name }}</title></Head>

        <div class="flex flex-wrap items-start justify-between gap-4">
            <div class="min-w-0">
                <div class="flex items-center gap-3">
                    <h1 class="truncate font-display text-2xl font-bold tracking-tight text-ink-950">{{ prompt.name }}</h1>
                    <OBadge :tone="prompt.visibility === 'public' ? 'accent' : 'neutral'">{{ t(`prompts.visibility.${prompt.visibility}`) }}</OBadge>
                    <span v-if="prompt.current_version" class="font-mono text-xs text-ink-300">v{{ prompt.current_version }}</span>
                </div>
                <p v-if="prompt.description" class="mt-1 text-sm text-ink-500">{{ prompt.description }}</p>
            </div>
            <div class="flex items-center gap-2">
                <Link href="/prompts" class="self-center text-sm text-ink-500 hover:text-ink-900">{{ t('common.back') }}</Link>
                <span class="mx-1 h-5 w-px bg-ink-100" aria-hidden="true" />
                <OButton variant="secondary" :disabled="form.processing" @click="publish">{{ t('marketplace.publish') }}</OButton>
                <span class="mx-1 h-5 w-px bg-ink-100" aria-hidden="true" />
                <OButton variant="secondary" :disabled="form.processing" @click="duplicate">{{ t('prompts.duplicate') }}</OButton>
                <a :href="`/prompts/${props.prompt.id}/export`" download class="inline-flex items-center rounded-full border border-ink-100 bg-card px-3 py-1.5 text-xs font-medium text-ink-600 hover:bg-paper-50">{{ t('common.export') }}</a>
                <span class="mx-1 h-5 w-px bg-ink-100" aria-hidden="true" />
                <OButton variant="danger" @click="destroy">{{ t('prompts.delete') }}</OButton>
            </div>
        </div>

        <!-- Tabs -->
        <div class="mt-6 flex gap-1 border-b border-ink-100" role="tablist">
            <button
                v-for="tb in [{ id: 'editor', label: t('prompts.content') }, { id: 'versions', label: t('prompts.versions') }, { id: 'analytics', label: t('prompts.analytics.title') }, { id: 'abTest', label: t('prompts.abTest.title') }, { id: 'regression', label: t('prompts.regression.title') }]"
                :key="tb.id"
                role="tab"
                :aria-selected="tab === tb.id"
                class="-mb-px border-b-2 px-4 py-2 text-sm font-medium transition-colors"
                :class="tab === tb.id ? 'border-accent-600 text-accent-700' : 'border-transparent text-ink-500 hover:text-ink-900'"
                @click="tab = tb.id as 'editor' | 'versions' | 'analytics' | 'abTest' | 'regression'; if (tab === 'analytics') loadAnalytics()"
            >
                {{ tb.label }}
            </button>
        </div>

        <!-- Editor tab -->
        <div v-if="tab === 'editor'" class="mt-6 grid gap-6 lg:grid-cols-[1fr_18rem]">
            <div>
                <div class="mb-2 flex items-center justify-between">
                    <OButton size="sm" variant="ghost" :disabled="!currentContent" @click="copyCurrent">
                        {{ copied ? t('common.copied') : t('common.copy') }}
                    </OButton>
                </div>
                <textarea
                    v-model="form.content"
                    rows="20"
                    :aria-label="t('prompts.content')"
                    class="w-full rounded-card border border-ink-200 bg-card px-4 py-3 font-mono text-sm leading-relaxed shadow-panel focus:border-accent-500"
                />
                <div class="mt-3 flex items-center gap-3">
                    <OInput v-model="form.changelog" :placeholder="t('prompts.changelog')" class="max-w-xs flex-1" />
                    <OButton :disabled="form.processing || !dirty" @click="saveAsVersion">
                        {{ form.processing ? t('common.saving') : t('prompts.saveAsNewVersion') }}
                    </OButton>
                </div>
            </div>

            <div class="space-y-6">
                <OPanel :title="t('prompts.runAgainst')">
                    <select
                        v-model="selectedBenchmarkId"
                        class="w-full rounded-lg border border-ink-200 bg-card px-3 py-2 text-sm focus:border-accent-500"
                        :aria-label="t('prompts.runAgainst')"
                        aria-describedby="run-benchmark-hint"
                    >
                        <option value="" disabled>—</option>
                        <option v-for="benchmark in benchmarks" :key="benchmark.id" :value="benchmark.id">
                            {{ benchmark.name }} ({{ benchmark.cases_count }})
                        </option>
                    </select>
                    <p id="run-benchmark-hint" class="mt-2 text-xs text-ink-500">{{ t('prompts.runHint') }}</p>
                    <div class="mt-3 space-y-2">
                        <OButton class="w-full" :disabled="!selectedBenchmarkId" @click="startRun('optimize')">
                            {{ t('runs.mode.optimize') }}
                        </OButton>
                        <OButton variant="secondary" class="w-full" :disabled="!selectedBenchmarkId" @click="startRun('evaluate')">
                            {{ t('runs.mode.evaluate') }}
                        </OButton>
                    </div>
                    <Link v-if="benchmarks.length === 0" href="/benchmarks/wizard" class="mt-3 block text-center text-xs font-medium text-accent-600 hover:text-accent-700">
                        {{ t('dashboard.createBenchmark') }} →
                    </Link>
                </OPanel>

                <!-- Playground: multi-turn chat with the current draft -->
                <OPanel :title="t('prompts.playground')" class="glass">
                    <div class="mb-3 flex items-center justify-between">
                        <p class="text-xs leading-relaxed text-ink-500">{{ t('prompts.playgroundChatHint') }}</p>
                        <button v-if="playgroundMessages.length" type="button" class="text-[11px] text-ink-400 underline hover:text-ink-600" @click="clearPlayground">{{ t('common.clear') }}</button>
                    </div>
                    <div class="h-56 overflow-y-auto rounded-xl border border-ink-100 bg-paper-50 p-3 scroll-thin" ref="playgroundScroll">
                        <div v-if="playgroundMessages.length === 0" class="flex h-full items-center justify-center text-xs text-ink-400">
                            {{ t('prompts.playgroundEmpty') }}
                        </div>
                        <div v-for="(msg, idx) in playgroundMessages" :key="idx" class="mb-3" data-testid="playground-message">
                            <div :class="msg.role === 'user' ? 'ml-8 flex justify-end' : 'mr-8'">
                                <div :class="msg.role === 'user' ? 'rounded-2xl rounded-br-sm bg-ink-900 px-3 py-2 text-white' : 'rounded-2xl rounded-bl-sm bg-card border border-ink-100 px-3 py-2 text-ink-800'">
                                    <p class="whitespace-pre-wrap text-sm leading-relaxed">{{ msg.content }}</p>
                                    <button v-if="msg.role === 'assistant'" type="button" class="mt-1 text-[10px] text-ink-400 hover:text-ink-600" @click="copyPlaygroundMessage(msg.content)">{{ t('common.copy') }}</button>
                                </div>
                            </div>
                        </div>
                        <div ref="playgroundBottom" />
                    </div>
                    <div class="mt-3 flex gap-2">
                        <textarea
                            v-model="playgroundDraft"
                            data-testid="playground-input"
                            rows="2"
                            :placeholder="t('prompts.playgroundInput')"
                            class="min-h-[3rem] flex-1 resize-none rounded-xl border border-ink-200 bg-card px-3 py-2.5 text-sm shadow-sm transition focus:border-emerald-400 focus:ring-2 focus:ring-emerald-100"
                            @keydown.enter.exact.prevent="sendPlaygroundMessage"
                        />
                        <OButton
                            data-testid="playground-send"
                            :aria-label="t('prompts.playground')"
                            variant="secondary"
                            size="sm"
                            class="self-end hover-glow-emerald"
                            :class="playgroundLoading ? 'shimmer' : ''"
                            :disabled="playgroundLoading || playgroundDraft.trim() === ''"
                            @click="sendPlaygroundMessage"
                        >
                            <span v-if="playgroundLoading" class="icon-pulse">●</span>
                            <span v-else>▶</span>
                        </OButton>
                    </div>
                    <p v-if="playgroundError" class="mt-2 rounded-lg bg-rose-50 px-3 py-2 text-xs text-rose-600">{{ playgroundError }}</p>
                </OPanel>

                <!-- AI Insights — on-demand prompt review -->
                <OPanel :title="t('prompts.insightTitle')" class="glass">
                    <p class="mb-3 text-xs leading-relaxed text-ink-500">{{ t('prompts.insightHint') }}</p>
                    <OButton
                        variant="secondary"
                        size="sm"
                        class="w-full hover-glow-emerald"
                        :class="insightLoading ? 'shimmer' : ''"
                        :disabled="insightLoading"
                        @click="runInsight"
                    >
                        <span v-if="insightLoading" class="icon-pulse">●</span>
                        <span v-else>✦</span> {{ insightLoading ? t('common.loading') : t('prompts.insightButton') }}
                    </OButton>
                    <div v-if="insightLoading" class="mt-3 space-y-2">
                        <div class="h-3 w-full rounded bg-ink-100 shimmer" />
                        <div class="h-3 w-5/6 rounded bg-ink-100 shimmer" />
                    </div>
                    <pre v-else-if="insight" class="scroll-thin mt-3 max-h-64 overflow-auto whitespace-pre-wrap rounded-xl border border-emerald-200 bg-emerald-50/40 p-4 font-mono text-xs leading-relaxed text-emerald-900 dark:bg-emerald-950/20">{{ insight }}</pre>
                    <p v-else-if="insightError" class="mt-2 rounded-lg bg-rose-50 px-3 py-2 text-xs text-rose-600">{{ insightError }}</p>
                </OPanel>

            </div>
        </div>


        <!-- Versions tab -->
        <div v-else-if="tab === 'versions'" class="mt-6 space-y-4">
            <!-- Compare any two historical versions (backend LCS endpoint) -->
            <OPanel title="Diff">
                <div class="flex flex-wrap items-center gap-2">
                    <select v-model="diffFrom" class="rounded-lg border border-ink-200 bg-card px-3 py-2 text-sm focus:border-accent-500" :aria-label="t('prompts.diff.baseLabel')">
                        <option v-for="v in prompt.versions" :key="'f' + v.id" :value="v.id">v{{ v.version }}</option>
                    </select>
                    <span class="font-mono text-xs text-ink-400">→</span>
                    <select v-model="diffTo" class="rounded-lg border border-ink-200 bg-card px-3 py-2 text-sm focus:border-accent-500" :aria-label="t('prompts.diff.compareLabel')">
                        <option v-for="v in prompt.versions" :key="'t' + v.id" :value="v.id">v{{ v.version }}</option>
                    </select>
                    <OButton variant="secondary" size="sm" :disabled="diffLoading || !diffFrom || !diffTo || diffFrom === diffTo" @click="loadDiff">
                        {{ diffLoading ? '…' : t('prompts.diff.compare') }}
                    </OButton>
                    <span v-if="diffMeta" class="ml-auto font-mono text-xs text-ink-400">
                        v{{ prompt.versions.find((v) => v.id === diffMeta!.from)?.version }} → v{{ prompt.versions.find((v) => v.id === diffMeta!.to)?.version }}
                    </span>
                </div>
                <div v-if="diffOps" class="scroll-thin mt-3 max-h-96 overflow-auto rounded-xl border border-ink-100 bg-paper-100/60 dark:bg-paper-100/40 p-3 font-mono text-xs leading-relaxed">
                    <div
                        v-for="(line, i) in diffOps"
                        :key="i"
                        class="whitespace-pre-wrap rounded px-2 py-0.5"
                        :class="line.op === 'delete' ? 'bg-rose-450/10 text-ink-900' : line.op === 'insert' ? 'bg-mint-500/10 text-ink-900' : 'text-ink-500'"
                    >
                        <span class="select-none font-bold" :class="line.op === 'delete' ? 'text-rose-450' : line.op === 'insert' ? 'text-mint-600' : 'text-ink-300'">{{ line.op === 'delete' ? '-' : line.op === 'insert' ? '+' : ' ' }}</span>{{ line.text }}
                    </div>
                </div>
                <p v-else class="mt-3 text-center font-mono text-xs text-ink-300">{{ t('prompts.diff.pickTwo') }}</p>

                <div v-if="diffFrom && diffTo && diffFrom !== diffTo" class="mt-3">
                    <OButton size="sm" :disabled="diffExplainLoading" @click="explainDiff">{{ diffExplainLoading ? t('common.loading') : t('prompts.diff.explain') }}</OButton>
                </div>

                <div v-if="diffExplain" class="mt-3 rounded-xl border border-ink-100 bg-paper-50 p-3">
                    <p class="text-sm font-semibold text-ink-900">{{ diffExplain.summary }}</p>
                    <ul class="mt-2 space-y-1">
                        <li v-for="(change, i) in diffExplain.changes" :key="i" class="text-sm text-ink-700">
                            <span class="font-semibold" :class="change.impact === 'positive' ? 'text-mint-600' : change.impact === 'negative' ? 'text-rose-600' : 'text-ink-500'">{{ change.type }}</span>: {{ change.description }}
                        </li>
                    </ul>
                    <p class="mt-2 text-sm text-ink-500">{{ t('prompts.diff.recommendation') }}: {{ diffExplain.recommendation }}</p>
                </div>
            </OPanel>
            <OPanel v-for="version in prompt.versions" :key="version.id">
                <template #actions>
                    <div class="flex items-center gap-2">
                        <OButton
                            variant="ghost"
                            size="sm"
                            :disabled="!currentContent"
                            @click="compareWith = compareWith === version.id ? null : version.id"
                        >
                            {{ compareWith === version.id ? t('prompts.diff.hideCompare') : t('prompts.diff.compare') }}
                        </OButton>
                        <OButton
                            v-if="version.version !== prompt.current_version"
                            variant="secondary"
                            size="sm"
                            @click="restore(version.id)"
                        >
                            {{ t('prompts.restore') }}
                        </OButton>
                        <OBadge v-else tone="mint">{{ t('prompts.current') }}</OBadge>
                    </div>
                </template>
                <div class="flex items-center gap-3 text-sm">
                    <span class="font-mono font-semibold text-ink-950">v{{ version.version }}</span>
                    <span class="text-ink-500">{{ version.changelog ?? '—' }}</span>
                    <span class="ml-auto text-xs text-ink-300">{{ version.created_at }}</span>
                </div>

                <!-- Word-level diff against the current version -->
                <div v-if="compareWith === version.id && currentContent" class="mt-3">
                    <ODiff :old-text="version.content" :new-text="currentContent" />
                </div>
                <pre
                    v-else
                    class="mt-3 max-h-40 overflow-auto whitespace-pre-wrap rounded-lg bg-paper-100 p-3 font-mono text-xs leading-relaxed text-ink-700 scroll-thin"
                >{{ version.content }}</pre>
            </OPanel>
        </div>

        <!-- Analytics tab -->
        <div v-else-if="tab === 'analytics'" class="mt-6">
            <OPanel :title="t('prompts.analytics.title')">
                <div v-if="analyticsLoading" class="space-y-2">
                    <div class="h-3 w-full rounded bg-ink-100 shimmer" />
                    <div class="h-3 w-5/6 rounded bg-ink-100 shimmer" />
                </div>
                <p v-else-if="analyticsError" class="text-sm text-rose-600">{{ analyticsError }}</p>
                <div v-else-if="analytics" class="space-y-6">
                    <div class="grid gap-3 sm:grid-cols-4">
                        <div>
                            <p class="eyebrow">{{ t('prompts.analytics.runs') }}</p>
                            <p class="mt-1 font-display text-3xl font-semibold text-ink-950">{{ analytics.runs_count }}</p>
                        </div>
                        <div>
                            <p class="eyebrow">{{ t('prompts.analytics.completed') }}</p>
                            <p class="mt-1 font-display text-3xl font-semibold text-ink-950">{{ analytics.completed_count }}</p>
                        </div>
                        <div>
                            <p class="eyebrow">{{ t('prompts.analytics.avg') }}</p>
                            <p class="mt-1 font-display text-3xl font-semibold text-ink-950">{{ analytics.avg_score === null ? '—' : `${analytics.avg_score}%` }}</p>
                        </div>
                        <div>
                            <p class="eyebrow">{{ t('prompts.analytics.best') }}</p>
                            <p class="mt-1 font-display text-3xl font-semibold text-ink-950">{{ analytics.best_score === null ? '—' : `${analytics.best_score}%` }}</p>
                        </div>
                    </div>

                    <!-- Score over time -->
                    <div v-if="analytics.history.length > 1" class="space-y-2">
                        <p class="text-xs font-medium text-ink-700">{{ t('prompts.analytics.history') }}</p>
                        <div class="flex h-16 items-end gap-1" aria-hidden="true">
                            <div
                                v-for="(point, i) in analytics.history"
                                :key="i"
                                class="flex-1 rounded-t-sm transition-all"
                                :class="point.score >= 0.8 ? 'bg-mint-500' : 'bg-accent-400'"
                                :style="{ height: Math.max(4, point.score * 100) + '%' }"
                                :title="`${new Date(point.at).toLocaleDateString()}: ${Math.round(point.score * 100)}%`"
                            />
                        </div>
                    </div>

                    <!-- By benchmark -->
                    <div v-if="analytics.by_benchmark.length" class="space-y-2">
                        <p class="text-xs font-medium text-ink-700">{{ t('prompts.analytics.byBenchmark') }}</p>
                        <table class="w-full text-left text-sm">
                            <thead>
                                <tr class="border-b border-ink-100 text-xs uppercase tracking-wide text-ink-300">
                                    <th class="py-2 font-medium">{{ t('prompts.analytics.benchmarkName') }}</th>
                                    <th class="py-2 font-medium">{{ t('prompts.analytics.runs') }}</th>
                                    <th class="py-2 font-medium">{{ t('prompts.analytics.avg') }}</th>
                                    <th class="py-2 font-medium">{{ t('prompts.analytics.best') }}</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-ink-100">
                                <tr v-for="b in analytics.by_benchmark" :key="b.name">
                                    <td class="py-2">{{ b.name }}</td>
                                    <td class="py-2 font-mono text-xs">{{ b.runs_count }}</td>
                                    <td class="py-2 font-mono text-xs">{{ b.avg_score === null ? '—' : `${b.avg_score}%` }}</td>
                                    <td class="py-2 font-mono text-xs">{{ b.best_score === null ? '—' : `${b.best_score}%` }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <OEmptyState v-else :title="t('prompts.analytics.empty')" />
                </div>
                <OEmptyState v-else :title="t('prompts.analytics.empty')" />
            </OPanel>
        </div>

        <!-- A/B test tab -->
        <div v-else-if="tab === 'abTest'" class="mt-6">
            <OPanel :title="t('prompts.abTest.title')">
                <p class="mb-3 text-xs leading-relaxed text-ink-500">{{ t('prompts.abTest.hint') }}</p>
                <div class="grid gap-4 sm:grid-cols-3">
                    <OField :label="t('prompts.abTest.versionA')" for="abVersionA">
                        <select id="abVersionA" v-model="abVersionA" class="w-full rounded-lg border border-ink-200 bg-card px-3 py-2 text-sm focus:border-accent-500">
                            <option value="">{{ t('prompts.abTest.pick') }}</option>
                            <option v-for="v in prompt.versions" :key="'a' + v.id" :value="v.id">v{{ v.version }}</option>
                        </select>
                    </OField>
                    <OField :label="t('prompts.abTest.versionB')" for="abVersionB">
                        <select id="abVersionB" v-model="abVersionB" class="w-full rounded-lg border border-ink-200 bg-card px-3 py-2 text-sm focus:border-accent-500">
                            <option value="">{{ t('prompts.abTest.pick') }}</option>
                            <option v-for="v in prompt.versions" :key="'b' + v.id" :value="v.id">v{{ v.version }}</option>
                        </select>
                    </OField>
                    <OField :label="t('prompts.abTest.benchmark')" for="abBenchmark">
                        <select id="abBenchmark" v-model="selectedBenchmarkId" class="w-full rounded-lg border border-ink-200 bg-card px-3 py-2 text-sm focus:border-accent-500">
                            <option value="">{{ t('prompts.abTest.pickBenchmark') }}</option>
                            <option v-for="b in benchmarks" :key="b.id" :value="b.id">{{ b.name }}</option>
                        </select>
                    </OField>
                </div>
                <OButton class="mt-4" :disabled="abLoading || !abVersionA || !abVersionB || !selectedBenchmarkId || abVersionA === abVersionB" @click="runAbTest">
                    {{ abLoading ? t('common.loading') : t('prompts.abTest.run') }}
                </OButton>
                <p v-if="abError" class="mt-2 rounded-lg bg-rose-50 px-3 py-2 text-xs text-rose-600">{{ abError }}</p>
                <div v-if="abResult" class="mt-6 grid gap-4 sm:grid-cols-2">
                    <OPanel>
                        <p class="eyebrow">{{ t('prompts.abTest.versionA') }}</p>
                        <p class="mt-1 font-display text-3xl font-semibold text-ink-950">{{ Math.round(abResult.version_a.score * 100) }}%</p>
                        <p class="text-xs text-ink-400">{{ abResult.version_a.tokens }} tokens</p>
                    </OPanel>
                    <OPanel>
                        <p class="eyebrow">{{ t('prompts.abTest.versionB') }}</p>
                        <p class="mt-1 font-display text-3xl font-semibold text-ink-950">{{ Math.round(abResult.version_b.score * 100) }}%</p>
                        <p class="text-xs text-ink-400">{{ abResult.version_b.tokens }} tokens</p>
                    </OPanel>
                    <OPanel v-if="abResult.winner !== 'tie'" class="sm:col-span-2 border-mint-200 bg-mint-50/40">
                        <p class="eyebrow">{{ t('prompts.abTest.winner') }}</p>
                        <p class="mt-1 font-display text-3xl font-semibold text-ink-950">{{ abResult.winner === 'a' ? t('prompts.abTest.versionA') : t('prompts.abTest.versionB') }}</p>
                    </OPanel>
                    <OPanel v-else class="sm:col-span-2 border-ink-200 bg-paper-100">
                        <p class="eyebrow">{{ t('prompts.abTest.winner') }}</p>
                        <p class="mt-1 font-display text-3xl font-semibold text-ink-950">{{ t('prompts.abTest.tie') }}</p>
                    </OPanel>
                </div>
            </OPanel>
        </div>

        <!-- Regression test tab -->
        <div v-else-if="tab === 'regression'" class="mt-6">
            <OPanel :title="t('prompts.regression.title')">
                <p class="mb-3 text-xs leading-relaxed text-ink-500">{{ t('prompts.regression.hint') }}</p>
                <OField :label="t('prompts.regression.benchmarks')" for="regBenchmarks">
                    <select id="regBenchmarks" v-model="regressionBenchmarkIds" multiple class="w-full rounded-lg border border-ink-200 bg-card px-3 py-2 text-sm focus:border-accent-500" size="4">
                        <option v-for="b in benchmarks" :key="b.id" :value="b.id">{{ b.name }} ({{ b.cases_count }} cases)</option>
                    </select>
                    <p class="mt-1 text-xs text-ink-400">{{ t('prompts.regression.benchmarksHint') }}</p>
                </OField>
                <OField :label="t('prompts.regression.sampleInput')" for="regSample" class="mt-3">
                    <OInput id="regSample" v-model="regressionSample" :placeholder="t('prompts.regression.sampleInputHint')" />
                </OField>
                <OButton class="mt-4" :disabled="regressionLoading" @click="runRegression">
                    {{ regressionLoading ? t('common.loading') : t('prompts.regression.run') }}
                </OButton>
                <p v-if="regressionError" class="mt-2 rounded-lg bg-rose-50 px-3 py-2 text-xs text-rose-600">{{ regressionError }}</p>
                <div v-if="regressionResult" class="mt-6 space-y-4">
                    <div class="grid gap-4 sm:grid-cols-4">
                        <OPanel><p class="eyebrow">{{ t('prompts.regression.total') }}</p><p class="mt-1 font-display text-2xl font-semibold text-ink-950">{{ regressionResult.summary.total }}</p></OPanel>
                        <OPanel><p class="eyebrow">{{ t('prompts.regression.passed') }}</p><p class="mt-1 font-display text-2xl font-semibold text-mint-600">{{ regressionResult.summary.passed }}</p></OPanel>
                        <OPanel><p class="eyebrow">{{ t('prompts.regression.failed') }}</p><p class="mt-1 font-display text-2xl font-semibold text-rose-600">{{ regressionResult.summary.failed }}</p></OPanel>
                        <OPanel><p class="eyebrow">{{ t('prompts.regression.avgScore') }}</p><p class="mt-1 font-display text-2xl font-semibold text-ink-950">{{ Math.round(regressionResult.summary.avg_score * 100) }}%</p></OPanel>
                    </div>
                    <div v-for="r in regressionResult.results" :key="r.benchmark_id" class="rounded-2xl border border-ink-100 bg-card p-4">
                        <div class="flex items-center justify-between">
                            <h4 class="font-semibold text-ink-950">{{ r.benchmark_name }}</h4>
                            <OBadge :tone="r.status === 'pass' ? 'mint' : 'rose'">{{ r.status === 'pass' ? t('prompts.regression.pass') : t('prompts.regression.fail') }}</OBadge>
                        </div>
                        <p class="text-xs text-ink-400">{{ Math.round(r.score * 100) }}% · {{ r.category }}</p>
                    </div>
                </div>
            </OPanel>
        </div>
    </AppLayout>
</template>
