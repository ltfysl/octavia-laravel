<script setup lang="ts">
import { computed, ref } from 'vue';
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
    } catch {
        playgroundError.value = t('common.error');
    } finally {
        playgroundLoading.value = false;
    }
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
                    class="w-full rounded-card border border-ink-200 bg-white px-4 py-3 font-mono text-sm leading-relaxed shadow-panel focus:border-accent-500"
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
                        class="w-full rounded-lg border border-ink-200 bg-white px-3 py-2 text-sm focus:border-accent-500"
                        :aria-label="t('prompts.runAgainst')"
                        aria-describedby="run-benchmark-hint"
                    >
                        <option value="" disabled>—</option>
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

                <!-- Playground: test the (possibly unsaved) prompt on one input -->
                <OPanel :title="t('prompts.playground')">
                    <textarea
                        v-model="playgroundInput"
                        rows="4"
                        :placeholder="t('benchmarks.wizard.caseInput')"
                        class="w-full rounded-lg border border-ink-200 bg-white px-3 py-2 text-sm focus:border-accent-500"
                    />
                    <OButton
                        variant="secondary"
                        size="sm"
                        class="mt-2 w-full"
                        :disabled="playgroundLoading || playgroundInput.trim() === ''"
                        @click="runPlayground"
                    >
                        ▶ {{ playgroundLoading ? t('common.loading') : t('prompts.playground') }}
                    </OButton>
                    <pre
                        v-if="playgroundOutput !== null"
                        class="mt-3 max-h-56 overflow-auto whitespace-pre-wrap rounded-lg bg-paper-100 p-3 font-mono text-xs leading-relaxed text-ink-700 scroll-thin"
                    >{{ playgroundOutput }}</pre>
                    <p v-if="playgroundError" class="mt-2 text-xs text-rose-450">{{ playgroundError }}</p>
                </OPanel>
            </div>
        </div>

        <!-- Versions tab -->
        <div v-else class="mt-6 space-y-4">
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
