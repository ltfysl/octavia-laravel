<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import PublicLayout from '../layouts/PublicLayout.vue';

const props = defineProps<{ page: string }>();

const content: Record<string, { title: string; sections: Array<{ h: string; p: string }> }> = {
    privacy: {
        title: 'Privacy policy',
        sections: [
            { h: 'What we store', p: 'Octavia stores your account details (name, email, hashed password), the prompts and benchmarks you create, and the results of runs you start. We do not sell this data or share it with third parties for advertising.' },
            { h: 'Model providers', p: 'When you run evaluations, your prompt content and test inputs are sent to the AI model provider configured for your workspace. You can use a self-hosted, OpenAI-compatible endpoint to keep all data inside your own infrastructure.' },
            { h: 'Deletion', p: 'Deleting a prompt, benchmark or run removes it permanently. Deleting your account removes all associated data.' },
        ],
    },
    terms: {
        title: 'Terms of service',
        sections: [
            { h: 'The service', p: 'Octavia provides tools to evaluate, optimize and share prompts and benchmarks. The service is provided as-is while it is in public development; availability and APIs may change.' },
            { h: 'Your content', p: 'You retain ownership of everything you create. By publishing an item to the marketplace you allow other users of Octavia to install and use a copy of it.' },
            { h: 'Acceptable use', p: 'Do not use Octavia to generate unlawful content, to attack model providers, or to resell access to the platform.' },
        ],
    },
};

const doc = content[props.page] ?? content.privacy;
</script>

<template>
    <PublicLayout>
        <Head>
            <title>{{ doc.title }} — Octavia</title>
            <meta name="description" :content="`${doc.title} for Octavia, the prompt laboratory.`" />
        </Head>
        <article class="mx-auto max-w-3xl px-4 py-16 sm:px-6">
            <h1 class="font-display text-3xl font-bold tracking-tight text-ink-950">{{ doc.title }}</h1>
            <p class="mt-2 text-sm text-ink-300">Last updated: August 2026</p>
            <section v-for="section in doc.sections" :key="section.h" class="mt-8">
                <h2 class="font-display text-lg font-semibold text-ink-950">{{ section.h }}</h2>
                <p class="mt-2 leading-relaxed text-ink-500">{{ section.p }}</p>
            </section>
        </article>
    </PublicLayout>
</template>
