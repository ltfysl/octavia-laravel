# Interactions

## Motion
- Transitions: colors 150ms ease. Layout animates only via the score bar's 500ms width transition — the one place motion carries meaning (the score climbing is the product).
- Marketing pages only: `v-reveal` scroll-settle (opacity 0→1, 1rem rise, 600ms ease-out, IntersectionObserver, optional stagger). Never applied inside the app; never hides content under `prefers-reduced-motion: reduce`.
- Card hover physics (marketing + feature cards): lift `-translate-y-1`, shadow deepen, icon color swap over 500–700ms ease-out. No scale jumps, no bounce.
- No page-enter animations inside the app; perceived speed beats flourish.

## Feedback
- Saving states replace button labels ("Saving…", disabled) — never toasts for routine saves; success shows as inline flash banner via Inertia shared props.
- Destructive actions always confirm first (native confirm until volume justifies a dialog).
- Long-running runs: status badge + polling endpoint (`GET /runs/{id}/status`) ready for live updates; current sync queue completes instantly in dev.

## Micro-interactions
- Nav hover: background fill only, no color jumps.
- Landing FAQ uses `<details>` with a rotating plus glyph — zero JS.
- Score bars animate on mount; sparkline bars scale from bottom.

## Keyboard
- Full tab order follows DOM order; wizard steppers are buttons (Enter works).
- Forms submit on Enter; no focus traps exist because no modals exist.

## Loading
- Inertia progress bar (violet) on navigation.
- Run page while pending: panel with loading text; empty-state only after terminal status.
