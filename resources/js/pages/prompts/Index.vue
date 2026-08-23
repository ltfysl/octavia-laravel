<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
import { ref, computed } from 'vue';
import AppLayout from '../../layouts/AppLayout.vue';
import OEmptyState from '../../components/ui/OEmptyState.vue';
import OBadge from '../../components/ui/OBadge.vue';

const props = defineProps<{
    prompts: {
        data: Array<{
            id: number;
            name: string;
            description: string | null;
            visibility: string;
            version: number | null;
            runs_count: number;
            updated_at: string;
        }>;
        links: Array<{ url: string | null; label: string; active: boolean }>;
    };
}>();

const { t, d } = useI18n();

const searchQuery = ref('');
const visibilityFilter = ref<'all' | 'private' | 'public'>('all');

const filtered = computed(() => {
    let data = props.prompts.data;
    if (visibilityFilter.value !== 'all') {
        data = data.filter((p) => p.visibility === visibilityFilter.value);
    }
    if (searchQuery.value.trim()) {
        const q = searchQuery.value.toLowerCase();
        data = data.filter((p) => p.name.toLowerCase().includes(q) || (p.description ?? '').toLowerCase().includes(q));
    }
    return data;
});

const privateCount = computed(() => props.prompts.data.filter((p) => p.visibility === 'private').length);
const publicCount = computed(() => props.prompts.data.filter((p) => p.visibility === 'public').length);

const exportCsv = () => {
    const rows = [['Name','Visibility','Version','Runs','Updated'], ...filtered.value.map((p) => [p.name, p.visibility, String(p.version ?? ''), String(p.runs_count), new Date(p.updated_at).toLocaleDateString()])];
    const csv = rows.map((r) => r.map((v) => `"${String(v).replace(/"/g, '""')}"`).join(',')).join('\n');
    const blob = new Blob([csv], { type: 'text/csv' });
    const url = URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url; a.download = 'prompts.csv'; a.click();
    URL.revokeObjectURL(url);
};
</script>

<template>
    <AppLayout>
        <Head><title>{{ t('prompts.title') }}</title></Head>

        <div class="flex flex-wrap items-start justify-between gap-4">
            <div>
                <div class="flex items-center gap-2">
                    <span class="h-2 w-2 rotate-45 bg-accent-600" aria-hidden="true" />
                    <p class="eyebrow">Specimen library</p>
                    <span class="rounded-full bg-ink-950 px-2 py-0.5 font-mono text-xs font-medium text-white">{{ prompts.data.length }}</span>
                </div>
                <h1 class="display-hero mt-2 text-3xl tracking-tight text-ink-950">{{ t('prompts.title') }}</h1>
                <p class="mt-1 max-w-xl text-sm leading-relaxed text-ink-500">{{ t('prompts.subtitle') }}</p>
            </div>
            <Link href="/prompts/create" class="inline-flex items-center gap-1.5 rounded-md bg-ink-950 px-4 py-2 text-sm font-medium text-white transition-colors hover:bg-ink-700">
                <span aria-hidden="true">+</span> {{ t('prompts.new') }}
            </Link>
        </div>
        <!-- Metrics — denser than test, but clearer -->
        <div class="mt-6 grid grid-cols-3 gap-3">
            <div class="rounded-card border border-ink-100 bg-white p-4">
                <p class="eyebrow">Total</p>
                <p class="display-hero mt-1 text-2xl tracking-tight text-ink-950">{{ prompts.data.length }}</p>
                <p class="mt-1 text-xs text-ink-400">specimens</p>
            </div>
            <div class="rounded-card border border-ink-100 bg-white p-4">
                <p class="eyebrow">Private</p>
                <p class="display-hero mt-1 text-2xl tracking-tight text-ink-950">{{ privateCount }}</p>
                <p class="mt-1 text-xs text-ink-400">only you</p>
            </div>
            <div class="rounded-card border border-ink-100 bg-white p-4">
                <p class="eyebrow">Public</p>
                <p class="display-hero mt-1 text-2xl tracking-tight text-ink-950">{{ publicCount }}</p>
                <p class="mt-1 text-xs text-ink-400">marketplace</p>
            </div>
        </div>

        <!-- Toolbar — search + filter + export -->
        <div class="mt-6 flex flex-wrap items-center gap-3">
            <div class="flex flex-1 min-w-[220px] items-center gap-2 rounded-full border border-ink-200 bg-white px-3 py-2 shadow-sm">
                <svg class="h-4 w-4 text-ink-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" /></svg>
                <input v-model="searchQuery" placeholder="Search prompts…" class="flex-1 bg-transparent text-sm outline-none placeholder:text-ink-400" />
                <span v-if="searchQuery" class="cursor-pointer text-ink-400 hover:text-ink-600" @click="searchQuery=''">✕</span>
            </div>
            <div class="flex items-center gap-1 rounded-full bg-ink-100 p-1">
                <button type="button" class="rounded-full px-3 py-1.5 text-xs font-medium transition" :class="visibilityFilter === 'all' ? 'bg-white text-ink-900 shadow-sm' : 'text-ink-500 hover:text-ink-700'" @click="visibilityFilter = 'all'">All</button>
                <button type="button" class="rounded-full px-3 py-1.5 text-xs font-medium transition" :class="visibilityFilter === 'private' ? 'bg-white text-ink-900 shadow-sm' : 'text-ink-500 hover:text-ink-700'" @click="visibilityFilter = 'private'">Private</button>
                <button type="button" class="rounded-full px-3 py-1.5 text-xs font-medium transition" :class="visibilityFilter === 'public' ? 'bg-white text-ink-900 shadow-sm' : 'text-ink-500 hover:text-ink-700'" @click="visibilityFilter = 'public'">Public</button>
            </div>
            <button type="button" class="inline-flex items-center gap-1.5 rounded-full border border-ink-200 bg-white px-4 py-2 text-sm font-medium text-ink-700 shadow-sm transition hover:bg-paper-100 hover:text-ink-900" @click="exportCsv">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3" /></svg>
                Export CSV
            </button>
        </div>

        <OEmptyState
            v-if="prompts.data.length === 0"
            class="mt-8"
            :title="t('prompts.empty')"
        >
            <template #action>
                <Link href="/prompts/create" class="rounded-lg bg-ink-950 px-4 py-2 text-sm font-medium text-white hover:bg-ink-700">{{ t('prompts.new') }}</Link>
            </template>
        </OEmptyState>
        <div v-else-if="filtered.length === 0" class="mt-8 rounded-card border border-dashed border-ink-200 bg-white p-8 text-center">
            <p class="text-sm font-medium text-ink-900">No prompts match “{{ searchQuery }}”</p>
            <p class="mt-1 text-sm text-ink-500">Try a different search or clear filters.</p>
            <button type="button" class="mt-4 rounded-full bg-ink-950 px-4 py-1.5 text-sm font-medium text-white hover:bg-ink-900" @click="searchQuery=''; visibilityFilter='all'">Clear filters</button>
        </div>
        <ul v-else class="mt-8 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
            <li v-for="prompt in filtered" :key="prompt.id">
                <Link :href="`/prompts/${prompt.id}`" class="group flex h-full flex-col overflow-hidden rounded-card border border-ink-100 bg-white card-lift">
                    <div class="h-1 w-full" :class="prompt.visibility === 'public' ? 'bg-accent-600' : 'bg-ink-900'" aria-hidden="true" />
                    <div class="flex h-full flex-col p-5">
                        <div class="flex items-start justify-between gap-2">
                            <h2 class="line-clamp-2 font-display text-sm font-semibold leading-tight text-ink-950 group-hover:text-accent-700">{{ prompt.name }}</h2>
                            <OBadge :tone="prompt.visibility === 'public' ? 'accent' : 'neutral'" class="shrink-0">{{ t(`prompts.visibility.${prompt.visibility}`) }}</OBadge>
                        </div>
                        <p class="mt-2 line-clamp-2 flex-1 text-sm leading-relaxed text-ink-500">{{ prompt.description ?? '—' }}</p>
                        <div class="mt-4 flex items-center justify-between border-t border-dashed border-ink-100 pt-3">
                            <span class="inline-flex items-center gap-1.5 rounded-md border border-ink-100 bg-paper-100 px-2 py-1 font-mono text-xs font-medium text-ink-700">
                                <span class="h-1.5 w-1.5 rotate-45 bg-ink-300" aria-hidden="true" /> v{{ prompt.version ?? '—' }}
                            </span>
                            <span class="text-right font-mono text-xs text-ink-300">{{ t('prompts.runsCount', { count: prompt.runs_count }) }} · {{ d(new Date(prompt.updated_at), 'short') }}</span>
                        </div>
                    </div>
                </Link>
            </li>
        </ul>

        <!-- Pagination -->
        <nav v-if="prompts.links.length > 3" class="mt-6 flex justify-center gap-1" aria-label="Pagination">
            <component
                :is="link.url ? Link : 'span'"
                v-for="(link, i) in prompts.links"
                :key="i"
                :href="link.url ?? '#'"
                class="rounded-lg px-3 py-1.5 text-sm"
                :class="link.active ? 'bg-ink-950 text-white' : link.url ? 'text-ink-500 hover:bg-paper-200' : 'text-ink-200'"
                v-html="link.label"
            />
        </nav>
    </AppLayout>
</template>
