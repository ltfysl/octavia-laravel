<script setup lang="ts">
import { ref } from 'vue';
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
import AppLayout from '../../layouts/AppLayout.vue';
import OPanel from '../../components/ui/OPanel.vue';
import OButton from '../../components/ui/OButton.vue';
import OField from '../../components/ui/OField.vue';
import OInput from '../../components/ui/OInput.vue';
import OEmptyState from '../../components/ui/OEmptyState.vue';

const props = defineProps<{
    collections: Array<{ id: number; name: string; description: string | null; benchmarks_count: number }>;
    benchmarks: Array<{ id: number; name: string }>;
}>();

const { t } = useI18n();

const showForm = ref(false);

const form = useForm({
    name: '',
    description: '',
    benchmark_ids: [] as number[],
});

const toggle = (id: number) => {
    const i = form.benchmark_ids.indexOf(id);
    if (i >= 0) form.benchmark_ids.splice(i, 1);
    else form.benchmark_ids.push(id);
};

const create = () => form.post('/collections', {
    onSuccess: () => {
        form.reset();
        showForm.value = false;
    },
});

const destroy = (id: number) => {
    if (confirm(t('benchmarks.collections.deleteConfirm'))) {
        router.delete(`/collections/${id}`);
    }
};
</script>

<template>
    <AppLayout>
        <Head><title>{{ t('benchmarks.collections.title') }}</title></Head>

        <div class="flex items-start justify-between gap-4">
            <div>
                <h1 class="font-display text-2xl font-bold tracking-tight text-ink-950">{{ t('benchmarks.collections.title') }}</h1>
                <p class="mt-1 text-sm text-ink-500">{{ t('benchmarks.collections.subtitle') }}</p>
            </div>
            <OButton @click="showForm = !showForm">+ {{ t('benchmarks.collections.new') }}</OButton>
        </div>

        <!-- Create form -->
        <OPanel v-if="showForm" class="mt-6 max-w-2xl" :title="t('benchmarks.collections.create')">
            <form class="space-y-5" @submit.prevent="create">
                <OField :label="t('benchmarks.wizard.name')" for="col-name" required>
                    <OInput id="col-name" v-model="form.name" required autofocus />
                </OField>
                <OField :label="t('prompts.description')" for="col-desc">
                    <OInput id="col-desc" v-model="form.description" />
                </OField>
                <fieldset>
                    <legend class="mb-2 text-sm font-medium text-ink-700">{{ t('benchmarks.collections.pickBenchmarks') }}</legend>
                    <div class="space-y-1.5">
                        <label v-for="benchmark in benchmarks" :key="benchmark.id" class="flex cursor-pointer items-center gap-2.5 rounded-lg border border-ink-100 px-3 py-2 text-sm hover:bg-paper-100">
                            <input
                                type="checkbox"
                                class="h-4 w-4 rounded border-ink-200 accent-accent-600"
                                :checked="form.benchmark_ids.includes(benchmark.id)"
                                @change="toggle(benchmark.id)"
                            />
                            {{ benchmark.name }}
                        </label>
                    </div>
                    <p v-if="form.errors.benchmark_ids" class="mt-1 text-xs text-rose-450">{{ form.errors.benchmark_ids }}</p>
                </fieldset>
                <div class="flex justify-end gap-3">
                    <OButton variant="ghost" @click="showForm = false">{{ t('common.cancel') }}</OButton>
                    <OButton type="submit" :disabled="form.processing || form.benchmark_ids.length === 0">
                        {{ form.processing ? t('common.saving') : t('benchmarks.collections.create') }}
                    </OButton>
                </div>
            </form>
        </OPanel>

        <OEmptyState v-if="collections.length === 0 && !showForm" class="mt-8" :title="t('benchmarks.collections.empty')" />

        <ul v-else class="mt-8 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
            <li v-for="collection in collections" :key="collection.id">
                <article class="flex h-full flex-col rounded-card border border-ink-100 bg-white p-5 shadow-panel">
                    <h2 class="font-display text-sm font-semibold text-ink-950">{{ collection.name }}</h2>
                    <p class="mt-1.5 flex-1 text-sm text-ink-500">{{ collection.description ?? '—' }}</p>
                    <div class="mt-4 flex items-center justify-between border-t border-ink-100 pt-3 text-xs text-ink-300">
                        <span>{{ t('benchmarks.collections.count', { count: collection.benchmarks_count }) }}</span>
                        <button type="button" class="font-medium text-rose-450 hover:underline" @click="destroy(collection.id)">{{ t('common.delete') }}</button>
                    </div>
                    <Link href="/runs/create" class="mt-3 text-xs font-medium text-accent-600 hover:text-accent-700">▶ {{ t('runs.new') }}</Link>
                </article>
            </li>
        </ul>
    </AppLayout>
</template>
