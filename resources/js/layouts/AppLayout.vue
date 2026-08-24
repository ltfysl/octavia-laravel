<script setup lang="ts">
import { computed, ref } from 'vue';
import { Link, router, usePage } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
import OAssistantChat from '../components/OAssistantChat.vue';

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

const sharedNotifications = computed(
    () => (page.props as unknown as { notifications?: { unread?: number; items?: NotificationItem[] } }).notifications,
);
const notifications = computed(() => ({
    unread: sharedNotifications.value?.unread ?? 0,
    items: sharedNotifications.value?.items ?? [],
}));

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
    { href: '/tournaments', label: t('nav.tournaments'), icon: 'trophy' },
    { href: '/reports', label: t('nav.reports'), icon: 'chart' },
    { href: '/audit', label: t('nav.audit'), icon: 'clock' },
    { href: '/marketplace', label: t('nav.marketplace'), icon: 'store' },
]);

const adminNav = computed(() =>
    page.props.auth?.user?.is_admin
        ? [{ href: '/admin', label: 'Admin' }, { href: '/admin/marketplace', label: 'Marketplace' }, { href: '/admin/reports', label: 'Reports' }]
        : [],
);
const isActive = (href: string) => page.url.startsWith(href);

// Theme toggle — persisted, class-based dark mode
const isDark = ref(typeof document !== 'undefined' && document.documentElement.classList.contains('dark'));
const toggleTheme = () => {
    isDark.value = !isDark.value;
    document.documentElement.classList.toggle('dark', isDark.value);
    try { localStorage.setItem('octavia-theme', isDark.value ? 'dark' : 'light'); } catch {}
};

// Command palette — reference parity, better: glass + spotlight + keyboard
const commandOpen = ref(false);
const commandQuery = ref('');
const filteredNav = computed(() => {
    const q = commandQuery.value.toLowerCase();
    if (!q) return nav.value;
    return nav.value.filter((n) => n.label.toLowerCase().includes(q) || n.href.toLowerCase().includes(q));
});
const openCommand = () => { commandOpen.value = true; commandQuery.value = ''; setTimeout(() => document.getElementById('command-input')?.focus(), 50); };
const closeCommand = () => { commandOpen.value = false; };

const onKeyDown = (e: KeyboardEvent) => {
    const isMac = navigator.platform.toUpperCase().includes('MAC');
    if ((isMac ? e.metaKey : e.ctrlKey) && e.key.toLowerCase() === 'k') {
        e.preventDefault();
        commandOpen.value ? closeCommand() : openCommand();
    } else if (e.key === '?' && !commandOpen.value) {
        // help — could open shortcuts modal, for now toggle command
        const target = e.target as HTMLElement | null;
        if (target && ['INPUT','TEXTAREA','SELECT'].includes(target.tagName)) return;
        e.preventDefault();
        openCommand();
    } else if (e.key === 'Escape' && commandOpen.value) {
        closeCommand();
    }
    // Vim-style G+? navigation (G+D etc.) — lightweight
    if ((e.target as HTMLElement)?.tagName && ['INPUT','TEXTAREA'].includes((e.target as HTMLElement).tagName)) return;
};

if (typeof window !== 'undefined') {
    window.addEventListener('keydown', onKeyDown);
}

</script>

<template>
    <div class="min-h-screen lg:grid lg:grid-cols-[18rem_1fr] xl:grid-cols-[23.75rem_1fr]">
        <!-- Sidebar — glass, wider at xl for dashboard bento -->

        <aside class="hidden lg:flex lg:h-screen lg:flex-col lg:border-r lg:border-ink-100 lg:bg-card/80 lg:backdrop-blur supports-[backdrop-filter]:bg-card/60">
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
                    :class="isActive(item.href) ? 'bg-card text-ink-950 shadow-panel' : 'text-ink-500 hover:bg-paper-200 hover:text-ink-900'"
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
                    :class="isActive(item.href) ? 'bg-card text-ink-950 shadow-panel' : 'text-ink-500 hover:bg-paper-200 hover:text-ink-900'"
                    :aria-current="isActive(item.href) ? 'page' : undefined"
                >
                    <span class="h-1.5 w-1.5 rotate-45" :class="isActive(item.href) ? 'bg-accent-600' : 'bg-ink-300'" />
                    {{ item.label }}
                </Link>
            </nav>
            <div class="border-t border-ink-100 px-3 py-3">
                <Link href="/settings/profile" class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm transition-colors" :class="isActive('/settings') ? 'bg-card text-ink-950 shadow-panel' : 'text-ink-500 hover:bg-paper-200 hover:text-ink-900'">
                    <span class="flex h-7 w-7 items-center justify-center rounded-full bg-ink-100 text-xs font-semibold text-ink-700">{{ user?.name?.charAt(0).toUpperCase() }}</span>
                    <span class="truncate">{{ user?.name }}</span>
                </Link>
                <Link href="/settings/billing" class="mt-1 flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium transition-colors" :class="isActive('/settings/billing') ? 'bg-violet-50 text-violet-700' : 'text-ink-500 hover:bg-paper-100 hover:text-ink-900'">
                    {{ t('billing.title') }}
                </Link>
                <Link href="/settings/presets" class="mt-1 flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium transition-colors" :class="isActive('/settings/presets') ? 'bg-accent-50 text-accent-700' : 'text-ink-500 hover:bg-paper-100 hover:text-ink-900'">
                    {{ t('settings.presets.title') }}
                </Link>
            <Link href="/settings/api-keys" class="mt-1 flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium transition-colors" :class="isActive('/settings/api-keys') ? 'bg-accent-50 text-accent-700' : 'text-ink-500 hover:bg-paper-100 hover:text-ink-900'">
                {{ t('settings.apiKeys.title') }}
            </Link>
            <Link href="/settings/webhooks" class="mt-1 flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium transition-colors" :class="isActive('/settings/webhooks') ? 'bg-accent-50 text-accent-700' : 'text-ink-500 hover:bg-paper-100 hover:text-ink-900'">
                {{ t('settings.webhooks.title') }}
            </Link>
            </div>
        </aside>

        <!-- Mobile top bar -->
        <div class="sticky top-0 z-30 flex items-center justify-between border-b border-ink-100 bg-card/90 px-4 py-3 backdrop-blur lg:hidden">
            <Link href="/dashboard" class="flex items-center gap-2">
                <span class="flex h-7 w-7 items-center justify-center rounded-md bg-accent-600 font-display text-sm font-bold text-ink-950">O</span>
                <span class="font-display font-semibold text-ink-950">Octavia</span>
            </Link>
            <details class="relative">
                <summary class="flex h-9 w-9 cursor-pointer list-none items-center justify-center rounded-lg border border-ink-200 text-ink-700" aria-label="Menu">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" /></svg>
                </summary>
                <nav class="absolute right-0 z-40 mt-2 w-56 rounded-card border border-ink-100 bg-card p-2 shadow-pop" aria-label="Mobile">
                    <Link v-for="item in nav" :key="item.href" :href="item.href" class="block rounded-lg px-3 py-2 text-sm" :class="isActive(item.href) ? 'bg-paper-100 text-ink-950' : 'text-ink-700'">{{ item.label }}</Link>
                    <hr class="my-2 border-ink-100" />
                    <Link href="/settings/profile" class="block rounded-lg px-3 py-2 text-sm text-ink-700">{{ t('nav.settings') }}</Link>
                    <Link href="/settings/billing" class="block rounded-lg px-3 py-2 text-sm text-ink-700">{{ t('billing.title') }}</Link>
                    <Link href="/settings/presets" class="block rounded-lg px-3 py-2 text-sm text-ink-700">{{ t('settings.presets.title') }}</Link>
                    <Link href="/settings/api-keys" class="block rounded-lg px-3 py-2 text-sm text-ink-700">{{ t('settings.apiKeys.title') }}</Link>
                    <Link href="/settings/webhooks" class="block rounded-lg px-3 py-2 text-sm text-ink-700">{{ t('settings.webhooks.title') }}</Link>
                    <Link href="/logout" method="post" as="button" class="block w-full rounded-lg px-3 py-2 text-left text-sm text-rose-450">{{ t('nav.logOut') }}</Link>
                </nav>
            </details>
        </div>

        <!-- Main -->
        <main class="min-w-0">
            <div class="mx-auto max-w-[1400px] px-4 py-6 sm:px-6 lg:px-10 lg:py-8">
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
                <div class="mb-6 hidden items-center justify-between gap-4 lg:flex">
                    <Link href="/search" class="flex items-center gap-2 rounded-full border border-ink-200 bg-card px-3 py-1.5 text-sm text-ink-400 shadow-sm transition hover:border-ink-300 hover:bg-paper-100 hover:text-ink-600">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" /></svg>
                        <span>Search</span>
                        <span class="ml-2 hidden sm:inline-flex items-center gap-1 rounded bg-ink-100 px-1.5 py-0.5 font-mono text-xs text-ink-500">
                            <span>⌘</span><span>K</span>
                        </span>
                    </Link>
                    <div class="flex items-center gap-2">
                        <Link href="/search" class="flex h-8 w-8 items-center justify-center rounded-lg text-ink-500 transition hover:bg-paper-100 hover:text-ink-900 lg:hidden" aria-label="Search">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" /></svg>
                        </Link>
                        <details v-if="user" class="relative">
                            <summary class="relative flex h-8 w-8 cursor-pointer list-none items-center justify-center rounded-lg text-ink-500 transition-colors hover:bg-paper-100 hover:text-ink-900 [&::-webkit-details-marker]:hidden" aria-label="Notifications" @click="markRead()">
                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 0 0 5.454-1.31A8.967 8.967 0 0 1 18 9.75V9A6 6 0 0 0 6 9v.75a8.967 8.967 0 0 1-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 0 1-5.714 0m5.714 0a3 3 0 1 1-5.714 0" /></svg>
                                <span
                                    v-if="notifications.unread > 0"
                                    class="absolute -right-0.5 -top-0.5 flex h-4 min-w-4 items-center justify-center rounded-full bg-accent-600 px-1 text-[10px] font-semibold text-ink-950"
                                >{{ notifications.unread }}</span>
                            </summary>
                            <div class="absolute right-0 z-40 mt-2 w-80 rounded-card border border-ink-100 bg-card p-2 shadow-pop">
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
                        <button type="button" class="flex h-8 w-8 items-center justify-center rounded-lg text-ink-500 transition hover:bg-paper-100 hover:text-ink-900" :aria-label="isDark ? 'Switch to light mode' : 'Switch to dark mode'" @click="toggleTheme">
                            <svg v-if="isDark" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M12 3v2.25m6.364.386l-1.591 1.591M21 12h-2.25m-.386 6.364l-1.591-1.591M12 18.75V21m-4.773-4.227l-1.591 1.591M5.25 12H3m4.227-4.773L5.636 5.636M15.75 12a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0Z" /></svg>
                            <svg v-else class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M21.752 15.002A9.72 9.72 0 0118 15.75c-5.385 0-9.75-4.365-9.75-9.75 0-1.33.266-2.597.748-3.752A9.753 9.753 0 003 11.25C3 16.635 7.365 21 12.75 21a9.753 9.753 0 009.002-5.998z" /></svg>
                        </button>
                        <button type="button" class="hidden h-8 w-8 items-center justify-center rounded-lg text-ink-400 transition hover:bg-paper-100 hover:text-ink-700 md:flex" aria-label="Keyboard shortcuts" title="Press ? for shortcuts">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M9.879 7.519c1.171-1.025 3.071-1.025 4.242 0 1.172 1.025 1.172 2.687 0 3.712-.203.179-.43.326-.67.442-.745.361-1.45.999-1.45 1.827v.75M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9 5.25h.008v.008H12v-.008z" /></svg>
                        </button>
                        <Link v-if="user" href="/logout" method="post" as="button" class="text-sm text-ink-500 transition-colors hover:text-ink-900">{{ t('nav.logOut') }}</Link>
                    </div>
                </div>
                <slot />
            </div>
        </main>

        <!-- Command Palette — glass, spotlight -->
        <div
            v-if="commandOpen"
            class="fixed inset-0 z-50 flex items-start justify-center bg-ink-950/20 p-4 pt-[18vh] backdrop-blur-sm"
            @click.self="closeCommand"
        >
            <div class="w-full max-w-lg overflow-hidden rounded-[1.5rem] border border-white/20 bg-card shadow-[0_24px_64px_rgba(14,26,29,0.18)]">
                <div class="flex items-center gap-3 border-b border-ink-100 px-4 py-3">
                    <svg class="h-4 w-4 text-ink-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" /></svg>
                    <input
                        id="command-input"
                        v-model="commandQuery"
                        placeholder="Search navigation, prompts, benchmarks…"
                        class="flex-1 bg-transparent text-sm outline-none placeholder:text-ink-400"
                        @keydown.escape="closeCommand"
                    />
                    <span class="rounded bg-ink-100 px-1.5 py-0.5 font-mono text-xs text-ink-500">ESC</span>
                </div>
                <div class="max-h-80 overflow-auto p-2">
                    <Link
                        v-for="item in filteredNav"
                        :key="item.href"
                        :href="item.href"
                        class="flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium text-ink-700 transition hover:bg-paper-100 hover:text-ink-900"
                        @click="closeCommand"
                    >
                        <span class="h-1.5 w-1.5 rotate-45 bg-accent-600" aria-hidden="true" />
                        {{ item.label }}
                        <span class="ml-auto font-mono text-xs text-ink-300">{{ item.href }}</span>
                    </Link>
                    <p v-if="filteredNav.length === 0" class="px-3 py-8 text-center text-sm text-ink-400">No results — try “prompts” or “benchmarks”</p>
                </div>
                <div class="flex items-center justify-between border-t border-ink-100 bg-paper-50 px-4 py-2 text-xs text-ink-400">
                    <span>Press <span class="rounded bg-card px-1 py-0.5 font-mono shadow-sm">↵</span> to navigate</span>
                    <span class="hidden sm:inline"> <span class="rounded bg-card px-1 py-0.5 font-mono shadow-sm">?</span> for help · <span class="rounded bg-card px-1 py-0.5 font-mono shadow-sm">⌘K</span> to toggle</span>
                </div>
            </div>
        </div>
        <OAssistantChat v-if="user" />
    </div>
</template>
