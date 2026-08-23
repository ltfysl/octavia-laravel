# AGENTS.md — app/Models

## What lives here
Eloquent models: AuditLog.php, Benchmark.php, BenchmarkCase.php, BenchmarkCollection.php, BenchmarkCriterion.php, CaseResult.php, ….

## Rules
- Relations, scopes, casts and accessors only; no business logic.
- Use eager loading; never query inside loops.
- Keep fillable explicit where the project uses it.
