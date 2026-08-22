<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
</script>

<template>
    <div class="flex min-h-screen flex-col bg-paper-50">
        <header class="px-6 pt-6">
            <Link href="/" class="inline-flex items-center gap-2.5" aria-label="Octavia home">
                <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-accent-600 font-display text-lg font-bold text-ink-950">O</span>
                <span class="font-display text-lg font-semibold tracking-tight text-ink-950">Octavia</span>
            </Link>
        </header>
        <main class="flex flex-1 items-center justify-center px-4 py-10">
            <div class="w-full max-w-md">
                <slot />
            </div>
        </main>
        <footer class="pb-6 text-center text-xs text-ink-300">
            © {{ new Date().getFullYear() }} Octavia
        </footer>
    </div>
</template>
