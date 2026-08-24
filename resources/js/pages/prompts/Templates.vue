<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
import { ref, computed, onMounted } from 'vue';
import AppLayout from '../../layouts/AppLayout.vue';
import OBadge from '../../components/ui/OBadge.vue';
import OEmptyState from '../../components/ui/OEmptyState.vue';

interface Template {
    id: number;
    name: string;
    description: string | null;
    category: string;
    difficulty: string;
    tags: string;
    body: string;
    example_use_cases: string | null;
    recommended_benchmark_type: string | null;
}

const { t } = useI18n();
const templates = ref<Template[]>([]);
const loading = ref(true);
const activeCategory = ref('all');
const searchQuery = ref('');
const categories = ['all', 'general', 'marketing', 'support', 'coding'];

const filtered = computed(() => {
    let result = templates.value;

    if (activeCategory.value !== 'all') {
        result = result.filter((template) => template.category === activeCategory.value);
    }

    if (searchQuery.value.trim()) {
        const q = searchQuery.value.toLowerCase();
        result = result.filter((template) =>
            template.name.toLowerCase().includes(q) ||
            (template.description ?? '').toLowerCase().includes(q) ||
            template.tags.toLowerCase().includes(q)
        );
    }

    return result;
});

onMounted(async () => {
    try {
        const res = await fetch('/prompts/templates/list', { headers: { Accept: 'application/json' } });
        if (res.ok) {
            templates.value = await res.json();
        }
    } catch {
        // silent
    } finally {
        loading.value = false;
    }
});
</script>

<template>
    <AppLayout>
        <Head><title>{{ t('prompts.templates.title') }}</title></Head>

        <div class="flex items-center justify-between">
            <div>
                <p class="eyebrow">{{ t('prompts.templates.eyebrow') }}</p>
                <h1 class="mt-1 font-display text-2xl font-bold tracking-tight text-ink-950">{{ t('prompts.templates.title') }}</h1>
            </div>
            <Link href="/prompts" class="text-sm text-ink-500 hover:text-ink-900">{{ t('common.back') }}</Link>
        </div>

        <div class="mt-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div class="flex flex-wrap gap-2">
                <button
                    v-for="category in categories"
                    :key="category"
                    @click="activeCategory = category"
                    class="rounded-full px-3 py-1 text-xs font-medium transition-colors"
                    :class="activeCategory === category ? 'bg-ink-950 text-white' : 'border border-ink-200 bg-card text-ink-600 hover:bg-paper-50'"
                >
                    {{ t(`prompts.templates.categories.${category}`) }}
                </button>
            </div>
            <input
                v-model="searchQuery"
                type="search"
                :placeholder="t('prompts.templates.searchPlaceholder')"
                class="w-full rounded-lg border border-ink-200 bg-card px-3 py-2 text-sm sm:w-64"
            />
        </div>

        <div v-if="loading" class="mt-8 text-sm text-ink-500">{{ t('common.loading') }}</div>

        <OEmptyState v-else-if="filtered.length === 0" :title="t('prompts.templates.empty')" class="mt-8" />

        <div v-else class="mt-6 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
            <div v-for="template in filtered" :key="template.id" class="rounded-2xl border border-ink-100 bg-card p-5 shadow-sm transition-all hover:shadow-md">
                <div class="flex items-center justify-between">
                    <p class="text-xs font-medium uppercase tracking-wide text-ink-400">{{ template.category }}</p>
                    <OBadge tone="neutral">{{ template.difficulty }}</OBadge>
                </div>
                <h2 class="mt-2 font-display text-lg font-semibold text-ink-950">{{ template.name }}</h2>
                <p class="mt-1 line-clamp-2 text-sm text-ink-500">{{ template.description ?? '' }}</p>
                <p class="mt-2 text-xs text-ink-400">{{ template.tags }}</p>
                <div class="mt-4 flex items-center justify-between">
                    <Link :href="`/prompts/create?template=${template.id}`" class="text-sm font-medium text-accent-700 hover:text-accent-900">
                        {{ t('prompts.templates.use') }}
                    </Link>
                    <span v-if="template.recommended_benchmark_type" class="text-xs text-ink-400">{{ template.recommended_benchmark_type }}</span>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
