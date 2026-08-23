<script setup lang="ts">
import { ref, nextTick, computed } from 'vue';
import { useI18n } from 'vue-i18n';
import { usePage } from '@inertiajs/vue3';

const { t } = useI18n();
const page = usePage<{ prompt?: { name: string } | null; benchmark?: { name: string } | null; run?: { name: string; mode: string } | null }>();

interface ChatMessage {
    role: 'system' | 'user' | 'assistant';
    content: string;
}

const contextMessage = computed<ChatMessage>(() => {
    const parts: string[] = [];
    const path = typeof window !== 'undefined' ? window.location.pathname : '';

    if (path.includes('/prompts/') && page.props.prompt?.name) {
        parts.push(`The user is currently viewing the prompt "${page.props.prompt.name}".`);
    } else if (path.includes('/benchmarks/') && page.props.benchmark?.name) {
        parts.push(`The user is currently viewing the benchmark "${page.props.benchmark.name}".`);
    } else if (path.includes('/runs/') && page.props.run?.name) {
        parts.push(`The user is currently viewing the run "${page.props.run.name}" (mode: ${page.props.run.mode}).`);
    }

    if (parts.length === 0) {
        return { role: 'system', content: 'The user is browsing the Octavia prompt-lab application.' };
    }

    return { role: 'system', content: parts.join(' ') + ' Keep your answer relevant to this context.' };
});

const open = ref(false);
const busy = ref(false);
const draft = ref('');
const thread = ref<ChatMessage[]>([]);
const listEl = ref<HTMLElement | null>(null);

const systemPrompt = () => ({
    role: 'system' as const,
    content:
        'You are the Octavia prompt-lab assistant. Answer briefly and practically about prompts, benchmarks, evaluation and prompt evolution. Reply in the language the user writes in.',
});

const toggle = () => {
    open.value = !open.value;
    if (open.value) void nextTick(() => listEl.value?.scrollTo({ top: listEl.value.scrollHeight }));
};

const send = async () => {
    const text = draft.value.trim();
    if (! text || busy.value) return;

    thread.value.push({ role: 'user', content: text });
    draft.value = '';
    busy.value = true;
    void nextTick(() => listEl.value?.scrollTo({ top: listEl.value.scrollHeight }));

    try {
        const res = await fetch('/assistant/chat', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                Accept: 'application/json',
                'X-CSRF-TOKEN': (document.querySelector('meta[name=csrf-token]') as HTMLMetaElement | null)?.content ?? '',
            },
            credentials: 'include',
            body: JSON.stringify({
                messages: [systemPrompt(), contextMessage.value, ...thread.value.slice(-10)],
            }),
        });

        if (! res.ok) throw new Error(String(res.status));
        const data: { reply?: string } = await res.json();
        thread.value.push({ role: 'assistant', content: data.reply ?? t('assistant.error') });
    } catch {
        thread.value.push({ role: 'assistant', content: t('assistant.error') });
    } finally {
        busy.value = false;
        void nextTick(() => listEl.value?.scrollTo({ top: listEl.value.scrollHeight }));
    }
};

const onKey = (event: KeyboardEvent) => {
    if (event.key === 'Enter' && ! event.shiftKey) {
        event.preventDefault();
        void send();
    }
};
</script>

<template>
    <!-- Launcher -->
    <button
        type="button"
        class="fixed bottom-6 right-6 z-50 flex h-14 w-14 items-center justify-center rounded-full bg-ink-950 text-white shadow-lg transition-transform hover:scale-105 focus-visible:outline focus-visible:outline-2 focus-visible:outline-accent-600"
        :aria-expanded="open"
        aria-controls="octavia-assistant"
        :aria-label="t('assistant.open')"
        @click="toggle"
    >
        <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" d="M9.813 15.904 9 18.75l-.813-2.846a4.5 4.5 0 0 0-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 0 0 3.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 0 0 3.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 0 0-3.09 3.09ZM18.259 8.715 18 9.75l-.259-1.035a3.375 3.375 0 0 0-2.455-2.456L14.25 6l1.036-.259a3.375 3.375 0 0 0 2.455-2.456L18 2.25l.259 1.035a3.375 3.375 0 0 0 2.456 2.456L21.75 6l-1.035.259a3.375 3.375 0 0 0-2.456 2.456Z" />
        </svg>
    </button>

    <!-- Panel -->
    <Transition
        enter-active-class="transition duration-200 ease-out"
        enter-from-class="translate-y-2 opacity-0"
        leave-active-class="transition duration-150 ease-in"
        leave-to-class="translate-y-2 opacity-0"
    >
        <div
            v-if="open"
            id="octavia-assistant"
            class="fixed bottom-24 right-6 z-50 flex h-[28rem] w-[22rem] max-w-[calc(100vw-3rem)] flex-col overflow-hidden rounded-card border border-ink-100 bg-card shadow-lg"
            role="dialog"
            :aria-label="t('assistant.title')"
        >
            <div class="flex items-center justify-between border-b border-ink-100 px-4 py-3">
                <p class="text-sm font-semibold text-ink-950">{{ t('assistant.title') }}</p>
                <button type="button" class="text-ink-400 transition-colors hover:text-ink-700" :aria-label="t('assistant.close')" @click="open = false">✕</button>
            </div>

            <div ref="listEl" class="flex-1 space-y-3 overflow-auto px-4 py-3" aria-live="polite">
                <p v-if="thread.length === 0" class="pt-8 text-center text-sm text-ink-400">{{ t('assistant.empty') }}</p>
                <div
                    v-for="(message, index) in thread"
                    :key="index"
                    class="max-w-[85%] whitespace-pre-wrap rounded-xl px-3 py-2 text-sm"
                    :class="message.role === 'user'
                        ? 'ml-auto bg-ink-950 text-white'
                        : 'bg-paper-100 text-ink-900'"
                >{{ message.content }}</div>
                <p v-if="busy" class="w-fit rounded-xl bg-paper-100 px-3 py-2 text-sm text-ink-400">{{ t('assistant.thinking') }}</p>
            </div>

            <div class="border-t border-ink-100 p-3">
                <div class="flex items-end gap-2">
                    <textarea
                        v-model="draft"
                        rows="2"
                        class="flex-1 resize-none rounded-xl border border-ink-100 bg-paper-50 px-3 py-2 text-sm outline-none placeholder:text-ink-400 focus:border-accent-600"
                        :placeholder="t('assistant.placeholder')"
                        :disabled="busy"
                        @keydown="onKey"
                    />
                    <button
                        type="button"
                        class="rounded-xl bg-ink-950 px-3 py-2 text-sm font-medium text-white transition-colors hover:bg-ink-700 disabled:opacity-50"
                        :disabled="busy || draft.trim() === ''"
                        @click="send"
                    >{{ t('assistant.send') }}</button>
                </div>
            </div>
        </div>
    </Transition>
</template>
