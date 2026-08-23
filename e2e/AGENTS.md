# AGENTS.md — e2e

## What lives here
Playwright E2E tests and helpers: auth.setup.ts, global-setup.ts, journey.spec.ts, server-router.php.

## Rules
- Tests always run against the `octavia_e2e` database via `e2e/server-router.php`.
- `auth.setup.ts` saves auth state; specs stay isolated and deterministic.
- Never point E2E at the dev database; wipe per run.
