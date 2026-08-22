<script setup lang="ts">
import { ref } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
import AppLayout from '../../layouts/AppLayout.vue';
import OPanel from '../../components/ui/OPanel.vue';
import OButton from '../../components/ui/OButton.vue';
import OBadge from '../../components/ui/OBadge.vue';

const props = defineProps<{
    benchmark: {
        id: number;
        name: string;
        description: string | null;
        category: string;
        visibility: string;
        version: number;
        cases: Array<{
            id: number;
            title: string;
            input: string;
            weight: number;
            criteria: Array<{ id: number; type: string; label: string; config: Record<string, unknown> }>;
        }>;
    };
    prompts: Array<{ id: number; name: string }>;
}>();

const { t } = useI18n();

const promptId = ref<number | ''>('');

const startRun = (mode: 'evaluate' | 'optimize') => {
    if (! promptId.value) return;
    router.post('/runs', { prompt_id: promptId.value, benchmark_id: props.benchmark.id, mode });
};

const publish = () => {
    router.post('/marketplace/publish', {
        item_type: 'benchmark',
        item_id: props.benchmark.id,
        summary: props.benchmark.description ?? '',
    });
};

const destroy = () => {
    if (confirm(t('benchmarks.deleteConfirm'))) {
        router.delete(`/benchmarks/${props.benchmark.id}`);
    }
};
</script>

<template>
    <AppLayout>
        <Head><title>{{ benchmark.name }}</title></Head>

        <div class="flex flex-wrap items-start justify-between gap-4">
            <div>
                <div class="flex items-center gap-3">
                    <h1 class="font-display text-2xl font-bold tracking-tight text-ink-950">{{ benchmark.name }}</h1>
                    <OBadge tone="violet">{{ t(`benchmarks.categories.${benchmark.category}`) }}</OBadge>
                    <span class="font-mono text-xs text-ink-300">v{{ benchmark.version }}</span>
                </div>
                <p v-if="benchmark.description" class="mt-1 max-w-2xl text-sm text-ink-500">{{ benchmark.description }}</p>
            </div>
            <div class="flex items-center gap-2">
                <OButton variant="secondary" @click="publish">{{ t('marketplace.publish') }}</OButton>
                <OButton variant="ghost" @click="destroy">{{ t('benchmarks.delete') }}</OButton>
                <Link href="/benchmarks" class="self-center text-sm text-ink-500 hover:text-ink-900">{{ t('common.back') }}</Link>
            </div>
        </div>

        <!-- Run panel -->
        <OPanel class="mt-6" :title="t('benchmarks.run')">
            <div class="flex flex-wrap items-end gap-3">
                <select
                    v-model="promptId"
                    class="w-64 rounded-lg border border-ink-200 bg-white px-3 py-2 text-sm focus:border-violet-500"
                    :aria-label="t('nav.prompts')"
                >
                    <option value="" disabled>—</option>
                    <option v-for="prompt in prompts" :key="prompt.id" :value="prompt.id">{{ prompt.name }}</option>
                </select>
                <OButton :disabled="!promptId" @click="startRun('optimize')">{{ t('runs.mode.optimize') }}</OButton>
                <OButton variant="secondary" :disabled="!promptId" @click="startRun('evaluate')">{{ t('runs.mode.evaluate') }}</OButton>
            </div>
        </OPanel>

        <!-- Cases -->
        <section class="mt-8">
            <h2 class="mb-3 text-sm font-semibold uppercase tracking-wide text-ink-300">
                {{ t('benchmarks.cases') }} · {{ benchmark.cases.length }}
            </h2>
            <div class="space-y-4">
                <OPanel v-for="(c, i) in benchmark.cases" :key="c.id">
                    <template #actions>
                        <span class="font-mono text-xs text-ink-300">#{{ i + 1 }}</span>
                    </template>
                    <p class="text-sm font-medium text-ink-950">{{ c.title }}</p>
                    <pre class="mt-2 whitespace-pre-wrap rounded-lg bg-paper-100 p-3 font-mono text-xs leading-relaxed text-ink-700">{{ c.input }}</pre>
                    <ul class="mt-3 space-y-1.5">
                        <li v-for="cr in c.criteria" :key="cr.id" class="flex items-center gap-2 text-sm text-ink-700">
                            <svg class="h-4 w-4 shrink-0 text-violet-500" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" /></svg>
                            {{ cr.label }}
                            <span class="ml-auto font-mono text-[10px] uppercase text-ink-300">{{ cr.type }}</span>
                        </li>
                    </ul>
                </OPanel>
            </div>
        </section>
    </AppLayout>
</template>
