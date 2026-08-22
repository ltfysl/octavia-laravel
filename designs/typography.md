# Typography

Loaded via Bunny Fonts (GDPR-friendly): **Archivo** 500–700, **IBM Plex Sans** 400–600, **JetBrains Mono**.

## Families
| Family | Token | Use |
|--------|-------|-----|
| Archivo | `font-display` | Headings, logo, stat numbers, panel titles, `.eyebrow` map labels. The instrument-panel voice: engineered, slightly condensed caps when letterspaced. |
| IBM Plex Sans | `font-sans` | Everything else: body, forms, tables, navigation. Engineering credibility without coldness. |
| JetBrains Mono | `font-mono` | Prompt content (the product's core object), scores, versions, regex patterns. Prompts must always read as code-like artifacts. |

## Scale
| Element | Class |
|---------|-------|
| Page h1 | `font-display text-2xl font-bold tracking-tight` |
| Hero readout | `font-display text-5xl font-bold tabular-nums tracking-tight` |
| Section h2 (marketing) | `font-display text-3xl font-bold` |
| Card title / h3 | `font-display text-sm/base font-semibold` |
| Eyebrow / map label | `.eyebrow` (`font-display text-[11px] font-semibold uppercase tracking-[0.14em] text-ink-300`) — section markers and stat labels only |
| Body | `text-sm` app, `text-base/lg` marketing |
| Scores & versions | `font-mono text-xs tabular-nums` |

## Rules
- Tracking-tight on all display headings; Plex Sans body stays default.
- Numbers that compare (scores, steps) are always `tabular-nums`.
- German copy runs ~15% longer than English; layouts must tolerate it (flex wraps, no fixed-width labels).
