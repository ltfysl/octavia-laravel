# Layout

## App shell (authenticated)
Two-column grid on ≥lg: `16rem sidebar + fluid main`. Below lg the sidebar collapses into a hamburger sheet; nothing is desktop-only.

- Sidebar: white, 1px right border. Logo top, nav middle, user/settings bottom. Active item = violet-50 fill + violet-700 text + small dot indicator.
- Main: max-w-6xl, px-10/py-8 desktop, px-4/py-6 mobile.
- Page header pattern: h1 + optional subtitle left, actions right, always the first block of a page.

## Density
Octavia shows structured evidence (steps, criteria, outputs). Panels are compact: 20px horizontal padding, 16px vertical. Lists inside panels use dividers (`divide-ink-100`) instead of card-in-card nesting.

## Key screens
- **Run detail** is the flagship layout: summary stats row → score bar → step timeline (left rail) → step detail (right). The left rail lists every step with its phase badge and score so the evolution history is scannable at a glance.
- **Benchmark wizard**: numbered stepper (Basics → Cases → Review), one concept per screen, review step renders exactly what will be stored.
- **Prompt editor**: two-pane — content textarea (mono, dominant) + run panel (benchmark picker + optimize/evaluate buttons).

## Responsive behavior
- Stat grids: 2-col mobile → 4-col desktop.
- Tables (runs index): score column hidden below md.
- Wizard case editor: single column mobile, two-column desktop.
- Touch targets ≥ 40px on mobile controls.

## Empty states
Every list screen ships an `OEmptyState`: dashed-border surface, spark glyph, one sentence of guidance, primary action. Never a blank area.
