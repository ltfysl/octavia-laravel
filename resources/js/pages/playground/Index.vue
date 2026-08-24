<script setup lang="ts">
import { ref } from 'vue';
import { Head } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
import AppLayout from '../../layouts/AppLayout.vue';
import OPanel from '../../components/ui/OPanel.vue';
import OButton from '../../components/ui/OButton.vue';

interface Message {
    role: 'user' | 'assistant';
    content: string;
}

const { t } = useI18n();

const systemPrompt = ref('');
const input = ref('');
const messages = ref<Message[]>([]);
const loading = ref(false);
const error = ref('');

const send = async () => {
    if (!input.value.trim() || loading.value) return;

    const userMessage = input.value.trim();
    messages.value.push({ role: 'user', content: userMessage });
    input.value = '';
    loading.value = true;
    error.value = '';

    try {
        const res = await fetch('/playground/chat', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector<HTMLMetaElement>('meta[name=csrf-token]')?.content ?? '',
                Accept: 'application/json',
            },
            credentials: 'include',
            body: JSON.stringify({
                message: userMessage,
                systemPrompt: systemPrompt.value,
                history: messages.value.slice(0, -1),
            }),
        });

        if (!res.ok) throw new Error();

        const data = await res.json();
        messages.value.push({ role: data.role, content: data.content });
    } catch {
        error.value = t('common.error');
    } finally {
        loading.value = false;
    }
};

const clear = () => {
    messages.value = [];
    input.value = '';
    error.value = '';
};
</script>

<template>
    <AppLayout>
        <Head><title>{{ t('playground.title') }}</title></Head>

        <div class="mb-6 flex items-start justify-between gap-4">
            <div>
                <h1 class="font-display text-3xl font-bold tracking-tight text-ink-950">{{ t('playground.title') }}</h1>
                <p class="mt-1 text-sm text-ink-500">{{ t('playground.subtitle') }}</p>
            </div>
            <OButton variant="secondary" size="sm" @click="clear">{{ t('playground.clear') }}</OButton>
        </div>

        <OPanel :title="t('playground.systemPrompt')">
            <textarea
                v-model="systemPrompt"
                rows="3"
                class="w-full rounded-lg border border-ink-200 bg-card px-3 py-2 text-sm text-ink-900 focus:border-accent-500"
                :placeholder="t('playground.systemPromptPlaceholder')"
            />
        </OPanel>

        <OPanel :title="t('playground.chat')" class="mt-4">
            <div class="flex h-[28rem] flex-col">
                <div class="min-h-0 flex-1 space-y-3 overflow-y-auto pr-2">
                    <div
                        v-for="(msg, idx) in messages"
                        :key="idx"
                        class="max-w-[85%] rounded-xl px-4 py-2 text-sm"
                        :class="msg.role === 'user' ? 'ml-auto bg-accent-600 text-white' : 'bg-paper-100 text-ink-900'"
                    >
                        <p class="whitespace-pre-wrap">{{ msg.content }}</p>
                    </div>
                    <p v-if="error" class="rounded-lg bg-rose-50 px-3 py-2 text-xs text-rose-600">{{ error }}</p>
                </div>

                <div class="mt-4 flex gap-2 border-t border-ink-100 pt-4">
                    <input
                        v-model="input"
                        type="text"
                        class="min-w-0 flex-1 rounded-lg border border-ink-200 bg-card px-3 py-2 text-sm focus:border-accent-500"
                        :placeholder="t('playground.messagePlaceholder')"
                        @keydown.enter.prevent="send"
                    />
                    <OButton :disabled="loading || !input.trim()" @click="send">
                        {{ loading ? t('common.loading') : t('playground.send') }}
                    </OButton>
                </div>
            </div>
        </OPanel>
    </AppLayout>
</template>
