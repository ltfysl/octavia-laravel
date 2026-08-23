# AGENTS.md — app/Broadcasting

## What lives here
Laravel channel authorization classes: AuthorizeRunChannel.php.

## Rules
- Verify user ownership/team membership before granting access.
- Keep channel logic simple; delegate to policies.
- Never leak run/benchmark state to unauthorized users.
