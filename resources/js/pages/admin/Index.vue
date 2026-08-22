<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import AppLayout from '../../layouts/AppLayout.vue';
import OPanel from '../../components/ui/OPanel.vue';
import OBadge from '../../components/ui/OBadge.vue';

const props = defineProps<{
    stats: {
        users: number;
        prompts: number;
        benchmarks: number;
        runs: number;
        activeRuns: number;
        failedRuns: number;
        marketplaceItems: number;
    };
    recentUsers: Array<{ id: number; name: string; email: string; is_admin: boolean; created_at: string }>;
    recentRuns: Array<{ id: number; name: string; status: string; score: number | null; user: string | null; created_at: string }>;
}>();


</script>

<template>
    <AppLayout>
        <Head><title>Admin</title><meta name="robots" content="noindex" /></Head>

        <h1 class="font-display text-2xl font-bold tracking-tight text-ink-950">Admin</h1>

        <div class="mt-6 grid grid-cols-2 gap-4 lg:grid-cols-4">
            <OPanel v-for="stat in [
                { label: 'Users', value: stats.users },
                { label: 'Prompts', value: stats.prompts },
                { label: 'Benchmarks', value: stats.benchmarks },
                { label: 'Runs', value: stats.runs },
                { label: 'Active runs', value: stats.activeRuns },
                { label: 'Failed runs', value: stats.failedRuns },
                { label: 'Marketplace', value: stats.marketplaceItems },
            ]" :key="stat.label">
                <p class="text-xs font-medium uppercase tracking-wide text-ink-300">{{ stat.label }}</p>
                <p class="mt-2 font-display text-3xl font-bold tabular-nums text-ink-950">{{ stat.value }}</p>
            </OPanel>
        </div>

        <div class="mt-8 grid gap-6 lg:grid-cols-2">
            <OPanel title="Newest users">
                <ul class="divide-y divide-ink-100 text-sm">
                    <li v-for="user in recentUsers" :key="user.id" class="flex items-center gap-3 py-2">
                        <span class="min-w-0 flex-1 truncate">{{ user.name }}</span>
                        <OBadge v-if="user.is_admin" tone="accent">admin</OBadge>
                        <span class="text-xs text-ink-300">{{ user.created_at?.slice(0, 10) }}</span>
                    </li>
                </ul>
            </OPanel>

            <OPanel title="Latest runs">
                <ul class="divide-y divide-ink-100 text-sm">
                    <li v-for="run in recentRuns" :key="run.id" class="flex items-center gap-3 py-2">
                        <OBadge :tone="run.status === 'completed' ? 'mint' : run.status === 'failed' ? 'rose' : 'neutral'">{{ run.status }}</OBadge>
                        <span class="min-w-0 flex-1 truncate">{{ run.name }}</span>
                        <span class="text-xs text-ink-300">{{ run.user }}</span>
                    </li>
                </ul>
            </OPanel>
        </div>
    </AppLayout>
</template>
