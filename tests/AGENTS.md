# AGENTS.md — tests

## What lives here
Pest test suite root and shared helpers (`Pest.php`, `TestCase.php`).

## Rules
- `RefreshDatabase` is bound globally in `Pest.php`.
- Use factories and `Http::fake()`; no real external HTTP.
- Tests are deterministic, isolated and full-suite-safe.
