<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
import AppLayout from '../../layouts/AppLayout.vue';
import OPanel from '../../components/ui/OPanel.vue';
import OButton from '../../components/ui/OButton.vue';
import OField from '../../components/ui/OField.vue';
import OInput from '../../components/ui/OInput.vue';

interface Key {
    id: number;
    provider: string;
    is_active: boolean;
    last_used_at: string | null;
    created_at: string;
}

const props = defineProps<{
    keys: Key[];
    availableProviders: string[];
}>();

const { t } = useI18n();

const form = useForm({
    provider: props.availableProviders[0] ?? 'openai',
    api_key: '',
});

const submit = () => {
    form.post('/settings/provider-keys', {
        onSuccess: () => form.reset(),
    });
};

const toggleActive = (key: Key) => {
    useForm({ is_active: !key.is_active }).patch(`/settings/provider-keys/${key.id}`);
};

const destroy = (id: number) => {
    if (confirm(t('settings.providerKeys.deleteConfirm'))) {
        useForm({}).delete(`/settings/provider-keys/${id}`);
    }
};
</script>

<template>
    <AppLayout>
        <Head><title>{{ t('settings.providerKeys.title') }}</title></Head>

        <h1 class="font-display text-2xl font-bold tracking-tight text-ink-950">{{ t('settings.providerKeys.title') }}</h1>

        <form class="mt-6 space-y-4 rounded-2xl border border-ink-100 bg-card p-5" @submit.prevent="submit">
            <OField :label="t('settings.providerKeys.provider')" for="provider" required>
                <select
                    id="provider"
                    v-model="form.provider"
                    class="w-full rounded-lg border border-ink-200 bg-card px-3 py-2 text-sm focus:border-accent-500"
                    required
                >
                    <option v-for="p in availableProviders" :key="p" :value="p">{{ p }}</option>
                </select>
            </OField>

            <OField :label="t('settings.providerKeys.apiKey')" for="api_key" required>
                <OInput id="api_key" v-model="form.api_key" type="password" autocomplete="off" required />
            </OField>

            <OButton type="submit" :disabled="form.processing">{{ t('settings.providerKeys.save') }}</OButton>
        </form>

        <div v-if="keys.length > 0" class="mt-8 space-y-3">
            <OPanel v-for="key in keys" :key="key.id" :title="key.provider" :subtitle="key.last_used_at ? `Last used: ${new Date(key.last_used_at).toLocaleString()}` : 'Never used'">
                <div class="flex items-center justify-between gap-2">
                    <div class="flex items-center gap-2 text-sm text-ink-500">
                        <span class="h-2 w-2 rounded-full" :class="key.is_active ? 'bg-mint-500' : 'bg-ink-300'" />
                        <span>{{ key.is_active ? t('common.active') : t('common.inactive') }}</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <OButton size="sm" variant="secondary" @click="toggleActive(key)">
                            {{ key.is_active ? t('common.deactivate') : t('common.activate') }}
                        </OButton>
                        <OButton size="sm" variant="danger" @click="destroy(key.id)">{{ t('common.delete') }}</OButton>
                    </div>
                </div>
            </OPanel>
        </div>
    </AppLayout>
</template>
