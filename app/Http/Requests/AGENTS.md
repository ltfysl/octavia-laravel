# AGENTS.md — app/Http/Requests

## What lives here
Form request validation: StorePromptRequest.php, UpdatePromptRequest.php.

## Rules
- Reusable rules as traits or custom Rule classes.
- Never call `validate()` directly in controllers.
- Authorize explicitly in `authorize()`.
