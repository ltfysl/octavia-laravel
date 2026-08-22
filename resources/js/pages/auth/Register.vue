<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
import AuthLayout from '../../layouts/AuthLayout.vue';
import OButton from '../../components/ui/OButton.vue';
import OField from '../../components/ui/OField.vue';
import OInput from '../../components/ui/OInput.vue';

const { t } = useI18n();

const form = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
});

const submit = () => form.post('/register');
</script>

<template>
    <AuthLayout>
        <Head><title>{{ t('nav.signUp') }}</title><meta name="robots" content="noindex" /></Head>

        <h1 class="font-display text-2xl font-bold tracking-tight text-ink-950">{{ t('auth.registerTitle') }}</h1>
        <p class="mt-1 text-sm text-ink-500">Free to start. No credit card.</p>

        <form class="mt-8 space-y-5" @submit.prevent="submit">
            <OField :label="t('auth.name')" for="name" required>
                <OInput id="name" v-model="form.name" autocomplete="name" required autofocus />
            </OField>
            <p v-if="form.errors.name" class="-mt-3 text-xs text-rose-450">{{ form.errors.name }}</p>

            <OField :label="t('auth.email')" for="email" required>
                <OInput id="email" v-model="form.email" type="email" autocomplete="email" required />
            </OField>
            <p v-if="form.errors.email" class="-mt-3 text-xs text-rose-450">{{ form.errors.email }}</p>

            <OField :label="t('auth.password')" for="password" required hint="At least 8 characters.">
                <OInput id="password" v-model="form.password" type="password" autocomplete="new-password" required />
            </OField>
            <p v-if="form.errors.password" class="-mt-3 text-xs text-rose-450">{{ form.errors.password }}</p>

            <OField :label="t('auth.confirmPassword')" for="password_confirmation" required>
                <OInput id="password_confirmation" v-model="form.password_confirmation" type="password" autocomplete="new-password" required />
            </OField>

            <OButton type="submit" size="lg" class="w-full" :disabled="form.processing">
                {{ form.processing ? t('common.loading') : t('auth.signUpFree') }}
            </OButton>
        </form>

        <p class="mt-6 text-center text-sm text-ink-500">
            {{ t('auth.hasAccount') }}
            <Link href="/login" class="font-medium text-violet-600 hover:text-violet-700">{{ t('nav.logIn') }}</Link>
        </p>
    </AuthLayout>
</template>
