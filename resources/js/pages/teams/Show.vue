<script setup lang="ts">
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
import AppLayout from '../../layouts/AppLayout.vue';
import OBadge from '../../components/ui/OBadge.vue';
import OButton from '../../components/ui/OButton.vue';
import OField from '../../components/ui/OField.vue';
import OInput from '../../components/ui/OInput.vue';

const props = defineProps<{
    team: { id: number; name: string; owner_id: number };
    members: Array<{ id: number; name: string; email: string; role: string }>;
}>();

const { t } = useI18n();

const inviteForm = useForm({
    email: '',
    role: 'member',
});

const invite = () => inviteForm.post(`/teams/${props.team.id}/invite`, {
    preserveScroll: true,
    onSuccess: () => inviteForm.reset(),
});

const removeMember = (id: number) => {
    if (confirm(t('teams.removeConfirm'))) {
        router.delete(`/teams/${props.team.id}/members/${id}`, { preserveScroll: true });
    }
};
</script>

<template>
    <AppLayout>
        <Head><title>{{ team.name }}</title></Head>

        <div class="flex items-start justify-between gap-4">
            <h1 class="font-display text-2xl font-bold tracking-tight text-ink-950">{{ team.name }}</h1>
            <Link href="/teams" class="text-sm text-ink-500 hover:text-ink-900">{{ t('common.back') }}</Link>
        </div>

        <!-- Members -->
        <section class="mt-8">
            <h2 class="mb-3 text-sm font-semibold uppercase tracking-wide text-ink-300">{{ t('teams.members') }}</h2>
            <ul class="divide-y divide-ink-100 rounded-card border border-ink-100 bg-white shadow-panel">
                <li v-for="member in members" :key="member.id" class="flex items-center gap-3 px-5 py-3">
                    <span class="flex h-7 w-7 shrink-0 items-center justify-center rounded-full bg-ink-100 text-xs font-semibold text-ink-700">
                        {{ member.name.charAt(0).toUpperCase() }}
                    </span>
                    <div class="min-w-0 flex-1">
                        <span class="block truncate text-sm font-medium text-ink-900">{{ member.name }}</span>
                        <span class="text-xs text-ink-300">{{ member.email }}</span>
                    </div>
                    <OBadge :tone="member.role === 'owner' ? 'violet' : member.role === 'admin' ? 'mint' : 'neutral'">
                        {{ member.role }}
                    </OBadge>
                    <button
                        v-if="member.role !== 'owner'"
                        type="button"
                        class="shrink-0 text-xs text-rose-450 hover:underline"
                        @click="removeMember(member.id)"
                    >
                        {{ t('common.delete') }}
                    </button>
                </li>
            </ul>
        </section>

        <!-- Invite form -->
        <section class="mt-8 max-w-md rounded-card border border-ink-100 bg-white p-5 shadow-panel">
            <h2 class="mb-4 font-display text-sm font-semibold text-ink-950">{{ t('teams.inviteTitle') }}</h2>
            <form class="space-y-4" @submit.prevent="invite">
                <OField :label="t('auth.email')" for="invite-email" required>
                    <OInput id="invite-email" v-model="inviteForm.email" type="email" required />
                </OField>
                <p v-if="inviteForm.errors.email" class="-mt-2 text-xs text-rose-450">{{ inviteForm.errors.email }}</p>
                <OField :label="t('teams.role')" for="invite-role">
                    <select id="invite-role" v-model="inviteForm.role" class="w-full rounded-lg border border-ink-200 bg-white px-3 py-2 text-sm focus:border-violet-500">
                        <option value="member">{{ t('teams.member') }}</option>
                        <option value="admin">{{ t('teams.admin') }}</option>
                    </select>
                </OField>
                <OButton type="submit" :disabled="inviteForm.processing || !inviteForm.email.trim()">
                    {{ inviteForm.processing ? t('common.saving') : t('teams.invite') }}
                </OButton>
            </form>
        </section>
    </AppLayout>
</template>
