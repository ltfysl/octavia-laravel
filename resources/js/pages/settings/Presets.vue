<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
import { ref } from 'vue';
import AppLayout from '../../layouts/AppLayout.vue';
import OPanel from '../../components/ui/OPanel.vue';
import OButton from '../../components/ui/OButton.vue';
import OField from '../../components/ui/OField.vue';
import OInput from '../../components/ui/OInput.vue';
import OBadge from '../../components/ui/OBadge.vue';

interface Preset {
    id: number;
    name: string;
    description: string | null;
    is_default: boolean;
    config: {
        mode: string;
        provider: string;
        model: string;
        max_steps: number;
        target_score: number;
        temperature: number;
    };
}

const props = defineProps<{
    presets: Preset[];
}>();

const { t } = useI18n();

const editing = ref<number | null>(null);

const blank = {
    name: '',
    description: '',
    config: {
        mode: 'optimize',
        provider: 'mock',
        model: '',
        max_steps: 8,
        target_score: 0.95,
        temperature: 0.7,
    },
    is_default: false,
};

const form = useForm({ ...blank });

const reset = () => {
    editing.value = null;
    form.reset();
    Object.assign(form, blank);
};

const startEdit = (preset: Preset) => {
    editing.value = preset.id;
    Object.assign(form, {
        name: preset.name,
        description: preset.description ?? '',
        config: { ...preset.config },
        is_default: preset.is_default,
    });
};

const submit = () => {
    if (editing.value) {
        form.patch(`/settings/presets/${editing.value}`, { onSuccess: () => reset() });
    } else {
        form.post('/settings/presets', { onSuccess: () => reset() });
    }
};

const destroy = (preset: Preset) => {
    if (! confirm(t('settings.presets.confirmDelete', { name: preset.name }))) return;
    form.delete(`/settings/presets/${preset.id}`);
};
</script>

<template>
    <AppLayout>
        <Head><title>{{ t('settings.presets.title') }}</title></Head>

        <h1 class="font-display text-2xl font-bold tracking-tight text-ink-950">{{ t('settings.presets.title') }}</h1>
        <p class="mt-1 text-sm text-ink-500">{{ t('settings.presets.subtitle') }}</p>

        <div class="mt-6 grid gap-6 lg:grid-cols-[1fr_24rem]">
            <OPanel :title="editing ? t('settings.presets.edit') : t('settings.presets.new')">
                <form class="space-y-4" @submit.prevent="submit">
                    <OField :label="t('settings.presets.name')" for="name" required>
                        <OInput id="name" v-model="form.name" required />
                    </OField>
                    <OField :label="t('settings.presets.description')" for="description">
                        <OInput id="description" v-model="form.description" />
                    </OField>
                    <div class="grid gap-4 sm:grid-cols-2">
                        <OField :label="t('settings.presets.mode')" for="mode">
                            <select id="mode" v-model="form.config.mode" class="w-full rounded-lg border border-ink-200 bg-card px-3 py-2 text-sm focus:border-accent-500">
                                <option value="evaluate">{{ t('settings.presets.modes.evaluate') }}</option>
                                <option value="optimize">{{ t('settings.presets.modes.optimize') }}</option>
                            </select>
                        </OField>
                        <OField :label="t('settings.presets.provider')" for="provider">
                            <OInput id="provider" v-model="form.config.provider" />
                        </OField>
                        <OField :label="t('settings.presets.model')" for="model">
                            <OInput id="model" v-model="form.config.model" />
                        </OField>
                        <OField :label="t('settings.presets.temperature')" for="temperature">
                            <OInput id="temperature" v-model="form.config.temperature" type="number" step="0.1" min="0" max="2" />
                        </OField>
                        <OField :label="t('settings.presets.maxSteps')" for="max_steps">
                            <OInput id="max_steps" v-model="form.config.max_steps" type="number" min="1" max="50" />
                        </OField>
                        <OField :label="t('settings.presets.targetScore')" for="target_score">
                            <OInput id="target_score" v-model="form.config.target_score" type="number" step="0.01" min="0" max="1" />
                        </OField>
                    </div>
                    <label class="flex cursor-pointer items-start gap-3">
                        <input v-model="form.is_default" type="checkbox" class="mt-0.5 h-4 w-4 rounded border-ink-200 accent-accent-600" />
                        <span class="text-sm font-medium text-ink-900">{{ t('settings.presets.isDefault') }}</span>
                    </label>
                    <div class="flex gap-2">
                        <OButton type="submit" :disabled="form.processing">{{ form.processing ? t('common.saving') : (editing ? t('settings.presets.save') : t('settings.presets.create')) }}</OButton>
                        <OButton v-if="editing" variant="secondary" type="button" @click="reset">{{ t('common.cancel') }}</OButton>
                    </div>
                </form>
            </OPanel>

            <div class="space-y-4">
                <OPanel v-for="preset in props.presets" :key="preset.id" class="relative">
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <div class="flex items-center gap-2">
                                <span class="font-medium text-ink-950">{{ preset.name }}</span>
                                <OBadge v-if="preset.is_default" tone="mint">{{ t('settings.presets.default') }}</OBadge>
                            </div>
                            <p v-if="preset.description" class="mt-0.5 text-xs text-ink-500">{{ preset.description }}</p>
                            <p class="mt-1 font-mono text-[10px] text-ink-400">{{ preset.config.mode }} · {{ preset.config.provider }}{{ preset.config.model ? `/${preset.config.model}` : '' }} · {{ Math.round(preset.config.target_score * 100) }}% · {{ preset.config.max_steps }} steps</p>
                        </div>
                        <div class="flex gap-1">
                            <OButton variant="ghost" size="sm" @click="startEdit(preset)">{{ t('common.edit') }}</OButton>
                            <OButton variant="ghost" size="sm" @click="destroy(preset)">{{ t('common.delete') }}</OButton>
                        </div>
                    </div>
                </OPanel>
            </div>
        </div>
    </AppLayout>
</template>
