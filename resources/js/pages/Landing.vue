<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
import PublicLayout from '../layouts/PublicLayout.vue';

defineProps<{ page: string }>();

const { t } = useI18n();

const features = [
    {
        title: 'Benchmarks that mean something',
        body: 'Define test cases with concrete success criteria — required phrases, forbidden content, patterns, or AI-judged requirements. Group them by domain: coding, marketing, sales.',
        icon: 'M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z',
    },
    {
        title: 'Evolution engine',
        body: 'Start from any prompt. Octavia evaluates it against your benchmarks, mutates it, keeps what scores better and repeats — until the target score is reached.',
        icon: 'M3.75 13.5l10.5-11.25L12 10.5h8.25L9.75 21.75 12 13.5H3.75Z',
    },
    {
        title: 'Every step, fully visible',
        body: 'Like fine-tuning dashboards: see the exact prompt at step 5, its score, which benchmark requirements failed and why. Nothing is a black box.',
        icon: 'M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 0 1 3 19.875v-6.75ZM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 0 1-1.125-1.125V8.625ZM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 0 1-1.125-1.125V4.125Z',
    },
    {
        title: 'Live playground',
        body: 'Test any prompt version instantly against a single case or a full benchmark suite before you commit to a run.',
        icon: 'M15.75 17.25v3.375c0 .621-.504 1.125-1.125 1.125h-9.75a1.125 1.125 0 0 1-1.125-1.125V7.875c0-.621.504-1.125 1.125-1.125H6.75a9.06 9.06 0 0 1 1.5.124m7.5 10.376h3.375c.621 0 1.125-.504 1.125-1.125V11.25c0-4.46-3.243-8.161-7.5-8.876a9.06 9.06 0 0 0-1.5-.124H9.375c-.621 0-1.125.504-1.125 1.125v3.5m7.5 10.375H9.375a1.125 1.125 0 0 1-1.125-1.125v-9.25m12 6.625v-1.875a3.375 3.375 0 0 0-3.375-3.375h-1.5a1.125 1.125 0 0 1-1.125-1.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H9.75',
    },
    {
        title: 'Versioned prompt library',
        body: 'Every change is a version with a changelog. Restore any previous state, compare versions, and always know which prompt produced which result.',
        icon: 'M12 6.042A8.967 8.967 0 0 0 6 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 0 1 6 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 0 1 6-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0 0 18 18a8.967 8.967 0 0 0-6 2.292m0-14.25v14.25',
    },
    {
        title: 'Community marketplace',
        body: 'Install benchmarks and prompts published by others — versioned, reviewed and ready to run. Publish your own in one click.',
        icon: 'M13.5 21v-7.5a.75.75 0 0 1 .75-.75h3a.75.75 0 0 1 .75.75V21m-4.5 0H2.36m11.14 0H18m0 0h3.64m-1.39 0V9.349M3.75 21V9.349m0 0a3.001 3.001 0 0 0 3.75-.615A2.993 2.993 0 0 0 9.75 9.75c.896 0 1.7-.393 2.25-1.016a2.993 2.993 0 0 0 2.25 1.016c.896 0 1.7-.393 2.25-1.015a3.001 3.001 0 0 0 3.75.614m-16.5 0a3.004 3.004 0 0 1-.621-4.72l1.189-1.19A1.5 1.5 0 0 1 5.378 3h13.243a1.5 1.5 0 0 1 1.06.44l1.19 1.189a3 3 0 0 1-.621 4.72M6.75 18h3.75a.75.75 0 0 0 .75-.75V13.5a.75.75 0 0 0-.75-.75H6.75a.75.75 0 0 0-.75.75v3.75c0 .414.336.75.75.75Z',
    },
];

const steps = [
    {
        n: '01',
        title: 'Describe what "good" looks like',
        body: 'Build a benchmark with the wizard: add test cases, define success criteria per case, set weights. Two minutes, no code.',
    },
    {
        n: '02',
        title: 'Run the evolution',
        body: 'Octavia evaluates your prompt, mutates it, benchmarks each candidate and hill-climbs until the target score is hit or steps run out.',
    },
    {
        n: '03',
        title: 'Inspect, keep, share',
        body: 'Review every step in detail, keep the winning prompt as a new version, publish it or its benchmark to the marketplace.',
    },
];

const faqs = [
    {
        q: 'How is this different from just asking an AI to improve my prompt?',
        a: 'Asking for improvements gives you no evidence. Octavia measures: every mutation is scored against your own test cases, so you know exactly whether a change helped, hurt, or did nothing — and you can replay the whole history.',
    },
    {
        q: 'Do I need coding skills?',
        a: 'No. Benchmarks are built with a guided wizard using plain language. Criteria like "output contains X" or "an AI judge checks Y" need no code. Regex criteria are available when you want them.',
    },
    {
        q: 'Which AI models does Octavia use?',
        a: 'Octavia talks to any OpenAI-compatible endpoint (OpenAI, Azure, OpenRouter, local models via Ollama). A built-in deterministic mock model lets you explore the full workflow without any API key.',
    },
    {
        q: 'Who owns the prompts and benchmarks I create?',
        a: 'You do. Everything is private by default. Publishing to the marketplace is opt-in, per item, and reversible.',
    },
];
</script>

<template>
    <PublicLayout>
        <Head>
            <title>Octavia — Benchmark, evolve and fine-tune your prompts</title>
            <meta name="description" :content="t('landing.heroSubtitle')" />
            <meta property="og:title" content="Octavia — Benchmark, evolve and fine-tune your prompts" />
            <meta property="og:description" :content="t('landing.heroSubtitle')" />
            <meta property="og:type" content="website" />
            <meta name="twitter:card" content="summary_large_image" />
            <script type="application/ld+json">{{ JSON.stringify({
                '@context': 'https://schema.org',
                '@type': 'SoftwareApplication',
                name: 'Octavia',
                applicationCategory: 'DeveloperApplication',
                description: 'Prompt laboratory: benchmark, evolve and fine-tune prompts against custom benchmarks.',
                offers: { '@type': 'Offer', price: '0', priceCurrency: 'USD' },
            }) }}</script>
        </Head>

        <!-- Hero -->
        <section class="relative overflow-hidden">
            <div class="pointer-events-none absolute inset-x-0 -top-40 h-80 bg-[radial-gradient(60%_60%_at_50%_50%,rgba(117,99,232,0.12),transparent)]" aria-hidden="true" />
            <div class="mx-auto max-w-6xl px-4 pb-24 pt-24 text-center sm:px-6 sm:pb-28 sm:pt-32">
                <p v-reveal class="mb-5 inline-flex items-center gap-2 rounded-full border border-violet-200 bg-violet-50 px-3 py-1 text-xs font-medium text-violet-700">
                    <span class="h-1.5 w-1.5 rounded-full bg-violet-500" aria-hidden="true" />
                    Fine-tuning, but for prompts
                </p>
                <h1
                    v-reveal
                    class="mx-auto max-w-5xl font-display text-4xl font-bold leading-tight tracking-tight text-ink-950 sm:text-5xl md:text-6xl"
                >
                    {{ t('landing.heroTitle') }}
                </h1>
                <p v-reveal class="mx-auto mt-6 max-w-2xl text-lg leading-relaxed text-ink-500">
                    {{ t('landing.heroSubtitle') }}
                </p>
                <div v-reveal class="mt-9 flex flex-col items-center justify-center gap-3 sm:flex-row">
                    <a href="/register" class="w-full rounded-lg bg-violet-600 px-6 py-3 text-base font-medium text-white transition-colors hover:bg-violet-700 sm:w-auto">
                        {{ t('landing.cta') }}
                    </a>
                    <a href="#how" class="w-full rounded-lg border border-ink-200 bg-white px-6 py-3 text-base font-medium text-ink-900 transition-colors hover:border-ink-300 sm:w-auto">
                        {{ t('landing.secondaryCta') }}
                    </a>
                </div>
                <div v-reveal class="group mx-auto mt-16 max-w-4xl rounded-card border border-ink-100 bg-white p-2 shadow-pop transition-all duration-700 ease-out hover:-translate-y-1 hover:shadow-[0_24px_64px_rgb(16_16_24/0.12)]">
                    <div class="rounded-lg border border-ink-100 bg-paper-50 p-5 text-left font-mono text-xs sm:text-sm">
                        <div class="flex items-center justify-between border-b border-ink-100 pb-3">
                            <span class="font-sans font-semibold text-ink-900">Run · Tagline writer × Tagline quality</span>
                            <span class="rounded-full bg-mint-100 px-2 py-0.5 font-sans text-xs font-medium text-mint-600">completed</span>
                        </div>
                        <div class="space-y-2 pt-3 text-ink-700">
                            <div class="flex items-center gap-3"><span class="w-14 shrink-0 text-ink-300">step 1</span><span>initial prompt</span><span class="ml-auto w-16 rounded-full bg-rose-450/90 px-2 py-0.5 text-center font-sans text-white">33%</span></div>
                            <div class="flex items-center gap-3"><span class="w-14 shrink-0 text-ink-300">step 2</span><span>+ explicit brand-name requirement</span><span class="ml-auto w-16 rounded-full bg-amber-450 px-2 py-0.5 text-center font-sans text-white">67%</span></div>
                            <div class="flex items-center gap-3"><span class="w-14 shrink-0 text-ink-300">step 3</span><span>+ length constraint added</span><span class="ml-auto w-16 rounded-full bg-mint-500 px-2 py-0.5 text-center font-sans text-white">100%</span></div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- How it works -->
        <section id="how" class="border-y border-ink-100 bg-white py-24 md:py-32">
            <div class="mx-auto max-w-6xl px-4 sm:px-6">
                <h2 class="text-center font-display text-3xl font-bold tracking-tight text-ink-950">{{ t('landing.howTitle') }}</h2>
                <div class="mt-12 grid gap-10 md:mt-16 md:grid-cols-3 md:gap-12">
                    <div v-for="(step, i) in steps" :key="step.n" v-reveal="{ delay: i * 120 }">
                        <span class="font-mono text-sm font-semibold text-violet-600">{{ step.n }}</span>
                        <h3 class="mt-3 font-display text-lg font-semibold text-ink-950">{{ step.title }}</h3>
                        <p class="mt-2 leading-relaxed text-ink-500">{{ step.body }}</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Features -->
        <section id="features" class="py-24 md:py-32">
            <div class="mx-auto max-w-6xl px-4 sm:px-6">
                <h2 class="text-center font-display text-3xl font-bold tracking-tight text-ink-950">{{ t('landing.featuresTitle') }}</h2>
                <div class="mt-12 grid gap-6 sm:grid-cols-2 md:mt-16 lg:grid-cols-3">
                    <div
                        v-for="(feature, i) in features"
                        :key="feature.title"
                        v-reveal="{ delay: (i % 3) * 100 }"
                        class="group rounded-card border border-ink-100 bg-white p-6 shadow-panel transition-all duration-500 ease-out hover:-translate-y-1 hover:border-violet-200 hover:shadow-pop"
                    >
                        <span class="flex h-10 w-10 items-center justify-center rounded-lg bg-violet-50 text-violet-600 transition-colors duration-500 group-hover:bg-violet-600 group-hover:text-white">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" :d="feature.icon" /></svg>
                        </span>
                        <h3 class="mt-4 font-display text-base font-semibold text-ink-950">{{ feature.title }}</h3>
                        <p class="mt-2 text-sm leading-relaxed text-ink-500">{{ feature.body }}</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- FAQ -->
        <section class="border-t border-ink-100 bg-white py-24 md:py-32">
            <div class="mx-auto max-w-3xl px-4 sm:px-6">
                <h2 class="text-center font-display text-3xl font-bold tracking-tight text-ink-950">{{ t('landing.faqTitle') }}</h2>
                <div v-reveal class="mt-10 space-y-3">
                    <details v-for="faq in faqs" :key="faq.q" class="group rounded-card border border-ink-100 bg-paper-50 px-5 py-4 open:bg-white">
                        <summary class="cursor-pointer list-none font-medium text-ink-950 marker:hidden [&::-webkit-details-marker]:hidden">
                            <span class="flex items-center justify-between gap-4">
                                {{ faq.q }}
                                <svg class="h-4 w-4 shrink-0 text-ink-300 transition-transform group-open:rotate-45" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" d="M12 4.5v15m7.5-7.5h-15" /></svg>
                            </span>
                        </summary>
                        <p class="mt-3 text-sm leading-relaxed text-ink-500">{{ faq.a }}</p>
                    </details>
                </div>
            </div>
        </section>

        <!-- Final CTA -->
        <section class="py-24 md:py-32">
            <div class="mx-auto max-w-6xl px-4 sm:px-6">
                <div v-reveal class="rounded-card bg-ink-950 px-8 py-14 text-center md:py-20">
                    <h2 class="font-display text-3xl font-bold tracking-tight text-white">Your next prompt deserves evidence.</h2>
                    <p class="mx-auto mt-3 max-w-xl text-ink-300">Create a free account, build your first benchmark in minutes and watch Octavia evolve your prompt step by step.</p>
                    <a href="/register" class="mt-8 inline-block rounded-lg bg-violet-600 px-6 py-3 font-medium text-white transition-colors hover:bg-violet-500">
                        {{ t('landing.cta') }}
                    </a>
                </div>
            </div>
        </section>
    </PublicLayout>
</template>
