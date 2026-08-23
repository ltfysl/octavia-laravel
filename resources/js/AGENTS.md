# AGENTS.md — resources/js

## What lives here
Frontend entry points and shared modules: app.js, app.ts, echo.ts.

## Rules
- `app.ts` boots Inertia; `echo.ts` configures Reverb.
- Prefer TypeScript; avoid `any`.
- Use i18n, theme tokens and the design system everywhere.
