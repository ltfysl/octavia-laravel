<script setup lang="ts">
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';
import { useI18n } from 'vue-i18n';
import AppLayout from '../../layouts/AppLayout.vue';
import OBadge from '../../components/ui/OBadge.vue';
import OButton from '../../components/ui/OButton.vue';
import OEmptyState from '../../components/ui/OEmptyState.vue';
import OField from '../../components/ui/OField.vue';
import OInput from '../../components/ui/OInput.vue';

defineProps<{
    teams: Array<{ id: number; name: string; role: string | null; member_count: number; is_owner: boolean }>;
}>();

const { t } = useI18n();

const showForm = ref(false);
const form = useForm({ name: '' });

const create = () => form.post('/teams', {
    onSuccess: () => {
        form.reset();
        showForm.value = false;
    },
});

const removeTeam = (id: number) => {
    if (confirm(t('teams.deleteConfirm'))) {
        router.delete(`/teams/${id}`);
    }
};
</script>

<template>
    <AppLayout>
        <Head><title>{{ t('teams.title') }}</title></Head>

        <div class="flex flex-wrap items-start justify-between gap-4">
            <div>
                <h1 class="font-display text-2xl font-bold tracking-tight text-ink-950">{{ t('teams.title') }}</h1>
                <p class="mt-1 text-sm text-ink-500">{{ t('teams.subtitle') }}</p>
            </div>
            <OButton @click="showForm = !showForm">+ {{ t('teams.new') }}</OButton>
        </div>

        <!-- Create form -->
        <div v-if="showForm" class="mt-6 max-w-md rounded-card border border-ink-100 bg-white p-5 shadow-panel">
            <form class="space-y-4" @submit.prevent="create">
                <OField :label="t('teams.teamName')" for="team-name" required>
                    <OInput id="team-name" v-model="form.name" required autofocus />
                </OField>
                <p v-if="form.errors.name" class="-mt-2 text-xs text-rose-450">{{ form.errors.name }}</p>
                <div class="flex justify-end gap-3">
                    <OButton variant="ghost" type="button" @click="showForm = false">{{ t('common.cancel') }}</OButton>
                    <OButton type="submit" :disabled="form.processing || !form.name.trim()">
                        {{ form.processing ? t('common.saving') : t('common.create') }}
                    </OButton>
                </div>
            </form>
        </div>

        <OEmptyState v-if="teams.length === 0 && !showForm" class="mt-8" :title="t('teams.empty')">
            <template #action>
                <OButton @click="showForm = true">+ {{ t('teams.new') }}</OButton>
            </template>
        </OEmptyState>

        <ul v-else class="mt-8 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
            <li v-for="team in teams" :key="team.id">
                <article class="flex h-full flex-col rounded-card border border-ink-100 bg-white p-5 shadow-panel">
                    <div class="flex items-center justify-between gap-2">
                        <h2 class="truncate font-display text-sm font-semibold text-ink-950">{{ team.name }}</h2>
                        <OBadge v-if="team.is_owner" tone="violet">{{ t('teams.owner') }}</OBadge>
                    </div>
                    <p class="mt-2 flex-1 text-sm text-ink-500">{{ t('teams.memberCount', { count: team.member_count }) }}</p>
                    <div class="mt-4 flex items-center justify-between border-t border-ink-100 pt-3">
                        <Link :href="`/teams/${team.id}`" class="text-xs font-medium text-violet-600 hover:text-violet-700">
                            {{ t('common.edit') }} →
                        </Link>
                        <button
                            v-if="team.is_owner"
                            type="button"
                            class="text-xs text-rose-450 hover:underline"
                            @click="removeTeam(team.id)"
                        >
                            {{ t('common.delete') }}
                        </button>
                    </div>
                </article>
            </li>
        </ul>
    </AppLayout>
</template>
