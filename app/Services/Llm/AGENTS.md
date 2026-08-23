# AGENTS.md — app/Services/Llm

## What lives here
LLM provider manager, contract and implementations: LlmManager.php, LlmResponse.php.

## Rules
- New providers implement `Contracts/LlmProvider.php`.
- `LlmManager` resolves the configured default from the container.
- Keep providers isolated; no business logic about prompts/benchmarks here.
- Update tests whenever `MockLlmProvider` behavior changes.
