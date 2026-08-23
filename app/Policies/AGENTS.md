# AGENTS.md — app/Policies

## What lives here
Authorization policies: BenchmarkCollectionPolicy.php, BenchmarkPolicy.php, PromptPolicy.php, RunPolicy.php.

## Rules
- One policy per model; methods mirror controller actions.
- Return strict booleans; avoid message leaks.
- Controllers call `authorize()` or `$this->authorize()`.
