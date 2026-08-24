<script setup lang="ts">
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import { useEcho } from '../../echo';
import { useI18n } from 'vue-i18n';
import { onMounted, onUnmounted, ref, computed } from 'vue';
import AppLayout from '../../layouts/AppLayout.vue';
import OPanel from '../../components/ui/OPanel.vue';
import OEmptyState from '../../components/ui/OEmptyState.vue';
import OBadge from '../../components/ui/OBadge.vue';
import OScoreBar from '../../components/ui/OScoreBar.vue';

const props = defineProps<{
    stats: { prompts: number; benchmarks: number; activeRuns: number; bestScore: number };
    scoreHistory: Array<{ at: string; score: number }>;
    topPrompts: Array<{ id: number; name: string; avg_score: number | null; best_score: number | null }>;
    recentRuns: Array<{
        id: number;
        name: string;
        status: string;
        mode: string;
        score: number | null;
        prompt?: { id: number; name: string } | null;
        benchmark?: { id: number; name: string } | null;
        created_at: string;
    }>;
    leaderboard: Array<{ rank: number; id: number; run_id: number; run_name: string; prompt_content: string; score: number; strategy: string }>;
    promptCategories: Array<{ label: string; count: number; fill: number }>;
    benchmarkCategories: Array<{ label: string; count: number; fill: number }>;
    scoreDistribution: Array<{ range: string; count: number }>;
}>();

const { t } = useI18n();

const statusTone: Record<string, 'mint' | 'amber' | 'rose' | 'neutral' | 'accent'> = {
    completed: 'mint',
    running: 'accent',
    pending: 'neutral',
    failed: 'rose',
    cancelled: 'neutral',
};

// Typewriter for Command Input
const prompts = ["Benchmark the new onboarding prompt...", "Evolve tagline writer against quality suite...", "Test 'Eco bottle' criteria edge cases...", "Optimize for 95% target..."];
const typed = ref("");
const commandQuery = ref("");
const goSearch = () => {
    const q = commandQuery.value.trim();
    if (q) router.get('/search', { q });
};
const promptIndex = ref(0);
const charIndex = ref(0);
const isDeleting = ref(false);
let typeTimer: number | undefined;

// WOW: tilt + spotlight + scramble
const tiltCard = ref<HTMLElement | null>(null);
const scrambleRef = ref<HTMLElement | null>(null);
const spotX = ref(200);
const spotY = ref(120);
const onTiltMove = (e: MouseEvent) => {
    const el = tiltCard.value;
    if (!el) return;
    const rect = el.getBoundingClientRect();
    spotX.value = e.clientX - rect.left;
    spotY.value = e.clientY - rect.top;
    const px = (e.clientX - rect.left) / rect.width - 0.5;
    const py = (e.clientY - rect.top) / rect.height - 0.5;
    el.style.transform = `perspective(900px) rotateY(${px * 6}deg) rotateX(${-py * 6}deg) translateZ(0)`;
};
const onTiltLeave = () => {
    const el = tiltCard.value;
    if (el) el.style.transform = "perspective(900px) rotateY(0) rotateX(0)";
};

onMounted(() => {
    // scramble “laboratory”
    const target = "laboratory";
    const chars = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz";
    let frame = 0;
    const scramble = () => {
        if (!scrambleRef.value) return;
        if (frame > 18) { scrambleRef.value.textContent = target; return; }
        scrambleRef.value.textContent = target.split("").map((c,i) => i < target.length - frame + 6 ? c : chars[Math.floor(Math.random()*chars.length)]).join("");
        frame++;
        window.setTimeout(scramble, 42);
    };
    window.setTimeout(scramble, 400);
    const tick = () => {
        const full = prompts[promptIndex.value];
        if (!isDeleting.value) {
            typed.value = full.slice(0, charIndex.value + 1);
            charIndex.value++;
            if (charIndex.value === full.length) {
                isDeleting.value = true;
                typeTimer = window.setTimeout(tick, 1400);
                return;
            }
        } else {
            typed.value = full.slice(0, charIndex.value - 1);
            charIndex.value--;
            if (charIndex.value === 0) {
                isDeleting.value = false;
                promptIndex.value = (promptIndex.value + 1) % prompts.length;
            }
        }
        typeTimer = window.setTimeout(tick, isDeleting.value ? 32 : 58);
    };
    typeTimer = window.setTimeout(tick, 600);
});
onUnmounted(() => window.clearTimeout(typeTimer));

// Live status pulse - derived
const liveRuns = computed(() => props.recentRuns.filter(r => r.status === 'running').slice(0, 3));
const maxScoreDistribution = computed(() => Math.max(1, ...props.scoreDistribution.map(b => b.count)));

// Chart — score trend with fallback width (fixes chartWidth 0 bug from test)
const chartData = computed(() => props.scoreHistory.map((h) => Math.round(h.score * 100)));
const chartWidth = 600;
const chartPath = computed(() => {
    if (chartData.value.length < 2) return '';
    const w = chartWidth;
    const h = 100;
    const step = w / (chartData.value.length - 1);
    return chartData.value.map((v, i) => `${i === 0 ? 'M' : 'L'} ${i * step} ${h - v}`).join(' ');
});
const chartAreaPath = computed(() => {
    if (chartData.value.length < 2) return '';
    const line = chartPath.value;
    const w = chartWidth;
    const h = 100;
    return `${line} L ${w} ${h} L 0 ${h} Z`;
});
// Count-up — reference parity (use-count-up) but lighter: rAF
const animated = ref({ prompts: 0, benchmarks: 0, activeRuns: 0, bestScore: 0 });
const animateTo = (target: number, setter: (v: number) => void, duration = 900) => {
    const start = performance.now();
    const tick = (now: number) => {
        const p = Math.min(1, (now - start) / duration);
        const eased = 1 - Math.pow(1 - p, 3);
        setter(Math.round(target * eased));
        if (p < 1) requestAnimationFrame(tick);
    };
    requestAnimationFrame(tick);
};
const page = usePage<{ auth: { user: { id: number } } }>();

onMounted(() => {
    const echo = useEcho();
    const userId = page.props.auth?.user?.id;
    if (echo && userId) {
        echo.private(`App.Models.User.${userId}`).listen('.progress', () => router.reload({ only: ['recentRuns', 'stats', 'scoreHistory', 'topPrompts', 'leaderboard'] }));
    }

    animateTo(props.stats.prompts, (v) => (animated.value.prompts = v));
    animateTo(props.stats.benchmarks, (v) => (animated.value.benchmarks = v));
    animateTo(props.stats.activeRuns, (v) => (animated.value.activeRuns = v));
    animateTo(Math.round(props.stats.bestScore * 100), (v) => (animated.value.bestScore = v), 1200);
});

</script>

<template>
    <AppLayout>
        <Head><title>{{ t('dashboard.title') }}</title></Head>

        <div class="max-w-[1400px] mx-auto">
            <!-- WOW HERO — Cinematic Field Station -->
            <section class="relative -mx-4 -mt-6 overflow-hidden pinned-dark bg-ink-950 sm:-mx-6 lg:-mx-10">
                <div class="pointer-events-none absolute inset-0">
                    <div class="absolute -left-20 top-10 h-[520px] w-[520px] rounded-full bg-accent-600/20 blur-[90px] animate-[float_8s_ease-in-out_infinite]" />
                    <div class="absolute -right-20 top-32 h-[460px] w-[460px] rounded-full bg-emerald-400/10 blur-[80px] animate-[float_10s_ease-in-out_infinite_reverse]" />
                    <div class="absolute left-1/3 top-1/2 h-[600px] w-[800px] -translate-x-1/2 rounded-full bg-card/[0.04] blur-[60px]" />
                    <!-- contour lines over dark -->
                    <svg class="absolute inset-0 h-full w-full opacity-[0.08]" viewBox="0 0 1440 600" fill="none" preserveAspectRatio="xMidYMid slice" aria-hidden="true">
                        <path d="M-40 420 C240 380 380 440 620 400 S1060 340 1480 360" stroke="white" stroke-width="1" />
                        <path d="M-40 340 C260 300 420 360 700 320 S1100 260 1480 280" stroke="white" stroke-width="1" opacity=".6" />
                        <path d="M120 260 C380 220 520 280 820 240 S1220 180 1520 200" stroke="white" stroke-width="1" opacity=".3" />
                        <path d="M-40 480 C280 440 500 480 800 440 S1280 380 1520 400" stroke="white" stroke-width="1" stroke-dasharray="6 8" opacity=".5" />
                    </svg>
                    <!-- grain -->
                    <div class="absolute inset-0 opacity-[0.03]" style="background-image: url('data:image/svg+xml;utf8,<svg xmlns=%22http://www.w3.org/2000/svg%22 width=%22200%22 height=%22200%22><filter id=%22n%22><feTurbulence type=%22fractalNoise%22 baseFrequency=%220.9%22/></filter><rect width=%22100%25%22 height=%22100%25%22 filter=%22url(%23n)%22 opacity=%220.4%22/></svg>')" />
                </div>

                <div class="relative grid items-center gap-8 px-4 py-10 sm:px-6 lg:grid-cols-[1.25fr_0.9fr] lg:px-10 lg:py-14 min-h-[540px] lg:min-h-[560px]">
                    <div class="relative">
                        <div class="inline-flex items-center gap-2 rounded-full border border-white/10 bg-card/[0.06] px-3 py-1 text-[11px] font-medium tracking-wide text-white/70 backdrop-blur">
                            <span class="h-2 w-2 animate-pulse rounded-full bg-accent-500" />
                            FIELD STATION · LIVE
                            <span class="ml-2 hidden sm:inline h-4 w-px bg-card/10" />
                            <span class="hidden sm:inline font-mono text-white/40">ELEV {{ animated.bestScore }}.0</span>
                        </div>
                        <!-- Kinetic headline with stroke gradient -->
                        <h1 class="display-hero mt-5 text-5xl leading-[0.85] tracking-tighter text-white md:text-7xl">
                            Your prompt<br />
                            <span class="relative inline-block">
                                <span ref="scrambleRef" class="relative z-10 bg-gradient-to-r from-white to-white/60 bg-clip-text text-transparent">laboratory</span>
                                <span class="absolute inset-0 bg-gradient-to-r from-accent-400 via-white to-accent-400 bg-clip-text text-transparent opacity-0 animate-[shimmer_3s_linear_infinite]" aria-hidden="true">laboratory</span>
                            </span>
                            <span class="ml-3 inline-flex -translate-y-2 rounded-full bg-accent-600 px-3 py-1 font-mono text-xs font-bold tracking-widest text-ink-950">{{ t('dashboard.atAGlance') }}</span>
                        </h1>
                        <p class="mt-4 max-w-[52ch] text-base leading-relaxed text-white/60">
                            {{ t('dashboard.subtitle') }} — traverse the fitness landscape. Every run is an expedition, every version a waypoint.
                        </p>
                        <div class="mt-7 flex flex-wrap items-center gap-3">
                            <Link ref="magneticBtn" href="/runs/create" class="group relative inline-flex items-center gap-3 rounded-full bg-card px-6 py-3 text-sm font-semibold text-ink-950 shadow-[0_8px_30px_rgba(0,0,0,0.3)] transition-all hover:shadow-[0_12px_40px_rgba(0,0,0,0.4)] active:scale-[0.98]">
                                Start expedition
                                <span class="flex h-7 w-7 items-center justify-center rounded-full bg-ink-950 text-white transition-transform group-hover:translate-x-0.5">→</span>
                            </Link>
                            <div class="flex items-center gap-3 rounded-full border border-white/10 bg-card/5 px-4 py-2 backdrop-blur">
                                <span class="flex h-6 w-6 items-center justify-center rounded-full bg-card/10 text-white text-xs">◈</span>
                                <span class="font-mono text-xs text-white/70">{{ animated.prompts }} {{ t('dashboard.specimens') }} · {{ animated.benchmarks }} suites</span>
                            </div>
                        </div>
                        <div class="mt-6 flex items-center gap-4 font-mono text-xs text-white/40">
                            <span>01 — FIELD STATION</span>
                            <span class="h-px w-12 bg-card/20" />
                            <span>CONTOUR INTERVAL 10%</span>
                        </div>
                    </div>

                    <!-- Tilt Spotlight Card -->
                    <div class="relative lg:pl-4">
                        <div
                            ref="tiltCard"
                            class="group relative overflow-hidden rounded-[2rem] border border-white/10 bg-card p-7 shadow-[0_32px_64px_-16px_rgba(0,0,0,0.5)] backdrop-blur transition-transform duration-200 will-change-transform lg:p-8"
                            @mousemove="onTiltMove"
                            @mouseleave="onTiltLeave"
                        >
                            <!-- spotlight border -->
                            <div class="pointer-events-none absolute -inset-px rounded-[2rem] opacity-0 transition-opacity duration-300 group-hover:opacity-100" :style="`background: radial-gradient(420px circle at ${spotX}px ${spotY}px, rgba(234,88,12,0.15), transparent 80%)`" aria-hidden="true" />
                            <div class="absolute inset-0 bg-field-grid opacity-[0.04]" aria-hidden="true" />
                            <div class="relative">
                                <div class="flex items-center justify-between">
                                    <p class="eyebrow !text-ink-400">Best avg. score — elevation</p>
                                    <span class="rounded-full bg-ink-950 px-2.5 py-1 font-mono text-xs font-medium text-white">{{ t('dashboard.peak') }}</span>
                                </div>
                                <div class="mt-3 flex items-baseline gap-3">
                                    <span class="display-hero text-6xl font-extrabold tracking-tighter text-ink-950 md:text-7xl">{{ animated.bestScore }}<span class="text-accent-600">%</span></span>
                                    <span class="rounded-full bg-mint-100 px-2.5 py-1 font-mono text-xs font-medium text-mint-600">datum · live</span>
                                </div>
                                <div class="mt-5">
                                    <OScoreBar :score="stats.bestScore" :target="0.95" />
                                </div>
                                <div class="mt-5 grid grid-cols-3 gap-3 border-t border-ink-100 pt-5">
                                    <div>
                                        <p class="font-mono text-[11px] uppercase tracking-wide text-ink-300">{{ t('nav.prompts') }}</p>
                                        <p class="font-display text-2xl font-bold text-ink-950">{{ animated.prompts }}</p>
                                        <div class="mt-1 h-1 overflow-hidden rounded-full bg-ink-100"><div class="h-full w-2/3 bg-ink-900" /></div>
                                    </div>
                                    <div class="border-l border-ink-100 pl-3">
                                        <p class="font-mono text-[11px] uppercase tracking-wide text-ink-300">{{ t('nav.benchmarks') }}</p>
                                        <p class="font-display text-2xl font-bold text-ink-950">{{ animated.benchmarks }}</p>
                                        <div class="mt-1 h-1 overflow-hidden rounded-full bg-ink-100"><div class="h-full w-1/2 bg-ink-900" /></div>
                                    </div>
                                    <div class="border-l border-ink-100 pl-3">
                                        <p class="font-mono text-[11px] uppercase tracking-wide text-ink-300">{{ t('dashboard.active') }}</p>
                                        <p class="font-display text-2xl font-bold text-ink-950">{{ animated.activeRuns }}</p>
                                        <div class="mt-1 flex items-center gap-1"><span class="h-1.5 w-1.5 animate-pulse rounded-full bg-emerald-500" /><span class="h-1 w-12 rounded-full bg-emerald-500" /></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- floating waypoint -->
                        <div class="absolute -bottom-4 -left-4 hidden items-center gap-3 rounded-2xl border border-white/20 bg-card px-4 py-3 shadow-[0_16px_32px_rgba(0,0,0,0.2)] lg:flex">
                            <span class="flex h-9 w-9 items-center justify-center rounded-full bg-accent-600 text-ink-950">▲</span>
                            <div class="text-xs leading-tight">
                                <p class="font-semibold text-ink-950">{{ t('dashboard.waypointReached') }}</p>
                                <p class="font-mono text-ink-400">interpolated · 80%</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Kinetic marquee -->
                <div class="absolute bottom-0 left-0 w-full border-t border-white/10 bg-black/20 py-3 backdrop-blur">
                    <div class="flex w-max animate-[marquee_28s_linear_infinite] gap-8 whitespace-nowrap font-mono text-xs tracking-widest text-white/50">
                        <span v-for="i in 6" :key="i" class="flex items-center gap-8"><span>OCTAVIA FIELD STATION</span><span class="h-1 w-1 rotate-45 bg-accent-600" /><span>PROMPT LABORATORY</span><span class="h-1 w-1 rotate-45 bg-card/30" /><span>ELEV {{ animated.bestScore }}% — CONTOUR 10%</span><span class="h-1 w-1 rotate-45 bg-accent-600" /></span>
                    </div>
                </div>
            </section>

            <!-- Bento 2.0 — 5 archetypes -->
            <section class="mt-10 grid grid-cols-1 gap-4 md:grid-cols-12">
                <!-- Intelligent List -->
                <div class="group relative overflow-hidden rounded-[2rem] border border-slate-200/60 bg-card p-6 shadow-[0_20px_40px_-15px_rgba(0,0,0,0.05)] md:col-span-4" style="--index:0">
                    <div class="flex items-center justify-between">
                        <p class="eyebrow">{{ t('dashboard.intelligentList') }}</p>
                        <span class="h-2 w-2 animate-pulse rounded-full bg-emerald-500" />
                    </div>
                    <h3 class="mt-2 font-display text-lg font-semibold tracking-tight text-ink-950">{{ t('dashboard.recentExpeditions') }}</h3>
                    <ul class="mt-4 space-y-2">
                        <li v-for="(run, i) in recentRuns.slice(0, 4)" :key="run.id" class="group flex items-center gap-3 rounded-2xl border border-transparent bg-paper-100/70 px-3 py-2.5 transition-all duration-300 hover:border-ink-200 hover:bg-paper-100 hover:shadow-sm" :style="`animation-delay: ${i * 90}ms`">
                            <Link :href="`/runs/${run.id}`" class="flex w-full items-center gap-3">
                                <span class="flex h-7 w-7 items-center justify-center rounded-full bg-card text-[11px] font-bold text-ink-700 shadow-sm group-hover:bg-accent-100 group-hover:text-accent-800">{{ String(i+1).padStart(2,'0') }}</span>
                                <span class="min-w-0 flex-1 truncate text-sm font-medium text-ink-900 group-hover:text-accent-700">{{ run.name }}</span>
                                <OBadge :tone="statusTone[run.status] ?? 'neutral'" class="!px-1.5 !py-0 text-[11px]">{{ t(`runs.status.${run.status}`) }}</OBadge>
                            </Link>
                        </li>
                        <li v-if="recentRuns.length===0" class="rounded-2xl border border-dashed border-ink-200 px-3 py-6 text-center text-sm text-ink-400">{{ t('dashboard.noExpeditions') }}</li>
                    </ul>
                    <p class="mt-3 font-mono text-xs text-ink-300">{{ t('dashboard.autoSorting') }}</p>
                </div>

                <!-- Command Input — Typewriter -->
                <div class="relative overflow-hidden rounded-[2rem] border border-slate-200/60 bg-card p-6 shadow-[0_20px_40px_-15px_rgba(0,0,0,0.05)] md:col-span-4 flex flex-col" style="--index:1">
                    <p class="eyebrow">{{ t('dashboard.commandInput') }}</p>
                    <h3 class="mt-2 font-display text-lg font-semibold tracking-tight text-ink-950">{{ t('dashboard.askOctavia') }}</h3>
                    <div class="mt-4 flex-1 rounded-2xl border border-ink-100 bg-paper-50 p-4">
                        <div class="flex items-center gap-2 text-xs text-ink-400">
                            <span class="h-2 w-2 rounded-full bg-rose-400" /><span class="h-2 w-2 rounded-full bg-amber-400" /><span class="h-2 w-2 rounded-full bg-mint-400" />
                            <span class="ml-auto font-mono">octavia — zsh</span>
                        </div>
                        <form class="mt-3 font-mono text-sm leading-relaxed text-ink-900" @submit.prevent="goSearch">
                            <label class="flex items-center gap-2">
                                <span class="text-ink-300" aria-hidden="true">$</span>
                                <input
                                    v-model="commandQuery"
                                    type="search"
                                    :placeholder="typed"
                                    :aria-label="t('dashboard.askOctavia')"
                                    class="w-full bg-transparent font-mono text-sm text-ink-900 outline-none placeholder:text-ink-300"
                                />
                                <span class="ml-0.5 inline-block h-4 w-2 shrink-0 animate-pulse bg-ink-900" aria-hidden="true" />
                            </label>
                        </form>
                        <div class="mt-3 h-1 w-full overflow-hidden rounded-full bg-ink-100">
                            <div class="h-full w-2/3 animate-[shimmer_1.8s_ease-in-out_infinite] bg-gradient-to-r from-transparent via-accent-400 to-transparent" />
                        </div>
                    </div>
                    <Link href="/prompts/create" class="mt-4 inline-flex items-center gap-2 self-start rounded-full border border-ink-900 bg-ink-950 px-4 py-1.5 text-xs font-medium text-white hover:bg-ink-900">{{ t('nav.newPrompt') }} <span aria-hidden="true">↗</span></Link>
                </div>

                <!-- Live Status — breathing -->
                <div class="relative overflow-hidden rounded-[2rem] border border-slate-200/60 pinned-dark p-6 text-white shadow-[0_20px_40px_-15px_rgba(0,0,0,0.2)] md:col-span-4 flex flex-col" style="--index:2">
                    <div class="absolute inset-0 bg-gradient-to-br from-white/[0.06] to-transparent" aria-hidden="true" />
                    <p class="eyebrow !text-white/50">{{ t('dashboard.liveStatus') }}</p>
                    <h3 class="mt-2 font-display text-lg font-semibold tracking-tight">{{ t('dashboard.fieldActivity') }}</h3>
                    <div class="mt-4 flex-1 space-y-3">
                        <Link v-for="run in (liveRuns.length ? liveRuns : recentRuns.slice(0,2))" :key="run.id" :href="`/runs/${run.id}`" class="group flex items-center gap-3 rounded-2xl border border-white/10 bg-card/[0.06] px-3 py-2.5 backdrop-blur transition-colors hover:bg-white/[0.08]">
                            <span class="relative flex h-2.5 w-2.5">
                                <span class="absolute inline-flex h-full w-full animate-ping rounded-full bg-emerald-400 opacity-40" />
                                <span class="relative inline-flex h-2.5 w-2.5 rounded-full bg-emerald-400" />
                            </span>
                            <span class="min-w-0 flex-1 truncate text-sm font-medium group-hover:text-white/90">{{ run.name }}</span>
                            <span class="font-mono text-xs text-white/60">{{ run.score !== null ? Math.round(run.score*100)+'%' : '…' }}</span>
                        </Link>
                        <div v-if="recentRuns.length===0" class="rounded-2xl border border-white/10 px-3 py-6 text-center text-sm text-white/60">{{ t('dashboard.idle') }}</div>
                    </div>
                    <div class="mt-4 flex items-center gap-2 rounded-full bg-card/10 px-3 py-2 text-xs">
                        <span class="h-2 w-2 rounded-full bg-emerald-400" /> {{ animated.activeRuns }} {{ t('dashboard.climbing') }}
                        <span class="ml-auto font-mono text-white/60">{{ animated.prompts }} {{ t('dashboard.specimens') }}</span>
                    </div>
                </div>

                <!-- Wide Data Stream — marquee -->
                <div class="group relative overflow-hidden rounded-[2rem] border border-slate-200/60 bg-card p-0 shadow-[0_20px_40px_-15px_rgba(0,0,0,0.05)] md:col-span-8" style="--index:3">
                    <div class="flex items-center justify-between px-6 pt-6">
                        <div>
                            <p class="eyebrow">{{ t('dashboard.wideDataStream') }}</p>
                            <h3 class="mt-1 font-display text-lg font-semibold tracking-tight text-ink-950">{{ t('dashboard.promptSpecimens') }}</h3>
                        </div>
                        <Link href="/prompts" class="rounded-full border border-ink-100 bg-card px-3 py-1 text-xs font-medium text-ink-600 hover:bg-paper-50">{{ t('dashboard.viewAll') }} →</Link>
                    </div>
                    <div class="relative mt-4 overflow-hidden border-y border-ink-50 bg-paper-50/60 py-4">
                        <div class="flex w-max animate-[marquee_22s_linear_infinite] gap-3 pl-6 group-hover:[animation-play-state:paused]">
                            <div v-for="n in 8" :key="n" class="flex w-[220px] shrink-0 flex-col gap-2 rounded-2xl border border-white bg-card p-4 shadow-sm">
                                <div class="flex items-center gap-2">
                                    <span class="h-1.5 w-1.5 rotate-45 bg-accent-600" aria-hidden="true" />
                                    <span class="font-mono text-xs text-ink-400">#{{ String(n).padStart(3,'0') }}</span>
                                    <span class="ml-auto rounded-full bg-ink-950 px-1.5 py-0.5 font-mono text-[10px] text-white">v{{ (n%3)+1 }}</span>
                                </div>
                                <p class="line-clamp-2 text-sm font-medium leading-snug text-ink-900">Product tagline writer — variant {{n}}</p>
                                <div class="mt-1 h-1.5 overflow-hidden rounded-full bg-ink-100"><div class="h-full rounded-full bg-accent-500" :style="`width:${38 + n*7}%`" /></div>
                            </div>
                            <!-- duplicate for seamless loop -->
                            <div v-for="n in 8" :key="'d'+n" class="flex w-[220px] shrink-0 flex-col gap-2 rounded-2xl border border-white bg-card p-4 shadow-sm" aria-hidden="true">
                                <div class="flex items-center gap-2"><span class="h-1.5 w-1.5 rotate-45 bg-accent-600" /><span class="font-mono text-xs text-ink-400">#{{ String(n).padStart(3,'0') }}</span><span class="ml-auto rounded-full bg-ink-950 px-1.5 py-0.5 font-mono text-[10px] text-white">v{{ (n%3)+1 }}</span></div>
                                <p class="line-clamp-2 text-sm font-medium leading-snug text-ink-900">Product tagline writer — variant {{n}}</p>
                                <div class="mt-1 h-1.5 overflow-hidden rounded-full bg-ink-100"><div class="h-full rounded-full bg-accent-500" :style="`width:${38 + n*7}%`" /></div>
                            </div>
                        </div>
                    </div>
                    <p class="px-6 pb-4 pt-3 font-mono text-xs text-ink-300">infinite carousel · hover to pause</p>
                </div>

                <!-- Contextual UI — focus mode -->
                <div class="relative overflow-hidden rounded-[2rem] border border-slate-200/60 bg-card p-6 shadow-[0_20px_40px_-15px_rgba(0,0,0,0.05)] md:col-span-4 flex flex-col" style="--index:4">
                    <p class="eyebrow">{{ t('dashboard.focusMode') }}</p>
                    <h3 class="mt-2 font-display text-lg font-semibold tracking-tight text-ink-950">{{ t('dashboard.currentPrompt') }}</h3>
                    <div class="mt-4 rounded-2xl border border-ink-100 bg-paper-50 p-4 font-mono text-xs leading-relaxed text-ink-700">
                        <span>You are a marketing assistant.</span>
                        <mark class="rounded bg-accent-200 px-1 py-0.5 font-medium text-ink-900">Write a tagline for the product the user describes.</mark>
                        <span class="opacity-60"> Keep it under 8 words.</span>
                    </div>
                    <!-- floating toolbar -->
                    <div class="pointer-events-none mt-4 flex justify-center">
                        <div class="flex items-center gap-1 rounded-full border border-ink-100 bg-card p-1 shadow-[0_8px_20px_rgba(14,26,29,0.08)] animate-[float_3s_ease-in-out_infinite]">
                            <span class="flex h-7 w-7 items-center justify-center rounded-full bg-ink-950 text-white text-xs">B</span>
                            <span class="flex h-7 w-7 items-center justify-center rounded-full bg-paper-100 text-ink-600 text-xs">I</span>
                            <span class="h-4 w-px bg-ink-100" />
                            <span class="flex h-7 w-7 items-center justify-center rounded-full bg-accent-600 text-ink-950 text-xs">▶</span>
                        </div>
                    </div>
                    <p class="mt-3 text-center font-mono text-xs text-ink-300">staggered highlight · floating toolbar</p>
                </div>
            </section>

            <!-- Chart — Score Trend (fix for chartWidth 0 guard) -->
            <section class="mt-4 overflow-hidden rounded-[2rem] border border-slate-200 bg-card p-6 shadow-[0_20px_40px_-15px_rgba(0,0,0,0.05)]">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="eyebrow">{{ t('dashboard.performance') }}</p>
                        <h3 class="mt-1 font-display text-lg font-semibold tracking-tight text-ink-950">{{ t('dashboard.scoreEvolution') }}</h3>
                    </div>
                    <span class="rounded-full bg-ink-950 px-3 py-1 font-mono text-xs text-white">{{ t('dashboard.nRuns', { count: recentRuns.length }) }}</span>
                </div>
                <div class="mt-6 h-40 w-full">
                    <svg v-if="chartData.length > 1" :viewBox="`0 0 ${chartWidth} 100`" class="h-full w-full overflow-visible" preserveAspectRatio="none" aria-hidden="true">
                        <defs>
                            <linearGradient id="grad" x1="0" y1="0" x2="0" y2="1">
                                <stop offset="0%" stop-color="rgb(16 185 129)" stop-opacity="0.3" />
                                <stop offset="100%" stop-color="rgb(16 185 129)" stop-opacity="0" />
                            </linearGradient>
                        </defs>
                        <path :d="chartPath" fill="none" stroke="rgb(16 185 129)" stroke-width="2" stroke-linejoin="round" />
                        <path :d="chartAreaPath" fill="url(#grad)" stroke="none" />
                    </svg>
                    <div v-else class="flex h-full items-center justify-center rounded-xl bg-paper-50 text-sm text-ink-400">
                        Not enough data — run an evolution to see the trend
                    </div>
                </div>
                <p class="mt-2 font-mono text-xs text-ink-300">fallback width 600 when container 0 — no blank chart</p>
            </section>

            <!-- Leaderboard — top prompts -->
            <section v-if="topPrompts.length" class="mt-8 overflow-hidden rounded-[2rem] border border-slate-200 bg-card p-6 shadow-[0_20px_40px_-15px_rgba(0,0,0,0.05)]">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="eyebrow">{{ t('dashboard.leaderboard.title') }}</p>
                        <h3 class="mt-1 font-display text-lg font-semibold tracking-tight text-ink-950">{{ t('dashboard.leaderboard.heading') }}</h3>
                    </div>
                    <div class="flex items-center gap-2">
                        <a href="/export/leaderboard" class="rounded-full border border-ink-100 bg-card px-3 py-1 text-xs font-medium text-ink-600 hover:border-ink-200 hover:text-ink-900">{{ t('common.export') }}</a>
                        <span class="rounded-full bg-ink-950 px-3 py-1 font-mono text-xs text-white">{{ topPrompts.length }}</span>
                    </div>
                </div>
                <div class="mt-4 overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="border-b border-ink-100 text-left text-xs text-ink-400">
                                <th class="pb-2 font-medium">{{ t('common.name') }}</th>
                                <th class="pb-2 pr-4 text-right font-medium">{{ t('dashboard.leaderboard.best') }}</th>
                                <th class="pb-2 text-right font-medium">{{ t('dashboard.leaderboard.avg') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="prompt in topPrompts" :key="prompt.id" class="border-b border-ink-50 last:border-0">
                                <td class="py-3">
                                    <Link :href="`/prompts/${prompt.id}`" class="font-medium text-ink-900 hover:text-accent-700">{{ prompt.name }}</Link>
                                </td>
                                <td class="py-3 pr-4 text-right font-mono text-ink-950">{{ prompt.best_score ?? '-' }}</td>
                                <td class="py-3 text-right font-mono text-ink-950">{{ prompt.avg_score ?? '-' }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </section>

            <!-- Field Actions — keep but more compact, after bento -->
            <section class="mt-8">
                <div class="flex items-center justify-between">
                    <h2 class="eyebrow">{{ t('dashboard.fieldActions.title') }}</h2>
                    <span class="hidden sm:inline font-mono text-xs text-ink-300">01 — 04</span>
                </div>
                <div class="mt-3 grid gap-3 sm:grid-cols-2 lg:grid-cols-4">
                    <Link href="/runs/create" class="group relative overflow-hidden rounded-2xl border border-ink-950 bg-ink-950 p-5 text-white transition-all hover:shadow-[0_16px_30px_rgba(14,26,29,0.18)] active:scale-[0.98]">
                        <div class="absolute right-3 top-3 font-mono text-xs text-white/40">01</div>
                        <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-accent-600 text-ink-950">▶</div>
                        <p class="mt-4 font-display text-sm font-semibold">{{ t('dashboard.startRun') }}</p>
                        <p class="mt-1 text-xs leading-relaxed text-white/60">Evaluate or evolve</p>
                    </Link>
                    <Link v-for="act in [{n:'02', title: t('dashboard.createPrompt'), desc:t('dashboard.fieldActions.newSpecimen'), href:'/prompts/create'}, {n:'03', title: t('dashboard.createBenchmark'), desc:t('dashboard.fieldActions.buildSuite'), href:'/benchmarks/wizard'}, {n:'04', title: t('dashboard.browseMarketplace'), desc:t('dashboard.fieldActions.community'), href:'/marketplace'}]" :key="act.n" :href="act.href" class="group rounded-2xl border border-ink-100 bg-card p-5 transition-all hover:border-ink-200 hover:shadow-[0_16px_30px_rgba(14,26,29,0.06)] active:scale-[0.98]">
                        <div class="flex items-center justify-between"><span class="font-mono text-xs text-ink-300">{{ act.n }}</span><span class="h-2 w-2 rotate-45 bg-ink-200 group-hover:bg-accent-500 transition-colors" /></div>
                        <p class="mt-3 font-display text-sm font-semibold text-ink-950">{{ act.title }}</p>
                        <p class="mt-1 text-xs text-ink-500">{{ act.desc }}</p>
                    </Link>
                </div>
            </section>


            <!-- Leaderboard — top candidates -->
            <section v-if="leaderboard.length" class="mt-4 overflow-hidden rounded-[2rem] border border-slate-200 bg-card p-6 shadow-[0_20px_40px_-15px_rgba(0,0,0,0.05)]">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="eyebrow">{{ t('dashboard.candidates.title') }}</p>
                        <h3 class="mt-1 font-display text-lg font-semibold tracking-tight text-ink-950">{{ t('dashboard.candidates.heading') }}</h3>
                    </div>
                    <span class="rounded-full bg-ink-950 px-3 py-1 font-mono text-xs text-white">{{ leaderboard.length }}</span>
                </div>
                <div class="mt-4 overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="border-b border-ink-100 text-left text-xs text-ink-400">
                                <th class="pb-2 font-medium">#</th>
                                <th class="pb-2 font-medium">{{ t('dashboard.candidates.candidate') }}</th>
                                <th class="pb-2 font-medium">{{ t('dashboard.candidates.run') }}</th>
                                <th class="pb-2 pr-4 text-right font-medium">{{ t('dashboard.candidates.score') }}</th>
                                <th class="pb-2 text-right font-medium">{{ t('dashboard.candidates.strategy') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="step in leaderboard" :key="step.id" class="border-b border-ink-50 last:border-0">
                                <td class="py-3 font-mono text-ink-400">{{ step.rank }}</td>
                                <td class="py-3">
                                    <span class="font-medium text-ink-900">{{ step.prompt_content }}</span>
                                </td>
                                <td class="py-3">
                                    <Link :href="`/runs/${step.run_id}`" class="text-ink-600 hover:text-accent-700">{{ step.run_name }}</Link>
                                </td>
                                <td class="py-3 pr-4 text-right font-mono text-ink-950">{{ step.score }}%</td>
                                <td class="py-3 text-right">
                                    <span class="rounded-full bg-paper-100 px-2 py-0.5 text-xs text-ink-600">{{ step.strategy }}</span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </section>

            <!-- Category breakdown + score distribution bento -->
            <section v-if="promptCategories.length || benchmarkCategories.length || scoreDistribution.length" class="mt-4 grid grid-cols-1 gap-4 md:grid-cols-3">
                <div v-if="promptCategories.length" class="overflow-hidden rounded-[2rem] border border-slate-200/60 bg-card p-6 shadow-[0_20px_40px_-15px_rgba(0,0,0,0.05)]">
                    <p class="eyebrow">{{ t('dashboard.categories.prompts') }}</p>
                    <h3 class="mt-2 font-display text-lg font-semibold tracking-tight text-ink-950">{{ t('dashboard.specimensByField') }}</h3>
                    <div class="mt-4 space-y-3">
                        <div v-for="cat in promptCategories" :key="cat.label" class="space-y-1">
                            <div class="flex items-center justify-between text-sm">
                                <span class="font-medium text-ink-900">{{ cat.label }}</span>
                                <span class="font-mono text-xs text-ink-500">{{ cat.count }}</span>
                            </div>
                            <div class="h-2 overflow-hidden rounded-full bg-ink-100">
                                <div class="h-full rounded-full bg-accent-500" :style="`width:${cat.fill}%`" />
                            </div>
                        </div>
                    </div>
                </div>

                <div v-if="benchmarkCategories.length" class="overflow-hidden rounded-[2rem] border border-slate-200/60 bg-card p-6 shadow-[0_20px_40px_-15px_rgba(0,0,0,0.05)]">
                    <p class="eyebrow">{{ t('dashboard.categories.benchmarks') }}</p>
                    <h3 class="mt-2 font-display text-lg font-semibold tracking-tight text-ink-950">{{ t('dashboard.suitesByField') }}</h3>
                    <div class="mt-4 space-y-3">
                        <div v-for="cat in benchmarkCategories" :key="cat.label" class="space-y-1">
                            <div class="flex items-center justify-between text-sm">
                                <span class="font-medium text-ink-900">{{ cat.label }}</span>
                                <span class="font-mono text-xs text-ink-500">{{ cat.count }}</span>
                            </div>
                            <div class="h-2 overflow-hidden rounded-full bg-ink-100">
                                <div class="h-full rounded-full bg-emerald-500" :style="`width:${cat.fill}%`" />
                            </div>
                        </div>
                    </div>
                </div>

                <div v-if="scoreDistribution.length" class="overflow-hidden rounded-[2rem] border border-slate-200/60 bg-card p-6 shadow-[0_20px_40px_-15px_rgba(0,0,0,0.05)]">
                    <p class="eyebrow">{{ t('dashboard.distribution.title') }}</p>
                    <h3 class="mt-2 font-display text-lg font-semibold tracking-tight text-ink-950">{{ t('dashboard.scoreRanges') }}</h3>
                    <div class="mt-4 space-y-3">
                        <div v-for="bucket in scoreDistribution" :key="bucket.range" class="space-y-1">
                            <div class="flex items-center justify-between text-sm">
                                <span class="font-medium text-ink-900">{{ bucket.range }}</span>
                                <span class="font-mono text-xs text-ink-500">{{ bucket.count }}</span>
                            </div>
                            <div class="h-2 overflow-hidden rounded-full bg-ink-100">
                                <div class="h-full rounded-full bg-ink-900" :style="`width:${Math.min(100, bucket.count * 100 / maxScoreDistribution)}%`" />
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Recent runs — keep timeline but denser -->
            <section class="mt-8">
                <div class="flex items-center justify-between">
                    <h2 class="eyebrow">{{ t('dashboard.recentRuns') }}</h2>
                    <Link href="/runs" class="hidden text-xs font-medium text-ink-500 hover:text-ink-900 sm:inline">{{ t('nav.runs') }} →</Link>
                </div>
                <OPanel v-if="recentRuns.length > 0" class="!p-0 mt-3 overflow-hidden">
                    <ul class="divide-y divide-ink-100">
                        <li v-for="run in recentRuns" :key="run.id" class="group">
                            <Link :href="`/runs/${run.id}`" class="flex items-center gap-4 px-4 py-3 transition-colors hover:bg-paper-50">
                                <span class="flex h-7 w-7 shrink-0 items-center justify-center rounded-full bg-card border border-ink-100"><span class="h-2 w-2 rotate-45" :class="{ 'bg-mint-500': run.status==='completed', 'bg-accent-500': run.status==='running', 'bg-ink-200': run.status!=='completed'&&run.status!=='running' }" /></span>
                                <OBadge :tone="statusTone[run.status] ?? 'neutral'">{{ t(`runs.status.${run.status}`) }}</OBadge>
                                <span class="min-w-0 flex-1 truncate text-sm font-medium text-ink-900">{{ run.name }}</span>
                                <OScoreBar v-if="run.score!==null" :score="run.score" :show-value="true" class="hidden w-40 sm:flex" />
                                <span class="text-ink-300 group-hover:text-ink-700">›</span>
                            </Link>
                        </li>
                    </ul>
                </OPanel>
                <OEmptyState v-else :title="t('runs.empty')" class="mt-3"><template #action><Link href="/runs/create" class="rounded-full bg-ink-950 px-4 py-2 text-sm font-medium text-white hover:bg-ink-900">{{ t('runs.new') }}</Link></template></OEmptyState>
            </section>
        </div>

        <style>
        @keyframes marquee { from { transform: translateX(0) } to { transform: translateX(-50%) } }
        @keyframes shimmer { 0% { transform: translateX(-100%) } 100% { transform: translateX(200%) } }
        @keyframes float { 0%,100% { transform: translateY(0) } 50% { transform: translateY(-6px) } }
        </style>
    </AppLayout>
</template>
