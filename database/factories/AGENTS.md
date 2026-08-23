# AGENTS.md — database/factories

## What lives here
Eloquent model factories: BenchmarkFactory.php, PromptFactory.php, UserFactory.php.

## Rules
- Factories define sensible defaults; use states for variants.
- No real external side effects (email, HTTP, etc.) in factories.
- Keep deterministic so tests are reproducible.
