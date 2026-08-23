# AGENTS.md — app/Observers

## What lives here
Eloquent model observers: RunObserver.php, RunProgressObserver.php, RunStepProgressObserver.php.

## Rules
- Keep observers light; no heavy DB writes.
- Prefer events for cross-model side effects.
- Avoid business logic.
