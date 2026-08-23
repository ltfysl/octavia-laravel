# AGENTS.md — app/Events

## What lives here
Domain events: RunProgress.php.

## Rules
- Events are lightweight signals; no heavy logic.
- Listeners handle side effects.
- Keep event payloads minimal and typed.
