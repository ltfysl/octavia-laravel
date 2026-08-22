/**
 * Scroll-triggered reveal via IntersectionObserver. Elements start
 * slightly translated + transparent and settle into place when they
 * enter the viewport. Supports an optional stagger delay via the
 * binding value (v-reveal="{ delay: 120 }"). Respects
 * prefers-reduced-motion by never hiding content.
 */

const observer = new IntersectionObserver(
    (entries) => {
        for (const entry of entries) {
            if (!entry.isIntersecting) continue;
            const el = entry.target as HTMLElement;
            const delay = Number(el.dataset.revealDelay ?? 0);

            window.setTimeout(() => el.classList.add('reveal-visible'), delay);
            observer.unobserve(el);
        }
    },
    { threshold: 0.15 },
);

const reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

export const vReveal = {
    mounted(el: HTMLElement, binding: { value?: { delay?: number } }): void {
        if (reducedMotion) return;

        if (binding.value?.delay) {
            el.dataset.revealDelay = String(binding.value.delay);
        }

        el.classList.add('reveal-init');
        observer.observe(el);
    },
    unmounted(el: HTMLElement): void {
        observer.unobserve(el);
    },
};
