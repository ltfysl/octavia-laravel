# AGENTS.md — app/Data/Evaluation

## What lives here
Evaluation result DTOs: CaseOutcome.php, CriterionOutcome.php, EvaluationSummary.php.

## Rules
- Immutable value objects; no side effects.
- Used by `EvaluationService` and `EvolutionService`; do not mutate business state.
- Keep constructors explicit; add type hints for every property.
