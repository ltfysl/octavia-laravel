<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
import AppLayout from '../../layouts/AppLayout.vue';
import OEmptyState from '../../components/ui/OEmptyState.vue';
import OBadge from '../../components/ui/OBadge.vue';

defineProps<{
    benchmarks: Array<{
        id: number;
        name: string;
        description: string | null;
        category: string;
        visibility: string;
        cases_count: number;
        version: number;
        updated_at: string;
    }>;
}>();

const { t } = useI18n();
</script>

<template>
    <AppLayout>
        <Head><title>{{ t('benchmarks.title') }}</title></Head>

        <div class="flex items-start justify-between gap-4">
            <div>
                <h1 class="font-display text-2xl font-bold tracking-tight text-ink-950">{{ t('benchmarks.title') }}</h1>
                <p class="mt-1 text-sm text-ink-500">{{ t('benchmarks.subtitle') }}</p>
            </div>
            <Link href="/benchmarks/wizard" class="rounded-lg bg-ink-950 px-4 py-2 text-sm font-medium text-white transition-colors hover:bg-ink-700">
                + {{ t('benchmarks.new') }}
            </Link>
        </div>

        <OEmptyState v-if="benchmarks.length === 0" class="mt-8" :title="t('benchmarks.empty')">
            <template #action>
                <Link href="/benchmarks/wizard" class="rounded-lg bg-ink-950 px-4 py-2 text-sm font-medium text-white hover:bg-ink-700">{{ t('benchmarks.wizard.title') }}</Link>
            </template>
        </OEmptyState>

        <ul v-else class="mt-8 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
            <li v-for="benchmark in benchmarks" :key="benchmark.id">
                <Link :href="`/benchmarks/${benchmark.id}`" class="group flex h-full flex-col rounded-card border border-ink-100 bg-white p-5 shadow-panel transition-colors hover:border-accent-200">
                    <div class="flex items-center justify-between gap-2">
                        <h2 class="truncate font-display text-sm font-semibold text-ink-950 group-hover:text-accent-700">{{ benchmark.name }}</h2>
                        <OBadge tone="accent">{{ t(`benchmarks.categories.${benchmark.category}`) }}</OBadge>
                    </div>
                    <p class="mt-2 line-clamp-2 flex-1 text-sm text-ink-500">{{ benchmark.description ?? '—' }}</p>
                    <div class="mt-4 flex items-center justify-between border-t border-ink-100 pt-3 text-xs text-ink-300">
                        <span>{{ t('benchmarks.caseCount', { count: benchmark.cases_count }) }}</span>
                        <span class="font-mono">v{{ benchmark.version }}</span>
                    </div>
                </Link>
            </li>
        </ul>
    </AppLayout>
</template>
