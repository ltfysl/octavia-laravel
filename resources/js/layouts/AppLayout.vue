<script setup lang="ts">
import { computed, ref } from 'vue';
import { Link, router, usePage } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';

const page = usePage<{ auth: { user: { id: number; name: string; is_admin?: boolean; email_verified_at?: string | null } | null } }>();
const { t } = useI18n();

const user = computed(() => page.props.auth?.user ?? null);

interface QuotaInfo {
    used: number;
    limit: number;
}

const runQuota = computed(
    () => (page.props as unknown as { runQuota: QuotaInfo | null }).runQuota ?? null,
);

// Soft warning once 80% of the daily quota is consumed.
const quotaLow = computed(() => {
    const quota = runQuota.value;
    if (! quota || quota.limit <= 0) return false;

    return quota.used / quota.limit >= 0.8;
});

interface NotificationItem {
    id: string;
    read: boolean;
    run_id: number | null;
    run_name: string | null;
    status: string | null;
    score: number | null;
    at: string | null;
}

const notifications = computed(
    () => (page.props as unknown as { notifications: { unread: number; items: NotificationItem[] } }).notifications
        ?? { unread: 0, items: [] },
);

const markRead = () => {
    if (notifications.value.unread > 0) {
        router.post('/notifications/mark-read', {}, { preserveScroll: true });
    }
}

const removeNotification = (id: string) => {
    router.delete(`/notifications/${id}`, { preserveScroll: true });
};

const verificationSent = ref(false);

const resendVerification = () => {
    router.post('/email/verification-notification', {}, {
        preserveScroll: true,
        onSuccess: () => {
            verificationSent.value = true;
        },
    });
};

const nav = computed(() => [
    { href: '/dashboard', label: t('nav.dashboard'), icon: 'grid' },
    { href: '/prompts', label: t('nav.prompts'), icon: 'sparkles' },
    { href: '/benchmarks', label: t('nav.benchmarks'), icon: 'checklist' },
    { href: '/collections', label: t('benchmarks.collections.title'), icon: 'layers' },
    { href: '/marketplace', label: t('nav.marketplace'), icon: 'store' },
]);

const adminNav = computed(() =>
    page.props.auth?.user?.is_admin
        ? [{ href: '/admin', label: 'Admin' }, { href: '/admin/marketplace', label: 'Marketplace' }, { href: '/admin/reports', label: 'Reports' }]
        : [],
);
const isActive = (href: string) => page.url.startsWith(href);
</script>

<template>
    <div class="min-h-screen lg:grid lg:grid-cols-[16rem_1fr]">
        <!-- Sidebar -->

        <aside class="hidden lg:flex lg:h-screen lg:flex-col lg:border-r lg:border-ink-100 lg:bg-paper-100">
            <div class="px-5 pt-6 pb-4">
                <Link href="/dashboard" class="flex items-center gap-2.5" aria-label="Octavia home">
                    <span class="flex h-8 w-8 items-center justify-center rounded-md bg-accent-600 font-display text-lg font-bold text-ink-950">O</span>
                    <span class="font-display text-lg font-semibold tracking-tight text-ink-950">Octavia</span>
                </Link>
            </div>
            <nav class="flex-1 space-y-0.5 px-3" aria-label="Main">
                <Link
                    v-for="item in nav"
                    :key="item.href"
                    :href="item.href"
                    class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm transition-colors"
                    :class="isActive(item.href) ? 'bg-white text-ink-950 shadow-panel' : 'text-ink-500 hover:bg-paper-200 hover:text-ink-900'"
                    :aria-current="isActive(item.href) ? 'page' : undefined"
                >
                    <span class="h-1.5 w-1.5 rotate-45" :class="isActive(item.href) ? 'bg-accent-600' : 'bg-ink-300'" />
                    {{ item.label }}
                </Link>
            </nav>
            <nav v-if="adminNav.length > 0" class="space-y-0.5 px-3 pb-3" aria-label="Admin">
                <Link
                    v-for="item in adminNav"
                    :key="item.href"
                    :href="item.href"
                    class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm transition-colors"
                    :class="isActive(item.href) ? 'bg-white text-ink-950 shadow-panel' : 'text-ink-500 hover:bg-paper-200 hover:text-ink-900'"
                    :aria-current="isActive(item.href) ? 'page' : undefined"
                >
                    <span class="h-1.5 w-1.5 rotate-45" :class="isActive(item.href) ? 'bg-accent-600' : 'bg-ink-300'" />
                    {{ item.label }}
                </Link>
            </nav>
            <div class="border-t border-ink-100 px-3 py-3">
                <Link href="/settings/profile" class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm transition-colors" :class="isActive('/settings') ? 'bg-white text-ink-950 shadow-panel' : 'text-ink-500 hover:bg-paper-200 hover:text-ink-900'">
                    <span class="flex h-7 w-7 items-center justify-center rounded-full bg-ink-100 text-xs font-semibold text-ink-700">{{ user?.name?.charAt(0).toUpperCase() }}</span>
                    <span class="truncate">{{ user?.name }}</span>
                </Link>
            </div>
        </aside>

        <!-- Mobile top bar -->
        <div class="sticky top-0 z-30 flex items-center justify-between border-b border-ink-100 bg-white/90 px-4 py-3 backdrop-blur lg:hidden">
            <Link href="/dashboard" class="flex items-center gap-2">
                <span class="flex h-7 w-7 items-center justify-center rounded-md bg-accent-600 font-display text-sm font-bold text-ink-950">O</span>
                <span class="font-display font-semibold text-ink-950">Octavia</span>
            </Link>
            <details class="relative">
                <summary class="flex h-9 w-9 cursor-pointer list-none items-center justify-center rounded-lg border border-ink-200 text-ink-700" aria-label="Menu">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" /></svg>
                </summary>
                <nav class="absolute right-0 z-40 mt-2 w-56 rounded-card border border-ink-100 bg-white p-2 shadow-pop" aria-label="Mobile">
                    <Link v-for="item in nav" :key="item.href" :href="item.href" class="block rounded-lg px-3 py-2 text-sm" :class="isActive(item.href) ? 'bg-paper-100 text-ink-950' : 'text-ink-700'">{{ item.label }}</Link>
                    <hr class="my-2 border-ink-100" />
                    <Link href="/settings/profile" class="block rounded-lg px-3 py-2 text-sm text-ink-700">{{ t('nav.settings') }}</Link>
                    <Link href="/logout" method="post" as="button" class="block w-full rounded-lg px-3 py-2 text-left text-sm text-rose-450">{{ t('nav.logOut') }}</Link>
                </nav>
            </details>
        </div>

        <!-- Main -->
        <main class="min-w-0">
            <div class="mx-auto max-w-6xl px-4 py-6 sm:px-6 lg:px-10 lg:py-8">
                <div
                    v-if="user && !user.email_verified_at && !verificationSent"
                    class="mb-6 flex flex-wrap items-center justify-between gap-3 rounded-card border border-amber-450/40 bg-amber-100/50 px-4 py-3"
                    role="status"
                >
                    <p class="text-sm text-ink-900">{{ t('verification.banner') }}</p>
                    <button type="button" class="text-sm font-medium text-accent-600 hover:text-accent-700" @click="resendVerification">
                        {{ t('verification.resend') }}
                    </button>
                </div>
                <div
                    v-if="quotaLow"
                    class="mb-6 flex flex-wrap items-center justify-between gap-3 rounded-card border border-amber-450/40 bg-amber-100/50 px-4 py-3"
                    role="status"
                >
                    <p class="text-sm text-ink-900">
                        {{ t('runs.quotaWarning', { used: runQuota?.used, limit: runQuota?.limit }) }}
                    </p>
                </div>
                <div class="mb-6 hidden items-center justify-end gap-4 lg:flex">
                    <details v-if="user" class="relative">
                        <summary class="relative flex h-8 w-8 cursor-pointer list-none items-center justify-center rounded-lg text-ink-500 transition-colors hover:bg-paper-100 hover:text-ink-900 [&::-webkit-details-marker]:hidden" aria-label="Notifications" @click="markRead()">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 0 0 5.454-1.31A8.967 8.967 0 0 1 18 9.75V9A6 6 0 0 0 6 9v.75a8.967 8.967 0 0 1-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 0 1-5.714 0m5.714 0a3 3 0 1 1-5.714 0" /></svg>
                            <span
                                v-if="notifications.unread > 0"
                                class="absolute -right-0.5 -top-0.5 flex h-4 min-w-4 items-center justify-center rounded-full bg-accent-600 px-1 text-[10px] font-semibold text-ink-950"
                            >{{ notifications.unread }}</span>
                        </summary>
                        <div class="absolute right-0 z-40 mt-2 w-80 rounded-card border border-ink-100 bg-white p-2 shadow-pop">
                            <p class="px-2 pb-1 pt-1 text-xs font-semibold uppercase tracking-wide text-ink-300">Notifications</p>
                            <p v-if="notifications.items.length === 0" class="px-2 py-3 text-sm text-ink-300">—</p>
                            <div
                                v-for="item in notifications.items"
                                :key="item.id"
                                class="-mx-1 flex items-start gap-2.5 rounded-lg px-3 py-2 transition-colors"
                                :class="item.read ? '' : 'bg-accent-50/60'"
                            >
                                <Link v-if="item.run_id" :href="`/runs/${item.run_id}`" class="flex min-w-0 flex-1 items-start gap-2.5">
                                    <span class="mt-1 h-1.5 w-1.5 shrink-0 rounded-full" :class="item.read ? 'bg-transparent' : 'bg-accent-500'" aria-hidden="true" />
                                    <span class="min-w-0 flex-1 truncate text-sm text-ink-900">{{ item.run_name }}</span>
                                </Link>
                                <span v-else class="mt-1 h-1.5 w-1.5 shrink-0 rounded-full bg-transparent" aria-hidden="true" />
                                <span v-if="item.score !== null" class="shrink-0 pt-0.5 font-mono text-xs tabular-nums text-mint-600">{{ Math.round(item.score * 100) }}%</span>
                                <button
                                    type="button"
                                    class="shrink-0 text-ink-300 transition-colors hover:text-rose-450"
                                    :aria-label="'Delete'"
                                    @click.prevent="removeNotification(item.id)"
                                >
                                    <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" /></svg>
                                </button>
                            </div>
                            <Link href="/notifications" class="block px-3 pt-2 text-center text-xs font-medium text-accent-600 hover:text-accent-700">
                                {{ t('nav.notifications') }} →
                            </Link>
                        </div>
                    </details>
                    <Link v-if="user" href="/logout" method="post" as="button" class="text-sm text-ink-500 transition-colors hover:text-ink-900">{{ t('nav.logOut') }}</Link>
                </div>
                <slot />
            </div>
        </main>
    </div>
</template>
