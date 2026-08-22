<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
import AppLayout from '../layouts/AppLayout.vue';

defineProps<{
    query: string;
    results: {
        prompts: Array<{ type: string; id: number; title: string; subtitle: string | null; url: string }>;
        benchmarks: Array<{ type: string; id: number; title: string; subtitle: string | null; url: string }>;
    };
}>();

const { t } = useI18n();

const hasResults = (results: unknown) => results !== null && Array.isArray(results) && (results as unknown[]).length > 0;
</script>

<template>
    <AppLayout>
        <Head><title>{{ t('common.search') }} — {{ query }}</title></Head>

        <h1 class="font-display text-2xl font-bold tracking-tight text-ink-950">{{ t('common.search') }}: “{{ query }}”</h1>
        <p class="mt-1 text-sm text-ink-500">{{ t('search.subtitle') }}</p>

        <!-- Prompts -->
        <section v-if="hasResults(results.prompts)" class="mt-8">
            <h2 class="mb-3 text-sm font-semibold uppercase tracking-wide text-ink-300">{{ t('prompts.title') }}</h2>
            <ul class="space-y-3">
                <li v-for="item in results.prompts" :key="'p' + item.id">
                    <Link :href="item.url" class="block rounded-card border border-ink-100 bg-white p-4 shadow-panel transition-colors hover:border-accent-200">
                        <span class="font-display text-sm font-semibold text-ink-950">{{ item.title }}</span>
                        <span v-if="item.subtitle" class="mt-1 block text-xs text-ink-500">{{ item.subtitle }}</span>
                    </Link>
                </li>
            </ul>
        </section>

        <!-- Benchmarks -->
        <section v-if="hasResults(results.benchmarks)" class="mt-8">
            <h2 class="mb-3 text-sm font-semibold uppercase tracking-wide text-ink-300">{{ t('benchmarks.title') }}</h2>
            <ul class="space-y-3">
                <li v-for="item in results.benchmarks" :key="'b' + item.id">
                    <Link :href="item.url" class="block rounded-card border border-ink-100 bg-white p-4 shadow-panel transition-colors hover:border-accent-200">
                        <span class="font-display text-sm font-semibold text-ink-950">{{ item.title }}</span>
                        <span v-if="item.subtitle" class="mt-1 block text-xs text-ink-500">{{ item.subtitle }}</span>
                    </Link>
                </li>
            </ul>
        </section>

        <OEmptyState
            v-if="!hasResults(results.prompts) && !hasResults(results.benchmarks)"
            class="mt-8"
            :title="t('search.noResults')"
        />
    </AppLayout>
</template>
