# AGENTS.md — app/Data

## What lives here
Typed domain data structures / DTOs.

## Rules
- Prefer typed DTOs over raw arrays across service boundaries.
- Transform only; never query the database from a DTO.
- Use attribute-based mapping only when key renaming is needed.
