<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
import { ref, computed } from 'vue';
import AppLayout from '../../layouts/AppLayout.vue';
import OPanel from '../../components/ui/OPanel.vue';
import OBadge from '../../components/ui/OBadge.vue';
import OButton from '../../components/ui/OButton.vue';

const { t } = useI18n();

defineProps<{
    providers: Array<{ name: string; driver: string; configured: boolean }>;
    prompts: Array<{ id: number; name: string }>;
    benchmarks: Array<{ id: number; name: string }>;
    results: Array<{
        id: number;
        provider: string;
        status: string;
        best_score: number | null;
        benchmark_name: string | null;
    }>;
}>();

const promptId = ref<number | ''>('');
const benchmarkId = ref<number | ''>('');
const selected = ref<string[]>([]);

const configuredCount = computed(() => selected.value.length);
const canStart = computed(() => promptId.value !== '' && benchmarkId.value !== '' && configuredCount.value >= 2);

const toggle = (name: string) => {
    selected.value = selected.value.includes(name)
        ? selected.value.filter((n) => n !== name)
        : [...selected.value, name];
};

const start = () => {
    router.post('/tournaments', {
        prompt_id: promptId.value,
        benchmark_id: benchmarkId.value,
        providers: selected.value,
    });
};

const medal = (i: number) => `${String(i + 1).padStart(2, '0')}`;
</script>

<template>
    <AppLayout>
        <Head><title>{{ t('tournaments.title') }}</title></Head>

        <div class="flex flex-wrap items-start justify-between gap-4">
            <div>
                <div class="flex items-center gap-2">
                    <span class="h-2 w-2 rotate-45 bg-accent-600" aria-hidden="true" />
                    <p class="eyebrow">{{ t('tournaments.newTitle') }}</p>
                </div>
                <h1 class="display-hero mt-2 text-3xl tracking-tight text-ink-950">{{ t('tournaments.title') }}</h1>
                <p class="mt-1 max-w-xl text-sm leading-relaxed text-ink-500">
                    {{ t('tournaments.subtitle') }}
                </p>
            </div>
        </div>

        <div class="mt-6 grid gap-4 lg:grid-cols-2">
            <!-- Setup -->
            <OPanel :title="t('tournaments.newTitle')">
                <div class="space-y-4">
                    <div>
                        <label class="eyebrow mb-1.5 block" for="t-prompt">{{ t('tournaments.prompt') }}</label>
                        <select id="t-prompt" v-model="promptId" class="w-full rounded-lg border border-ink-200 bg-card px-3 py-2 text-sm focus:border-accent-500">
                            <option value="" disabled>—</option>
                            <option v-for="p in prompts" :key="p.id" :value="p.id">{{ p.name }}</option>
                        </select>
                    </div>
                    <div>
                        <label class="eyebrow mb-1.5 block" for="t-benchmark">{{ t('tournaments.benchmark') }}</label>
                        <select id="t-benchmark" v-model="benchmarkId" class="w-full rounded-lg border border-ink-200 bg-card px-3 py-2 text-sm focus:border-accent-500">
                            <option value="" disabled>—</option>
                            <option v-for="b in benchmarks" :key="b.id" :value="b.id">{{ b.name }}</option>
                        </select>
                    </div>
                    <div>
                        <p class="eyebrow mb-1.5">{{ t('tournaments.providers') }} <span class="font-mono normal-case tracking-normal">({{ t('tournaments.selected', { n: configuredCount }) }})</span></p>
                        <div class="grid gap-2 sm:grid-cols-2">
                            <label
                                v-for="prov in providers"
                                :key="prov.name"
                                class="flex cursor-pointer items-center gap-2.5 rounded-xl border px-3 py-2.5 text-sm transition"
                                :class="[
                                    selected.includes(prov.name) ? 'border-accent-500 bg-accent-50/50' : 'border-ink-200 bg-card hover:border-ink-300',
                                    !prov.configured ? 'opacity-50' : '',
                                ]"
                            >
                                <input
                                    type="checkbox"
                                    class="h-4 w-4 rounded border-ink-300 accent-accent-600"
                                    :checked="selected.includes(prov.name)"
                                    :disabled="!prov.configured"
                                    @change="toggle(prov.name)"
                                />
                                <span class="font-mono font-medium text-ink-900">{{ prov.name }}</span>
                                <span v-if="!prov.configured" class="ml-auto text-xs text-ink-400">{{ t('tournaments.notConfigured') }}</span>
                                <span v-else class="ml-auto rounded-full bg-mint-100 px-1.5 py-0.5 font-mono text-[10px] text-mint-600">{{ t('tournaments.ready') }}</span>
                            </label>
                        </div>
                    </div>
                    <OButton variant="primary" class="w-full" :disabled="!canStart" @click="start">
                        {{ t('tournaments.start', { n: configuredCount }) }}
                    </OButton>
                    <p v-if="configuredCount === 1" class="text-xs text-amber-600">{{ t('tournaments.onlyOne') }}</p>
                </div>
            </OPanel>

            <!-- Ranking -->
            <OPanel :title="t('tournaments.ranking')">
                <div v-if="results.length === 0" class="py-10 text-center">
                    <p class="font-mono text-sm text-ink-300">{{ t('tournaments.noneYet') }}</p>
                    <p class="mt-1 text-xs text-ink-400">{{ t('tournaments.noneYetHint') }}</p>
                </div>
                <ul v-else class="divide-y divide-ink-100">
                    <li v-for="(run, i) in results" :key="run.id" class="flex items-center gap-3 py-3">
                        <span class="w-8 text-center font-mono text-sm font-bold" :class="i === 0 ? 'text-accent-600' : 'text-ink-400'">{{ medal(i) }}</span>
                        <span class="min-w-0 flex-1 truncate font-mono text-sm font-medium text-ink-900">{{ run.provider }}</span>
                        <OBadge :tone="run.status === 'completed' ? 'mint' : run.status === 'failed' ? 'rose' : 'neutral'">{{ run.status }}</OBadge>
                        <span class="w-14 text-right font-mono text-sm tabular-nums" :class="run.best_score !== null ? 'text-ink-900' : 'text-ink-300'">
                            {{ run.best_score !== null ? Math.round(run.best_score * 100) + '%' : '…' }}
                        </span>
                        <Link :href="`/runs/${run.id}`" class="text-xs font-medium text-accent-600 hover:text-accent-700">→</Link>
                    </li>
                </ul>
                <p v-if="results.length > 0" class="mt-3 text-center font-mono text-xs text-ink-300">{{ t('tournaments.processing') }}</p>
            </OPanel>
        </div>
    </AppLayout>
</template>
