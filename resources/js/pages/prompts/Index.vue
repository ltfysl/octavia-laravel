<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
import AppLayout from '../../layouts/AppLayout.vue';
import OEmptyState from '../../components/ui/OEmptyState.vue';
import OBadge from '../../components/ui/OBadge.vue';

defineProps<{
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
</script>

<template>
    <AppLayout>
        <Head><title>{{ t('prompts.title') }}</title></Head>

        <div class="flex items-start justify-between gap-4">
            <div>
                <h1 class="font-display text-2xl font-bold tracking-tight text-ink-950">{{ t('prompts.title') }}</h1>
                <p class="mt-1 text-sm text-ink-500">{{ t('prompts.subtitle') }}</p>
            </div>
            <Link href="/prompts/create" class="rounded-lg bg-ink-950 px-4 py-2 text-sm font-medium text-white transition-colors hover:bg-ink-700">
                + {{ t('prompts.new') }}
            </Link>
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

        <ul v-else class="mt-8 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
            <li v-for="prompt in prompts.data" :key="prompt.id">
                <Link :href="`/prompts/${prompt.id}`" class="group flex h-full flex-col rounded-card border border-ink-100 bg-white p-5 shadow-panel transition-colors hover:border-accent-200">
                    <div class="flex items-center justify-between gap-2">
                        <h2 class="truncate font-display text-sm font-semibold text-ink-950 group-hover:text-accent-700">{{ prompt.name }}</h2>
                        <OBadge :tone="prompt.visibility === 'public' ? 'accent' : 'neutral'">{{ t(`prompts.visibility.${prompt.visibility}`) }}</OBadge>
                    </div>
                    <p class="mt-2 line-clamp-2 flex-1 text-sm text-ink-500">{{ prompt.description ?? '—' }}</p>
                    <div class="mt-4 flex items-center justify-between border-t border-ink-100 pt-3 text-xs text-ink-300">
                        <span class="font-mono">v{{ prompt.version ?? '—' }}</span>
                        <span>{{ t('prompts.runsCount', { count: prompt.runs_count }) }} · {{ d(new Date(prompt.updated_at), 'short') }}</span>
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
