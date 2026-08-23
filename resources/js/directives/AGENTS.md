# AGENTS.md — resources/js/directives

## What lives here
Custom Vue directives: reveal.ts.

## Rules
- `v-reveal` is the only sanctioned scroll motion.
- Respect `prefers-reduced-motion`; never hide content under it.
- Keep directives focused and framework-agnostic in behavior.
