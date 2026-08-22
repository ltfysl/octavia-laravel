<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
import PublicLayout from '../layouts/PublicLayout.vue';

const { t } = useI18n();

const plans = [
    { key: 'free', featured: false },
    { key: 'pro', featured: true },
] as const;

const howItems = [
    'pricing.how1',
    'pricing.how2',
    'pricing.how3',
] as const;
</script>

<template>
    <PublicLayout>
        <Head>
            <title>Octavia — {{ t('pricing.title') }}</title>
            <meta name="description" :content="t('pricing.subtitle')" />
        </Head>

        <section class="mx-auto max-w-5xl px-4 pb-20 pt-20 text-center sm:px-6 sm:pt-28">
            <h1 class="font-display text-4xl font-bold tracking-tight text-ink-950 sm:text-5xl">{{ t('pricing.title') }}</h1>
            <p class="mx-auto mt-4 max-w-xl text-lg text-ink-500">{{ t('pricing.subtitle') }}</p>

            <div class="mt-14 grid gap-6 text-left sm:grid-cols-2">
                <div
                    v-for="plan in plans"
                    :key="plan.key"
                    class="rounded-card border bg-white p-8 shadow-panel transition-transform duration-500 hover:-translate-y-1"
                    :class="plan.featured ? 'border-violet-300 ring-2 ring-violet-200' : 'border-ink-100'"
                >
                    <div class="flex items-center justify-between">
                        <h2 class="font-display text-xl font-bold text-ink-950">{{ t(`pricing.${plan.key}.name`) }}</h2>
                        <span
                            v-if="plan.featured"
                            class="rounded-full bg-violet-50 px-2.5 py-0.5 text-xs font-medium text-violet-700"
                        >★</span>
                    </div>
                    <p class="mt-4">
                        <span class="font-display text-4xl font-bold text-ink-950">{{ t(`pricing.${plan.key}.price`) }}</span>
                        <span class="ml-2 text-sm text-ink-500">{{ t(`pricing.${plan.key}.period`) }}</span>
                    </p>
                    <ul class="mt-6 space-y-3 text-sm text-ink-700">
                        <li v-for="feature in (t(`pricing.${plan.key}.features`) as unknown as string[])" :key="feature" class="flex items-start gap-2.5">
                            <svg class="mt-0.5 h-4 w-4 shrink-0 text-mint-600" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" /></svg>
                            {{ feature }}
                        </li>
                    </ul>
                    <a
                        :href="plan.key === 'free' ? '/register' : '#'"
                        class="mt-8 block rounded-lg px-4 py-2.5 text-center text-sm font-medium transition-colors"
                        :class="plan.featured
                            ? 'border border-ink-200 bg-white text-ink-500'
                            : 'bg-violet-600 text-white hover:bg-violet-700'"
                    >{{ t(`pricing.${plan.key}.cta`) }}</a>
                </div>
            </div>

            <p class="mt-6 text-xs text-ink-300">{{ t('pricing.note') }}</p>
        </section>

        <section class="border-t border-ink-100 bg-white py-20">
            <div class="mx-auto max-w-3xl px-4 sm:px-6">
                <h2 class="text-center font-display text-2xl font-bold tracking-tight text-ink-950">{{ t('pricing.howTitle') }}</h2>
                <ol class="mt-8 space-y-4">
                    <li v-for="(item, i) in howItems" :key="item" class="flex items-start gap-4">
                        <span class="font-mono text-sm font-semibold text-violet-600">0{{ i + 1 }}</span>
                        <p class="text-sm leading-relaxed text-ink-500">{{ t(item) }}</p>
                    </li>
                </ol>
            </div>
        </section>
    </PublicLayout>
</template>
