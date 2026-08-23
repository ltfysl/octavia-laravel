# AGENTS.md — app/Http

## What lives here
The HTTP layer: controllers, middleware, form requests and API resources.

## Rules
- All HTTP classes are entry points only; no domain logic.
- Prefer dependency injection and named route helpers.
- One concern per middleware; keep middleware order intentional in `bootstrap/app.php`.
- Validate every write path; authorize before delegating.
