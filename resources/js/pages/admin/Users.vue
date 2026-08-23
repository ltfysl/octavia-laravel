<script setup lang="ts">
import { Head, router } from '@inertiajs/vue3';
import { ref } from 'vue';
import AppLayout from '../../layouts/AppLayout.vue';
import OBadge from '../../components/ui/OBadge.vue';

defineProps<{
    users: {
        data: Array<{
            id: number;
            name: string;
            email: string;
            is_admin: boolean;
            prompts_count: number;
            benchmarks_count: number;
            runs_count: number;
            created_at: string;
        }>;
        links: Array<{ url: string | null; label: string; active: boolean }>;
    };
    filters: { q: string };
}>();

const search = ref('');

const doSearch = () => router.get('/admin/users', search.value ? { q: search.value } : {}, { preserveState: true });

const toggleAdmin = (id: number) => router.post(`/admin/users/${id}/toggle-admin`);
const destroy = (id: number) => {
    if (confirm('Delete this user and all their data?')) {
        router.delete(`/admin/users/${id}`);
    }
};
</script>

<template>
    <AppLayout>
        <Head><title>Admin · Users</title><meta name="robots" content="noindex" /></Head>

        <div class="flex flex-wrap items-center justify-between gap-4">
            <h1 class="font-display text-2xl font-bold tracking-tight text-ink-950">Users</h1>
            <form class="flex gap-2" role="search" @submit.prevent="doSearch">
                <input
                    v-model="search"
                    type="search"
                    placeholder="Name or email…"
                    class="w-56 rounded-lg border border-ink-200 bg-card px-3 py-2 text-sm focus:border-accent-500"
                />
                <button type="submit" class="rounded-lg border border-ink-200 bg-card px-3 py-2 text-sm hover:bg-paper-100">Search</button>
            </form>
        </div>

        <div class="mt-6 overflow-hidden rounded-card border border-ink-100 bg-card shadow-panel">
            <table class="w-full text-left text-sm">
                <thead>
                    <tr class="border-b border-ink-100 text-xs uppercase tracking-wide text-ink-300">
                        <th class="px-5 py-3 font-medium">User</th>
                        <th class="hidden px-5 py-3 font-medium sm:table-cell">Content</th>
                        <th class="px-5 py-3 font-medium">Role</th>
                        <th class="px-5 py-3 font-medium"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-ink-100">
                    <tr v-for="user in users.data" :key="user.id">
                        <td class="px-5 py-3">
                            <span class="block font-medium text-ink-950">{{ user.name }}</span>
                            <span class="text-xs text-ink-300">{{ user.email }} · since {{ user.created_at?.slice(0, 10) }}</span>
                        </td>
                        <td class="hidden px-5 py-3 text-xs text-ink-500 sm:table-cell">
                            {{ user.prompts_count }}p · {{ user.benchmarks_count }}b · {{ user.runs_count }}r
                        </td>
                        <td class="px-5 py-3">
                            <OBadge :tone="user.is_admin ? 'accent' : 'neutral'">{{ user.is_admin ? 'admin' : 'user' }}</OBadge>
                        </td>
                        <td class="px-5 py-3 text-right">
                            <button type="button" class="text-xs font-medium text-accent-600 hover:text-accent-700" @click="toggleAdmin(user.id)">
                                {{ user.is_admin ? 'Revoke admin' : 'Make admin' }}
                            </button>
                            <button type="button" class="ml-3 text-xs font-medium text-rose-450 hover:underline" @click="destroy(user.id)">Delete</button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <nav v-if="users.links.length > 3" class="mt-6 flex justify-center gap-1" aria-label="Pagination">
            <component
                :is="link.url ? 'a' : 'span'"
                v-for="(link, i) in users.links"
                :key="i"
                :href="link.url ?? '#'"
                class="rounded-lg px-3 py-1.5 text-sm"
                :class="link.active ? 'bg-ink-950 text-white' : link.url ? 'text-ink-500 hover:bg-paper-200' : 'text-ink-200'"
                v-html="link.label"
            />
        </nav>
    </AppLayout>
</template>
