<script setup lang="ts">
import { computed, reactive, ref } from 'vue';
import { Head, router } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
import AppLayout from '../../layouts/AppLayout.vue';
import OButton from '../../components/ui/OButton.vue';
import OField from '../../components/ui/OField.vue';
import OInput from '../../components/ui/OInput.vue';
import OBadge from '../../components/ui/OBadge.vue';

const props = defineProps<{
    categories: Array<{ value: string; label: string }>;
}>();

const { t } = useI18n();

const step = ref(1);
const steps = [t('benchmarks.wizard.stepBasics'), t('benchmarks.wizard.stepCases'), t('benchmarks.wizard.stepReview')];

interface Criterion {
    type: string;
    label: string;
    value: string;
    pattern: string;
    description: string;
}

interface CaseItem {
    title: string;
    input: string;
    criteria: Criterion[];
}

const basics = reactive({
    name: '',
    description: '',
    category: 'general',
    visibility: 'private',
});

const cases = ref<CaseItem[]>([
    { title: '', input: '', criteria: [{ type: 'contains', label: '', value: '', pattern: '', description: '' }] },
]);

const addCase = () => {
    cases.value.push({ title: '', input: '', criteria: [{ type: 'contains', label: '', value: '', pattern: '', description: '' }] });
};

const addCriterion = (c: CaseItem) => {
    c.criteria.push({ type: 'contains', label: '', value: '', pattern: '', description: '' });
};

const removeCriterion = (c: CaseItem, i: number) => {
    if (c.criteria.length > 1) c.criteria.splice(i, 1);
};

const removeCase = (i: number) => {
    if (cases.value.length > 1) cases.value.splice(i, 1);
};

const stepValid = computed(() => {
    if (step.value === 1) return basics.name.trim() !== '';
    if (step.value === 2) {
        return cases.value.every((c) =>
            c.title.trim() !== ''
            && c.input.trim() !== ''
            && c.criteria.every((cr) => {
                if (cr.label.trim() === '') return false;
                if (cr.type === 'contains' || cr.type === 'not_contains') return cr.value.trim() !== '';
                if (cr.type === 'regex') return cr.pattern.trim() !== '';
                return true; // llm_judge falls back to label
            }),
        );
    }
    return true;
});

const criterionConfig = (cr: Criterion) => {
    if (cr.type === 'regex') return { pattern: cr.pattern };
    if (cr.type === 'llm_judge') return { description: cr.description || cr.label };
    return { values: cr.value.split(',').map((v) => v.trim()).filter(Boolean) };
};

const saving = ref(false);

const submit = () => {
    saving.value = true;
    router.post('/benchmarks', {
        ...basics,
        cases: cases.value.map((c) => ({
            title: c.title,
            input: c.input,
            weight: 1,
            criteria: c.criteria.map((cr) => ({
                type: cr.type,
                label: cr.label,
                config: criterionConfig(cr),
            })),
        })),
    }, {
        onError: () => { saving.value = false; },
    });
};
</script>

<template>
    <AppLayout>
        <Head><title>{{ t('benchmarks.wizard.title') }}</title></Head>

        <h1 class="font-display text-2xl font-bold tracking-tight text-ink-950">{{ t('benchmarks.wizard.title') }}</h1>

        <!-- Stepper -->
        <ol class="mt-6 flex items-center gap-2" aria-label="Progress">
            <li v-for="(label, i) in steps" :key="label" class="flex items-center gap-2">
                <span
                    class="flex h-7 w-7 items-center justify-center rounded-full text-xs font-semibold"
                    :class="step > i + 1 ? 'bg-mint-500 text-white' : step === i + 1 ? 'bg-ink-950 text-white' : 'bg-paper-200 text-ink-500'"
                    :aria-current="step === i + 1 ? 'step' : undefined"
                >
                    {{ step > i + 1 ? '✓' : i + 1 }}
                </span>
                <span class="hidden text-sm sm:inline" :class="step === i + 1 ? 'font-medium text-ink-950' : 'text-ink-500'">{{ label }}</span>
                <span v-if="i < steps.length - 1" class="mx-1 h-px w-6 bg-ink-200" aria-hidden="true" />
            </li>
        </ol>

        <!-- Step 1: Basics -->
        <section v-if="step === 1" class="mt-8 max-w-xl space-y-5">
            <OField :label="t('benchmarks.wizard.name')" for="bm-name" required>
                <OInput id="bm-name" v-model="basics.name" placeholder="e.g. Marketing copy quality" required autofocus />
            </OField>
            <OField :label="t('benchmarks.wizard.description')" for="bm-desc">
                <textarea id="bm-desc" v-model="basics.description" rows="3" class="w-full rounded-lg border border-ink-200 bg-white px-3 py-2 text-sm focus:border-accent-500" />
            </OField>
            <OField :label="t('benchmarks.wizard.category')" for="bm-cat">
                <select id="bm-cat" v-model="basics.category" class="w-full rounded-lg border border-ink-200 bg-white px-3 py-2 text-sm focus:border-accent-500">
                    <option v-for="cat in props.categories" :key="cat.value" :value="cat.value">{{ cat.label }}</option>
                </select>
            </OField>
            <fieldset>
                <legend class="mb-2 text-sm font-medium text-ink-700">{{ t('prompts.visibility') }}</legend>
                <div class="flex gap-3">
                    <button
                        v-for="vis in ['private', 'public']"
                        :key="vis"
                        type="button"
                        class="rounded-lg border px-4 py-2 text-sm font-medium transition-colors"
                        :class="basics.visibility === vis ? 'border-accent-500 bg-accent-50 text-accent-700' : 'border-ink-200 text-ink-700'"
                        @click="basics.visibility = vis"
                    >
                        {{ t(`prompts.visibility.${vis}`) }}
                    </button>
                </div>
            </fieldset>
        </section>

        <!-- Step 2: Cases -->
        <section v-if="step === 2" class="mt-8 space-y-6">
            <div v-for="(c, ci) in cases" :key="ci" class="rounded-card border border-ink-100 bg-white p-5 shadow-panel">
                <div class="flex items-center justify-between">
                    <h2 class="font-display text-sm font-semibold text-ink-950">{{ t('benchmarks.wizard.caseTitle') }} {{ ci + 1 }}</h2>
                    <button v-if="cases.length > 1" type="button" class="text-xs text-rose-450 hover:underline" @click="removeCase(ci)">{{ t('common.delete') }}</button>
                </div>
                <div class="mt-4 grid gap-4 lg:grid-cols-2">
                    <OField :label="t('benchmarks.wizard.caseTitle')" required>
                        <OInput v-model="c.title" />
                    </OField>
                    <OField :label="t('benchmarks.wizard.criteria')" :hint="' '" />
                </div>
                <OField :label="t('benchmarks.wizard.caseInput')" required class="mt-0">
                    <textarea v-model="c.input" rows="3" class="w-full rounded-lg border border-ink-200 bg-white px-3 py-2 text-sm focus:border-accent-500" />
                </OField>

                <div class="mt-4 space-y-3 rounded-lg bg-paper-100/70 p-4">
                    <p class="text-xs font-semibold uppercase tracking-wide text-ink-300">{{ t('benchmarks.wizard.criteria') }}</p>
                    <div v-for="(cr, cri) in c.criteria" :key="cri" class="rounded-lg border border-ink-100 bg-white p-3">
                        <div class="flex items-start gap-3">
                            <select
                                v-model="cr.type"
                                class="w-44 shrink-0 rounded-lg border border-ink-200 bg-white px-2 py-1.5 text-sm focus:border-accent-500"
                                :aria-label="t('benchmarks.wizard.criterionType')"
                            >
                                <option value="contains">{{ t('benchmarks.wizard.contains') }}</option>
                                <option value="not_contains">{{ t('benchmarks.wizard.notContains') }}</option>
                                <option value="regex">{{ t('benchmarks.wizard.regex') }}</option>
                                <option value="llm_judge">{{ t('benchmarks.wizard.llmJudge') }}</option>
                            </select>
                            <OInput v-model="cr.label" :placeholder="t('benchmarks.wizard.criteria')" class="flex-1" />
                            <button v-if="c.criteria.length > 1" type="button" class="mt-1.5 text-ink-300 hover:text-rose-450" :aria-label="t('common.delete')" @click="removeCriterion(c, cri)">✕</button>
                        </div>
                        <div class="mt-2 pl-0 sm:pl-48">
                            <OInput v-if="cr.type === 'contains' || cr.type === 'not_contains'" v-model="cr.value" placeholder="keyword one, keyword two" />
                            <OInput v-else-if="cr.type === 'regex'" v-model="cr.pattern" placeholder="/pattern/i" class="font-mono" />
                            <textarea v-else-if="cr.type === 'llm_judge'" v-model="cr.description" rows="2" class="w-full rounded-lg border border-ink-200 px-3 py-2 text-sm focus:border-accent-500" placeholder="Describe what the output must achieve…" />
                        </div>
                    </div>
                    <button type="button" class="text-xs font-medium text-accent-600 hover:text-accent-700" @click="addCriterion(c)">
                        + {{ t('benchmarks.wizard.addCriterion') }}
                    </button>
                </div>
            </div>

            <button type="button" class="w-full rounded-card border border-dashed border-ink-200 py-3 text-sm font-medium text-ink-500 transition-colors hover:border-accent-300 hover:text-accent-600" @click="addCase">
                + {{ t('benchmarks.wizard.addCase') }}
            </button>
        </section>

        <!-- Step 3: Review -->
        <section v-if="step === 3" class="mt-8 max-w-3xl">
            <p class="text-sm text-ink-500">{{ t('benchmarks.wizard.reviewHint') }}</p>
            <div class="mt-4 rounded-card border border-ink-100 bg-white p-5 shadow-panel">
                <div class="flex items-center gap-3">
                    <h2 class="font-display text-base font-semibold text-ink-950">{{ basics.name }}</h2>
                    <OBadge tone="accent">{{ props.categories.find((c) => c.value === basics.category)?.label }}</OBadge>
                </div>
                <p v-if="basics.description" class="mt-1 text-sm text-ink-500">{{ basics.description }}</p>
                <ul class="mt-4 space-y-3">
                    <li v-for="(c, ci) in cases" :key="ci" class="rounded-lg border border-ink-100 p-3">
                        <p class="text-sm font-medium text-ink-950">{{ c.title }}</p>
                        <p class="mt-1 line-clamp-2 text-xs text-ink-500">“{{ c.input }}”</p>
                        <ul class="mt-2 space-y-1">
                            <li v-for="(cr, cri) in c.criteria" :key="cri" class="flex items-center gap-2 text-xs text-ink-700">
                                <span class="h-1.5 w-1.5 rounded-full bg-accent-400" aria-hidden="true" />
                                {{ cr.label }}
                                <span class="ml-auto font-mono text-[10px] uppercase text-ink-300">{{ cr.type }}</span>
                            </li>
                        </ul>
                    </li>
                </ul>
            </div>
        </section>

        <!-- Navigation -->
        <div class="mt-8 flex items-center justify-between">
            <OButton variant="ghost" :disabled="step === 1" @click="step--">{{ t('common.back') }}</OButton>
            <OButton v-if="step < 3" size="lg" :disabled="!stepValid" @click="step++">{{ t('common.next') }}</OButton>
            <OButton v-else size="lg" :disabled="saving" @click="submit">
                {{ saving ? t('common.saving') : t('benchmarks.wizard.finish') }}
            </OButton>
        </div>
    </AppLayout>
</template>
