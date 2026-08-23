<script setup lang="ts">
import { ref } from 'vue';
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
import AppLayout from '../../layouts/AppLayout.vue';
import OEmptyState from '../../components/ui/OEmptyState.vue';
import OBadge from '../../components/ui/OBadge.vue';
import OButton from '../../components/ui/OButton.vue';
import OInput from '../../components/ui/OInput.vue';

defineProps<{
    items: {
        data: Array<{
            id: number;
            item_type: string;
            title: string;
            summary: string | null;
            version: number;
            downloads: number;
            featured: boolean;
            publisher: string | null;
            installed_version: number | null;
        }>;
        links: Array<{ url: string | null; label: string; active: boolean }>;
    };
    filters: { type: string | null; q: string };
}>();

const { t } = useI18n();

const search = ref('');

const applyFilter = (type: string | null) => {
    router.get('/marketplace', type ? { type } : {}, { preserveState: true });
};

const doSearch = () => {
    router.get('/marketplace', search.value ? { q: search.value } : {}, { preserveState: true });
};

const install = (id: number) => {
    router.post(`/marketplace/${id}/install`);
};

const reportItem = ref<number | null>(null);
const reportReason = ref('inappropriate');
const reportMessage = ref('');
const reportForm = useForm({ reason: 'inappropriate', message: '' });

const submitReport = (id: number) => {
    reportForm.reason = reportReason.value;
    reportForm.message = reportMessage.value;
    reportForm.post(`/marketplace/${id}/report`, {
        preserveScroll: true,
        onSuccess: () => {
            reportItem.value = null;
            reportMessage.value = '';
        },
    });
};
</script>

<template>
    <AppLayout>
        <Head><title>{{ t('marketplace.title') }}</title></Head>

        <div class="flex flex-wrap items-start justify-between gap-4">
            <div>
                <h1 class="font-display text-2xl font-bold tracking-tight text-ink-950">{{ t('marketplace.title') }}</h1>
                <p class="mt-1 text-sm text-ink-500">{{ t('marketplace.subtitle') }}</p>
            </div>
        </div>

        <!-- Filters -->
        <div class="mt-6 flex flex-wrap items-center gap-3">
            <div class="flex rounded-lg border border-ink-200 bg-card p-0.5" role="group" :aria-label="t('marketplace.browse')">
                <button
                    v-for="filter in [{ v: null, label: t('marketplace.browse') }, { v: 'prompt', label: t('marketplace.prompts') }, { v: 'benchmark', label: t('marketplace.benchmarks') }]"
                    :key="String(filter.v)"
                    class="rounded-md px-3 py-1.5 text-sm font-medium transition-colors"
                    :class="(filters.type ?? null) === filter.v ? 'bg-ink-950 text-white' : 'text-ink-500 hover:text-ink-900'"
                    @click="applyFilter(filter.v)"
                >
                    {{ filter.label }}
                </button>
            </div>
            <form class="ml-auto flex gap-2" role="search" @submit.prevent="doSearch">
                <OInput v-model="search" :placeholder="t('marketplace.searchPlaceholder')" class="w-56" />
                <OButton variant="secondary" type="submit">{{ t('common.search') }}</OButton>
            </form>
        </div>

        <OEmptyState v-if="items.data.length === 0" class="mt-8" :title="t('marketplace.empty')" />

        <ul v-else class="mt-8 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
            <li v-for="item in items.data" :key="item.id">
                <article class="flex h-full flex-col rounded-card border border-ink-100 bg-card p-5 shadow-panel">
                    <div class="flex items-center justify-between gap-2">
                        <OBadge :tone="item.item_type === 'benchmark' ? 'accent' : 'neutral'">
                            {{ item.item_type === 'benchmark' ? t('marketplace.benchmarks') : t('marketplace.prompts') }}
                        </OBadge>
                        <span v-if="item.featured" class="text-xs font-medium text-amber-450">★ Featured</span>
                    </div>
                    <h2 class="mt-3 font-display text-sm font-semibold text-ink-950">{{ item.title }}</h2>
                    <p class="mt-1.5 line-clamp-3 flex-1 text-sm text-ink-500">{{ item.summary ?? '—' }}</p>
                    <div class="mt-4 flex items-center justify-between border-t border-ink-100 pt-3 text-xs text-ink-300">
                        <span>{{ t('marketplace.byAuthor', { author: item.publisher ?? '?' }) }} · v{{ item.version }}</span>
                        <span>{{ t('marketplace.downloads', { count: item.downloads }) }}</span>
                    </div>
                    <OBadge
                        v-if="item.installed_version !== null && item.installed_version < item.version"
                        tone="amber"
                        class="mt-3 self-start"
                    >
                        {{ t('marketplace.updateAvailable', { version: item.version }) }}
                    </OBadge>
                    <OButton variant="secondary" size="sm" class="mt-3 w-full" @click="install(item.id)">
                        {{ item.installed_version !== null && item.installed_version < item.version ? t('marketplace.update') : t('marketplace.install') }}
                    </OButton>
                    <button
                        type="button"
                        class="mt-2 text-xs text-ink-300 transition-colors hover:text-rose-450"
                        @click="reportItem = reportItem === item.id ? null : item.id"
                    >
                        ⚑ {{ t('marketplace.reportTitle') }}
                    </button>
                    <form v-if="reportItem === item.id" class="mt-2 space-y-2" @submit.prevent="submitReport(item.id)">
                        <select v-model="reportReason" class="w-full rounded-lg border border-ink-200 bg-card px-2 py-1.5 text-xs focus:border-accent-500" :aria-label="t('marketplace.reportReason')">
                            <option value="inappropriate">{{ t('marketplace.reasons.inappropriate') }}</option>
                            <option value="spam">{{ t('marketplace.reasons.spam') }}</option>
                            <option value="copyright">{{ t('marketplace.reasons.copyright') }}</option>
                            <option value="broken">{{ t('marketplace.reasons.broken') }}</option>
                            <option value="other">{{ t('marketplace.reasons.other') }}</option>
                        </select>
                        <textarea v-model="reportMessage" rows="2" :placeholder="t('marketplace.reportPlaceholder')" class="w-full rounded-lg border border-ink-200 bg-card px-2 py-1.5 text-xs focus:border-accent-500" />
                        <div class="flex justify-end gap-2">
                            <OButton variant="ghost" size="sm" type="button" @click="reportItem = null">{{ t('common.cancel') }}</OButton>
                            <OButton size="sm" type="submit" :disabled="reportForm.processing">{{ t('marketplace.reportSubmit') }}</OButton>
                        </div>
                    </form>
                </article>
            </li>
        </ul>

        <nav v-if="items.links.length > 3" class="mt-6 flex justify-center gap-1" aria-label="Pagination">
            <component
                :is="link.url ? Link : 'span'"
                v-for="(link, i) in items.links"
                :key="i"
                :href="link.url ?? '#'"
                class="rounded-lg px-3 py-1.5 text-sm"
                :class="link.active ? 'bg-ink-950 text-white' : link.url ? 'text-ink-500 hover:bg-paper-200' : 'text-ink-200'"
                v-html="link.label"
            />
        </nav>
    </AppLayout>
</template>
