# DESIGNS.md — Octavia Design System

This is the index and rationale overview. Detailed specs live in `designs/`.

## The idea in one sentence
Octavia looks like a **precision instrument on a warm desk**: calm paper surfaces, one electric violet accent, monospace prompt artifacts — scientific, confident, never loud.

## Documents
| File | Scope |
|------|-------|
| [designs/brand.md](designs/brand.md) | Name, positioning, voice, logo rules |
| [designs/colors.md](designs/colors.md) | Ink/Paper/Violet/Signal token system |
| [designs/typography.md](designs/typography.md) | Space Grotesk + Inter + JetBrains Mono scale |
| [designs/layout.md](designs/layout.md) | App shell, density, key screens, responsive rules |
| [designs/components.md](designs/components.md) | The `O*` component library contract |
| [designs/interactions.md](designs/interactions.md) | Motion, feedback, keyboard, loading |
| [designs/landing-page.md](designs/landing-page.md) | Marketing site structure & SEO |
| [designs/dashboard.md](designs/dashboard.md) | Post-login home decisions |

## Why these decisions

1. **Warm paper instead of white/gray.** Every competitor dashboard is cold gray. The warm off-white (`paper-50`) makes long reading sessions (prompt texts!) easier and gives the product a distinct identity that survives even without the logo.

2. **Exactly one accent.** Violet-600 is reserved for identity + primary action. All meaning is carried by three signal colors with fixed semantics. A user can read any screen's health at a glance without reading a word.

3. **Monospace for prompts is non-negotiable.** Prompts are the product's raw material; they must always look like artifacts being worked on, never like chat bubbles. This single rule separates Octavia from chat wrappers.

4. **Scores are the hero.** The score bar (with target comparison) and the step timeline are the two elements users see most. They get the only meaningful animation in the product.

5. **No modals, no toasts (yet).** Fewer primitives = fewer inconsistencies. Flashes are inline, confirms are native. Revisit at real scale.

6. **German-aware layouts.** DE copy is longer; every layout uses flexible wrapping and no fixed-width text containers. Both locales are first-class, not a translation layer.

## Implementation
Tokens: `resources/css/app.css` (`@theme` block, Tailwind v4). Components: `resources/js/components/ui/`. Any new UI must reuse these tokens — raw hex values in components are a bug.
