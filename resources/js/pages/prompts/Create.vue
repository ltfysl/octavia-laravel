<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
import AppLayout from '../../layouts/AppLayout.vue';
import OButton from '../../components/ui/OButton.vue';
import OField from '../../components/ui/OField.vue';
import OInput from '../../components/ui/OInput.vue';

const { t } = useI18n();

const form = useForm({
    name: '',
    description: '',
    visibility: 'private',
    content: '',
});

const submit = () => form.post('/prompts');
</script>

<template>
    <AppLayout>
        <Head><title>{{ t('prompts.new') }}</title></Head>

        <div class="flex items-center justify-between">
            <h1 class="font-display text-2xl font-bold tracking-tight text-ink-950">{{ t('prompts.new') }}</h1>
            <Link href="/prompts" class="text-sm text-ink-500 hover:text-ink-900">{{ t('common.back') }}</Link>
        </div>

        <form class="mt-6 grid gap-6 lg:grid-cols-[20rem_1fr]" @submit.prevent="submit">
            <div class="space-y-5">
                <OField :label="t('prompts.name')" for="name" required>
                    <OInput id="name" v-model="form.name" required autofocus />
                </OField>
                <p v-if="form.errors.name" class="-mt-3 text-xs text-rose-450">{{ form.errors.name }}</p>

                <OField :label="t('prompts.description')" for="description">
                    <textarea
                        id="description"
                        v-model="form.description"
                        rows="3"
                        class="w-full rounded-lg border border-ink-200 bg-white px-3 py-2 text-sm focus:border-accent-500"
                    />
                </OField>

                <OField :label="t('prompts.visibility')" for="visibility">
                    <select
                        id="visibility"
                        v-model="form.visibility"
                        class="w-full rounded-lg border border-ink-200 bg-white px-3 py-2 text-sm focus:border-accent-500"
                    >
                        <option value="private">{{ t('prompts.visibility.private') }}</option>
                        <option value="public">{{ t('prompts.visibility.public') }}</option>
                    </select>
                </OField>
            </div>

            <div>
                <OField :label="t('prompts.content')" for="content" required hint="The system prompt Octavia will evaluate and evolve.">
                    <textarea
                        id="content"
                        v-model="form.content"
                        rows="18"
                        required
                        class="w-full rounded-lg border border-ink-200 bg-white px-4 py-3 font-mono text-sm leading-relaxed focus:border-accent-500"
                    />
                </OField>
                <p v-if="form.errors.content" class="mt-1 text-xs text-rose-450">{{ form.errors.content }}</p>

                <div class="mt-4 flex justify-end gap-3">
                    <OButton type="submit" size="lg" :disabled="form.processing">
                        {{ form.processing ? t('common.saving') : t('common.create') }}
                    </OButton>
                </div>
            </div>
        </form>
    </AppLayout>
</template>
