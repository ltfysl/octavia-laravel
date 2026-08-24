<script setup lang="ts">
import { computed, ref } from 'vue';
import { Head, router } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
import AppLayout from '../../layouts/AppLayout.vue';
import OPanel from '../../components/ui/OPanel.vue';
import OButton from '../../components/ui/OButton.vue';
import OField from '../../components/ui/OField.vue';

const props = defineProps<{
    prompts: Array<{ id: number; name: string; version: number | null }>;
    benchmarks: Array<{ id: number; name: string; cases_count: number }>;
    collections?: Array<{ id: number; name: string; benchmarks_count: number }>;
    providers: Array<{ value: string; label: string; model: string | null }>;
    costOptimized: boolean;
    defaultModel: string;
}>();

const { t } = useI18n();

const form = ref({
    prompt_id: '' as number | '',
    benchmark_id: '' as number | '',
    collection_id: '' as number | '',
    mode: 'optimize' as 'evaluate' | 'optimize',
    max_steps: 8,
    target_score: 0.95,
    cost_optimized: props.costOptimized,
    model: props.defaultModel,
});

const submit = () => {
    router.post('/runs', {
        prompt_id: form.value.prompt_id,
        benchmark_id: form.value.benchmark_id || undefined,
        collection_id: form.value.collection_id || undefined,
        mode: form.value.mode,
        max_steps: form.value.max_steps,
        target_score: form.value.target_score,
        cost_optimized: form.value.cost_optimized,
        model: form.value.model || undefined,
    });
};

const canSubmit = computed(() => !! form.value.prompt_id && (!! form.value.benchmark_id || !! form.value.collection_id));
</script>

<template>
    <AppLayout>
        <Head><title>{{ t('runs.new') }}</title></Head>

        <h1 class="font-display text-2xl font-bold tracking-tight text-ink-950">{{ t('runs.new') }}</h1>
        <p class="mt-1 text-sm text-ink-500">{{ t('runs.subtitle') }}</p>

        <form class="mt-8 max-w-xl space-y-6" @submit.prevent="submit">
            <OPanel>
                <div class="space-y-5">
                    <OField :label="t('nav.prompts')" for="prompt" required>
                        <select id="prompt" v-model="form.prompt_id" required class="w-full rounded-lg border border-ink-200 bg-card px-3 py-2 text-sm focus:border-accent-500">
                            <option value="" disabled>—</option>
                            <option v-for="prompt in prompts" :key="prompt.id" :value="prompt.id">{{ prompt.name }} (v{{ prompt.version }})</option>
                        </select>
                    </OField>

                    <OField :label="t('nav.benchmarks')" for="benchmark" :hint="form.collection_id ? '—' : undefined">
                        <select id="benchmark" v-model="form.benchmark_id" :disabled="!!form.collection_id" class="w-full rounded-lg border border-ink-200 bg-card px-3 py-2 text-sm focus:border-accent-500 disabled:opacity-40">
                            <option value="" disabled>—</option>
                            <option v-for="benchmark in benchmarks" :key="benchmark.id" :value="benchmark.id">{{ benchmark.name }} ({{ benchmark.cases_count }})</option>
                        </select>
                    </OField>

                    <OField v-if="collections && collections.length > 0" :label="t('benchmarks.collections.title')" for="collection">
                        <select id="collection" v-model="form.collection_id" :disabled="!!form.benchmark_id" class="w-full rounded-lg border border-ink-200 bg-card px-3 py-2 text-sm focus:border-accent-500 disabled:opacity-40">
                            <option value="" disabled>—</option>
                            <option v-for="collection in collections" :key="collection.id" :value="collection.id">{{ collection.name }} ({{ collection.benchmarks_count }})</option>
                        </select>
                    </OField>
                </div>
            </OPanel>

            <OPanel :title="t('runs.mode.optimize')">
                <div class="grid gap-3 sm:grid-cols-2">
                    <button
                        v-for="mode in ['optimize', 'evaluate']"
                        :key="mode"
                        type="button"
                        class="rounded-lg border px-4 py-3 text-left transition-colors"
                        :class="form.mode === mode ? 'border-accent-500 bg-accent-50' : 'border-ink-200 hover:border-ink-300'"
                        :aria-pressed="form.mode === mode"
                        @click="form.mode = mode as 'optimize' | 'evaluate'"
                    >
                        <span class="block text-sm font-medium text-ink-950">{{ t(`runs.mode.${mode}`) }}</span>
                    </button>
                </div>

                <div v-if="form.mode === 'optimize'" class="mt-5 grid gap-4 sm:grid-cols-2">
                    <OField :label="t('runs.target')" for="target" :hint="'0.1 – 1.0'">
                        <input id="target" v-model.number="form.target_score" type="number" min="0.1" max="1" step="0.05" class="w-full rounded-lg border border-ink-200 px-3 py-2 text-sm focus:border-accent-500" />
                    </OField>
                    <OField label="Max steps" for="steps" :hint="'1 – 20'">
                        <input id="steps" v-model.number="form.max_steps" type="number" min="1" max="20" class="w-full rounded-lg border border-ink-200 px-3 py-2 text-sm focus:border-accent-500" />
                    </OField>
                </div>

                <div v-if="form.mode === 'optimize'" class="mt-5 space-y-4">
                    <OField :label="t('runs.model')" for="model">
                        <input id="model" v-model="form.model" type="text" :placeholder="defaultModel" class="w-full rounded-lg border border-ink-200 bg-card px-3 py-2 text-sm focus:border-accent-500" />
                    </OField>

                    <label class="flex cursor-pointer items-center gap-3 rounded-lg border border-ink-200 bg-card p-3 transition hover:border-ink-300">
                        <input v-model="form.cost_optimized" type="checkbox" class="h-4 w-4 rounded border-ink-300 text-accent-600 focus:ring-accent-500" />
                        <div>
                            <p class="text-sm font-medium text-ink-900">{{ t('runs.costOptimized') }}</p>
                            <p class="text-xs text-ink-500">{{ t('runs.costOptimizedHint') }}</p>
                        </div>
                    </label>
                </div>
            </OPanel>

            <div class="flex justify-end">
                <OButton type="submit" size="lg" :disabled="!canSubmit">▶ {{ t('runs.new') }}</OButton>
            </div>
        </form>
    </AppLayout>
</template>
