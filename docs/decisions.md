# ADR-001: Mock-first LLM provider

**Status:** accepted (2026-08-21)

## Context
The evolution engine needs an LLM. Requiring an API key would block local development, CI, demos and the entire test suite.

## Decision
The default provider is a deterministic mock (`MockLlmProvider`) with a stable, documented behavior contract:
- task mode echoes user input plus explicit bullet/numbered requirements found in the prompt;
- judge mode scores keyword overlap between a criterion and the output;
- optimizer mode appends unmet requirements as bullets to the current prompt.

Together these make hill-climbing demonstrably work end to end without external calls.

## Consequences
- Tests assert real engine behavior, not HTTP mocks.
- Demos run offline.
- Switching to production is configuration only (`OCTAVIA_LLM_PROVIDER=openai` + key); the `OpenAiCompatibleProvider` speaks any chat-completions API.

# ADR-002: Criteria as rows, not JSON blobs

**Status:** accepted

## Context
Success criteria could live in a JSON column on cases.

## Decision
Criteria are their own table (`benchmark_criteria`) with type enum, label, position, and a small typed `config` JSON whose shape depends on the type. Results mirror this (`criteria_results` with snapshot label + detail).

## Consequences
- Referential integrity for results; criteria can be listed/joined/indexed.
- Deleting/replacing benchmark cases cascades cleanly; historical result labels survive via snapshots on `criterion_results.criterion_label`.

# ADR-003: Runs are append-only history

**Status:** accepted

## Context
Users must be able to replay "what did step 5 look like and why did it fail".

## Decision
Every evaluation persists a `run_steps` row (prompt content, score, tokens) with full per-case and per-criterion children. Mutations are recorded as separate `mutate` phase steps with rationale. Runs are never mutated after terminal status except cancellation.

## Consequences
- The UI can render the full evolution timeline from data alone.
- Storage grows with runs; acceptable at current scale, prune policy documented in operations docs later.

# ADR-004: Hill-climbing over fancy optimizers

**Status:** accepted

## Context
Prompt optimization could use beam search, bandits, or genetic approaches.

## Decision
Plain hill-climbing: evaluate → mutate incumbent best → keep if better (with 0.01 improvement threshold) → stop on target/stale(3)/max-steps. Every mutation is persisted even when rejected.

## Consequences
- Predictable cost: ≤ max_steps × (1 evaluation + 1 mutation call).
- Rejected mutations remain visible — negative evidence has product value ("this direction made it worse").
- Upgrading the strategy later only touches `EvolutionService`.
