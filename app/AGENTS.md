# AGENTS.md — app

## What lives here
Core Laravel application layer: domain logic, HTTP entry points and services.

## Rules
- Business logic belongs in `Services`, `Actions` or domain classes; keep models thin.
- All categorical values are Enums; never scatter magic strings.
- Use Policies, Form Requests and Resources for authorization, validation and API shapes.
- Queue slow work and keep Jobs idempotent.
