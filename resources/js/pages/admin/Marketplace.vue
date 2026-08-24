<script setup lang="ts">
import { Head, router } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
import { ref } from 'vue';
import AppLayout from '../../layouts/AppLayout.vue';
import OBadge from '../../components/ui/OBadge.vue';


const { t } = useI18n();

defineProps<{
    items: Array<{
        id: number;
        item_type: string;
        title: string;
        publisher: string | null;
        version: number;
        downloads: number;
        listed: boolean;
        published_at: string | null;
    }>;
}>();

const busyId = ref<number | null>(null);

const setListed = (id: number, listed: boolean) => {
    busyId.value = id;
    router.post(`/admin/marketplace/${id}/listed`, { listed }, {
        onFinish: () => {
            busyId.value = null;
        },
    });

};

</script>

<template>
    <AppLayout>
        <Head><title>Admin · {{ t('admin.marketplace.title') }}</title><meta name="robots" content="noindex" /></Head>

        <h1 class="font-display text-2xl font-bold tracking-tight text-ink-950">{{ t('admin.marketplace.title') }}</h1>
        <p class="mt-1 text-sm text-ink-500">{{ t('admin.marketplace.subtitle') }}</p>

        <div class="mt-6 overflow-hidden rounded-card border border-ink-100 bg-card shadow-panel">
            <table class="w-full text-left text-sm">
                <thead>
                    <tr class="border-b border-ink-100 text-xs uppercase tracking-wide text-ink-300">
                        <th class="px-5 py-3 font-medium">{{ t('admin.marketplace.item') }}</th>
                        <th class="hidden px-5 py-3 font-medium sm:table-cell">{{ t('admin.marketplace.publisher') }}</th>
                        <th class="px-5 py-3 font-medium">{{ t('admin.marketplace.status') }}</th>
                        <th class="px-5 py-3 font-medium"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-ink-100">
                    <tr v-for="item in items" :key="item.id">
                        <td class="px-5 py-3">
                            <span class="block font-medium text-ink-950">{{ item.title }}</span>
                            <span class="text-xs text-ink-300">{{ item.item_type }} · v{{ item.version }} · {{ item.downloads }} installs</span>
                        </td>
                        <td class="hidden px-5 py-3 text-xs text-ink-500 sm:table-cell">{{ item.publisher ?? '—' }}</td>
                        <td class="px-5 py-3">
                            <OBadge :tone="item.listed ? 'mint' : 'neutral'">{{ item.listed ? 'listed' : 'unlisted' }}</OBadge>
                        </td>
                        <td class="px-5 py-3 text-right">
                            <button
                                type="button"
                                class="text-xs font-medium"
                                :class="item.listed ? 'text-rose-450 hover:underline' : 'text-accent-600 hover:text-accent-700'"
                                :disabled="busyId === item.id"
                                @click="setListed(item.id, !item.listed)"
                            >
                                {{ item.listed ? 'Unlist' : 'Relist' }}
                            </button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </AppLayout>
</template>
