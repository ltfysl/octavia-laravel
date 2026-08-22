<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
import AuthLayout from '../../layouts/AuthLayout.vue';
import OButton from '../../components/ui/OButton.vue';
import OField from '../../components/ui/OField.vue';
import OInput from '../../components/ui/OInput.vue';

const { t } = useI18n();

const form = useForm({
    email: '',
    password: '',
    remember: true,
});

const submit = () => form.post('/login');
</script>

<template>
    <AuthLayout>
        <Head><title>{{ t('nav.logIn') }}</title><meta name="robots" content="noindex" /></Head>

        <h1 class="font-display text-2xl font-bold tracking-tight text-ink-950">{{ t('auth.welcomeBack') }}</h1>
        <p class="mt-1 text-sm text-ink-500">{{ t('auth.loginTitle') }}</p>

        <form class="mt-8 space-y-5" @submit.prevent="submit">
            <OField :label="t('auth.email')" for="email" required>
                <OInput id="email" v-model="form.email" type="email" autocomplete="email" required autofocus />
            </OField>
            <p v-if="form.errors.email" class="-mt-3 text-xs text-rose-450">{{ form.errors.email }}</p>

            <div>
                <OField :label="t('auth.password')" for="password" required>
                    <OInput id="password" v-model="form.password" type="password" autocomplete="current-password" required />
                </OField>
                <div class="mt-1.5 flex items-center justify-between text-xs">
                    <label class="flex items-center gap-2 text-ink-500">
                        <input v-model="form.remember" type="checkbox" class="h-3.5 w-3.5 rounded border-ink-200 accent-accent-600" />
                        {{ t('auth.rememberMe') }}
                    </label>
                    <Link href="/forgot-password" class="font-medium text-accent-600 hover:text-accent-700">{{ t('auth.forgotPassword') }}</Link>
                </div>
                <p v-if="form.errors.password" class="mt-1 text-xs text-rose-450">{{ form.errors.password }}</p>
            </div>

            <OButton type="submit" size="lg" class="w-full" :disabled="form.processing">
                {{ form.processing ? t('common.loading') : t('nav.logIn') }}
            </OButton>
        </form>

        <p class="mt-6 text-center text-sm text-ink-500">
            {{ t('auth.noAccount') }}
            <Link href="/register" class="font-medium text-accent-600 hover:text-accent-700">{{ t('nav.signUp') }}</Link>
        </p>
    </AuthLayout>
</template>
