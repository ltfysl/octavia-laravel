# AGENTS.md — app/Jobs

## What lives here
Queue jobs for long-running or external work: ProcessRunJob.php.

## Rules
- Make every job idempotent.
- Define timeout, retries and backoff.
- Fail gracefully onto the owning record.
