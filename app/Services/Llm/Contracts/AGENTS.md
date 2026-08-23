# AGENTS.md — app/Services/Llm/Contracts

## What lives here
LLM provider contract(s): LlmProvider.php.

## Rules
- Keep the interface minimal and stable; changes ripple through all providers.
- Define explicit method signatures with typed returns.
- Avoid leaking implementation-specific configuration into the contract.
