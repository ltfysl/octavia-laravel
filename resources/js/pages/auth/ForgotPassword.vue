<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
import AuthLayout from '../../layouts/AuthLayout.vue';
import OButton from '../../components/ui/OButton.vue';
import OField from '../../components/ui/OField.vue';
import OInput from '../../components/ui/OInput.vue';

const { t } = useI18n();

const form = useForm({ email: '' });

const submit = () => form.post('/forgot-password');
</script>

<template>
    <AuthLayout>
        <Head><title>{{ t('auth.forgotPassword') }}</title><meta name="robots" content="noindex" /></Head>

        <h1 class="font-display text-2xl font-bold tracking-tight text-ink-950">{{ t('auth.resetPassword') }}</h1>
        <p class="mt-1 text-sm text-ink-500">{{ t('auth.sendResetLink') }}.</p>

        <form v-if="!form.wasSuccessful" class="mt-8 space-y-5" @submit.prevent="submit">
            <OField :label="t('auth.email')" for="email" required>
                <OInput id="email" v-model="form.email" type="email" autocomplete="email" required autofocus />
            </OField>
            <p v-if="form.errors.email" class="-mt-3 text-xs text-rose-450">{{ form.errors.email }}</p>

            <OButton type="submit" size="lg" class="w-full" :disabled="form.processing">
                {{ form.processing ? t('common.loading') : t('auth.sendResetLink') }}
            </OButton>
        </form>
        <p v-else class="mt-6 rounded-lg bg-mint-100 px-4 py-3 text-sm text-mint-600">
            If the address exists in our system, a reset link is on its way.
        </p>
    </AuthLayout>
</template>
