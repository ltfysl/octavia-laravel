<script setup lang="ts">
import { ref } from 'vue';
import { Head, router } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
import AuthLayout from '../../layouts/AuthLayout.vue';
import OButton from '../../components/ui/OButton.vue';

const { t, locale } = useI18n();

const selectedLocale = ref(locale.value);
const withSample = ref(true);

const finish = () => {
    router.post('/welcome/complete', {
        locale: selectedLocale.value,
        sample: withSample.value,
    });
};
</script>

<template>
    <AuthLayout>
        <Head><title>Welcome</title><meta name="robots" content="noindex" /></Head>

        <div class="text-center">
            <span class="mx-auto mb-6 flex h-14 w-14 items-center justify-center rounded-2xl bg-accent-600 font-display text-2xl font-bold text-ink-950">O</span>
            <h1 class="font-display text-2xl font-bold tracking-tight text-ink-950">Welcome to your prompt lab</h1>
            <p class="mx-auto mt-2 max-w-sm text-sm leading-relaxed text-ink-500">
                Octavia benchmarks prompts against test suites and evolves them automatically. Two quick choices and you're in.
            </p>
        </div>

        <div class="mt-8 space-y-5">
            <fieldset class="rounded-card border border-ink-100 bg-card p-4">
                <legend class="px-1 text-sm font-medium text-ink-700">{{ t('settings.language') }}</legend>
                <div class="mt-1 grid grid-cols-2 gap-3">
                    <button
                        v-for="lang in [{ value: 'en', label: 'English' }, { value: 'de', label: 'Deutsch' }]"
                        :key="lang.value"
                        type="button"
                        class="rounded-lg border px-4 py-2.5 text-sm font-medium transition-colors"
                        :class="selectedLocale === lang.value ? 'border-accent-500 bg-accent-50 text-accent-700' : 'border-ink-200 text-ink-700 hover:border-ink-300'"
                        :aria-pressed="selectedLocale === lang.value"
                        @click="selectedLocale = lang.value; locale = lang.value"
                    >
                        {{ lang.label }}
                    </button>
                </div>
            </fieldset>

            <label class="flex cursor-pointer items-start gap-3 rounded-card border border-ink-100 bg-card p-4">
                <input v-model="withSample" type="checkbox" class="mt-0.5 h-4 w-4 rounded border-ink-200 accent-accent-600" />
                <span>
                    <span class="block text-sm font-medium text-ink-900">Add starter content</span>
                    <span class="mt-0.5 block text-xs leading-relaxed text-ink-500">Creates an example prompt and a runnable benchmark so you can start your first optimization run immediately.</span>
                </span>
            </label>

            <OButton size="lg" class="w-full" @click="finish">{{ t('common.next') }}</OButton>
            <button type="button" class="w-full text-center text-xs text-ink-300 hover:text-ink-500" @click="router.post('/welcome/complete', { sample: false })">
                {{ t('common.skipTour') }}
            </button>
        </div>
    </AuthLayout>
</template>
