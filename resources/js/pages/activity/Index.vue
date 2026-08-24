<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
import AppLayout from '../../layouts/AppLayout.vue';
import OPanel from '../../components/ui/OPanel.vue';
import OEmptyState from '../../components/ui/OEmptyState.vue';

interface ActivityItem {
    id: string;
    type: string;
    title: string;
    description: string;
    timestamp: string;
    status?: 'success' | 'error' | 'warning' | 'info';
    relatedId?: number;
    category?: string;
}

const props = defineProps<{ items: ActivityItem[] }>();

const { t } = useI18n();

const statusDot = (status?: string) => {
    switch (status) {
        case 'success':
            return 'bg-mint-500';
        case 'error':
            return 'bg-rose-500';
        case 'warning':
            return 'bg-amber-500';
        default:
            return 'bg-accent-500';
    }
};

const typeIcon = (type: string) => {
    switch (type) {
        case 'evolution_completed':
            return '✓';
        case 'evolution_failed':
            return '✕';
        case 'evolution_started':
            return '⟳';
        case 'prompt_saved':
            return 'P';
        case 'benchmark_created':
            return 'B';
        case 'notification':
            return 'N';
        default:
            return '•';
    }
};
</script>

<template>
    <AppLayout>
        <Head><title>{{ t('activity.title') }}</title></Head>

        <div class="mb-6">
            <h1 class="font-display text-3xl font-bold tracking-tight text-ink-950">{{ t('activity.title') }}</h1>
            <p class="mt-1 text-sm text-ink-500">{{ t('activity.subtitle') }}</p>
        </div>

        <OEmptyState
            v-if="items.length === 0"
            :title="t('activity.empty')"
            :description="t('activity.emptyHint')"
        />

        <div v-else class="space-y-3">
            <OPanel
                v-for="item in items"
                :key="item.id"
                :title="item.title"
                :subtitle="new Date(item.timestamp).toLocaleString()"
            >
                <div class="flex items-start gap-3">
                    <span
                        class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-paper-100 text-sm font-semibold text-ink-700"
                    >
                        {{ typeIcon(item.type) }}
                    </span>
                    <div class="min-w-0 flex-1">
                        <p class="text-sm text-ink-700">{{ item.description }}</p>
                        <div class="mt-2 flex items-center gap-2 text-xs text-ink-400">
                            <span class="h-2 w-2 rounded-full" :class="statusDot(item.status)" />
                            <span class="uppercase tracking-wider">{{ item.type }}</span>
                            <span
                                v-if="item.category"
                                class="rounded-full border border-ink-200 bg-paper-50 px-2 py-0.5 text-ink-500"
                            >{{ item.category }}</span>
                        </div>
                    </div>
                </div>
            </OPanel>
        </div>
    </AppLayout>
</template>
