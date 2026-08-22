<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
import AppLayout from '../../layouts/AppLayout.vue';
import OPanel from '../../components/ui/OPanel.vue';
import OEmptyState from '../../components/ui/OEmptyState.vue';

defineProps<{
    balance: number;
    transactions: Array<{
        id: number;
        delta: number;
        reason: string;
        meta: Record<string, unknown> | null;
        created_at: string | null;
    }>;
}>();

const { t, d } = useI18n();

const reasonLabel: Record<string, string> = {
    signup_grant: 'billing.signup_grant',
    admin_grant: 'billing.admin_grant',
    run_reserved: 'billing.reserved',
    run_refund: 'billing.refunded',
};

const reasonText = (reason: string) => {
    const key = reasonLabel[reason];
    return key ? t(key) : reason;
};
</script>

<template>
    <AppLayout>
        <Head><title>{{ t('billing.title') }}</title></Head>

        <h1 class="font-display text-2xl font-bold tracking-tight text-ink-950">{{ t('billing.title') }}</h1>
        <p class="mt-1 max-w-2xl text-sm text-ink-500">{{ t('billing.subtitle') }}</p>

        <div class="mt-6 max-w-2xl space-y-6">
            <OPanel :title="t('billing.balance')">
                <span class="font-display text-4xl font-bold text-violet-700">{{ balance }}</span>
            </OPanel>

            <OPanel :title="t('billing.history')">
                <OEmptyState v-if="transactions.length === 0" :title="t('billing.empty')" />
                <ul v-else class="divide-y divide-ink-100">
                    <li v-for="tx in transactions" :key="tx.id" class="flex items-center justify-between py-2.5 text-sm">
                        <div>
                            <span class="font-medium text-ink-950">{{ reasonText(tx.reason) }}</span>
                            <span v-if="tx.meta?.run_id" class="ml-1 text-xs text-ink-300">#{{ tx.meta.run_id }}</span>
                            <span v-if="tx.created_at" class="block text-xs text-ink-300">{{ d(new Date(tx.created_at), 'short') }}</span>
                        </div>
                        <span :class="tx.delta >= 0 ? 'font-semibold text-mint-600' : 'font-semibold text-ink-950'">
                            {{ tx.delta >= 0 ? '+' : '' }}{{ tx.delta }}
                        </span>
                    </li>
                </ul>
            </OPanel>
        </div>
    </AppLayout>
</template>
