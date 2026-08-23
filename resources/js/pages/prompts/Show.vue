<script setup lang="ts">
import { computed, ref, watch } from 'vue';
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
import AppLayout from '../../layouts/AppLayout.vue';
import OPanel from '../../components/ui/OPanel.vue';
import OButton from '../../components/ui/OButton.vue';
import OInput from '../../components/ui/OInput.vue';
import OBadge from '../../components/ui/OBadge.vue';
import ODiff from '../../components/ODiff.vue';

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

const tab = ref<'editor' | 'versions'>('editor');

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

// Diff between ANY two historical versions — hits the backend LCS endpoint
const diffFrom = ref<number | null>(null);
const diffTo = ref<number | null>(null);
const diffOps = ref<Array<{ op: string; text: string }> | null>(null);
const diffMeta = ref<{ from: number; to: number } | null>(null);
const diffLoading = ref(false);

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

// default pair: oldest -> current version
watch(() => props.prompt.versions, (versions: Array<{ id: number; version: number }>) => {
    if (versions.length >= 2 && !diffFrom.value) {
        diffFrom.value = versions[versions.length - 1].id;
        diffTo.value = versions[0].id;
    }
}, { immediate: true });

const playgroundHistory = ref<Array<{ input: string; output: string; at: string }>>([]);

const playgroundInput = ref('');
const playgroundOutput = ref<string | null>(null);
const playgroundLoading = ref(false);
const playgroundError = ref('');

const runPlayground = async () => {
    playgroundLoading.value = true;
    playgroundError.value = '';
    playgroundOutput.value = null;

    try {
        const res = await fetch(`/prompts/${props.prompt.id}/playground`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector<HTMLMetaElement>('meta[name=csrf-token]')?.content ?? '',
                Accept: 'application/json',
            },
            body: JSON.stringify({
                input: playgroundInput.value,
                content: form.content,
            }),
        });

        if (!res.ok) {
            playgroundError.value = t('common.error');
            return;
        }

        const data: { output: string } = await res.json();
        playgroundOutput.value = data.output;
        playgroundHistory.value.unshift({ input: playgroundInput.value, output: data.output, at: new Date().toISOString() });
        playgroundHistory.value = playgroundHistory.value.slice(0, 5);
    } catch {
        playgroundError.value = t('common.error');
    } finally {
        playgroundLoading.value = false;
    }
};
const copyOutput = () => {
    if (playgroundOutput.value) window.navigator.clipboard.writeText(playgroundOutput.value);
};

const selectedBenchmarkId = ref<number | ''>('');

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
                <OButton variant="danger" @click="destroy">{{ t('prompts.delete') }}</OButton>
            </div>
        </div>

        <!-- Tabs -->
        <div class="mt-6 flex gap-1 border-b border-ink-100" role="tablist">
            <button
                v-for="tb in [{ id: 'editor', label: t('prompts.content') }, { id: 'versions', label: t('prompts.versions') }]"
                :key="tb.id"
                role="tab"
                :aria-selected="tab === tb.id"
                class="-mb-px border-b-2 px-4 py-2 text-sm font-medium transition-colors"
                :class="tab === tb.id ? 'border-accent-600 text-accent-700' : 'border-transparent text-ink-500 hover:text-ink-900'"
                @click="tab = tb.id as 'editor' | 'versions'"
            >
                {{ tb.label }}
            </button>
        </div>

        <!-- Editor tab -->
        <div v-if="tab === 'editor'" class="mt-6 grid gap-6 lg:grid-cols-[1fr_18rem]">
            <div>
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

                <!-- Playground: test the (possibly unsaved) prompt on one input — glass + shimmer -->
                <OPanel :title="t('prompts.playground')" class="glass">
                    <p class="mb-3 text-xs leading-relaxed text-ink-500">Test your current draft against a single input before committing a version.</p>
                    <textarea
                        v-model="playgroundInput"
                        rows="4"
                        :placeholder="t('benchmarks.wizard.caseInput')"
                        class="w-full rounded-xl border border-ink-200 bg-card px-3 py-2.5 text-sm shadow-sm transition focus:border-emerald-400 focus:ring-2 focus:ring-emerald-100"
                    />
                    <OButton
                        variant="secondary"
                        size="sm"
                        class="mt-3 w-full hover-glow-emerald"
                        :class="playgroundLoading ? 'shimmer' : ''"
                        :disabled="playgroundLoading || playgroundInput.trim() === ''"
                        @click="runPlayground"
                    >
                        <span v-if="playgroundLoading" class="icon-pulse">●</span>
                        <span v-else>▶</span> {{ playgroundLoading ? t('common.loading') : t('prompts.playground') }}
                    </OButton>
                    <div v-if="playgroundLoading" class="mt-3 space-y-2">
                        <div class="h-3 w-full rounded bg-ink-100 shimmer" />
                        <div class="h-3 w-5/6 rounded bg-ink-100 shimmer" />
                        <div class="h-3 w-3/4 rounded bg-ink-100 shimmer" />
                    </div>
                    <div v-else-if="playgroundOutput !== null" class="relative mt-3">
                        <pre class="max-h-56 overflow-auto whitespace-pre-wrap rounded-xl pinned-dark p-4 font-mono text-xs leading-relaxed text-emerald-100 shadow-inner">{{ playgroundOutput }}</pre>
                        <button type="button" class="absolute right-2 top-2 rounded-md bg-card/10 px-2 py-1 font-mono text-xs text-white backdrop-blur hover:bg-card/20" @click="copyOutput">Copy</button>
                    </div>
                    <p v-if="playgroundError" class="mt-2 rounded-lg bg-rose-50 px-3 py-2 text-xs text-rose-600">{{ playgroundError }}</p>
                    <div v-if="playgroundHistory.length > 0" class="mt-4 border-t border-dashed border-ink-100 pt-3">
                        <p class="eyebrow mb-2">{{ t('prompts.playgroundHistory', { n: playgroundHistory.length }) }}</p>
                        <ul class="space-y-1.5">
                            <li v-for="(h, hi) in playgroundHistory" :key="hi">
                                <button
                                    type="button"
                                    class="flex w-full items-center gap-2 rounded-lg px-2 py-1.5 text-left transition hover:bg-paper-200/60"
                                    @click="playgroundInput = h.input; playgroundOutput = h.output"
                                >
                                    <span class="font-mono text-[10px] text-ink-300">{{ new Date(h.at).toLocaleTimeString() }}</span>
                                    <span class="min-w-0 flex-1 truncate text-xs text-ink-600">{{ h.input }}</span>
                                    <svg class="h-3 w-3 shrink-0 text-ink-300" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992V4.356m0 4.992c-1.11-3.036-4.047-5.198-7.5-5.198-4.008 0-7.26 3.09-7.5 7.02m19.5 0a8.188 8.188 0 01-1.56 4.86m-17.94-4.86a8.188 8.188 0 001.56 4.86" /></svg>
                                </button>
                            </li>
                        </ul>
                    </div>
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
        <div v-else class="mt-6 space-y-4">
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
    </AppLayout>
</template>
