# Typography

Loaded via Bunny Fonts (GDPR-friendly): **Inter** 400–700, **Space Grotesk** 500–700, **JetBrains Mono**.

## Families
| Family | Token | Use |
|--------|-------|-----|
| Space Grotesk | `font-display` | Headings, logo, stat numbers, panel titles. The technical, slightly engineered voice of the product. |
| Inter | `font-sans` | Everything else: body, forms, tables, navigation. |
| JetBrains Mono | `font-mono` | Prompt content (the product's core object), scores, versions, regex patterns. Prompts must always read as code-like artifacts. |

## Scale
| Element | Class |
|---------|-------|
| Page h1 | `font-display text-2xl font-bold tracking-tight` |
| Section h2 (marketing) | `font-display text-3xl font-bold` |
| Hero | `font-display text-4xl sm:text-6xl font-bold leading-tight tracking-tight` |
| Card title / h3 | `font-display text-sm/base font-semibold` |
| Body | `text-sm` app, `text-base/lg` marketing |
| Meta/labels | `text-xs uppercase tracking-wide text-ink-300` for stat labels only — never for interactive labels |
| Scores & versions | `font-mono text-xs tabular-nums` |

## Rules
- Tracking-tight on all display headings; Inter body stays default.
- Numbers that compare (scores, steps) are always `tabular-nums`.
- German copy runs ~15% longer than English; layouts must tolerate it (flex wraps, no fixed-width labels).
