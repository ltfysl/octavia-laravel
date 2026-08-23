# AGENTS.md — app/Services

## What lives here
Core business and domain services: CreditService.php, DiffService.php, EvaluationService.php, EvolutionService.php.

## Rules
- Services are stateless and operate on typed inputs/DTOs.
- Keep DB writes out of `EvaluationService` (see root AGENTS.md).
- `app/Services/Llm/` manages the LLM provider contract and concrete drivers.
- Add tests for service-level behavior and edge cases.
