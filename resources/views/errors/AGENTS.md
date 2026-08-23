# AGENTS.md — resources/views/errors

## What lives here
Blade error page templates: 403.blade.php, 404.blade.php, 419.blade.php, 429.blade.php, 500.blade.php, 503.blade.php, ….

## Rules
- Keep error pages lightweight and user-friendly.
- Use i18n for static copy; no exception details.
- Extend `base.blade.php` consistently.
