<script setup lang="ts">
import { computed } from 'vue';
import { Head, usePage, useForm, router } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
import AppLayout from '../../layouts/AppLayout.vue';
import OPanel from '../../components/ui/OPanel.vue';
import OButton from '../../components/ui/OButton.vue';
import OField from '../../components/ui/OField.vue';
import OInput from '../../components/ui/OInput.vue';

defineProps<{
    sessions: Array<{ id: string; agent: string; ip: string; last_active: string; current: boolean }>;
}>();

const { t, locale } = useI18n();
const page = usePage();
const user = computed(() => (page.props as unknown as { auth: { user: { name: string; email: string; locale: string; notify_run_completed_mail: boolean } } }).auth.user);

const profileForm = useForm({
    name: user.value.name,
    locale: user.value.locale,
    notify_run_completed_mail: user.value.notify_run_completed_mail ?? true,
});

const passwordForm = useForm({
    current_password: '',
    password: '',
    password_confirmation: '',
});

const logoutForm = useForm({ password: '' });

const saveProfile = () => profileForm.patch('/settings/profile', {
    onSuccess: () => {
        locale.value = profileForm.locale;
        document.cookie = `octavia_locale=${profileForm.locale}; path=/; max-age=31536000`;
    },
});

const savePassword = () => passwordForm.patch('/settings/password', {
    onSuccess: () => passwordForm.reset(),
});
</script>

<template>
    <AppLayout>
        <Head><title>{{ t('settings.title') }}</title></Head>

        <h1 class="font-display text-2xl font-bold tracking-tight text-ink-950">{{ t('settings.title') }}</h1>

        <div class="mt-6 max-w-2xl space-y-6">
            <OPanel :title="t('settings.profile')" :subtitle="user.email">
                <form class="space-y-5" @submit.prevent="saveProfile">
                    <OField :label="t('auth.name')" for="name" required>
                        <OInput id="name" v-model="profileForm.name" required />
                    </OField>
                    <OField :label="t('settings.language')" for="locale">
                        <select id="locale" v-model="profileForm.locale" class="w-full rounded-lg border border-ink-200 bg-card px-3 py-2 text-sm focus:border-accent-500">
                            <option value="en">English</option>
                            <option value="de">Deutsch</option>
                        </select>
                    </OField>
                    <label class="flex cursor-pointer items-start gap-3">
                        <input
                            v-model="profileForm.notify_run_completed_mail"
                            type="checkbox"
                            class="mt-0.5 h-4 w-4 rounded border-ink-200 accent-accent-600"
                        />
                        <span>
                            <span class="block text-sm font-medium text-ink-900">{{ t('settings.notifyRunCompleted') }}</span>
                            <span class="mt-0.5 block text-xs text-ink-500">{{ t('settings.notifyRunCompletedHint') }}</span>
                        </span>
                    </label>
                    <OButton type="submit" :disabled="profileForm.processing">{{ t('settings.save') }}</OButton>
                </form>
            </OPanel>

            <OPanel :title="t('settings.password')">
                <form class="space-y-5" @submit.prevent="savePassword">
                    <OField :label="t('settings.currentPassword')" for="current_password" required>
                        <OInput id="current_password" v-model="passwordForm.current_password" type="password" autocomplete="current-password" required />
                    </OField>
                    <p v-if="passwordForm.errors.current_password" class="-mt-3 text-xs text-rose-450">{{ passwordForm.errors.current_password }}</p>
                    <div class="grid gap-4 sm:grid-cols-2">
                        <OField :label="t('settings.newPassword')" for="new_password" required>
                            <OInput id="new_password" v-model="passwordForm.password" type="password" autocomplete="new-password" required />
                        </OField>
                        <OField :label="t('auth.confirmPassword')" for="confirm_password" required>
                            <OInput id="confirm_password" v-model="passwordForm.password_confirmation" type="password" autocomplete="new-password" required />
                        </OField>
                    </div>
                    <OButton type="submit" :disabled="passwordForm.processing">{{ t('settings.save') }}</OButton>
                </form>
            </OPanel>

            <OPanel :title="t('settings.sessions')">
                <ul class="divide-y divide-ink-100">
                    <li v-for="session in sessions" :key="session.id" class="flex items-center gap-3 py-2.5 text-sm">
                        <span class="h-2 w-2 rounded-full" :class="session.current ? 'bg-mint-500' : 'bg-ink-200'" aria-hidden="true" />
                        <span class="min-w-0 flex-1 truncate text-ink-700">{{ session.agent || 'Unknown device' }}</span>
                        <span class="text-xs text-ink-300">{{ session.ip }}</span>
                    </li>
                </ul>
                <form class="mt-4 flex items-end gap-3 border-t border-ink-100 pt-4" @submit.prevent="router.post('/settings/logout-others', { password: logoutForm.password })">
                    <OField :label="t('auth.password')" for="logout_pw" class="flex-1">
                        <OInput id="logout_pw" v-model="logoutForm.password" type="password" autocomplete="current-password" required />
                    </OField>
                    <OButton variant="secondary" type="submit" :disabled="logoutForm.processing">{{ t('settings.logOutOtherSessions') }}</OButton>
                </form>
            </OPanel>
        </div>
    </AppLayout>
</template>
