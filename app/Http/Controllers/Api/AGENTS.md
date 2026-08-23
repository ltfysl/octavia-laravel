# AGENTS.md — app/Http/Controllers/Api

## What lives here
Sanctum-protected API v1 controllers: AuthTokenController.php, PromptController.php, RunController.php.

## Rules
- Enforce token abilities (`read`/`write`) with middleware or inline checks.
- Use `app/Http/Resources` for all JSON responses.
- Ownership check via policy or `abort_unless($x->user_id === auth id, 404)`.
- Never expose internal exception details in API responses.
