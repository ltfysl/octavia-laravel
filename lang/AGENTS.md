# AGENTS.md — lang

## What lives here
Backend localization directories (`de/`, `en/`).

## Rules
- Mirror keys across `lang/en/` and `lang/de/`.
- Use `__()` with named keys; no inline strings in controllers or mail.
- Organize files by domain (`auth`, `notifications`, `billing`, etc.).
