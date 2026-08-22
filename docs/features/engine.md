# The Evolution Engine

## What it does
Given a prompt and a set of benchmarks, Octavia:
1. **Evaluates** the prompt: for every benchmark case it calls the model, then scores each criterion.
2. **Mutates**: asks the provider (as "Octavia, prompt engineer") for an improved prompt given the unmet requirements.
3. **Selects**: keeps whichever prompt scores better; repeats until target score, stale limit or step budget.

## Scoring model

### Criterion types (`App\Enums\CriterionType`)
| Type | Config | Score |
|------|--------|-------|
| `contains` | `values: string[]` | fraction of values found (case-insensitive); passed = all found |
| `not_contains` | `values: string[]` | 1.0 if none found, else 0.0 |
| `regex` | `pattern: string` | 1.0/0.0; invalid patterns score 0.0 with detail `invalid_pattern` |
| `llm_judge` | `description: string` | provider judges; parses `SCORE: <float>` from reply; passed at ≥ 0.7 |

### Aggregation
- Case score = mean of its criteria scores. Case passes at ≥ 0.8.
- Run/benchmark score = weighted mean over cases (`weight` column, default 1).

## Judge protocol
Judge requests are marked with `[OCTAVIA-JUDGE]` in the system message and demand a final line `SCORE: <float>`. Any provider works as long as it can follow that instruction. Unparseable judge replies score 0 — visible in `criteria_results.detail.judge_raw`.

## Step lifecycle
```
pending → running → completed | failed | cancelled
```
- Each evaluation → `run_steps` row (`phase=evaluate`, `mutation_type=initial` on step 1).
- Each mutation proposal → `run_steps` row (`phase=mutate`, rationale + proposed prompt).
- Cancellation is cooperative: checked between steps.
- On completion, if the best prompt differs from the prompt's current version, it is persisted as a new `PromptVersion` ("Optimized via run #N") and becomes current.

## Failure handling
- No benchmarks attached → run fails immediately with an error message.
- Provider construction errors (missing key) → run fails before any model call.
- Unexpected exceptions → run fails with truncated message; full trace reported to the log.

## Configuration (`config/llm.php`, env-overridable)
| Key | Default | Meaning |
|-----|---------|---------|
| `OCTAVIA_LLM_PROVIDER` | mock | mock \| openai |
| `OPENAI_API_KEY`, `OPENAI_BASE_URL`, `OPENAI_MODEL` | – | openai driver config |
| `OCTAVIA_EVOLUTION_MAX_STEPS` | 8 | default step budget per optimize run |
| `OCTAVIA_EVOLUTION_TARGET_SCORE` | 0.95 | stop when reached |
| `OCTAVIA_EVOLUTION_STALE_STEPS` | 3 | stop after N non-improving evaluations |

## Testing
`tests/Feature/EvolutionEngineTest.php` covers scoring semantics, regex validation, a full climb-to-target run with version persistence, and the no-benchmark failure path — all against the deterministic mock.
