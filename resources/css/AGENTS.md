# AGENTS.md — resources/css

## What lives here
Global CSS and Tailwind v4 theme tokens: app.css.

## Rules
- Define all theme tokens in `app.css` (`@theme`); no ad-hoc hex colors elsewhere.
- Keep custom CSS minimal; prefer Tailwind utilities.
- Respect `prefers-reduced-motion`; never hide content behind animations.
