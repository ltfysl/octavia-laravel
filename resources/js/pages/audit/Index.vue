<script setup lang="ts">
import { Head, router } from '@inertiajs/vue3';
import { ref, watch } from 'vue';
import AppLayout from '../../layouts/AppLayout.vue';
import OPanel from '../../components/ui/OPanel.vue';
import OBadge from '../../components/ui/OBadge.vue';

const props = defineProps<{
    logs: {
        data: Array<{
            id: number;
            action: string;
            category: string;
            entity_type: string | null;
            entity_name: string | null;
            description: string;
            severity: string;
            created_at: string;
        }>;
        links: Array<{ url: string | null; label: string; active: boolean }>;
    };
    filters: { category?: string | null; severity?: string | null; search?: string | null };
}>();

const severityTone: Record<string, 'mint' | 'amber' | 'rose' | 'neutral'> = {
    info: 'neutral',
    success: 'mint',
    warning: 'amber',
    error: 'rose',
};

const search = ref(props.filters.search ?? '');
const severity = ref<'all' | 'info' | 'success' | 'warning' | 'error'>(
    (props.filters.severity as any) ?? 'all',
);

// Server-side filtering — controller already supports category/severity/search
let debounce: number | undefined;
const applyFilters = () => {
    router.get(
        '/audit',
        {
            ...(search.value ? { search: search.value } : {}),
            ...(severity.value !== 'all' ? { severity: severity.value } : {}),
        },
        { preserveState: true, preserveScroll: true, replace: true },
    );
};

watch(search, () => {
    window.clearTimeout(debounce);
    debounce = window.setTimeout(applyFilters, 300);
});

watch(severity, applyFilters);
</script>

<template>
    <AppLayout>
        <Head><title>Audit Log</title></Head>

        <div class="flex flex-wrap items-start justify-between gap-4">
            <div>
                <div class="flex items-center gap-2">
                    <span class="h-2 w-2 rotate-45 bg-accent-600" aria-hidden="true" />
                    <p class="eyebrow">Activity trail</p>
                </div>
                <h1 class="display-hero mt-2 text-3xl tracking-tight text-ink-950">Audit Log</h1>
                <p class="mt-1 max-w-xl text-sm leading-relaxed text-ink-500">Who did what — every action recorded with category and severity.</p>
            </div>
        </div>

        <!-- Filters -->
        <div class="mt-6 flex flex-wrap items-center gap-3">
            <div class="flex flex-1 min-w-[220px] items-center gap-2 rounded-full border border-ink-200 bg-card px-3 py-2 shadow-sm">
                <svg class="h-4 w-4 text-ink-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" /></svg>
                <input v-model="search" placeholder="Search actions…" class="flex-1 bg-transparent text-sm outline-none placeholder:text-ink-400" />
            </div>
            <div class="flex items-center gap-1 rounded-full bg-ink-100 p-1">
                <button v-for="s in ['all', 'info', 'success', 'warning', 'error']" :key="s" type="button"
                    class="rounded-full px-3 py-1.5 text-xs font-medium capitalize transition"
                    :class="severity === s ? 'bg-card text-ink-900 shadow-sm' : 'text-ink-500 hover:text-ink-700'"
                    @click="severity = s as any"
                >{{ s }}</button>
            </div>
        </div>

        <!-- Log list -->
        <OPanel class="!p-0 mt-6 overflow-hidden">
            <ul class="divide-y divide-ink-100">
                <li v-for="log in logs.data" :key="log.id" class="flex items-start gap-3 px-5 py-3 transition-colors hover:bg-paper-50">
                    <span
                        class="mt-1 h-2 w-2 shrink-0 rounded-full"
                        :class="{ 'bg-mint-500': log.severity === 'success', 'bg-amber-450': log.severity === 'warning', 'bg-rose-450': log.severity === 'error', 'bg-ink-300': log.severity === 'info' }"
                        aria-hidden="true"
                    />
                    <div class="min-w-0 flex-1">
                        <div class="flex flex-wrap items-center gap-2">
                            <span class="font-mono text-xs font-semibold text-ink-950">{{ log.action }}</span>
                            <OBadge :tone="severityTone[log.severity] ?? 'neutral'" class="!px-1.5 !py-0 text-[11px]">{{ log.severity }}</OBadge>
                            <span class="rounded-full bg-paper-100 px-2 py-0.5 font-mono text-[11px] text-ink-500">{{ log.category }}</span>
                            <span v-if="log.entity_name" class="truncate text-xs text-ink-400">{{ log.entity_name }}</span>
                        </div>
                        <p class="mt-0.5 truncate text-sm text-ink-600">{{ log.description }}</p>
                    </div>
                </li>
            </ul>
            <p v-if="logs.data.length === 0" class="px-5 py-12 text-center text-sm text-ink-300">— no audit entries yet —</p>
        </OPanel>

        <!-- Pagination -->
        <nav v-if="logs.links.length > 3" class="mt-6 flex justify-center gap-1" aria-label="Pagination">
            <component
                :is="link.url ? 'a' : 'span'"
                v-for="(link, i) in logs.links"
                :key="i"
                :href="link.url ?? '#'"
                class="rounded-lg px-3 py-1.5 text-sm"
                :class="link.active ? 'bg-ink-950 text-white' : link.url ? 'text-ink-500 hover:bg-paper-200' : 'text-ink-200'"
                v-html="link.label"
            />
        </nav>
    </AppLayout>
</template>
