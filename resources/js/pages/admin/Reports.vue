<script setup lang="ts">
import { Head, router } from '@inertiajs/vue3';
import { ref } from 'vue';
import AppLayout from '../../layouts/AppLayout.vue';
import OBadge from '../../components/ui/OBadge.vue';
import OEmptyState from '../../components/ui/OEmptyState.vue';

defineProps<{
    reports: Array<{
        id: number;
        reason: string;
        message: string | null;
        reporter: string | null;
        item_id: number | null;
        item_title: string | null;
        item_listed: boolean;
        publisher: string | null;
        created_at: string | null;
    }>;
}>();

const busyId = ref<number | null>(null);

const resolve = (id: number, action: 'dismiss' | 'unlist') => {
    busyId.value = id;
    router.post(`/admin/reports/${id}/resolve/${action}`, {}, {
        onFinish: () => {
            busyId.value = null;
        },
    });
};
</script>

<template>
    <AppLayout>
        <Head><title>Admin · Reports</title><meta name="robots" content="noindex" /></Head>

        <h1 class="font-display text-2xl font-bold tracking-tight text-ink-950">Reports</h1>
        <p class="mt-1 text-sm text-ink-500">Open abuse reports on marketplace listings. Resolve dismisses the report; unlist also hides the item.</p>

        <OEmptyState v-if="reports.length === 0" class="mt-8" title="No open reports" />

        <ul v-else class="mt-8 space-y-4">
            <li v-for="report in reports" :key="report.id">
                <article class="rounded-card border border-ink-100 bg-card p-5 shadow-panel">
                    <div class="flex flex-wrap items-start justify-between gap-3">
                        <div class="min-w-0">
                            <div class="flex flex-wrap items-center gap-2">
                                <OBadge tone="rose">{{ report.reason }}</OBadge>
                                <span class="truncate font-display text-sm font-semibold text-ink-950">{{ report.item_title }}</span>
                                <OBadge :tone="report.item_listed ? 'mint' : 'neutral'">{{ report.item_listed ? 'listed' : 'unlisted' }}</OBadge>
                            </div>
                            <p v-if="report.message" class="mt-2 text-sm text-ink-500">“{{ report.message }}”</p>
                            <p class="mt-1.5 text-xs text-ink-300">
                                by {{ report.reporter ?? '—' }} · publisher {{ report.publisher ?? '—' }} · {{ report.created_at?.slice(0, 10) }}
                            </p>
                        </div>
                        <div class="flex shrink-0 gap-2">
                            <button
                                type="button"
                                class="rounded-lg border border-ink-200 px-3 py-1.5 text-xs font-medium text-ink-700 transition-colors hover:bg-paper-100"
                                :disabled="busyId === report.id"
                                @click="resolve(report.id, 'dismiss')"
                            >
                                Dismiss
                            </button>
                            <button
                                type="button"
                                class="rounded-lg bg-rose-450 px-3 py-1.5 text-xs font-medium text-white transition-opacity hover:opacity-90 disabled:opacity-50"
                                :disabled="busyId === report.id"
                                @click="resolve(report.id, 'unlist')"
                            >
                                Unlist item
                            </button>
                        </div>
                    </div>
                </article>
            </li>
        </ul>
    </AppLayout>
</template>
