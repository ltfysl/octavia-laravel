<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
import AppLayout from '../../layouts/AppLayout.vue';
import OBadge from '../../components/ui/OBadge.vue';
import OButton from '../../components/ui/OButton.vue';

defineProps<{
    feed: {
        data: Array<{
            id: string;
            read: boolean;
            run_id: number | null;
            run_name: string | null;
            status: string | null;
            score: number | null;
            at: string | null;
        }>;
        links: Array<{ url: string | null; label: string; active: boolean }>;
    };
    unreadCount: number;
}>();

const { t } = useI18n();

const markAllRead = () => router.post('/notifications/mark-read', {}, { preserveScroll: true });

const markUnread = (id: string) => router.post(`/notifications/${id}/unread`, {}, { preserveScroll: true });

const remove = (id: string) => router.delete(`/notifications/${id}`, { preserveScroll: true });
</script>

<template>
    <AppLayout>
        <Head><title>{{ t('nav.notifications') }}</title></Head>

        <div class="flex flex-wrap items-center justify-between gap-4">
            <div>
                <h1 class="font-display text-2xl font-bold tracking-tight text-ink-950">{{ t('nav.notifications') }}</h1>
                <p v-if="unreadCount > 0" class="mt-1 text-sm text-ink-500">{{ t('notifications.unreadCount', { count: unreadCount }) }}</p>
            </div>
            <OButton variant="secondary" :disabled="unreadCount === 0" @click="markAllRead">
                {{ t('notifications.markAllRead') }}
            </OButton>
        </div>

        <div class="mt-6 overflow-hidden rounded-card border border-ink-100 bg-white shadow-panel">
            <p v-if="feed.data.length === 0" class="px-5 py-8 text-center text-sm text-ink-300">—</p>
            <ul v-else class="divide-y divide-ink-100">
                <li
                    v-for="n in feed.data"
                    :key="n.id"
                    class="flex items-center gap-3 px-5 py-3"
                    :class="n.read ? '' : 'bg-accent-50/40'"
                >
                    <span class="h-2 w-2 shrink-0 rounded-full" :class="n.read ? 'bg-transparent' : 'bg-accent-500'" aria-hidden="true" />
                    <Link v-if="n.run_id" :href="`/runs/${n.run_id}`" class="min-w-0 flex-1 truncate text-sm font-medium text-ink-900 hover:text-accent-700">
                        {{ n.run_name }}
                    </Link>
                    <span v-else class="min-w-0 flex-1 truncate text-sm text-ink-900">{{ n.run_name }}</span>
                    <OBadge v-if="n.status === 'completed'" tone="mint">{{ t('runs.status.completed') }}</OBadge>
                    <OBadge v-else-if="n.status === 'failed'" tone="rose">{{ t('runs.status.failed') }}</OBadge>
                    <span v-if="n.score !== null" class="shrink-0 font-mono text-xs tabular-nums text-mint-600">{{ Math.round(n.score * 100) }}%</span>
                    <button
                        type="button"
                        class="shrink-0 text-xs text-ink-300 transition-colors hover:text-accent-600"
                        :title="t('notifications.markUnread')"
                        @click.stop="markUnread(n.id)"
                    >
                        {{ t('notifications.markUnread') }}
                    </button>
                    <button
                        type="button"
                        class="shrink-0 text-xs text-ink-300 transition-colors hover:text-rose-450"
                        :aria-label="t('common.delete')"
                        @click.stop="remove(n.id)"
                    >
                        ✕
                    </button>
                </li>
            </ul>
        </div>

        <nav v-if="feed.links.length > 3" class="mt-6 flex justify-center gap-1" aria-label="Pagination">
            <component
                :is="link.url ? Link : 'span'"
                v-for="(link, i) in feed.links"
                :key="i"
                :href="link.url ?? '#'"
                class="rounded-lg px-3 py-1.5 text-sm"
                :class="link.active ? 'bg-ink-950 text-white' : link.url ? 'text-ink-500 hover:bg-paper-200' : 'text-ink-200'"
                v-html="link.label"
            />
        </nav>
    </AppLayout>
</template>
