# Components

All shared UI lives in `resources/js/components/ui/` prefixed `O`. Rule: a pattern appears twice → it becomes a component; three times → it must be one.

## OButton
Variants: primary (violet fill), secondary (white + border), ghost (text only), danger (rose). Sizes sm/md/lg. Disabled state = 50% opacity, pointer-events none.

## OPanel
The fundamental surface: white, `rounded-card` (10px), 1px ink-100 border, `shadow-panel`. Optional title/subtitle header with right-side `actions` slot. Panels never nest.

## OInput
Text input with error text slot below. Errors come from Laravel via Inertia form errors — same shape everywhere.

## OField
Label + hint + required/optional marker wrapper. The only place labels are defined (a11y: label always rendered).

## OBadge
Tone-based pill: neutral, violet, mint, amber, rose. Used for run status, visibility, categories — never for decoration.

## OScoreBar
The product's signature data element. 0–1 score → percentage bar. Color logic: mint (≥ target or ≥ 80%), amber (50–79%), rose (< 50%). Optional target comparison. Always `role="meter"`.

## OEmptyState
Dashed border, spark icon, title, optional description + action slot. Required on every list screen.

## Deliberately absent (for now)
No modal component (confirm() suffices), no toast system (flash banners), no dropdown menu (mobile nav uses `<details>`). Each will be added when a real need appears, matching this visual language.
