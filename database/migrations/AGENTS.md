# AGENTS.md — database/migrations

## What lives here
Schema migrations: 0001_01_01_000000_create_users_table.php, 0001_01_01_000001_create_cache_table.php, 0001_01_01_000002_create_jobs_table.php, 2026_08_21_000100_create_prompt_and_benchmark_tables.php, 2026_08_21_000200_create_runs_and_marketplace_tables.php, 2026_08_21_114721_create_personal_access_tokens_table.php, ….

## Rules
- One logical schema change per migration; no chained generation in one shell line.
- Snapshot labels (e.g., `criterion_label`) so history survives source edits.
- FKs cascade where children belong to parents; index hot paths.
