<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
import AuthLayout from '../../layouts/AuthLayout.vue';
import OButton from '../../components/ui/OButton.vue';
import OField from '../../components/ui/OField.vue';
import OInput from '../../components/ui/OInput.vue';

const { t } = useI18n();

const props = defineProps<{
    email: string | null;
    token: string;
}>();

const form = useForm({
    token: props.token,
    email: props.email ?? '',
    password: '',
    password_confirmation: '',
});

const submit = () => form.post('/reset-password');
</script>

<template>
    <AuthLayout>
        <Head><title>{{ t('auth.resetPassword') }}</title><meta name="robots" content="noindex" /></Head>

        <h1 class="font-display text-2xl font-bold tracking-tight text-ink-950">{{ t('auth.resetPassword') }}</h1>

        <form class="mt-8 space-y-5" @submit.prevent="submit">
            <OField :label="t('auth.email')" for="email" required>
                <OInput id="email" v-model="form.email" type="email" autocomplete="email" required />
            </OField>
            <p v-if="form.errors.email" class="-mt-3 text-xs text-rose-450">{{ form.errors.email }}</p>

            <OField :label="t('auth.newPassword')" for="password" required>
                <OInput id="password" v-model="form.password" type="password" autocomplete="new-password" required autofocus />
            </OField>
            <p v-if="form.errors.password" class="-mt-3 text-xs text-rose-450">{{ form.errors.password }}</p>

            <OField :label="t('auth.confirmPassword')" for="password_confirmation" required>
                <OInput id="password_confirmation" v-model="form.password_confirmation" type="password" autocomplete="new-password" required />
            </OField>

            <OButton type="submit" size="lg" class="w-full" :disabled="form.processing">
                {{ form.processing ? t('common.loading') : t('auth.resetPassword') }}
            </OButton>
        </form>
    </AuthLayout>
</template>
