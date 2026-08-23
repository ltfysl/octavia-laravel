# AGENTS.md — database

## What lives here
Database layer: migrations, factories, seeders and the local sqlite file.

## Rules
- Never commit `database.sqlite` or other local DB files.
- Keep migrations versioned, paired and reversible.
- Use `database/migrations`, `database/factories`, `database/seeders` for their roles.
