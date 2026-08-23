# Colors

All tokens are defined once in `resources/css/app.css` under Tailwind v4 `@theme`.
Direction: **topographic survey**. Octavia maps and climbs the fitness landscape
of prompts, so the ground is cold fog, structure is deep spruce ink, and exactly
one international-orange signal accent marks identity, waypoints, and primary
actions.

## Ink — structure & text (spruce-slate, never pure black)
| Token | Value | Use |
|-------|-------|-----|
| ink-950 | #0e1a1d | Headings, strongest text, primary button fill |
| ink-900 | #142529 | Body text |
| ink-700 | #2f4449 | Secondary text, labels |
| ink-500 | #5c7076 | Muted text |
| ink-300 | #9fb2b6 | Placeholders, timestamps, eyebrows |
| ink-200 | #c9d5d7 | Borders |
| ink-100 | #e3ebec | Dividers, meter tracks |

## Paper — surfaces (cold survey fog)
| Token | Value | Use |
|-------|-------|-----|
| paper-50 | #fafbfb | App background |
| paper-100 | #f1f4f4 | Sidebar legend surface, panels, code blocks |
| paper-200 | #e7ecec | Hover fills |
| paper-300 | #dbe2e3 | Strong hover borders |

## Accent — the single signal color (international orange)
| Token | Value | Use |
|-------|-------|-----|
| accent-600 | #ea580c | Logo tile, waypoint markers, hero % sign, run CTA chip, active tab underline |
| accent-500 | #fb6d1e | Focus rings, CTA hover |
| accent-700 | #c2410c | Hover text on links |
| accent-100/200 | #ffe6d3/#ffc7a3 | Badges, selection background |

Orange is never paired with white text. Solid fills that carry a label use
`bg-ink-950 text-white`; orange fills take `text-ink-950` (≥ 4.5:1).

## Signal colors — meaning, never decoration
| Token | Use |
|-------|-----|
| mint-500 #1fa97a | Score ≥ target / ≥ 80%, passed criteria |
| amber-450 #e8a13c | Mid-range scores (50–79%), mutation steps |
| rose-450 #e0526b | Failing scores (< 50%), destructive actions, errors |

## Rules
- Never introduce new hues per feature. New categories reuse accent or neutral tones.
- Orange marks the route (identity, waypoints, the one primary CTA per view); it never floods large surfaces.
- Dark surfaces (`ink-950`) are used for primary buttons/key fills and the prompt-content block in run steps (code-instrument feel).
- Contrast: all text/background pairs meet WCAG AA; verified pairs: white on ink-950 ≈ 15:1, ink-950 on accent-600 ≈ 4.7:1, rose outline danger uses ink-900 label on tinted ground.

## Dark mode
Class-based (`html.dark`), toggled in the shell header, persisted in
`localStorage('octavia-theme')`, initialized pre-paint in `app.blade.php`
(falls back to `prefers-color-scheme`). Inversion is token-level:
`paper-*` become deep spruce surfaces, `ink-*` flip to fog-light text,
`--color-card` (#ffffff / #131b1e) replaces raw `bg-white`. Cinematic
hero surfaces and prompt terminals are pinned dark in both modes via
`.pinned-dark`; `bg-ink-950` labels auto-invert through
`.dark .bg-ink-950:not(.pinned-dark)`. Accent orange stays in both modes.
