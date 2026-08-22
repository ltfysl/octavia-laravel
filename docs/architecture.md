# Architecture

## Overview
Octavia is a Laravel 13 + Inertia v2 (Vue 3) monolith with a queue-backed prompt-evolution engine.

```
Browser ── Inertia (Vue 3 SPA feel) ── Controllers ── Services ── Eloquent ── MySQL
                                            │
                                            └── Jobs (queue: database/sync) ── LLM Provider ── OpenAI-compatible API / Mock
```

## Backend layers

### Services (`app/Services/`)
- `Evaluation\Llm\*` — see below.
- `EvaluationService` — the scoring core. Takes a provider, prompt content and benchmarks; returns an `EvaluationSummary` DTO. Pure function of inputs; no DB writes.
- `EvolutionService` — hill-climbing optimizer. Persists every evaluation as `RunStep` with per-case/per-criterion detail. Mutates only the incumbent best prompt. Stops on: target reached, stale counter (3 non-improving steps), or max steps.

### LLM abstraction (`app/Services/Llm/`)
- Contract `LlmProvider::complete(messages, options): LlmResponse`.
- `MockLlmProvider` — deterministic, credential-free. Three modes keyed by system-prompt markers:
  - `[OCTAVIA-JUDGE]` → keyword-overlap judge score.
  - `[OCTAVIA-OPTIMIZER]` → appends unmet requirements as bullets to the prompt.
  - default task mode → echoes input + explicit bullet requirements.
  This makes the full evolution loop demonstrable in dev/CI without an API key — and its behavior is unit-testable.
- `OpenAiCompatibleProvider` — any chat-completions endpoint (OpenAI/Azure/OpenRouter/Ollama).
- `LlmManager` resolves providers from `config/llm.php`; unknown/missing config throws before any job runs.

### Jobs
`ProcessRunJob` — single queued entry point for both run modes (evaluate once / optimize). Failures land on the run record (`status=failed`, `error`), never silently.

## Domain model

| Entity | Purpose |
|--------|---------|
| `Prompt`, `PromptVersion` | Versioned prompts; `current_version_id` points at the live version |
| `Benchmark`, `BenchmarkCase`, `BenchmarkCriterion` | Test suites. Criteria are typed rows (contains/not_contains/regex/llm_judge), not JSON blobs |
| `Run`, `RunStep`, `CaseResult`, `CriterionResult` | Immutable execution history; steps are numbered per run and replayable |
| `MarketplaceItem` | Polymorphic listing (prompt/benchmark); install deep-copies into the installing user's library |
| `BenchmarkCollection` | Groups benchmarks to run against one prompt |

## Frontend structure
- `resources/js/pages/**` — Inertia pages matched to controller render calls.
- `resources/js/components/ui/O*` — design-system components (see designs/components.md).
- `resources/js/layouts/` — AppLayout (authenticated), AuthLayout, PublicLayout.
- `resources/js/locales/{en,de}.json` — vue-i18n messages; backend strings use Laravel lang files. Locale resolution: user preference → `octavia_locale` cookie → app locale.

## Key decisions (ADRs)
See `docs/decisions/`.
