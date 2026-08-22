<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';

const { t } = useI18n();

const year = new Date().getFullYear();
</script>

<template>
    <div class="flex min-h-screen flex-col bg-paper-50">
        <header class="sticky top-0 z-40 border-b border-ink-100/80 bg-paper-50/90 backdrop-blur">
            <div class="mx-auto flex h-16 max-w-6xl items-center justify-between px-4 sm:px-6">
                <Link href="/" class="flex items-center gap-2.5" aria-label="Octavia home">
                    <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-accent-600 font-display text-lg font-bold text-ink-950">O</span>
                    <span class="font-display text-lg font-semibold tracking-tight text-ink-950">Octavia</span>
                </Link>
                <nav class="hidden items-center gap-7 text-sm font-medium text-ink-500 md:flex" aria-label="Main">
                    <a href="/#how" class="transition-colors hover:text-ink-900">{{ t('landing.howTitle') }}</a>
                    <a href="/#features" class="transition-colors hover:text-ink-900">{{ t('landing.featuresTitle') }}</a>
                    <Link href="/pricing" class="transition-colors hover:text-ink-900">Pricing</Link>
                </nav>
                <div class="flex items-center gap-3">
                    <Link href="/login" class="text-sm font-medium text-ink-700 transition-colors hover:text-ink-950">{{ t('nav.logIn') }}</Link>
                    <Link href="/register" class="rounded-lg bg-ink-950 px-4 py-2 text-sm font-medium text-white transition-colors hover:bg-ink-700">{{ t('nav.signUp') }}</Link>
                </div>
            </div>
        </header>

        <main class="flex-1">
            <slot />
        </main>

        <footer class="border-t border-ink-100 bg-white">
            <div class="mx-auto grid max-w-6xl gap-8 px-4 py-12 sm:px-6 md:grid-cols-[1fr_auto_auto]">
                <div>
                    <div class="flex items-center gap-2.5">
                        <span class="flex h-7 w-7 items-center justify-center rounded-md bg-accent-600 font-display text-sm font-bold text-ink-950">O</span>
                        <span class="font-display font-semibold text-ink-950">Octavia</span>
                    </div>
                    <p class="mt-3 max-w-sm text-sm text-ink-500">
                        The prompt laboratory. Benchmark, evolve and fine-tune prompts — not models.
                    </p>
                </div>
                <nav class="text-sm" aria-label="Product">
                    <h3 class="mb-3 font-semibold text-ink-950">Product</h3>
                    <ul class="space-y-2 text-ink-500">
                        <li><a href="/#how" class="hover:text-ink-900">How it works</a></li>
                        <li><a href="/#features" class="hover:text-ink-900">Features</a></li>
                        <li><Link href="/pricing" class="hover:text-ink-900">Pricing</Link></li>
                    </ul>
                </nav>
                <nav class="text-sm" aria-label="Legal">
                    <h3 class="mb-3 font-semibold text-ink-950">Legal</h3>
                    <ul class="space-y-2 text-ink-500">
                        <li><Link href="/privacy" class="hover:text-ink-900">Privacy policy</Link></li>
                        <li><Link href="/terms" class="hover:text-ink-900">Terms of service</Link></li>
                    </ul>
                </nav>
            </div>
            <div class="border-t border-ink-100 py-4 text-center text-xs text-ink-300">© {{ year }} Octavia. All rights reserved.</div>
        </footer>
    </div>
</template>
