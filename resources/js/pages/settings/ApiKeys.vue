<script setup lang="ts">
import { computed } from 'vue';
import { Head, useForm, router, usePage } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
import AppLayout from '../../layouts/AppLayout.vue';
import OPanel from '../../components/ui/OPanel.vue';
import OButton from '../../components/ui/OButton.vue';
import OField from '../../components/ui/OField.vue';
import OInput from '../../components/ui/OInput.vue';
import OEmptyState from '../../components/ui/OEmptyState.vue';

interface Token {
    id: number;
    name: string;
    abilities: string[];
    last_used_at: string | null;
    created_at: string;
    uses_count: number;
}

const props = defineProps<{
    tokens: Token[];
    availableScopes: string[];
}>();

const { t } = useI18n();
const page = usePage();
const flash = computed(() => page.props.flash as { success?: string; createdToken?: string });
const createdToken = computed(() => flash.value.createdToken);

const form = useForm({
    name: '',
    abilities: [] as string[],
});

const submit = () => {
    form.post('/settings/api-keys', {
        onSuccess: () => {
            form.reset();
        },
    });
};

const toggleAbility = (ability: string) => {
    if (form.abilities.includes(ability)) {
        form.abilities = form.abilities.filter((a) => a !== ability);
    } else {
        form.abilities.push(ability);
    }
};

const destroy = (id: number) => {
    if (confirm(t('settings.apiKeys.deleteConfirm'))) {
        router.delete(`/settings/api-keys/${id}`);
    }
};
</script>

<template>
    <AppLayout>
        <Head><title>{{ t('settings.apiKeys.title') }}</title></Head>

        <h1 class="font-display text-2xl font-bold tracking-tight text-ink-950">{{ t('settings.apiKeys.title') }}</h1>

        <form class="mt-6 space-y-4 rounded-2xl border border-ink-100 bg-card p-5" @submit.prevent="submit">
            <OField :label="t('settings.apiKeys.name')" for="name" required>
                <OInput id="name" v-model="form.name" required />
            </OField>

            <div>
                <p class="text-sm font-medium text-ink-900">{{ t('settings.apiKeys.scopes') }}</p>
                <div class="mt-2 flex flex-wrap gap-2">
                    <button
                        v-for="scope in availableScopes"
                        :key="scope"
                        type="button"
                        @click="toggleAbility(scope)"
                        class="rounded-full border px-3 py-1 text-xs font-medium transition-colors"
                        :class="form.abilities.includes(scope) ? 'border-accent-600 bg-accent-50 text-accent-700' : 'border-ink-200 bg-card text-ink-500 hover:bg-paper-50'"
                    >
                        {{ scope }}
                    </button>
                </div>
                <p v-if="form.errors.abilities" class="mt-1 text-xs text-rose-450">{{ form.errors.abilities }}</p>
            </div>

            <OButton type="submit" :disabled="form.processing || form.abilities.length === 0">{{ t('settings.apiKeys.create') }}</OButton>

            <p v-if="createdToken" class="mt-2 rounded-lg bg-mint-50 p-3 text-xs text-mint-800 font-mono break-all">{{ createdToken }}</p>
        </form>

        <OEmptyState v-if="tokens.length === 0" class="mt-8" :title="t('settings.apiKeys.empty')" />

        <div v-else class="mt-8 space-y-3">
            <OPanel v-for="token in tokens" :key="token.id" :title="token.name" :subtitle="token.last_used_at ? `${t('settings.apiKeys.lastUsed')} ${new Date(token.last_used_at).toLocaleString()}` : t('settings.apiKeys.neverUsed')">
                <div class="flex flex-wrap gap-1 text-xs text-ink-500">
                    <span v-for="ability in token.abilities" :key="ability" class="rounded-full border border-ink-200 bg-paper-50 px-2 py-0.5">{{ ability }}</span>
                    <span class="ml-auto">{{ token.uses_count }} {{ t('settings.apiKeys.uses') }}</span>
                </div>
                <div class="mt-3">
                    <OButton size="sm" variant="danger" @click="destroy(token.id)">{{ t('common.delete') }}</OButton>
                </div>
            </OPanel>
        </div>
    </AppLayout>
</template>
