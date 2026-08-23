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

        <div class="flex flex-wrap items-start justify-between gap-4">
            <div>
                <div class="flex items-center gap-2">
                    <span class="h-2 w-2 rotate-45 bg-accent-600" aria-hidden="true" />
                    <p class="eyebrow">Test suites</p>
                    <span class="rounded-full bg-ink-950 px-2 py-0.5 font-mono text-xs font-medium text-white">{{ benchmarks.length }}</span>
                </div>
                <h1 class="display-hero mt-2 text-3xl tracking-tight text-ink-950">{{ t('benchmarks.title') }}</h1>
                <p class="mt-1 max-w-xl text-sm leading-relaxed text-ink-500">{{ t('benchmarks.subtitle') }}</p>
            </div>
            <Link href="/benchmarks/wizard" class="inline-flex items-center gap-1.5 rounded-md bg-ink-950 px-4 py-2 text-sm font-medium text-white transition-colors hover:bg-ink-700">
                <span aria-hidden="true">+</span> {{ t('benchmarks.new') }}
            </Link>
        </div>

        <OEmptyState v-if="benchmarks.length === 0" class="mt-8" :title="t('benchmarks.empty')">
            <template #action>
                <Link href="/benchmarks/wizard" class="rounded-lg bg-ink-950 px-4 py-2 text-sm font-medium text-white hover:bg-ink-700">{{ t('benchmarks.wizard.title') }}</Link>
            </template>
        </OEmptyState>

        <ul v-else class="mt-8 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
            <li v-for="benchmark in benchmarks" :key="benchmark.id">
                <Link :href="`/benchmarks/${benchmark.id}`" class="group flex h-full flex-col overflow-hidden rounded-card border border-ink-100 bg-card card-lift">
                    <div class="h-1 w-full bg-ink-900" aria-hidden="true" />
                    <div class="flex h-full flex-col p-5">
                        <div class="flex items-start justify-between gap-2">
                            <h2 class="line-clamp-2 font-display text-sm font-semibold leading-tight text-ink-950 group-hover:text-accent-700">{{ benchmark.name }}</h2>
                            <OBadge tone="accent" class="shrink-0">{{ t(`benchmarks.categories.${benchmark.category}`) }}</OBadge>
                        </div>
                        <p class="mt-2 line-clamp-2 flex-1 text-sm leading-relaxed text-ink-500">{{ benchmark.description ?? '—' }}</p>
                        <div class="mt-4 flex items-center justify-between border-t border-dashed border-ink-100 pt-3">
                            <span class="inline-flex items-center gap-1.5 rounded-md bg-paper-100 px-2 py-1 font-mono text-xs text-ink-600">{{ t('benchmarks.caseCount', { count: benchmark.cases_count }) }}</span>
                            <span class="font-mono text-xs font-medium text-ink-300">v{{ benchmark.version }}</span>
                        </div>
                    </div>
                </Link>
            </li>
        </ul>
    </AppLayout>
</template>
