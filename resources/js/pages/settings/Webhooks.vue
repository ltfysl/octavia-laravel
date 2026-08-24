<script setup lang="ts">
import { ref } from 'vue';
import { Head, useForm, router } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
import AppLayout from '../../layouts/AppLayout.vue';
import OPanel from '../../components/ui/OPanel.vue';
import OButton from '../../components/ui/OButton.vue';
import OField from '../../components/ui/OField.vue';
import OInput from '../../components/ui/OInput.vue';
import OEmptyState from '../../components/ui/OEmptyState.vue';
import OBadge from '../../components/ui/OBadge.vue';

interface Webhook {
    id: number;
    url: string;
    description: string | null;
    events: string[];
    is_active: boolean;
    deliveries_count: number;
    created_at: string;
}

const props = defineProps<{
    webhooks: Webhook[];
    events: string[];
}>();

const { t } = useI18n();

const form = useForm({
    url: '',
    description: '',
    events: [] as string[],
});

const expanded = ref<number | null>(null);
const deliveries = ref<Record<number, any[]>>({});
const loadingDeliveries = ref<Record<number, boolean>>({});

const submit = () => form.post('/settings/webhooks', {
    onSuccess: () => form.reset(),
});

const toggleEvent = (event: string) => {
    if (form.events.includes(event)) {
        form.events = form.events.filter((e) => e !== event);
    } else {
        form.events.push(event);
    }
};

const toggleActive = (webhook: Webhook) => {
    const form = useForm({
        url: webhook.url,
        description: webhook.description ?? '',
        events: webhook.events,
        is_active: ! webhook.is_active,
    });
    form.patch(`/settings/webhooks/${webhook.id}`);
};

const destroy = (id: number) => {
    if (confirm(t('settings.webhooks.deleteConfirm'))) {
        router.delete(`/settings/webhooks/${id}`);
    }
};

const loadDeliveries = async (webhook: Webhook) => {
    if (expanded.value === webhook.id) {
        expanded.value = null;
        return;
    }
    expanded.value = webhook.id;
    if (deliveries.value[webhook.id]) return;
    loadingDeliveries.value[webhook.id] = true;
    try {
        const res = await fetch(`/settings/webhooks/${webhook.id}/deliveries`, {
            headers: { Accept: 'application/json' },
        });
        if (res.ok) {
            deliveries.value[webhook.id] = await res.json();
        }
    } catch {
        // silent
    } finally {
        loadingDeliveries.value[webhook.id] = false;
    }
};
</script>

<template>
    <AppLayout>
        <Head><title>{{ t('settings.webhooks.title') }}</title></Head>

        <h1 class="font-display text-2xl font-bold tracking-tight text-ink-950">{{ t('settings.webhooks.title') }}</h1>

        <form class="mt-6 space-y-4 rounded-2xl border border-ink-100 bg-card p-5" @submit.prevent="submit">
            <OField :label="t('settings.webhooks.url')" for="url" required>
                <OInput id="url" v-model="form.url" type="url" required />
            </OField>

            <OField :label="t('settings.webhooks.description')" for="description">
                <OInput id="description" v-model="form.description" />
            </OField>

            <div>
                <p class="text-sm font-medium text-ink-900">{{ t('settings.webhooks.events') }}</p>
                <div class="mt-2 flex flex-wrap gap-2">
                    <button
                        v-for="event in events"
                        :key="event"
                        type="button"
                        @click="toggleEvent(event)"
                        class="rounded-full border px-3 py-1 text-xs font-medium transition-colors"
                        :class="form.events.includes(event) ? 'border-accent-600 bg-accent-50 text-accent-700' : 'border-ink-200 bg-card text-ink-500 hover:bg-paper-50'"
                    >
                        {{ event }}
                    </button>
                </div>
                <p v-if="form.errors.events" class="mt-1 text-xs text-rose-450">{{ form.errors.events }}</p>
            </div>

            <OButton type="submit" :disabled="form.processing || form.events.length === 0">{{ t('settings.webhooks.add') }}</OButton>
        </form>

        <OEmptyState v-if="webhooks.length === 0" class="mt-8" :title="t('settings.webhooks.empty')" />

        <div v-else class="mt-8 space-y-3">
            <OPanel v-for="webhook in webhooks" :key="webhook.id" :title="webhook.url" :subtitle="webhook.description ?? ''">
                <div class="flex items-center gap-2 text-xs text-ink-500">
                    <OBadge :tone="webhook.is_active ? 'mint' : 'neutral'">{{ webhook.is_active ? t('common.yes') : t('common.no') }}</OBadge>
                    <span v-for="event in webhook.events" :key="event" class="rounded-full border border-ink-200 bg-paper-50 px-2 py-0.5">{{ event }}</span>
                    <span class="ml-auto">{{ webhook.deliveries_count }} deliveries</span>
                </div>
                <div class="mt-3 flex items-center gap-2">
                    <OButton size="sm" variant="secondary" @click="toggleActive(webhook)">{{ webhook.is_active ? t('settings.webhooks.deactivate') : t('settings.webhooks.activate') }}</OButton>
                    <OButton size="sm" variant="ghost" @click="loadDeliveries(webhook)">{{ t('settings.webhooks.showDeliveries') }}</OButton>
                    <OButton size="sm" tone="rose" variant="ghost" @click="destroy(webhook.id)">{{ t('common.delete') }}</OButton>
                </div>
                <div v-if="expanded === webhook.id" class="mt-3 rounded-xl border border-ink-100 bg-paper-50 p-3">
                    <p v-if="loadingDeliveries[webhook.id]" class="text-xs text-ink-500">{{ t('common.loading') }}</p>
                    <ul v-else-if="deliveries[webhook.id]?.length" class="space-y-1 text-xs">
                        <li v-for="d in deliveries[webhook.id]" :key="d.id" class="flex items-center justify-between">
                            <span>{{ d.event }}</span>
                            <span :class="d.status === 'delivered' ? 'text-mint-600' : 'text-rose-600'">{{ d.status }} <span v-if="d.response_code">({{ d.response_code }})</span></span>
                        </li>
                    </ul>
                    <p v-else class="text-xs text-ink-400">{{ t('settings.webhooks.noDeliveries') }}</p>
                </div>
            </OPanel>
        </div>
    </AppLayout>
</template>
