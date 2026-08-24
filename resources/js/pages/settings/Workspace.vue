<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
import AppLayout from '../../layouts/AppLayout.vue';
import OPanel from '../../components/ui/OPanel.vue';
import OButton from '../../components/ui/OButton.vue';
import OField from '../../components/ui/OField.vue';
import OInput from '../../components/ui/OInput.vue';
import OBadge from '../../components/ui/OBadge.vue';

const props = defineProps<{
    teams: Array<{ id: number; name: string; role: string; member_count: number; is_owner: boolean }>;
}>();

const { t } = useI18n();

const form = useForm({ name: '' });

const createTeam = () => {
    form.post('/teams', { onSuccess: () => form.reset() });
};
</script>

<template>
    <AppLayout>
        <Head><title>{{ t('settings.workspace') }}</title></Head>

        <h1 class="font-display text-2xl font-bold tracking-tight text-ink-950">{{ t('settings.workspace') }}</h1>
        <p class="mt-1 text-sm text-ink-500">{{ t('settings.workspaceSubtitle') }}</p>

        <div class="mt-8 max-w-2xl space-y-6">
            <OPanel :title="t('settings.workspaceCreate')">
                <form class="flex gap-2" @submit.prevent="createTeam">
                    <OField class="flex-1" :label="t('team.name')" for="team-name" inline>
                        <OInput id="team-name" v-model="form.name" required />
                    </OField>
                    <OButton type="submit" :disabled="form.processing">{{ t('common.create') }}</OButton>
                </form>
            </OPanel>

            <OPanel :title="t('settings.workspaceList')">
                <ul v-if="teams.length" class="divide-y divide-ink-100">
                    <li v-for="team in teams" :key="team.id" class="flex items-center justify-between py-3">
                        <div>
                            <p class="font-medium text-ink-900">{{ team.name }}</p>
                            <p class="text-xs text-ink-500">{{ team.member_count }} {{ t('settings.workspaceMembers') }} · {{ team.role }}</p>
                        </div>
                        <div class="flex items-center gap-2">
                            <OBadge :tone="team.is_owner ? 'accent' : 'neutral'">{{ team.is_owner ? t('team.owner') : t('team.member') }}</OBadge>
                            <a :href="`/teams/${team.id}`" class="text-sm text-ink-500 hover:text-ink-900">{{ t('common.manage') }} →</a>
                        </div>
                    </li>
                </ul>
                <p v-else class="text-sm text-ink-400">{{ t('settings.workspaceEmpty') }}</p>
            </OPanel>
        </div>
    </AppLayout>
</template>
