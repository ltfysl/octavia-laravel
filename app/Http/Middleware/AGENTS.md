# AGENTS.md — app/Http/Middleware

## What lives here
HTTP middleware: EnsureTokenHasScope.php, EnsureUserIsAdmin.php, HandleInertiaRequests.php, SecurityHeaders.php, SetLocale.php.

## Rules
- One cross-cutting concern per middleware.
- No business logic; only request/response transformation.
- Order is defined intentionally in `bootstrap/app.php`.
