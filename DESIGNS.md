# DESIGNS.md — Octavia Design System

This is the index and rationale overview. Detailed specs live in `designs/`.

## The idea in one sentence
Octavia looks like a **survey instrument for the fitness landscape of prompts**: cold fog surfaces, spruce ink, one international-orange signal accent, contour lines and waypoint markers — scientific, confident, never loud.

## Documents
| File | Scope |
|------|-------|
| [designs/brand.md](designs/brand.md) | Name, positioning, voice, logo rules |
| [designs/colors.md](designs/colors.md) | Ink/Fog/Signal-orange token system |
| [designs/typography.md](designs/typography.md) | Archivo + IBM Plex Sans + JetBrains Mono scale |
| [designs/layout.md](designs/layout.md) | App shell, density, key screens, responsive rules |
| [designs/components.md](designs/components.md) | The `O*` component library contract |
| [designs/interactions.md](designs/interactions.md) | Motion, feedback, keyboard, loading |
| [designs/landing-page.md](designs/landing-page.md) | Marketing site structure & SEO |
| [designs/dashboard.md](designs/dashboard.md) | Post-login home decisions |

## Why these decisions

1. **Cold fog, not warm cream.** The survey-paper ground (`paper-50`) keeps long reading sessions (prompt texts!) easy while the spruce ink and signal orange give an identity that survives without the logo. Warm-cream-plus-serif is what every generated template looks like; this is the opposite bet.

2. **Exactly one accent.** International orange is reserved for identity, waypoints, and the single primary CTA per view — never paired with white text. All meaning is carried by three signal colors with fixed semantics. A user can read any screen's health at a glance without reading a word.

3. **Monospace for prompts is non-negotiable.** Prompts are the product's raw material; they must always look like artifacts being worked on, never like chat bubbles. This single rule separates Octavia from chat wrappers.

4. **Scores are the hero.** The score bar (with target comparison) and the step timeline are the two elements users see most. They get the only meaningful animation in the product.

5. **No modals, no toasts (yet).** Fewer primitives = fewer inconsistencies. Flashes are inline, confirms are native. Revisit at real scale.

6. **German-aware layouts.** DE copy is longer; every layout uses flexible wrapping and no fixed-width text containers. Both locales are first-class, not a translation layer.

## Implementation
Tokens: `resources/css/app.css` (`@theme` block, Tailwind v4). Components: `resources/js/components/ui/`. Any new UI must reuse these tokens — raw hex values in components are a bug.
