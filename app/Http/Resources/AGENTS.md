# AGENTS.md — app/Http/Resources

## What lives here
API response transformers: BenchmarkResource.php, PromptResource.php, RunResource.php.

## Rules
- One resource per model/API surface.
- Do not expose sensitive internal state.
- Use `when`/`whenLoaded` for conditional data.
