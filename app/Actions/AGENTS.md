# AGENTS.md — app/Actions

## What lives here
High-level use-case actions: CreateStarterContent.php, ImportBenchmark.php, RunPlayground.php.

## Rules
- One responsibility per action; name with a verb.
- Do not bypass policies or validation here.
- Keep persistence through models, not raw DB calls.
