# AGENTS.md — app/Services/Llm/Providers

## What lives here
Concrete LLM provider implementations: MockLlmProvider.php, OpenAiCompatibleProvider.php.

## Rules
- Implement `LlmProvider` exactly; mock provider behavior is the test oracle.
- Wrap external HTTP with timeout and retry configuration.
- Keep providers stateless; configuration comes from `config/llm.php`.
