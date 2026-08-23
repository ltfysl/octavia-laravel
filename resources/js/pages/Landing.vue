<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
import { ref, onMounted } from 'vue';
import PublicLayout from '../layouts/PublicLayout.vue';

defineProps<{ page: string }>();

const { t } = useI18n();

const features = [
    {
        title: 'Benchmarks that mean something',
        body: 'Define test cases with concrete success criteria — contains checks, regex, or LLM-as-judge. Versioned and weighted.',
        icon: 'M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z',
    },
    {
        title: 'Evolution engine',
        body: 'Octavia mutates your prompt, scores each candidate and hill-climbs until your target is hit — fully automated.',
        icon: 'M3.75 13.5l10.5-11.25L12 10.5h8.25L9.75 21.75 12 13.5H3.75Z',
    },
    {
        title: 'Every step visible',
        body: 'See the exact prompt at step 5, its score, which criteria failed and why. No black box.',
        icon: 'M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z',
    },
    {
        title: 'Live playground',
        body: 'Test any version instantly against a single case or the whole suite before you commit.',
        icon: 'M9.75 3.75v-1.5a.75.75 0 0 1 .75-.75h4.5a.75.75 0 0 1 .75.75v1.5M3 9h18M5.25 12h13.5m-13.5 3h13.5M3 18h18',
    },
    {
        title: 'Versioned library',
        body: 'Every edit is a version. Diff, restore, compare — know exactly which prompt produced which result.',
        icon: 'M12 6.042A8.967 8.967 0 0 0 6 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 0 1 6 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 0 1 6-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0 0 18 18a8.967 8.967 0 0 0-6 2.292m0-14.25v14.25',
    },
    {
        title: 'Marketplace',
        body: 'Install community prompts & benchmarks in one click. Publish your own, versioned and reviewed.',
        icon: 'M13.5 21v-7.5a.75.75 0 0 1 .75-.75h3a.75.75 0 0 1 .75.75V21m-4.5 0H2.36m11.14 0H18m0 0h3.64m-1.39 0V9.349M3.75 21V9.349',
    },
];
const howSteps = [
    { n: '01', title: 'Describe what "good" looks like', body: 'Build a benchmark with the wizard: add cases, define criteria per case, set weights. Two minutes, no code.' },
    { n: '02', title: 'Run the evolution', body: 'Octavia evaluates your prompt, mutates it, benchmarks each candidate and hill-climbs until target or limit.' },
    { n: '03', title: 'Inspect, keep, share', body: 'Review every step, keep winner as new version, publish to marketplace.' },
];

const pricing = [
    {
        name: 'Starter',
        price: '$0',
        suffix: '/mo',
        desc: 'For exploring the workflow',
        cta: 'Start free',
        href: '/register',
        featured: false,
        features: ['Up to 3 prompts', '5 benchmarks', '100 runs / month', 'Community support', 'Mock model included'],
    },
    {
        name: 'Pro',
        price: '$29',
        suffix: '/mo',
        desc: 'For serious prompt work',
        cta: 'Start 14-day trial',
        href: '/register',
        featured: true,
        features: ['Unlimited prompts & benchmarks', '2,500 runs / month', 'Evolution engine', 'Version diff & restore', 'Priority support', 'Bring your own API key'],
    },
    {
        name: 'Team',
        price: '$79',
        suffix: '/mo',
        desc: 'For labs that ship together',
        cta: 'Contact sales',
        href: '/register',
        featured: false,
        features: ['Everything in Pro', '10,000 runs / month', 'Team workspaces', 'Shared marketplace', 'SSO & roles'],
    },
];

const faqs = [
    { q: 'How is this different from asking an AI to improve my prompt?', a: 'Asking gives no evidence. Octavia scores every mutation against your test cases, so you see whether a change helped, hurt, or did nothing — with a full replayable history.' },
    { q: 'Do I need to code?', a: 'No. The wizard is plain language. Checks like “contains X” or AI-judge need no code; regex is optional for power users.' },
    { q: 'Which models are supported?', a: 'Any OpenAI-compatible endpoint — OpenAI, Azure, OpenRouter, or local via Ollama. A deterministic mock lets you try everything without a key.' },
    { q: 'Who owns my prompts?', a: 'You do. Everything is private by default. Publishing to the marketplace is opt-in, per item, and reversible.' },
];

// WOW interactions
const tiltCard = ref<HTMLElement | null>(null);
const spotX = ref(260);
const spotY = ref(160);
const onTiltMove = (e: MouseEvent) => {
    const el = tiltCard.value;
    if (!el) return;
    const r = el.getBoundingClientRect();
    spotX.value = e.clientX - r.left;
    spotY.value = e.clientY - r.top;
    const px = (e.clientX - r.left) / r.width - 0.5;
    const py = (e.clientY - r.top) / r.height - 0.5;
    el.style.transform = `perspective(1000px) rotateY(${px * 7}deg) rotateX(${-py * 7}deg)`;
};
const onTiltLeave = () => {
    const el = tiltCard.value;
    if (el) el.style.transform = 'perspective(1000px) rotateY(0) rotateX(0)';
};

const scrambleText = ref('fine-tune your prompts');
onMounted(() => {
    const target = 'fine-tune your prompts';
    const chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';
    let frame = 0;
    const tick = () => {
        if (!scrambleText.value) return;
        if (frame > 16) { scrambleText.value = target; return; }
        scrambleText.value = target.split('').map((c, i) => c === ' ' ? ' ' : i < target.length - frame + 4 ? c : chars[Math.floor(Math.random()*chars.length)]).join('');
        frame++;
        setTimeout(tick, 40);
    };
    setTimeout(tick, 400);
});

// billing toggle (monthly/yearly visual only)
const yearly = ref(false);
</script>

<template>
    <PublicLayout>
        <Head>
            <title>Benchmark, evolve and fine-tune your prompts</title>
            <meta name="description" :content="t('landing.heroSubtitle')" />
            <meta property="og:title" content="Octavia — The prompt laboratory for teams that ship" />
            <meta property="og:description" :content="t('landing.heroSubtitle')" />
            <meta property="og:type" content="website" />
            <meta name="twitter:card" content="summary_large_image" />
            <component :is="'script'" type="application/ld+json">{{ JSON.stringify({ '@context': 'https://schema.org', '@type': 'SoftwareApplication', name: 'Octavia', applicationCategory: 'DeveloperApplication', operatingSystem: 'Web', description: 'Prompt laboratory: benchmark, evolve and fine-tune prompts against custom test suites.', offers: { '@type': 'Offer', price: '0', priceCurrency: 'USD' }, featureList: 'Benchmarks, Evolution engine, Versioning, Marketplace' }) }}</component>
        </Head>

        <!-- SAAS HERO — Dark, split, dashboard-forward -->
        <section class="relative overflow-hidden bg-[#0F172A]">
            <!-- ambient glows -->
            <div class="pointer-events-none absolute inset-0">
                <div class="absolute -left-32 -top-32 h-[720px] w-[720px] rounded-full bg-[#22C55E]/12 blur-[100px]" />
                <div class="absolute -right-40 top-20 h-[600px] w-[600px] rounded-full bg-[#38BDF8]/10 blur-[90px]" />
                <div class="absolute inset-0 opacity-[0.04]" style="background-image: radial-gradient(circle at 1px 1px, white 1px, transparent 0); background-size: 28px 28px;" />
                <svg class="absolute inset-0 h-full w-full opacity-[0.06]" viewBox="0 0 1440 800" fill="none" preserveAspectRatio="xMidYMid slice" aria-hidden="true">
                    <path d="M-60 520 C280 480 480 560 760 500 S1220 420 1520 440" stroke="white" stroke-width="1" />
                    <path d="M-60 440 C300 400 520 480 780 420 S1240 340 1520 360" stroke="white" opacity=".5" />
                </svg>
            </div>

            <div class="relative mx-auto grid max-w-[1400px] items-center gap-10 px-4 py-10 sm:px-6 lg:grid-cols-[1.05fr_1fr] lg:px-8 lg:py-16 min-h-[88dvh]">
                <div class="relative">
                    <div class="inline-flex items-center gap-2 rounded-full border border-white/10 bg-white/[0.06] px-3 py-1 backdrop-blur">
                        <span class="inline-flex h-2 w-2 animate-pulse rounded-full bg-[#22C55E]" aria-hidden="true" />
                        <span class="text-xs font-medium tracking-wide text-white/80">New — Evolution engine 2.0</span>
                        <span class="hidden sm:inline h-3 w-px bg-white/10" />
                        <span class="hidden sm:inline font-mono text-xs text-white/40">Octavia Field Station</span>
                    </div>

                    <h1 class="mt-6 font-display text-5xl font-bold leading-[0.9] tracking-tighter text-white md:text-6xl lg:text-[64px]">
                        <span class="block">The prompt</span>
                        <span class="block bg-gradient-to-r from-white via-white to-white/60 bg-clip-text text-transparent">laboratory</span>
                        <span class="block text-2xl font-normal tracking-tight text-white/60 md:text-3xl">for teams that <span ref="scrambleRef" class="text-white">{{ scrambleText }}</span></span>
                    </h1>

                    <p class="mt-5 max-w-[56ch] text-base leading-relaxed text-slate-300 md:text-lg">
                        {{ t('landing.heroSubtitle') }} Define success, let Octavia hill-climb your prompt, and ship what’s proven — not guessed.
                    </p>

                    <div class="mt-8 flex flex-col gap-3 sm:flex-row">
                        <Link href="/register" class="inline-flex items-center justify-center gap-2 rounded-full bg-[#22C55E] px-7 py-3.5 text-sm font-semibold text-[#0F172A] shadow-[0_0_20px_rgba(34,197,94,0.35)] transition-all hover:bg-[#16A34A] hover:shadow-[0_0_28px_rgba(34,197,94,0.45)] active:scale-[0.98]">
                            Start free — no card required
                            <span class="flex h-6 w-6 items-center justify-center rounded-full bg-[#0F172A] text-white">→</span>
                        </Link>
                        <a href="#how" class="inline-flex items-center justify-center rounded-full border border-white/15 bg-white/5 px-7 py-3.5 text-sm font-medium text-white backdrop-blur transition hover:bg-white/10 active:scale-[0.98]">
                            See how it works
                        </a>
                    </div>

                    <div class="mt-6 flex flex-wrap items-center gap-4 text-xs text-white/40">
                        <span class="inline-flex items-center gap-2"><span class="h-1.5 w-1.5 rounded-full bg-[#22C55E]" /> No credit card</span>
                        <span class="h-3 w-px bg-white/10" />
                        <span class="inline-flex items-center gap-2"><span class="h-1.5 w-1.5 rounded-full bg-white/60" /> Mock model included</span>
                        <span class="h-3 w-px bg-white/10 hidden sm:inline" />
                        <span class="hidden sm:inline">Trusted by prompt teams</span>
                    </div>

                    <!-- social proof logos -->
                    <div class="mt-8 flex flex-wrap items-center gap-6 border-t border-white/5 pt-6 opacity-60">
                        <span class="font-mono text-xs tracking-widest text-white/30">TRUSTED BY</span>
                        <div class="flex flex-wrap items-center gap-6 text-sm font-semibold tracking-tight text-white/50">
                            <span>Linear</span><span class="h-3 w-px bg-white/10" /><span>Perplexity</span><span class="h-3 w-px bg-white/10" /><span>Retool</span><span class="h-3 w-px bg-white/10" /><span>Vercel</span>
                        </div>
                    </div>
                </div>

                <!-- Dashboard preview — tilt + spotlight -->
                <div class="relative lg:pl-4">
                    <div
                        ref="tiltCard"
                        class="group relative overflow-hidden rounded-[2rem] border border-white/10 bg-[#1B2336] p-3 shadow-[0_32px_80px_rgba(0,0,0,0.5)] transition-transform duration-200 will-change-transform"
                        @mousemove="onTiltMove"
                        @mouseleave="onTiltLeave"
                    >
                        <div class="pointer-events-none absolute -inset-px rounded-[2rem] opacity-0 transition-opacity duration-300 group-hover:opacity-100" :style="`background: radial-gradient(520px circle at ${spotX}px ${spotY}px, rgba(34,197,94,0.16), transparent 72%)`" aria-hidden="true" />
                        <div class="relative overflow-hidden rounded-[1.5rem] border border-[#334155] bg-[#0F172A]">
                            <!-- window chrome -->
                            <div class="flex items-center justify-between border-b border-[#1E293B] bg-[#1B2336] px-4 py-3">
                                <div class="flex items-center gap-1.5">
                                    <span class="h-2.5 w-2.5 rounded-full bg-red-400" /><span class="h-2.5 w-2.5 rounded-full bg-yellow-400" /><span class="h-2.5 w-2.5 rounded-full bg-green-400" />
                                </div>
                                <span class="rounded-full bg-[#0F172A] px-2.5 py-1 font-mono text-xs text-slate-400">Run · Tagline writer × Tagline quality</span>
                                <span class="rounded-full bg-[#22C55E]/15 px-2.5 py-1 text-xs font-medium text-[#22C55E]">completed</span>
                            </div>
                            <div class="p-4">
                                <div class="grid grid-cols-3 gap-3 text-center">
                                    <div class="rounded-xl bg-[#1B2336] p-3">
                                        <p class="font-mono text-xs tracking-wide text-slate-400">BEST SCORE</p>
                                        <p class="mt-1 font-display text-2xl font-bold text-white">100<span class="text-[#22C55E]">%</span></p>
                                    </div>
                                    <div class="rounded-xl bg-[#1B2336] p-3">
                                        <p class="font-mono text-xs tracking-wide text-slate-400">STEPS</p>
                                        <p class="mt-1 font-display text-2xl font-bold text-white">3</p>
                                    </div>
                                    <div class="rounded-xl bg-[#1B2336] p-3">
                                        <p class="font-mono text-xs tracking-wide text-slate-400">TARGET</p>
                                        <p class="mt-1 font-display text-2xl font-bold text-white">95%</p>
                                    </div>
                                </div>
                                <div class="mt-4 space-y-2">
                                    <div class="flex items-center gap-3 rounded-xl bg-[#1E293B] px-3 py-2.5"><span class="w-14 font-mono text-xs text-slate-400">step 1</span><span class="text-sm text-slate-200">initial prompt</span><span class="ml-auto rounded-full bg-red-500/90 px-2 py-1 text-xs font-medium text-white">33%</span></div>
                                    <div class="flex items-center gap-3 rounded-xl bg-[#1E293B] px-3 py-2.5"><span class="w-14 font-mono text-xs text-slate-400">step 2</span><span class="text-sm text-slate-200">+ brand requirement</span><span class="ml-auto rounded-full bg-amber-500 px-2 py-1 text-xs font-medium text-white">67%</span></div>
                                    <div class="flex items-center gap-3 rounded-xl bg-[#1E293B] px-3 py-2.5"><span class="w-14 font-mono text-xs text-slate-400">step 3</span><span class="text-sm text-slate-200">+ length constraint</span><span class="ml-auto rounded-full bg-[#22C55E] px-2 py-1 text-xs font-medium text-white">100%</span></div>
                                </div>
                            </div>
                        </div>
                        <div class="absolute -bottom-3 -left-3 hidden items-center gap-2 rounded-2xl border border-[#334155] bg-[#1B2336] px-3 py-2 shadow-xl lg:flex">
                            <span class="flex h-7 w-7 items-center justify-center rounded-full bg-[#22C55E] text-xs text-[#0F172A]">▲</span>
                            <span class="font-mono text-xs font-medium text-white">Peak 100% · 3 steps</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="absolute bottom-0 left-0 w-full border-t border-white/10 bg-black/20 py-3 backdrop-blur">
                <div class="flex w-max animate-[marquee_28s_linear_infinite] gap-10 whitespace-nowrap font-mono text-xs tracking-widest text-white/45">
                    <span v-for="i in 6" :key="i" class="flex items-center gap-10"><span>BENCHMARK</span><span class="h-1 w-1 rotate-45 bg-[#22C55E]" /><span>EVOLVE</span><span class="h-1 w-1 rotate-45 bg-white/30" /><span>FINE-TUNE</span><span class="h-1 w-1 rotate-45 bg-[#22C55E]" /></span>
                </div>
            </div>
        </section>

        <!-- BENTO FEATURES — SaaS value props -->
        <section id="features" class="bg-[#F8FAFC] py-16 md:py-24">
            <div class="mx-auto max-w-[1400px] px-4 sm:px-6 lg:px-8">
                <div class="mx-auto max-w-2xl text-center">
                    <p class="font-mono text-xs font-semibold uppercase tracking-widest text-[#22C55E]">Capabilities</p>
                    <h2 class="mt-2 font-display text-4xl font-bold tracking-tighter text-[#0F172A] md:text-5xl">{{ t('landing.featuresTitle') }}</h2>
                    <p class="mt-4 text-base leading-relaxed text-slate-500">Six primitives that turn prompting from folklore into science.</p>
                </div>

                <div class="mt-12 grid gap-4 md:grid-cols-12">
                    <div class="group relative overflow-hidden rounded-[2rem] border border-slate-200 bg-white p-8 shadow-[0_12px_32px_rgba(15,23,42,0.06)] md:col-span-7">
                        <div class="absolute inset-0 opacity-[0.03]" style="background-image: radial-gradient(circle at 1px 1px, #0F172A 1px, transparent 0); background-size: 20px 20px;" aria-hidden="true" />
                        <span class="inline-flex h-11 w-11 items-center justify-center rounded-2xl bg-[#0F172A] text-white">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" :d="features[1].icon" /></svg>
                        </span>
                        <h3 class="mt-5 font-display text-xl font-semibold text-[#0F172A]">{{ features[1].title }}</h3>
                        <p class="mt-2 max-w-[52ch] text-sm leading-relaxed text-slate-500">{{ features[1].body }}</p>
                        <div class="mt-6 flex items-center gap-2">
                            <span class="h-2 w-8 rounded-full bg-[#22C55E]" /><span class="h-2 w-8 rounded-full bg-slate-200" /><span class="h-2 w-8 rounded-full bg-slate-200" />
                        </div>
                    </div>

                    <div class="relative overflow-hidden rounded-[2rem] bg-[#0F172A] p-8 text-white md:col-span-5">
                        <div class="absolute -right-12 -top-12 h-40 w-40 rounded-full border border-white/10" aria-hidden="true" />
                        <span class="inline-flex h-11 w-11 items-center justify-center rounded-2xl bg-white/10 backdrop-blur">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" :d="features[0].icon" /></svg>
                        </span>
                        <h3 class="mt-5 font-display text-lg font-semibold">{{ features[0].title }}</h3>
                        <p class="mt-2 text-sm leading-relaxed text-white/60">{{ features[0].body }}</p>
                        <Link href="/benchmarks/wizard" class="mt-5 inline-flex rounded-full bg-white px-4 py-2 text-xs font-semibold text-[#0F172A] hover:bg-slate-100">Create benchmark →</Link>
                    </div>

                    <div
                        v-for="f in features.slice(2)"
                        :key="f.title"
                        class="group relative overflow-hidden rounded-[2rem] border border-slate-200 bg-white p-6 shadow-[0_12px_32px_rgba(15,23,42,0.04)] transition hover:-translate-y-1 hover:shadow-[0_20px_40px_rgba(15,23,42,0.08)] md:col-span-4"
                    >
                        <span class="inline-flex h-10 w-10 items-center justify-center rounded-xl bg-slate-100 text-slate-700 transition group-hover:bg-[#22C55E] group-hover:text-[#0F172A]">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" :d="f.icon" /></svg>
                        </span>
                        <h3 class="mt-4 font-display text-base font-semibold text-[#0F172A]">{{ f.title }}</h3>
                        <p class="mt-2 text-sm leading-relaxed text-slate-500">{{ f.body }}</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- HOW IT WORKS — zig-zag, not 3 cards -->
        <section id="how" class="border-y border-slate-200 bg-white py-16 md:py-24">
            <div class="mx-auto grid max-w-[1400px] gap-10 px-4 sm:px-6 lg:grid-cols-[0.95fr_1.15fr] lg:px-8">
                <div class="lg:sticky lg:top-24 lg:self-start">
                    <p class="font-mono text-xs font-semibold uppercase tracking-widest text-[#22C55E]">How it works</p>
                    <h2 class="mt-2 font-display text-4xl font-bold tracking-tighter text-[#0F172A] md:text-5xl">{{ t('landing.howTitle') }}</h2>
                    <p class="mt-4 max-w-[42ch] text-sm leading-relaxed text-slate-500">Three moves. No black boxes. Every mutation measured against your definition of good.</p>
                    <div class="mt-6 hidden h-px w-full bg-gradient-to-r from-[#22C55E] to-transparent lg:block" />
                    <div class="mt-6 hidden items-center gap-3 lg:flex">
                        <span class="h-2 w-2 rounded-full bg-[#22C55E]" /> <span class="font-mono text-xs text-slate-400">01 → 02 → 03 · proven loop</span>
                    </div>
                </div>
                <div class="space-y-4">
                    <div v-for="(step, i) in howSteps" :key="step.n" class="group relative overflow-hidden rounded-[1.8rem] border border-slate-200 bg-slate-50 p-6 transition hover:border-slate-300 hover:bg-white hover:shadow-[0_12px_32px_rgba(15,23,42,0.06)]" :style="`animation-delay:${i*90}ms`">
                        <div class="absolute left-0 top-0 h-full w-1 bg-[#22C55E] opacity-0 transition group-hover:opacity-100" />
                        <div class="flex items-start gap-4">
                            <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-[#0F172A] font-mono text-sm font-bold text-white">{{ step.n }}</span>
                            <div>
                                <h3 class="font-display text-lg font-semibold text-[#0F172A]">{{ step.title }}</h3>
                                <p class="mt-2 text-sm leading-relaxed text-slate-500">{{ step.body }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- PRICING — SaaS core, missing before -->
        <section id="pricing" class="bg-[#0F172A] py-16 md:py-24">
            <div class="mx-auto max-w-[1400px] px-4 sm:px-6 lg:px-8">
                <div class="mx-auto max-w-2xl text-center">
                    <p class="font-mono text-xs font-semibold uppercase tracking-widest text-[#22C55E]">Pricing</p>
                    <h2 class="mt-2 font-display text-4xl font-bold tracking-tighter text-white md:text-5xl">Start free. Scale when proven.</h2>
                    <p class="mt-4 text-slate-400">All plans include the mock model. Bring your own API key when ready.</p>
                    <div class="mt-6 inline-flex rounded-full border border-white/10 bg-white/5 p-1 backdrop-blur">
                        <button :class="['rounded-full px-4 py-1.5 text-xs font-medium transition', !yearly ? 'bg-white text-[#0F172A]' : 'text-white/60']" @click="yearly=false">Monthly</button>
                        <button :class="['rounded-full px-4 py-1.5 text-xs font-medium transition', yearly ? 'bg-white text-[#0F172A]' : 'text-white/60']" @click="yearly=true">Yearly <span class="ml-1 rounded bg-[#22C55E] px-1.5 py-0.5 text-[10px] text-[#0F172A]">-20%</span></button>
                    </div>
                </div>

                <div class="mt-12 grid gap-6 md:grid-cols-3">
                    <div v-for="tier in pricing" :key="tier.name" :class="['relative flex flex-col rounded-[2rem] border p-8', tier.featured ? 'border-[#22C55E] bg-white text-[#0F172A] shadow-[0_24px_64px_rgba(34,197,94,0.18)] scale-[1.02]' : 'border-white/10 bg-[#1B2336] text-white']">
                        <span v-if="tier.featured" class="absolute -top-3 left-1/2 -translate-x-1/2 rounded-full bg-[#22C55E] px-3 py-1 text-xs font-bold text-[#0F172A]">MOST POPULAR</span>
                        <h3 class="font-display text-lg font-semibold" :class="tier.featured ? 'text-[#0F172A]' : 'text-white'">{{ tier.name }}</h3>
                        <p class="mt-1 text-sm" :class="tier.featured ? 'text-slate-500' : 'text-slate-400'">{{ tier.desc }}</p>
                        <p class="mt-6 flex items-baseline gap-1">
                            <span class="font-display text-4xl font-bold tracking-tighter">{{ yearly && tier.price !== '$0' ? '$' + (parseInt(tier.price.slice(1)) * 10) : tier.price }}</span>
                            <span class="text-sm" :class="tier.featured ? 'text-slate-400' : 'text-slate-400'">{{ tier.suffix }}{{ yearly ? ' billed yearly' : '' }}</span>
                        </p>
                        <Link :href="tier.href" :class="['mt-6 inline-flex justify-center rounded-full px-6 py-3 text-sm font-semibold transition active:scale-[0.98]', tier.featured ? 'bg-[#0F172A] text-white hover:bg-[#1E293B]' : 'bg-white text-[#0F172A] hover:bg-slate-100']">{{ tier.cta }}</Link>
                        <ul class="mt-8 space-y-3 border-t pt-6 text-sm" :class="tier.featured ? 'border-slate-200' : 'border-white/10'">
                            <li v-for="feat in tier.features" :key="feat" class="flex items-start gap-2">
                                <span class="mt-0.5 flex h-5 w-5 shrink-0 items-center justify-center rounded-full text-xs" :class="tier.featured ? 'bg-[#22C55E]/15 text-[#22C55E]' : 'bg-white/10 text-[#22C55E]'">✓</span>
                                <span :class="tier.featured ? 'text-slate-600' : 'text-slate-300'">{{ feat }}</span>
                            </li>
                        </ul>
                    </div>
                </div>

                <p class="mt-8 text-center font-mono text-xs text-white/40">All prices in USD · 14-day trial on Pro · Cancel anytime</p>
            </div>
        </section>

        <!-- FAQ — glass -->
        <section class="bg-white py-16 md:py-24">
            <div class="mx-auto max-w-3xl px-4 sm:px-6">
                <p class="text-center font-mono text-xs font-semibold uppercase tracking-widest text-[#22C55E]">Questions</p>
                <h2 class="mt-2 text-center font-display text-4xl font-bold tracking-tighter text-[#0F172A]">{{ t('landing.faqTitle') }}</h2>
                <div class="mt-10 space-y-3">
                    <details v-for="faq in faqs" :key="faq.q" class="group rounded-2xl border border-slate-200 bg-slate-50 px-6 py-4 open:bg-white open:shadow-[0_12px_32px_rgba(15,23,42,0.06)] transition">
                        <summary class="flex cursor-pointer list-none items-center justify-between gap-4 font-medium text-[#0F172A] marker:hidden">
                            {{ faq.q }}
                            <span class="flex h-7 w-7 shrink-0 items-center justify-center rounded-full border border-slate-200 bg-white text-slate-400 transition group-open:rotate-45 group-open:bg-[#0F172A] group-open:text-white group-open:border-[#0F172A]">+</span>
                        </summary>
                        <p class="mt-3 text-sm leading-relaxed text-slate-500">{{ faq.a }}</p>
                    </details>
                </div>
            </div>
        </section>

        <!-- FINAL CTA — mesh -->
        <section class="relative overflow-hidden bg-[#0F172A] py-16 md:py-24">
            <div class="pointer-events-none absolute inset-0">
                <div class="absolute -left-20 top-10 h-[500px] w-[500px] rounded-full bg-[#22C55E]/15 blur-[80px]" />
                <div class="absolute -right-20 bottom-10 h-[400px] w-[400px] rounded-full bg-white/5 blur-[70px]" />
            </div>
            <div class="relative mx-auto max-w-6xl px-4 text-center sm:px-6">
                <h2 class="font-display text-4xl font-bold tracking-tighter text-white md:text-5xl">Your next prompt deserves evidence.</h2>
                <p class="mx-auto mt-4 max-w-xl text-white/60">Create a free account, build your first benchmark in minutes and watch Octavia evolve your prompt.</p>
                <Link href="/register" class="mt-8 inline-flex items-center gap-2 rounded-full bg-white px-8 py-3.5 text-sm font-semibold text-[#0F172A] shadow-xl transition hover:bg-slate-100 active:scale-[0.98]">
                    {{ t('landing.cta') }} <span class="flex h-6 w-6 items-center justify-center rounded-full bg-[#0F172A] text-white">→</span>
                </Link>
                <p class="mt-4 font-mono text-xs text-white/30">No card required · Mock model included</p>
            </div>
        </section>
    </PublicLayout>
</template>

<style>
@keyframes marquee { from { transform: translateX(0) } to { transform: translateX(-50%) } }
@keyframes float { 0%,100% { transform: translateY(0) } 50% { transform: translateY(-10px) } }
</style>
