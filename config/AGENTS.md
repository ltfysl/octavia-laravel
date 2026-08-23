# AGENTS.md — config

## What lives here
Laravel configuration files: app.php, auth.php, broadcasting.php, cache.php, database.php, filesystems.php, ….

## Rules
- Use env-backed defaults; no hardcoded secrets.
- Document new options in `.env.example`.
- Keep per-domain configs focused and typed where appropriate.
