<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
import { ref, computed } from 'vue';
import AppLayout from '../../layouts/AppLayout.vue';
import OEmptyState from '../../components/ui/OEmptyState.vue';
import OBadge from '../../components/ui/OBadge.vue';

const props = defineProps<{
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

const { t, d } = useI18n();

const searchQuery = ref('');
const visibilityFilter = ref<'all' | 'private' | 'public'>('all');

const filtered = computed(() => {
    let data = props.benchmarks;
    if (visibilityFilter.value !== 'all') {
        data = data.filter((b) => b.visibility === visibilityFilter.value);
    }
    if (searchQuery.value.trim()) {
        const q = searchQuery.value.toLowerCase();
        data = data.filter((b) => b.name.toLowerCase().includes(q) || (b.description ?? '').toLowerCase().includes(q));
    }
    return data;
});

const privateCount = computed(() => props.benchmarks.filter((b) => b.visibility === 'private').length);
const publicCount = computed(() => props.benchmarks.filter((b) => b.visibility === 'public').length);
const totalCases = computed(() => props.benchmarks.reduce((sum, b) => sum + b.cases_count, 0));

const exportCsv = () => {
    const rows = [
        ['Name', 'Category', 'Visibility', 'Cases', 'Version', 'Updated'],
        ...filtered.value.map((b) => [
            b.name,
            b.category,
            b.visibility,
            String(b.cases_count),
            String(b.version),
            new Date(b.updated_at).toLocaleDateString(),
        ]),
    ];
    const csv = rows.map((r) => r.map((v) => `"${String(v).replace(/"/g, '""')}"`).join(',')).join('\n');
    const blob = new Blob([csv], { type: 'text/csv' });
    const url = URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = 'benchmarks.csv';
    a.click();
    URL.revokeObjectURL(url);
};
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

        <!-- Metrics — parity with prompts library -->
        <div class="mt-6 grid grid-cols-2 gap-3 sm:grid-cols-4">
            <div class="rounded-card border border-ink-100 bg-card p-4">
                <p class="eyebrow">Total</p>
                <p class="display-hero mt-1 text-2xl tracking-tight text-ink-950">{{ benchmarks.length }}</p>
                <p class="mt-1 text-xs text-ink-400">suites</p>
            </div>
            <div class="rounded-card border border-ink-100 bg-card p-4">
                <p class="eyebrow">Cases</p>
                <p class="display-hero mt-1 text-2xl tracking-tight text-ink-950">{{ totalCases }}</p>
                <p class="mt-1 text-xs text-ink-400">test cases</p>
            </div>
            <div class="rounded-card border border-ink-100 bg-card p-4">
                <p class="eyebrow">Private</p>
                <p class="display-hero mt-1 text-2xl tracking-tight text-ink-950">{{ privateCount }}</p>
                <p class="mt-1 text-xs text-ink-400">only you</p>
            </div>
            <div class="rounded-card border border-ink-100 bg-card p-4">
                <p class="eyebrow">Public</p>
                <p class="display-hero mt-1 text-2xl tracking-tight text-ink-950">{{ publicCount }}</p>
                <p class="mt-1 text-xs text-ink-400">marketplace</p>
            </div>
        </div>

        <!-- Toolbar — search + filter + export -->
        <div class="mt-6 flex flex-wrap items-center gap-3">
            <div class="flex min-w-[220px] flex-1 items-center gap-2 rounded-full border border-ink-200 bg-card px-3 py-2 shadow-sm">
                <svg class="h-4 w-4 text-ink-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" /></svg>
                <input v-model="searchQuery" placeholder="Search benchmarks…" class="flex-1 bg-transparent text-sm outline-none placeholder:text-ink-400" />
                <span v-if="searchQuery" class="cursor-pointer text-ink-400 hover:text-ink-600" @click="searchQuery = ''">✕</span>
            </div>
            <div class="flex items-center gap-1 rounded-full bg-ink-100 p-1">
                <button v-for="vf in ['all', 'private', 'public']" :key="vf" type="button"
                    class="rounded-full px-3 py-1.5 text-xs font-medium capitalize transition"
                    :class="visibilityFilter === vf ? 'bg-card text-ink-900 shadow-sm' : 'text-ink-500 hover:text-ink-700'"
                    @click="visibilityFilter = vf as 'all' | 'private' | 'public'"
                >{{ vf }}</button>
            </div>
            <button type="button" class="inline-flex items-center gap-1.5 rounded-full border border-ink-200 bg-card px-4 py-2 text-sm font-medium text-ink-700 shadow-sm transition hover:bg-paper-100 hover:text-ink-900" @click="exportCsv">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3" /></svg>
                Export CSV
            </button>
        </div>

        <OEmptyState v-if="benchmarks.length === 0" class="mt-8" :title="t('benchmarks.empty')">
            <template #action>
                <Link href="/benchmarks/wizard" class="rounded-lg bg-ink-950 px-4 py-2 text-sm font-medium text-white hover:bg-ink-700">{{ t('benchmarks.wizard.title') }}</Link>
            </template>
        </OEmptyState>

        <div v-else-if="filtered.length === 0" class="mt-8 rounded-card border border-dashed border-ink-200 bg-card p-8 text-center">
            <p class="text-sm font-medium text-ink-900">No benchmarks match “{{ searchQuery }}”</p>
            <p class="mt-1 text-sm text-ink-500">Try a different search or clear filters.</p>
            <button type="button" class="mt-4 rounded-full bg-ink-950 px-4 py-1.5 text-sm font-medium text-white hover:bg-ink-700" @click="searchQuery = ''; visibilityFilter = 'all'">{{ t('benchmarks.clearFilters') }}</button>
        </div>
        <ul v-else class="mt-8 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
            <li v-for="benchmark in filtered" :key="benchmark.id">
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
                            <span class="font-mono text-xs text-ink-300">v{{ benchmark.version }} · {{ d(new Date(benchmark.updated_at), 'short') }}</span>
                        </div>
                    </div>
                </Link>
            </li>
        </ul>
    </AppLayout>
</template>
